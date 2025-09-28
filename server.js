const WebSocket = require("ws");
const wss = new WebSocket.Server({ port: 8080 });

// Map to track connected clients
const clients = new Map();

// Mock DB functions (replace with real DB queries)
const db = {
    chats: [],
    async saveChat(message) {
        this.chats.push(message);
        console.log("Saved message to DB:", message); // log saved message
        return { status: "sent" };
    },
    async getChatHistory(patient_id, doctor_id) {
        const history = this.chats.filter(
            msg => msg.patient_id === patient_id && msg.doctor_id === doctor_id
        );
        console.log(`Fetched chat history for patient ${patient_id} and doctor ${doctor_id}:`, history);
        return history;
    }
};

wss.on("connection", (ws) => {
    console.log("New client connected");

    ws.on("message", async (message) => {
        let data;
        try {
            data = JSON.parse(message);
        } catch (err) {
            console.error("Invalid JSON:", message);
            return;
        }

        // --- Handle client status updates ---
        if (data.type === "status") {
            clients.set(data.id, { ws, role: data.role, doctor_id: data.doctor_id });
            console.log(`Client connected: ${data.id} (${data.role})`);

            // If patient connects, send doctor status immediately
            if (data.role === "patient" && data.doctor_id) {
                const doctor = clients.get(data.doctor_id);
                const online = !!doctor;
                ws.send(JSON.stringify({
                    type: "status",
                    role: "doctor",
                    id: data.doctor_id,
                    online
                }));
                console.log(`Sent doctor status to patient ${data.id}: online = ${online}`);

                // Also send chat history immediately
                const history = await db.getChatHistory(data.id, data.doctor_id);
                ws.send(JSON.stringify({ type: "chat_history", messages: history }));
            }

            // If doctor connects, broadcast online status to all their patients
            if (data.role === "doctor") {
                clients.forEach((client) => {
                    if (client.role === "patient" && client.doctor_id === data.id) {
                        client.ws.send(JSON.stringify({
                            type: "status",
                            role: "doctor",
                            id: data.id,
                            online: true
                        }));
                        console.log(`Broadcasted doctor ${data.id} online status to patient ${client.id}`);
                    }
                });
            }
        }

        // --- Handle fetching chat history manually ---
        if (data.type === "fetch_history") {
            const messages = await db.getChatHistory(data.patient_id, data.doctor_id);
            ws.send(JSON.stringify({ type: "chat_history", messages }));
            console.log(`Sent chat history to ${data.patient_id}`);
        }

        // --- Handle new chat messages ---
        if (data.type === "chat") {
            await db.saveChat(data);

            // Relay to recipient
            clients.forEach((client, clientId) => {
                if (
                    (data.sender === "patient" && client.role === "doctor" && clientId === data.doctor_id) ||
                    (data.sender === "doctor" && client.role === "patient" && clientId === data.patient_id)
                ) {
                    client.ws.send(JSON.stringify(data));
                    console.log(`Relayed message from ${data.sender} to ${clientId}`);
                }
            });
        }

        // --- Handle refresh/disconnect ---
        if (data.type === "disconnecting") {
            console.log(`Client is refreshing/leaving: ${data.id}`);
        }
    });

    ws.on("close", () => {
        for (const [id, client] of clients.entries()) {
            if (client.ws === ws) {
                console.log(`Client disconnected fully: ${id}`);
                clients.delete(id);
                break;
            }
        }
    });
});

console.log("WebSocket server running on ws://localhost:8080");
