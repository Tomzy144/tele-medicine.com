<script>
function refreshChat() {
    const chatMessages = $("#chatMessages");
    const chatInput = $("#chatInput");
    const doctorStatus = $("#doctorStatus");
    const patientId = $("#patient_id").val();
    const doctorId = $("#doctor_id").val();

    // --- Tick display helper ---
    const getTickHTML = (status) => ({
        sent: "✓",
        delivered: "✓✓",
        read: '<span style="color:blue">✓✓</span>'
    }[status] || "✓");

    // --- Censor helper ---
    function censorMessage(text) {
        return text
            .replace(/\+?\d[\d\s-]{6,}\d/g, "****")
            .replace(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/gi, "****")
            .replace(/\b(password|pass|pwd|secret)\b/gi, "****");
    }

    // --- Render one message ---
    function createMessage(msgData) {
        const msgClass = msgData.sender === "patient" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;
        innerHTML += msgData.sender === "patient"
            ? `<span class="ticks">${getTickHTML(msgData.status)}</span>`
            : `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        $("<div>").addClass(msgClass).html(innerHTML).appendTo(chatMessages);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // --- Doctor status update ---
    function updateDoctorStatus(online) {
        doctorStatus.text(online ? "Online" : "Offline")
                    .css("color", online ? "green" : "gray");
    }

    // --- WebSocket connection ---
    let ws;
    function connectWebSocket() {
        ws = new WebSocket("ws://localhost:8080");

        ws.onopen = () => {
            console.log("✅ WebSocket connected");

            // ask server for chat history & doctor status
            ws.send(JSON.stringify({ type: "get_history", doctor_id: doctorId, patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_status", doctor_id: doctorId }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log("📥 WS Message:", data);

            if (data.type === "history") {
                chatMessages.empty();
                data.data.forEach(createMessage);
            }

            if (data.type === "new_message") {
                createMessage(data);
            }

            if (data.type === "doctor_status_update" && data.doctor_id === doctorId) {
                updateDoctorStatus(data.status === "online");
            }

            if (data.type === "prescription_added") {
                console.log("📝 Prescription successfully saved.");
            }
        };

        ws.onclose = () => {
            console.warn("❌ WebSocket closed. Reconnecting in 5s...");
            setTimeout(connectWebSocket, 5000);
        };

        ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
    }
    connectWebSocket();

    // --- Send chat message ---
    function send_chat(event) {
        if (event && event.type === "keydown" && event.key !== "Enter") return;
        event?.preventDefault();

        const text = chatInput.val().trim();
        if (!text) return;

        const msgData = {
            type: "chat",
            sender: "patient",
            message: text,
            patient_id: patientId,
            doctor_id: doctorId,
            message_type: "text",
            status: "sent"
        };

        // send only (server saves + broadcasts back)
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msgData));
        }

        chatInput.val("");
    }

    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

    // --- Add to prescription ---
    window.addToPrescription = (btn) => {
        const messageText = $(btn).siblings(".text").text();
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: "add_to_prescription",
                message: messageText,
                patient_id: patientId,
                doctor_id: doctorId
            }));
            $(btn).text("✅");
        }
    };
}
</script>
