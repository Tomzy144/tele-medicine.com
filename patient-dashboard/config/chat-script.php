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
        ws = new WebSocket("ws://localhost:8080");

        ws.onopen = () => {
            console.log("✅ WebSocket connected");

            // login patient
            ws.send(JSON.stringify({ type: "patient_login", patient_id: patientId }));

            // ask server for chat history & doctor status
            ws.send(JSON.stringify({ type: "get_history", doctor_id: doctorId, patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_status", doctor_id: doctorId }));
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
                    if (data.success) {
                        console.log("📝 Prescription successfully saved:", data);

                        // Format date nicely
                        const formattedDate = new Date(data.date).toLocaleDateString("en-GB");

                        // Append to prescriptions table
                        const newRow = `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>Dr. ${data.doctor_id}</td>
                                <td>${data.prescription}</td>
                            </tr>
                        `;
                        $("#all-entries-body").prepend(newRow); // add to top of table
                    } else {
                        console.error("❌ Failed to save prescription:", data.error);
                        alert("Error saving prescription. Try again.");
                    }
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

        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msgData));
        }

        chatInput.val("");
    }


       // --- Add to prescription ---
       window.addToPrescription = function(btn) {
            var messageText = $(btn).siblings(".text").text().trim();

            if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({
                    type: "prescription_added",
                    patient_id: patientId,
                    doctor_id: doctorId,
                    prescription: messageText
                }));

                // Change the button to checkmark
                $(btn).text("✅");

                // ✅ Give chat feedback
                const reply = $("<div>")
                    .addClass("message sent")
                    .html("✅ '" + messageText + "' added to prescription list.");
                
                $("#chatMessages").append(reply);
                $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
            } else {
                alert("⚠️ Connection lost. Prescription not saved.");
            }
        };



        
    chatInput.on("keydown", send_chat);
    $("#sendBtn").on("click", send_chat);

     

}
</script>
