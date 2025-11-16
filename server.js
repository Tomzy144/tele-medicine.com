const WebSocket = require("ws");
const mysql = require("mysql2/promise");
const http = require("http");
const express = require("express");

const app = express();
const server = http.createServer(app);

const PORT = process.env.PORT || 8080;
const IS_PRODUCTION = process.env.NODE_ENV === 'production';

const wss = new WebSocket.Server({ server });

let db;
const clients = new Map(); // key: "doctor_123" or "patient_456", value: ws

app.get('/', (req, res) => res.send('WebSocket server running'));

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

// Helper to send JSON
function sendToClient(ws, data) {
  if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify(data));
}

// WebSocket connection
wss.on("connection", ws => {
  console.log("🔗 New client connected");

  ws.on("message", async message => {
    try {
      const data = JSON.parse(message);
      console.log("📥 Received:", data);

      // ---------- DOCTOR LOGIN ----------
      if (data.type === "doctor_login") {
        const doctorId = data.doctor_id?.trim();
        if (!doctorId) return sendToClient(ws, { type: "error", message: "doctor_id required" });

        // Track doctor connection
        clients.set("doctor_" + doctorId, ws);

        // Update online status
        await db.query("UPDATE doctor_tab SET online_status='1' WHERE doctor_id=?", [doctorId]);

        // Broadcast online status to all
        wss.clients.forEach(client => {
          sendToClient(client, { type: "doctor_status_update", doctor_id: doctorId, status: "online" });
        });

        // Send missed messages to doctor
        const [missed] = await db.query(
          `SELECT * FROM chat_messages WHERE doctor_id=? AND status IN ('sent','delivered')`,
          [doctorId]
        );
        if (missed.length) sendToClient(ws, { type: "missed_messages", data: missed });

        return;
      }

      // ---------- GET DOCTOR STATUS ----------
      if (data.type === "get_status") {
        const doctorId = data.doctor_id?.trim();
        if (!doctorId) return sendToClient(ws, { type: "error", message: "doctor_id required" });

        const [rows] = await db.query("SELECT online_status FROM doctor_tab WHERE doctor_id=?", [doctorId]);
        const status = rows.length ? (rows[0].online_status == 1 ? "online" : "offline") : "unknown";
        sendToClient(ws, { type: "doctor_status_update", doctor_id: doctorId, status });
        return;
      }

      // ---------- DOCTOR LOGOUT ----------
      if (data.type === "doctor_logout") {
        const doctorId = data.doctor_id?.trim();
        if (!doctorId) return;
        clients.delete("doctor_" + doctorId);
        await db.query("UPDATE doctor_tab SET online_status='0' WHERE doctor_id=?", [doctorId]);
        wss.clients.forEach(client => {
          sendToClient(client, { type: "doctor_status_update", doctor_id: doctorId, status: "offline" });
        });
        return;
      }

      // ---------- PATIENT LOGIN ----------
      if (data.type === "patient_login") {
        const patientId = data.patient_id?.trim();
        if (!patientId) return;
        clients.set("patient_" + patientId, ws);

        const [missed] = await db.query(
          `SELECT * FROM chat_messages WHERE patient_id=? AND status IN ('sent','delivered')`,
          [patientId]
        );
        if (missed.length) sendToClient(ws, { type: "missed_messages", data: missed });
        return;
      }

      // ---------- GET CHAT HISTORY ----------
      if (data.type === "get_history") {
        const doctorId = data.doctor_id?.trim();
        const patientId = data.patient_id?.trim();
        if (!doctorId || !patientId) return;

        const [rows] = await db.query(
          `SELECT sn, doctor_id, patient_id, sender, message, message_type, status, created_at 
           FROM chat_messages 
           WHERE doctor_id=? AND patient_id=? 
           ORDER BY created_at ASC`,
          [doctorId, patientId]
        );
        sendToClient(ws, { type: "history", data: rows });
        return;
      }

      // ---------- MARK SEEN ----------
      if (data.type === "mark_seen") {
        const { doctor_id, patient_id } = data;
        await db.query(
          "UPDATE chat_messages SET status='seen' WHERE doctor_id=? AND patient_id=?",
          [doctor_id, patient_id]
        );

        const patientWs = clients.get("patient_" + patient_id);
        if (patientWs) sendToClient(patientWs, { type: "messages_seen", doctor_id, patient_id });
        return;
      }

      // ---------- SEND CHAT ----------
      if (data.type === "chat") {
        const { doctor_id, patient_id, sender, message: msg, message_type } = data;

        const [result] = await db.query(
          `INSERT INTO chat_messages (doctor_id, patient_id, sender, message, message_type, status) 
           VALUES (?, ?, ?, ?, ?, 'sent')`,
          [doctor_id, patient_id, sender, msg, message_type || "text"]
        );

        // Update recent_chat_tab
        await db.query(
          `INSERT INTO recent_chat_tab (doctor_id, patient_id, last_time_contacted)
           VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE last_time_contacted = NOW()`,
          [doctor_id, patient_id]
        );

        const newMsg = {
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
          sendToClient(ws, { type: "message_delivered", message_id: result.insertId, delivered_to: "doctor" });
        }

        // Deliver to patient
        const patientWs = clients.get("patient_" + patient_id);
        if (patientWs) {
          if (sender === "doctor") {
            await db.query("UPDATE chat_messages SET status='delivered' WHERE sn=?", [result.insertId]);
            newMsg.status = "delivered";
          }
          sendToClient(patientWs, newMsg);
          sendToClient(ws, { type: "message_delivered", message_id: result.insertId, delivered_to: "patient" });
        }
        return;




        
      }

      // ---------- PRESCRIPTION ----------
      if (data.type === "prescription_added") {
        const { doctor_id, patient_id, prescription, tempId } = data;
        try {
          const [result] = await db.query(
            `INSERT INTO prescriptions_tab (doctor_id, patient_id, prescription, date, created_at) 
             VALUES (?, ?, ?, CURDATE(), NOW())`,
            [doctor_id, patient_id, prescription]
          );
          sendToClient(ws, { type: "prescription_added", success: true, sn: result.insertId, doctor_id, patient_id, prescription, tempId });
        } catch (err) {
          if (err.code === "ER_DUP_ENTRY") {
            sendToClient(ws, { type: "prescription_added", success: false, duplicate: true, prescription, tempId });
          } else {
            sendToClient(ws, { type: "prescription_added", success: false, error: true, prescription, tempId });
          }
        }
        return;
      }


      // ---------- VIDEO CALL SIGNALING ----------
          if (data.type === "video_offer") {
            // data: { type: 'video_offer', from: 'doctor_123', to: 'patient_456', sdp: ... }
            const recipientWs = clients.get(data.to);
            if (recipientWs) {
              sendToClient(recipientWs, {
                type: "video_offer",
                from: data.from,
                sdp: data.sdp
              });
            }
            return;
          }

          if (data.type === "video_answer") {
            const recipientWs = clients.get(data.to);
            if (recipientWs) {
              sendToClient(recipientWs, {
                type: "video_answer",
                from: data.from,
                sdp: data.sdp
              });
            }
            return;
          }

          if (data.type === "ice_candidate") {
            const recipientWs = clients.get(data.to);
            if (recipientWs) {
              sendToClient(recipientWs, {
                type: "ice_candidate",
                from: data.from,
                candidate: data.candidate
              });
            }
            return;
          }




    } catch (err) {
      console.error("❌ Error handling message:", err);
    }
  });

  ws.on("close", async () => {
    console.log("❌ Client disconnected");

    for (const [userId, clientWs] of clients.entries()) {
      if (clientWs === ws) {
        clients.delete(userId);

        if (userId.startsWith("doctor_")) {
          const doctorId = userId.split("_")[1];
          await db.query("UPDATE doctor_tab SET online_status='0' WHERE doctor_id=?", [doctorId]);
          wss.clients.forEach(client => {
            sendToClient(client, { type: "doctor_status_update", doctor_id: doctorId, status: "offline" });
          });
        }
        break;
      }
    }
  });
});

server.listen(PORT, () => {
  console.log(`🚀 Server running on port ${PORT}`);
});
