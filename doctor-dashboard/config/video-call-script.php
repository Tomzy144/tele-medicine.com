<script>
        let localStream;
        let peerConnection;
        const ICE_SERVERS = [{ urls: "stun:stun.l.google.com:19302" }];

            const popup = document.getElementById("videoCallPopup");
            const header = document.getElementById("videoCallHeader");

        // Open video call
        function open_videocall() {

        
            patient_name  = document.querySelector(".chat-user strong").textContent ;
            patient_id = document.getElementById('patient_id').value;
            popup.style.display = "flex";
            document.getElementById("videoCallUser").textContent = "Patient Name: " + patient_name;

            navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                .then(stream => {
                    localStream = stream;
                    const localVideo = document.getElementById("localVideo");
                    localVideo.srcObject = localStream;
                    localVideo.play();

                    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });
                    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

                    peerConnection.ontrack = event => {
                        const remoteVideo = document.getElementById("remoteVideo");
                        remoteVideo.srcObject = event.streams[0];
                        remoteVideo.play();
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

                    socket.send(JSON.stringify({ type: "start_call", to: patient_id }));
                })
                .catch(err => console.error("Cannot access camera/mic:", err));
        }

        // Close video call
        function closeVideoCall() {
            popup.style.display = "none";
            if (localStream) localStream.getTracks().forEach(track => track.stop());
            if (peerConnection) peerConnection.close();
        }

        // ----- DRAG LOGIC -----
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

</script>