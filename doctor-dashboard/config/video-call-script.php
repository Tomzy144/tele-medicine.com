<script>
       let localStream;
        let peerConnection;
        const ICE_SERVERS = [{ urls: "stun:stun.l.google.com:19302" }];
        const popup = document.getElementById("videoCallPopup");
        const header = document.getElementById("videoCallHeader");
        const body = document.getElementById("videoCallBody");

        // Open video call
      function open_videocall() {
    const patient_name = document.querySelector(".chat-user strong").textContent;
    const patient_id = document.getElementById('patient_id').value;

    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Patient: " + patient_name;

    const remoteVideo = document.getElementById("remoteVideo");
    const localPreview = document.getElementById("localPreview");

    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then(stream => {
            // Show local preview immediately
            localStream = stream;
            localPreview.srcObject = localStream;
            localPreview.play();

            peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            // Remote video appears only when the other person connects
            peerConnection.ontrack = event => {
                remoteVideo.srcObject = event.streams[0];
                remoteVideo.style.display = "block";
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
        .catch(err => console.error("Camera/mic error:", err));
}

        // Close video call
        function closeVideoCall() {
            popup.style.display = "none";
            if (localStream) localStream.getTracks().forEach(track => track.stop());
            if (peerConnection) peerConnection.close();
        }

        // Minimize / Restore
        function minimizeVideoCall() {
            if (body.style.display === "none") {
                body.style.display = "flex";
                popup.style.height = "300px";
            } else {
                body.style.display = "none";
                popup.style.height = "40px"; // header only
            }
        }

        // ----- Drag Logic -----
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



        let micEnabled = true;
        let speakerEnabled = true;

        // 🔇 Toggle Microphone
        function toggleMic() {
            if (!localStream) return;

            micEnabled = !micEnabled;
            localStream.getAudioTracks().forEach(track => track.enabled = micEnabled);

            const micBtn = document.getElementById("micBtn");
            micBtn.textContent = micEnabled ? "🎤" : "🔇";
        }

        // 🔊 Toggle Speaker (remote audio)
        function toggleSpeaker() {
            const remoteVideo = document.getElementById("remoteVideo");

            speakerEnabled = !speakerEnabled;
            remoteVideo.muted = !speakerEnabled;

            const speakerBtn = document.getElementById("speakerBtn");
            speakerBtn.textContent = speakerEnabled ? "🔊" : "🔈";
        }


</script>