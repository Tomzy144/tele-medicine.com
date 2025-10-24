const WebSocket = require("ws");
const mysql = require("mysql2/promise");

const PORT = process.env.PORT || 8082;
const wss = new WebSocket.Server({ port: PORT });

let db;
const clients = new Map();

// Connect to MySQL
(async () => {
  try {
    db = await mysql.createConnection({
      host: process.env.DB_HOST || "localhost",
      user: process.env.DB_USER || "root",
      password: process.env.DB_PASS || "",
      database: process.env.DB_NAME || "tele_medicine_db"
    });
    console.log("✅ MySQL connected.");
  } catch (err) {
    console.error("❌ MySQL connection failed:", err);
    process.exit(1);
  }
})();



console.log(`🚀 WebSocket server running on port ${PORT}`);


// WebSocket logic
wss.on("connection", (ws) => {
  console.log("✅ Client connected.");
  ws.on("message", (msg) => console.log("📩", msg.toString()));
  ws.on("close", () => console.log("❌ Client disconnected."));
});

// Start HTTP + WS on same Render port
server.listen(PORT, () => {
  console.log(`🚀 WebSocket + HTTP running on port ${PORT}`);
});


function sendToClient(ws, data) {
  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(data));
  }
}

wss.on("connection", ws => {
  console.log("🔗 New client connected");

  ws.on("message", async message => {
    try {
      const data = JSON.parse(message);
      console.log("📥 Received:", data);

     
      // ---------- DOCTOR LOGIN ----------
          if (data.type === "doctor_login") {
        console.log("Doctor login attempt:", data.doctor_id);

        // now check doctor status
        if (data.type === "get_status") {
          console.log("Doctor ID received for get_status:", data.doctor_id);

          if (!data.doctor_id) {
            console.error("get_status failed: doctor_id missing or empty");
            sendToClient(ws, { type: "error", message: "doctor_id required" });
            return;
          }

          const [rows] = await db.query(
            "SELECT online_status FROM doctor_tab WHERE doctor_id = ?",
            [data.doctor_id.trim()]
          );

          if (rows.length) {
            sendToClient(ws, { 
              type: "doctor_status", 
              doctor_id: data.doctor_id, 
              status: rows[0].online_status === 1 ? "online" : "offline" 
            });
          } else {
            sendToClient(ws, { type: "doctor_status", doctor_id: data.doctor_id, status: "not_found" });
          }
        }

        // Broadcast status
        wss.clients.forEach(client => {
          sendToClient(client, { 
            type: "doctor_status_update", 
            doctor_id: data.doctor_id, 
            status: "online" 
          });
        });

        // Send missed messages
        const [missed] = await db.query(
          `SELECT * FROM chat_messages 
          WHERE doctor_id=? AND status IN ('sent','delivered')`,
          [data.doctor_id]
        );

        if (missed.length) {
          sendToClient(ws, { type: "missed_messages", data: missed });
        }
      }


      // ---------- GET DOCTOR STATUS ----------
      if (data.type === "get_status") {
        if (!data.doctor_id || data.doctor_id.trim() === "") {
          console.error("get_status failed: doctor_id missing or empty");
          sendToClient(ws, { type: "error", message: "doctor_id required" });
          return;
        }

        const [rows] = await db.query(
          "SELECT online_status FROM doctor_tab WHERE doctor_id=?",
          [data.doctor_id]
        );

        if (rows.length) {
          sendToClient(ws, { 
            type: "doctor_status", 
            doctor_id: data.doctor_id, 
            status: rows[0].online_status == 1 ? "online" : "offline" 
          });
        } else {
          sendToClient(ws, { 
            type: "doctor_status", 
            doctor_id: data.doctor_id, 
            status: "unknown" 
          });
        }
      }



      if (data.type === "mark_seen") {
        await db.query("UPDATE chat_messages SET status='seen' WHERE doctor_id=? AND patient_id=?", 
          [data.doctor_id, data.patient_id]);

        // notify patient that doctor has read their messages
        const patientWs = clients.get("patient_" + data.patient_id);
        if (patientWs) {
          sendToClient(patientWs, { type: "messages_seen", doctor_id: data.doctor_id, patient_id: data.patient_id });
        }
      }



      // ---------- DOCTOR LOGOUT ----------
      if (data.type === "doctor_logout") {
        clients.delete("doctor_" + data.doctor_id);
        await db.query("UPDATE doctor_tab SET online_status='0' WHERE doctor_id=?", [data.doctor_id]);
        wss.clients.forEach(client => {
          sendToClient(client, { type: "doctor_status_update", doctor_id: data.doctor_id, status: "offline" });
        });
      }

      // ---------- PATIENT LOGIN ----------
      if (data.type === "patient_login") {
        clients.set("patient_" + data.patient_id, ws);

        // Send missed messages (sent or delivered)
        const [missed] = await db.query(
          `SELECT * FROM chat_messages WHERE patient_id=? AND status IN ('sent','delivered')`,
          [data.patient_id]
        );
        if (missed.length) {
          sendToClient(ws, { type: "missed_messages", data: missed });
        }
      }

      // ---------- GET CHAT HISTORY ----------
      if (data.type === "get_history") {
        const [rows] = await db.query(
          `SELECT sn, doctor_id, patient_id, sender, message, message_type, status, created_at 
           FROM chat_messages 
           WHERE doctor_id=? AND patient_id=? 
           ORDER BY created_at ASC`,
          [data.doctor_id, data.patient_id]
        );
        sendToClient(ws, { type: "history", data: rows });
      }

      // ---------- GET DOCTOR STATUS ----------
      if (data.type === "get_status") {
        const [rows] = await db.query("SELECT online_status FROM doctor_tab WHERE doctor_id=?", [data.doctor_id]);
        if (rows.length) {
          sendToClient(ws, {
            type: "doctor_status_update",
            doctor_id: data.doctor_id,
            status: rows[0].online_status == 1 ? "online" : "offline"
          });
        }
      }


          // ---------- SEND CHAT MESSAGE ----------
        if (data.type === "chat") {
          const { doctor_id, patient_id, sender, message: msg, message_type } = data;

          // Save chat message
          const [result] = await db.query(
            `INSERT INTO chat_messages 
              (doctor_id, patient_id, sender, message, message_type, status) 
            VALUES (?, ?, ?, ?, ?, ?)`,
            [doctor_id, patient_id, sender, msg, message_type || "text", "sent"]
          );

          console.log("✅ Chat saved with ID:", result.insertId);

          // --- Maintain recent_chat_tab ---
          await db.query(
            `INSERT INTO recent_chat_tab (doctor_id, patient_id, last_time_contacted)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_time_contacted = NOW()`,
            [doctor_id, patient_id]
          );

          // Build new message object
          let newMsg = {
            type: "new_message",
            sn: result.insertId,
            doctor_id,
            patient_id,
            sender,
            message: msg,
            message_type: message_type || "text",
            status: "sent",
            created_at: new Date().toISOString()
          };



        // Deliver to doctor
        const doctorWs = clients.get("doctor_" + doctor_id);
        if (doctorWs) {
          await db.query("UPDATE chat_messages SET status='delivered' WHERE sn=?", [result.insertId]);
          newMsg.status = "delivered";
          sendToClient(doctorWs, newMsg);

          // Notify sender
          const senderKey = sender === "doctor" ? "doctor_" + doctor_id : "patient_" + patient_id;
          const senderWs = clients.get(senderKey);
          if (senderWs) {
            sendToClient(senderWs, { type: "message_delivered", message_id: result.insertId, delivered_to: "doctor" });
          }
        }

        // Deliver to patient
        const patientWs = clients.get("patient_" + patient_id);
        if (patientWs) {
          if (sender === "doctor") {
            await db.query("UPDATE chat_messages SET status='delivered' WHERE sn=?", [result.insertId]);
            newMsg.status = "delivered";
          }
          sendToClient(patientWs, newMsg);

          const senderKey = sender === "doctor" ? "doctor_" + doctor_id : "patient_" + patient_id;
          const senderWs = clients.get(senderKey);
          if (senderWs) {
            sendToClient(senderWs, { type: "message_delivered", message_id: result.insertId, delivered_to: "patient" });
          }
        }
      }

    if (data.type === "prescription_added") {
    const { doctor_id, patient_id, prescription, tempId } = data; // ✅ receive tempId

    try {
      const [result] = await db.query(
        "INSERT INTO prescriptions_tab (doctor_id, patient_id, prescription, date, created_at) VALUES (?, ?, ?, CURDATE(), NOW())",
        [doctor_id, patient_id, prescription]
      );

      ws.send(JSON.stringify({
        type: "prescription_added",
        success: true,
        sn: result.insertId,
        doctor_id,
        patient_id,
        prescription,
        date: new Date().toISOString(),
        tempId   // ✅ send back tempId
      }));

    } catch (err) {
      if (err.code === "ER_DUP_ENTRY") {
        ws.send(JSON.stringify({
          type: "prescription_added",
          success: false,
          duplicate: true,
          prescription,
          tempId   // ✅ send back tempId
        }));
      } else {
        ws.send(JSON.stringify({
          type: "prescription_added",
          success: false,
          error: true,
          prescription,
          tempId   // ✅ send back tempId
        }));
      }
    }
  }







    } catch (err) {
      console.error("❌ Error handling message:", err);
    }
  });

  // ---------- WEBSOCKET CLOSE ----------
  ws.on("close", () => {
    console.log("❌ Client disconnected");

    for (const [userId, clientWs] of clients.entries()) {
      if (clientWs === ws) {
        clients.delete(userId);

        // Doctor disconnect
        if (userId.startsWith("doctor_")) {
          const doctorId = userId.split("_")[1];
          db.query("UPDATE doctor_tab SET online_status='0' WHERE doctor_id=?", [doctorId]);
          wss.clients.forEach(client => {
            sendToClient(client, { type: "doctor_status_update", doctor_id: doctorId, status: "offline" });
          });
        }

        break;
      }
    }
  });

});
