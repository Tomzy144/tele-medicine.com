<script>
        let localStream;
        let remoteStream;
        let peerConnection;

        let micEnabled = true;
        let speakerEnabled = true;

        const ICE_SERVERS = [{ urls: "stun:stun.l.google.com:19302" }];

        const popup = document.getElementById("videoCallPopup");
        const header = document.getElementById("videoCallHeader");



        // OPEN VIDEO CALL
        function open_videocall() {

            popup.style.display = "flex";

            const patient_name = document.querySelector(".chat-user strong").textContent;
            const patient_id = document.getElementById('patient_id').value;
            const called_person_picture = document.getElementById('chatUserPicture').src; // use .src

            document.getElementById("videoCallUser").textContent = "Patient: " + patient_name;
            document.getElementById("remotePlaceholder").src = called_person_picture; // directly assign



            // Start camera immediately
            navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                .then(stream => {

                    localStream = stream;

                    // Show local video immediately
                    const localVideo = document.getElementById("localVideo");
                    localVideo.srcObject = stream;
                    localVideo.play();

                    // Prepare peer connection
                    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

                    stream.getTracks().forEach(track => {
                        peerConnection.addTrack(track, stream);
                    });

                    // When remote video arrives
                   peerConnection.ontrack = event => {
                    const remoteVideo = document.getElementById("remoteVideo");
                    const placeholder = document.getElementById("remotePlaceholder");

                    remoteVideo.srcObject = event.streams[0];
                    remoteVideo.play();

                    // hide placeholder
                    placeholder.style.display = "none";
                    remoteVideo.style.display = "block";
                };


                    // ICE CANDIDATES
                    peerConnection.onicecandidate = event => {
                        if (event.candidate) {
                            socket.send(JSON.stringify({
                                type: "ice_candidate",
                                to: patient_id,
                                candidate: event.candidate
                            }));
                        }
                    };

                    // Notify backend
                    socket.send(JSON.stringify({ type: "start_call", to: patient_id }));
                });
        }


        // CLOSE CALL
        function closeVideoCall() {
            popup.style.display = "none";

            if (localStream) localStream.getTracks().forEach(t => t.stop());
            if (peerConnection) peerConnection.close();
        }


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