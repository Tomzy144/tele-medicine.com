<script>
        let localStream;
        let remoteStream;
        let peerConnection;

        let micEnabled = true;
        let speakerEnabled = true;

        const ICE_SERVERS = [{ urls: "stun:stun.l.google.com:19302" }];

        const popup = document.getElementById("videoCallPopup");
        const header = document.getElementById("videoCallHeader");




        // Use the correct WebSocket URL (adjust for localhost / deployed)
            const wsUrl = window.location.hostname === "localhost"
               ? "ws://localhost:8080"
            //    ? "ws://localhost/tele-medicine-serverjs"
                : "wss://tele-medicine-chat-server.onrender.com";

            const socket = new WebSocket(wsUrl);

            // Optional: Listen to messages
            socket.onmessage = function(event) {
                const data = JSON.parse(event.data);
                console.log("Received WS message:", data);

                if (data.type === "incoming_call") {
                    showIncomingCallPopup(data.from, data.name, data.picture);
                }

                // Handle ICE candidates, chat, etc...
            };

            // Handle connection open / errors
            socket.onopen = function() {
                console.log("✅ WebSocket connected");
            };

            socket.onerror = function(err) {
                console.error("❌ WebSocket error:", err);
            };

            socket.onclose = function() {
                console.log("WebSocket closed");
            };






        // OPEN VIDEO CALL
       function open_videocall() {
                // Show popup
                popup.style.display = "flex";

                const patient_name = document.querySelector(".chat-user strong").textContent;
                const patient_id = document.getElementById('patient_id').value;
                const called_person_picture = document.getElementById('chatUserPicture').src;

                // Update UI
                document.getElementById("videoCallUser").textContent = "Patient: " + patient_name;
                const placeholder = document.getElementById("remotePlaceholder");
                placeholder.src = called_person_picture;
                placeholder.style.display = "block";

                const remoteVideo = document.getElementById("remoteVideo");
                remoteVideo.style.display = "none";

                // ---- 1. Ping the other user ----
                socket.send(JSON.stringify({
                    type: "call_request",
                    to: patient_id,
                    from: document.getElementById('doctor_id').value,
                    name: document.getElementById('doctor_name3').textContent,
                    picture: document.getElementById('my_passport2').src
                }));

                // ---- 2. Start local camera/mic ----
                navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                    .then(stream => {
                        localStream = stream;

                        const localVideo = document.getElementById("localVideo");
                        localVideo.srcObject = localStream;
                        localVideo.play();

                        // ---- 3. Setup WebRTC peer connection ----
                        peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

                        localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

                        peerConnection.ontrack = event => {
                            remoteVideo.srcObject = event.streams[0];
                            remoteVideo.play();

                            // Show remote video, hide placeholder
                            placeholder.style.display = "none";
                            remoteVideo.style.display = "block";
                        };

                        peerConnection.onicecandidate = event => {
                            if (event.candidate) {
                                socket.send(JSON.stringify({
                                    type: "ice_candidate",
                                    to: patient_id,
                                    candidate: event.candidate
                                }));
                            }
                        };

                        // Notify backend to start call
                        socket.send(JSON.stringify({ type: "start_call", to: patient_id }));

                    })
                    .catch(err => console.error("Cannot access camera/mic:", err));
            }

            // ---- CLOSE CALL ----
            function closeVideoCall() {
                popup.style.display = "none";

                if (localStream) localStream.getTracks().forEach(track => track.stop());
                if (peerConnection) peerConnection.close();

                const remoteVideo = document.getElementById("remoteVideo");
                remoteVideo.srcObject = null;

                // Show placeholder again
                const placeholder = document.getElementById("remotePlaceholder");
                placeholder.style.display = "block";
            }

            // ---- DRAG LOGIC ----
            header.onmousedown = function(e) {
                e.preventDefault();
                let offsetX = e.clientX - popup.offsetLeft;
                let offsetY = e.clientY - popup.offsetTop;

                function mouseMoveHandler(e) {
                    popup.style.top = (e.clientY - offsetY) + "px";
                    popup.style.left = (e.clientX - offsetX) + "px";
                }

                function reset() {
                    document.removeEventListener("mousemove", mouseMoveHandler);
                    document.removeEventListener("mouseup", reset);
                }

                document.addEventListener("mousemove", mouseMoveHandler);
                document.addEventListener("mouseup", reset);
            };


        // --- MICROPHONE TOGGLE ---
        function toggleMic() {
            micEnabled = !micEnabled;

            localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);

            document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
        }


        // --- SPEAKER TOGGLE ---
        function toggleSpeaker() {
            speakerEnabled = !speakerEnabled;

            document.getElementById("remoteVideo").muted = !speakerEnabled;

            document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
        }


        // --- DRAG POPUP ---
        header.onmousedown = function (e) {
            e.preventDefault();

            let offsetX = e.clientX - popup.offsetLeft;
            let offsetY = e.clientY - popup.offsetTop;

            function move(e) {
                popup.style.left = e.clientX - offsetX + "px";
                popup.style.top = e.clientY - offsetY + "px";
            }

            function stop() {
                document.removeEventListener("mousemove", move);
                document.removeEventListener("mouseup", stop);
            }

            document.addEventListener("mousemove", move);
            document.addEventListener("mouseup", stop);
        };


        // ===== RESIZE LOGIC =====
        const resizeHandle = document.querySelector(".resize-handle");

        resizeHandle.addEventListener("mousedown", function (e) {
            e.preventDefault();

            let startX = e.clientX;
            let startY = e.clientY;

            let startWidth = parseInt(window.getComputedStyle(popup).width, 10);
            let startHeight = parseInt(window.getComputedStyle(popup).height, 10);

            function resize(e) {
                popup.style.width = startWidth + (e.clientX - startX) + "px";
                popup.style.height = startHeight + (e.clientY - startY) + "px";
            }

            function stopResize() {
                document.removeEventListener("mousemove", resize);
                document.removeEventListener("mouseup", stopResize);
            }

            document.addEventListener("mousemove", resize);
            document.addEventListener("mouseup", stopResize);
        });



</script>