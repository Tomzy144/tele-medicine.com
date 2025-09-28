<script>
function refreshChat() {
    const chatMessages = $("#chatMessages");
    const chatInput = $("#chatInput");
    const doctorStatus = $("#doctorStatus");
    const patientId = $("#patient_id").val();
    const doctorId = $("#doctor_id").val();

    // Ticks display
    function getTickHTML(status) {
        switch(status) {
            case 'sent': return "✓";
            case 'delivered': return "✓✓";
            case 'read': return '<span style="color:blue">✓✓</span>';
            default: return "✓";
        }
    }

    // Censor sensitive info
    function censorMessage(text) {
        const phoneRegex = /\+?\d[\d\s-]{6,}\d/g;
        const emailRegex = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i;
        const passwordRegex = /\b(password|pass|pwd|secret)\b/gi;
        return text.replace(phoneRegex, "****")
                   .replace(emailRegex, "****")
                   .replace(passwordRegex, "****");
    }

    // Display a single message
    function createMessage(msgData) {
        const msgClass = msgData.sender === "patient" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;

        if (msgData.sender === "patient") {
            innerHTML += `<span class="ticks">${getTickHTML(msgData.status)}</span>`;
        } else {
            innerHTML += `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        }

        const msgDiv = $("<div>").addClass(msgClass).html(innerHTML);
        chatMessages.append(msgDiv);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // --- Load chat history from PHP backend ---
    function loadChatHistory() {
        $.post(endPoint, {
            action: "load_messages",
            patient_id: patientId,
            doctor_id: doctorId
        }, function(res) {
            if (res && res.length) {
                chatMessages.empty();
                res.forEach(msg => createMessage(msg));
                console.log("📝 Chat history loaded from backend");
            }
        }, "json");
    }

    // --- Initialize WebSocket ---
    const ws = new WebSocket("ws://localhost:8080");

    ws.onopen = () => {
        console.log("✅ Connected to WebSocket server");

        // Send patient status
        ws.send(JSON.stringify({
            type: "status",
            role: "patient",
            id: patientId,
            doctor_id: doctorId
        }));

        // Load chat history initially
        loadChatHistory();
    };

    ws.onclose = (event) => console.warn("❌ WebSocket closed:", event);
    ws.onerror = (error) => console.error("⚠️ WebSocket error:", error);

    ws.onmessage = (event) => {
        console.log("📥 Message received:", event.data);
        const data = JSON.parse(event.data);

        if (data.type === "chat") {
            // Only render messages not already sent by this patient locally
            if (!(data.sender === "patient" && data.patient_id === patientId)) {
                createMessage(data);
                console.log("💬 New message displayed from server");
            }
        }

        if (data.type === "status" && data.role === "doctor" && data.id === doctorId) {
            const online = data.online;
            doctorStatus.text(online ? "Online" : "Offline")
                        .css("color", online ? "green" : "gray");
            console.log(`👨‍⚕️ Doctor is ${online ? "online" : "offline"}`);
        }
    };

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
            status: "sent",
            timestamp: new Date().toISOString()
        };

        createMessage(msgData);          // Display locally
        ws.send(JSON.stringify(msgData)); // Send to server
        console.log("📤 Message sent to server:", msgData);

        // Persist via PHP backend
        $.post(endPoint, {
            action: "send_message",
            sender: "patient",
            patient_id: patientId,
            doctor_id: doctorId,
            message: text,
            message_type: "text"
        }, (res) => console.log("Saved to DB:", res), "json");

        chatInput.val("");
    }

    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

    // --- Add to prescription ---
    window.addToPrescription = function(btn) {
        const messageText = $(btn).siblings(".text").text();
        $.post(endPoint, {
            action: "add_to_prescription",
            message: messageText,
            patient_id: patientId,
            doctor_id: doctorId
        }, (res) => $(btn).text("✅"), "json");
    };
}
</script>
