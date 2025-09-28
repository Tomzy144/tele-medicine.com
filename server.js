const WebSocket = require("ws");
const mysql = require("mysql2/promise");

const wss = new WebSocket.Server({ port: 8080 });

let db;

// Connect to MySQL
(async () => {
  try {
    db = await mysql.createConnection({
      host: "localhost",
      user: "root",
      password: "",
      database: "tele_medicine_db"
    });
    console.log("✅ MySQL connected.");
  } catch (err) {
    console.error("❌ MySQL connection failed:", err);
    process.exit(1);
  }
})();

// Broadcast helper
function broadcast(data) {
  wss.clients.forEach(client => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(data));
    }
  });
}

wss.on("connection", ws => {
  console.log("🔗 New client connected");

  ws.on("message", async message => {
    try {
      const data = JSON.parse(message);
      console.log("📥 Received:", data);

      // Doctor logs in
      if (data.type === "doctor_login") {
        await db.query("UPDATE doctor_tab SET online_status = '1' WHERE doctor_id = ?", [data.doctor_id]);
        broadcast({ type: "doctor_status_update", doctor_id: data.doctor_id, status: "online" });
      }

      // Doctor logs out
      if (data.type === "doctor_logout") {
        await db.query("UPDATE doctor_tab SET online_status = '0' WHERE doctor_id = ?", [data.doctor_id]);
        broadcast({ type: "doctor_status_update", doctor_id: data.doctor_id, status: "offline" });
      }

      // Patient/doctor asks for history
      if (data.type === "get_history") {
        const [rows] = await db.query(
          `SELECT sn, doctor_id, patient_id, sender, message, message_type, status, created_at 
           FROM chat_messages 
           WHERE doctor_id=? AND patient_id=? 
           ORDER BY created_at ASC`,
          [data.doctor_id, data.patient_id]
        );
        ws.send(JSON.stringify({ type: "history", data: rows }));
      }

      // Patient/doctor asks for status
      if (data.type === "get_status") {
        const [rows] = await db.query("SELECT online_status FROM doctor_tab WHERE doctor_id=?", [data.doctor_id]);
        if (rows.length) {
          ws.send(JSON.stringify({
            type: "doctor_status_update",
            doctor_id: data.doctor_id,
            status: rows[0].online_status === 1 ? "online" : "offline"
          }));
        }
      }

      // New chat message
      if (data.type === "chat") {
        const { doctor_id, patient_id, sender, message: msg, message_type, status } = data;

        try {
          const [result] = await db.query(
            "INSERT INTO chat_messages (doctor_id, patient_id, sender, message, message_type, status) VALUES (?, ?, ?, ?, ?, ?)",
            [doctor_id, patient_id, sender, msg, message_type || "text", status || "sent"]
          );

          console.log("✅ Chat saved with ID:", result.insertId);

          const newMsg = {
            type: "new_message",
            sn: result.insertId,
            doctor_id,
            patient_id,
            sender,
            message: msg,
            message_type: message_type || "text",
            status: status || "sent",
            created_at: new Date().toISOString()
          };

          broadcast(newMsg);
        } catch (err) {
          console.error("❌ DB Insert Error:", err.sqlMessage || err);
          ws.send(JSON.stringify({ type: "error", message: "DB insert failed" }));
        }
      }

      // Add to prescription
      if (data.type === "add_to_prescription") {
        console.log(`📝 Prescription add: ${data.message}`);
        // You can insert into prescriptions table here if needed
        ws.send(JSON.stringify({ type: "prescription_added", success: true }));
      }

    } catch (err) {
      console.error("❌ Error handling message:", err);
    }
  });

  ws.on("close", () => {
    console.log("❌ Client disconnected");
  });
});
