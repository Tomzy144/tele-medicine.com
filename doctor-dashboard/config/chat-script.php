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
        seen: '<span style="color:blue">✓✓</span>'
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
        const msgClass = msgData.sender === "doctor" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;

        if (msgData.sender === "patient") {
            innerHTML += `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        } else {
            innerHTML += `<span class="ticks">${getTickHTML(msgData.status)}</span>`;
        }

        $("<div>")
            .addClass(msgClass)
            .attr("data-id", msgData.sn)
            .html(innerHTML)
            .appendTo(chatMessages);

        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // --- Doctor status update ---
    function updateDoctorStatus(online) {
        doctorStatus.text(online ? "Online" : "Offline")
                    .css("color", online ? "green" : "gray");
    }

    // --- WebSocket connection ---
    let ws;
    let reconnectAttempts = 0;
    const MAX_RECONNECT = 5;

    function connectWebSocket() {
     const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://yemasconsults.co.uk/tele-medicine-chat-server";

        const socket = new WebSocket(wsUrl);

        socket.onopen = () => console.log("✅ WebSocket connected");
socket.onerror = err => console.error("❌ WebSocket error:", err);
socket.onclose = () => console.log("🔌 WebSocket closed");




        ws = new WebSocket(wsUrl);

        ws.onopen = () => {
            console.log("✅ WebSocket connected");
            reconnectAttempts = 0;

            // Doctor login
            ws.send(JSON.stringify({ type: "doctor_login", doctor_id: doctorId }));

            // Fetch chat history & status
            ws.send(JSON.stringify({ type: "get_history", doctor_id: doctorId, patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_status", doctor_id: doctorId }));
        };

        ws.onmessage = (event) => {
            let data;
            try {
                data = JSON.parse(event.data);
                console.log("📩 WS Message:", data);
            } catch (e) {
                console.error("❌ Error parsing message:", e);
                return;
            }

            switch (data.type) {
                case "history":
                case "missed_messages":
                    if (!Array.isArray(data.data)) break;

                    data.data.forEach(msg => {
                        // Avoid duplicates
                        if ($(`.message[data-id="${msg.sn}"]`).length === 0) {
                            createMessage(msg);
                        }
                    });

                    const unseenIds = data.data
                        .filter(m => m.sender === "patient" && m.status !== "seen")
                        .map(m => m.sn);

                    if (unseenIds.length) {
                        ws.send(JSON.stringify({
                            type: "mark_seen",
                            message_ids: unseenIds,
                            doctor_id: doctorId,
                            patient_id: patientId
                        }));
                    }
                    break;


                case "new_message":
                    createMessage(data);
                    if (data.sender === "patient") {
                        const audio = new Audio('/assets/notification.mp3');
                        audio.play().catch(() => {});
                        ws.send(JSON.stringify({
                            type: "mark_seen",
                            message_ids: [data.sn],
                            doctor_id: doctorId,
                            patient_id: patientId
                        }));
                    }
                    break;

                case "doctor_status_update":
                    if (data.doctor_id == doctorId) updateDoctorStatus(data.status === "online");
                    break;

                case "message_delivered":
                    $(`.message[data-id="${data.message_id}"] .ticks`).html(getTickHTML("delivered"));
                    break;

                case "messages_seen":
                    $(`.message[data-sender="doctor"] .ticks`).html(getTickHTML("seen"));
                    break;

                case "prescription_added":
                    handlePrescriptionResponse(data);
                    break;

                case "error":
                    console.error("⚠️ Server error:", data.message);
                    break;
            }
        };

        ws.onclose = () => {
            console.warn("❌ WebSocket closed. Reconnecting...");
            if (reconnectAttempts < MAX_RECONNECT) {
                setTimeout(() => {
                    reconnectAttempts++;
                    connectWebSocket();
                }, 2000 * reconnectAttempts);
            } else {
                alert("Connection lost. Please refresh the page.");
            }
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
            sender: "doctor",
            message: text,
            patient_id: patientId,
            doctor_id: doctorId,
            message_type: "text",
            status: "sent"
        };

        if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify(msgData));
        chatInput.val("");
    }

    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

    // --- Add to prescription ---
    window.addToPrescription = function(btn) {
        const messageText = $(btn).siblings(".text").text().trim();
        const tempId = Date.now() + "_" + Math.random().toString(36).substr(2, 5);

        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: "prescription_added",
                patient_id: patientId,
                doctor_id: doctorId,
                prescription: messageText,
                tempId
            }));

            $(btn).attr("data-tempid", tempId).text("⏳");

            const pendingDiv = $("<div>")
                .addClass("message sent")
                .attr("data-tempid", tempId)
                .html(`⏳ Adding '${messageText}' to prescription list...`);

            $("#chatMessages").append(pendingDiv);
            $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
        } else {
            alert("⚠️ Connection lost. Prescription not saved.");
        }
    };

    function handlePrescriptionResponse(data) {
        const { success, duplicate, error, tempId } = data;
        const targetBtn = $(`.reaction-btn[data-tempid="${tempId}"]`);
        const feedback = $(`.message.sent[data-tempid="${tempId}"]`);

        if (success) {
            targetBtn.text("✅").prop("disabled", true);
            feedback.html("✅ Prescription added successfully.");
        } else if (duplicate) {
            targetBtn.text("➕").prop("disabled", false);
            feedback.html("⚠️ Prescription already exists for today.");
        } else if (error) {
            targetBtn.text("➕").prop("disabled", false);
            feedback.html("❌ Error saving prescription. Try again.");
        }

        $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
    }
}
</script>
