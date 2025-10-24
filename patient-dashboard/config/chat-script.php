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
        const msgClass = msgData.sender === "patient" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;

        if (msgData.sender === "patient") {
            innerHTML += `<span class="ticks">${getTickHTML(msgData.status)}</span>`;
        } else {
            innerHTML += `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        }

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
            const isLocal = window.location.hostname === "localhost";
            const wsUrl = isLocal
                ? "ws://localhost:8081"
                : "wss://tele-medicine.onrender.com"; // same Render domain

            ws = new WebSocket(wsUrl);

            ws.onopen = () => console.log("✅ WebSocket connected");
            ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
            ws.onclose = () => {
                console.log("❌ WebSocket closed. Reconnecting in 5s...");
                setTimeout(connectWebSocket, 5000);
            };

            connectWebSocket();


        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log("📩 Message from server:", data);
            // handle data here...
        };



        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log("📥 WS Message:", data);

            if (data.type === "history" || data.type === "missed_messages") {
                chatMessages.empty();
                data.data.forEach(createMessage);

                // mark all messages as seen after rendering
                const unseenIds = data.data.filter(m => m.sender === "doctor" && m.status !== "seen").map(m => m.sn);
                if (unseenIds.length) {
                    ws.send(JSON.stringify({ type: "mark_seen", message_ids: unseenIds, seen_by: "patient" }));
                }
            }

            if (data.type === "new_message") {
                createMessage(data);

                // mark doctor's messages as seen immediately
                if (data.sender === "doctor" && data.status !== "seen") {
                    ws.send(JSON.stringify({ type: "mark_seen", message_ids: [data.sn], seen_by: "patient" }));
                }
            }

            if (data.type === "doctor_status_update" && data.doctor_id == doctorId) {
                updateDoctorStatus(data.status === "online");
            }

            if (data.type === "message_delivered") {
                // update tick for patient's own messages
                chatMessages.find(".message.sent").each(function() {
                    const tick = $(this).find(".ticks");
                    tick.html(getTickHTML("delivered"));
                });
            }

            if (data.type === "messages_seen") {
                // update tick to blue for messages seen by doctor
                chatMessages.find(".message.sent").each(function() {
                    const tick = $(this).find(".ticks");
                    tick.html(getTickHTML("seen"));
                });
            }

           // ---------- PRESCRIPTION ADDED ----------
              if (data.type === "prescription_added") {
                const { success, duplicate, error, doctor_id, prescription, date, tempId } = data;

            // Find the corresponding chat feedback / button via tempId
            const targetBtn = $(`.reaction-btn[data-tempid="${tempId}"]`);

            const feedback = $(`.message.sent[data-tempid="${tempId}"]`);

            if (data.type === "prescription_added") {
                const { success, duplicate, error, tempId } = data;

                // Find the button and feedback div using tempId
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

                // Scroll chat to bottom after update
                $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
            }


        }
       // ---------- END PRESCRIPTION ADDED ----------

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

        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msgData));
        }

        chatInput.val("");
    }


       // --- Add to prescription ---
            window.addToPrescription = function(btn) {
            var messageText = $(btn).siblings(".text").text().trim();
            const tempId = Date.now() + "_" + Math.random().toString(36).substr(2, 5);

            if (ws && ws.readyState === WebSocket.OPEN) {
                // Send prescription request with tempId
                ws.send(JSON.stringify({
                    type: "prescription_added",
                    patient_id: patientId,
                    doctor_id: doctorId,
                    prescription: messageText,
                    tempId
                }));

                // Save tempId on button
                $(btn).attr("data-tempid", tempId).text("⏳");

                // Create pending feedback div in chat
                const pendingDiv = $("<div>")
                    .addClass("message sent")
                    .attr("data-tempid", tempId)   // link to tempId
                    .html("⏳ Adding '" + messageText + "' to prescription list...");
                
                $("#chatMessages").append(pendingDiv);
                $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
            } else {
                alert("⚠️ Connection lost. Prescription not saved.");
            }
        };





        
    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

     

}
</script>
