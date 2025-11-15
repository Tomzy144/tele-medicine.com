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
        let reconnectAttempts = 0;
        const MAX_RECONNECT_ATTEMPTS = 5;

        function connectWebSocket() {
        const wsUrl = window.location.hostname === "localhost"
            ? "ws://localhost:8080"
            : "wss://tele-medicine-chat-server.onrender.com";


            ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log("✅ WebSocket connected");
                reconnectAttempts = 0;

                ws.send(JSON.stringify({ type: "patient_login", patient_id: patientId }));
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
                        chatMessages.empty();
                        if (Array.isArray(data.data)) {
                            data.data.forEach(createMessage);

                            const unseenIds = data.data
                                .filter(m => m.sender === "doctor" && m.status !== "seen")
                                .map(m => m.sn);

                            if (unseenIds.length) {
                                ws.send(JSON.stringify({
                                    type: "mark_seen",
                                    message_ids: unseenIds,
                                    doctor_id: doctorId,
                                    patient_id: patientId
                                }));
                            }
                        }
                        break;

                    case "new_message":
                        createMessage(data);
                        if (data.sender === "doctor") {
                            const audio = new Audio('/assets/notification.mp3');
                            audio.play().catch(e => console.log('Audio play failed:', e));

                            ws.send(JSON.stringify({
                                type: "mark_seen",
                                message_ids: [data.sn],
                                doctor_id: doctorId,
                                patient_id: patientId
                            }));
                        }
                        break;

                    case "doctor_status_update":
                        if (data.doctor_id == doctorId) {
                            updateDoctorStatus(data.status === "online");
                        }
                        break;

                    case "message_delivered":
                        if (data.message_id) {
                            $(`.message[data-id="${data.message_id}"] .ticks`)
                                .html(getTickHTML("delivered"));
                        }
                        break;

                    case "messages_seen":
                        $(`.message[data-sender="patient"] .ticks`)
                            .html(getTickHTML("seen"));
                        break;

                    case "prescription_added":
                        handlePrescriptionResponse(data);
                        break;

                    case "error":
                        console.error("⚠️ Server error:", data.message);
                        break;

                    default:
                        console.log("📩 Unhandled message type:", data.type);
                }
            };

            ws.onclose = (e) => {
                console.log("❌ WebSocket closed.", e.reason || "No reason");
                if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                    const timeout = Math.min(1000 * Math.pow(2, reconnectAttempts), 10000);
                    console.log(`🔄 Reconnecting in ${timeout / 1000}s... (Attempt ${reconnectAttempts + 1}/${MAX_RECONNECT_ATTEMPTS})`);
                    setTimeout(() => {
                        reconnectAttempts++;
                        connectWebSocket();
                    }, timeout);
                } else {
                    console.error("❌ Max reconnection attempts reached. Please refresh the page.");
                    alert("Connection lost. Please refresh the page to reconnect.");
                }
            };

            ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
        }

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
