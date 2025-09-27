 <script> 
 
 
    $(document).ready(function() {
            const chatMessages = $("#chatMessages");
            const chatInput = $("#chatInput");
            const doctorStatus = $("#doctorStatus");

            const patientId = $("#patient_id").val();
            const doctorId = $("#doctor_id").val();
           

            // Ticks display based on status
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


            // Send chat message
            function send_chat(event) {
                if (event && event.type === "keydown") {
                    if (event.key === "Enter" && !event.shiftKey) {
                        event.preventDefault();
                    } else return;
                }

                const text = chatInput.val().trim();
                if (!text) return;

                const msgData = {
                    sender: "patient",
                    message: text,
                    patient_id: patientId,
                    doctor_id: doctorId,
                    message_type: "text",
                    action: "send_message"
                };

                // Display locally
                createMessage({...msgData, status: "sent"});

                // AJAX to backend
                $.ajax({
                    url: endPoint,
                    type: 'POST',
                    data: msgData,
                    dataType: 'json',
                    success: function(data) {
                        // Update tick
                        $(".message.sent").each(function() {
                            const textEl = $(this).find(".text").text();
                            if (textEl === msgData.message) {
                                $(this).find(".ticks").html(getTickHTML(data.status));
                            }
                        });
                    },
                    error: function(err) {
                        console.error('Message send failed:', err);
                    }
                });

                chatInput.val("");
            }

            // Attach send events
            chatInput.on("keydown", send_chat);
            $("#sendBtn").on("click", send_chat);

           function updateDoctorStatus() {
                const doctorId = $('#doctor_id').val();

                $.ajax({
                    url: endPoint,
                    type: 'POST',
                    data: { action: 'doctor_status', doctor_id: doctorId },
                    dataType: 'json',
                    success: function(data) {
                        const online = data.online;
                        const $statusEl = $('#doctorStatus');

                        $statusEl.text(online ? "Online" : "Offline")
                                .css("color", online ? "green" : "gray");
                    },
                    error: function(err) {
                        console.error("Failed to fetch doctor status:", err);
                    }
                });
            }


          function refreshChat() {
            const patientId = $('#patient_id').val();
            const doctorId = $('#doctor_id').val();

            $.ajax({
                url: endPoint,
                type: 'POST',
                data: { action: 'load_messages', patient_id: patientId, doctor_id: doctorId },
                dataType: 'json',
                success: function(messages) {
                    const chatMessages = $("#chatMessages");
                    chatMessages.empty(); // clear previous messages

                    messages.forEach(msg => {
                        createMessage(msg); // render each message
                    });

                    // Scroll to bottom
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                },
                error: function(err) {
                    console.error("Failed to load chat messages:", err);
                }
            });
        }




           

            // Add to prescription function
            window.addToPrescription = function(btn) {
                const messageText = $(btn).siblings(".text").text();

                $.ajax({
                    url: endPoint,
                    type: 'POST',
                    data: {
                        action: "add_to_prescription",
                        message: messageText,
                        patient_id: patientId,
                        doctor_id: doctorId
                    },
                    dataType: 'json',
                    success: function() { $(btn).text("✅"); }
                });
            }

           setInterval(refreshChat, 5000);

         

             // Check every 10 seconds
            setInterval(updateDoctorStatus, 10000);

            // Initial check
            updateDoctorStatus();



        });


    </script>