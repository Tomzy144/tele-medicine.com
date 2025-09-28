const WebSocket = require("ws");
const mysql = require("mysql2/promise");

const wss = new WebSocket.Server({ port: 8080 });

let db;

// Connect to MySQL
(async () => {
  db = await mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "tele_medicine_db"
  });
  console.log("✅ MySQL connected.");
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

      // New chat message
      if (data.type === "chat") {
        const { doctor_id, patient_id, sender, message: msg, message_type, status } = data;

        const [result] = await db.query(
          "INSERT INTO chat_messages (doctor_id, patient_id, sender, message, message_type, status) VALUES (?, ?, ?, ?, ?, ?)",
          [doctor_id, patient_id, sender, msg, message_type || "text", status || "sent"]
        );

        broadcast({
          type: "new_message",
          sn: result.insertId,
          doctor_id,
          patient_id,
          sender,
          message: msg,
          message_type: message_type || "text",
          status: status || "sent",
          created_at: new Date().toISOString()
        });
      }
    } catch (err) {
      console.error("❌ Error handling message:", err);
    }
  });

  ws.on("close", () => {
    console.log("❌ Client disconnected");
  });
});
