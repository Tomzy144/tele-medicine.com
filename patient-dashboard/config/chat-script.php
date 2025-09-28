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

    // --- Render a single message ---
    function createMessage(msgData) {
        const msgClass = msgData.sender === "patient" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;
        innerHTML += msgData.sender === "patient"
            ? `<span class="ticks">${getTickHTML(msgData.status)}</span>`
            : `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        $("<div>").addClass(msgClass).html(innerHTML).appendTo(chatMessages);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // --- Load chat history once (initial only) ---
    function loadChatHistory() {
        $.post(endPoint, { action: "load_messages", patient_id: patientId, doctor_id: doctorId }, (res) => {
            chatMessages.empty();
            if (Array.isArray(res) && res.length) res.forEach(createMessage);
        }, "json");
    }

    // --- Load doctor status once (initial only) ---
    function loadDoctorStatus() {
        $.post(endPoint, { action: "doctor_status", doctor_id: doctorId }, (res) => {
            updateDoctorStatus(res.online === true || res.online === "1" || res.online === 1);
        }, "json");
    }

    // --- Update doctor status display ---
    function updateDoctorStatus(online) {
        doctorStatus.text(online ? "Online" : "Offline")
                    .css("color", online ? "green" : "gray");
    }

    // --- WebSocket connection (no polling) ---
    let ws;
    function connectWebSocket() {
        ws = new WebSocket("ws://localhost:8080");

        ws.onopen = () => {
            console.log("✅ WebSocket connected");
            ws.send(JSON.stringify({ type: "status", role: "patient", id: patientId, doctor_id: doctorId }));

            loadChatHistory();  // initial messages
            loadDoctorStatus(); // initial doctor status
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log("📥 WS Message:", data);

            if (data.type === "chat") {
                if (!(data.sender === "patient" && data.patient_id === patientId)) {
                    createMessage(data);
                }
            }

            if (data.type === "status" && data.role === "doctor" && data.id === doctorId) {
                updateDoctorStatus(data.online === true || data.online === "1" || data.online === 1);
            }
        };

        ws.onclose = () => {
            console.warn("❌ WebSocket closed. Reconnecting in 5s...");
            setTimeout(connectWebSocket, 5000);
        };

        ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
    }
    connectWebSocket();

    // --- Send chat ---
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
            status: "sent",
            timestamp: new Date().toISOString()
        };

        createMessage(msgData);

        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msgData));
        }

        // still save to DB
        $.post(endPoint, { action: "send_message", ...msgData });

        chatInput.val("");
    }

    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

    // --- Add to prescription ---
    window.addToPrescription = (btn) => {
        const messageText = $(btn).siblings(".text").text();
        $.post(endPoint, { action: "add_to_prescription", message: messageText, patient_id: patientId, doctor_id: doctorId }, () => {
            $(btn).text("✅");
        }, "json");
    };
}
</script>
