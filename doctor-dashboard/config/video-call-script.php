<script>
const popup = document.getElementById("videoCallPopup");
const header = document.getElementById("videoCallHeader");
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const placeholder = document.getElementById("remotePlaceholder");

let localStream;
let remoteStream;
let peerConnection;
let peerId;
let micEnabled = true;
let speakerEnabled = true;

// ====== WebSocket ======
const wsUrl =
    window.location.hostname === "localhost"
        ? "ws://localhost:8080"
        : "wss://tele-medicine-chat-server.onrender.com";

const socket = new WebSocket(wsUrl);

socket.onopen = () => console.log("✅ WebSocket connected");
socket.onerror = err => console.error("❌ WebSocket error:", err);
socket.onclose = () => console.log("🔌 WebSocket closed");

// ====== Incoming WebSocket Messages ======
socket.onmessage = async event => {
    const data = JSON.parse(event.data);
    console.log("📥 WS Message:", data);

    // Only handle messages for THIS doctor
    const doctorId = document.getElementById('doctor_id').value;
    if (data.to && data.to !== doctorId) return;

    switch (data.type) {

        // ======================= CALL REQUEST =======================
        case "call_request":
            peerId = data.from;
            console.log("📞 Incoming call request from", peerId);

            await initLocalMedia();
            createPeerConnection(peerId);

            // Auto-answer after receiving request
            openPopupUI(data.name, data.picture);

            break;

        // ======================= VIDEO OFFER =======================
        case "video_offer":
            peerId = data.from;

            await initLocalMedia();
            createPeerConnection(peerId);

            await peerConnection.setRemoteDescription(
                new RTCSessionDescription(data.offer)
            );

            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);

            socket.send(JSON.stringify({
                type: "video_answer",
                answer,
                to: peerId,
                from: doctorId
            }));

            openPopupUI(data.name, data.picture);
            break;

        // ======================= VIDEO ANSWER =======================
        case "video_answer":
            if (peerConnection) {
                await peerConnection.setRemoteDescription(
                    new RTCSessionDescription(data.answer)
                );
            }
            break;

        // ======================= ICE CANDIDATE =======================
        case "ice_candidate":
            try {
                await peerConnection.addIceCandidate(data.candidate);
            } catch (err) {
                console.error("ICE Error:", err);
            }
            break;
    }
};

// ====== ICE Servers (Metered.ca) ======
const ICE_SERVERS = [
    { urls: "stun:stun.relay.metered.ca:80" },
    {
        urls: "turn:global.relay.metered.ca:80",
        username: "ec4e996df1b54e5300c955bf",
        credential: "mElLDNWaGsNkqVSK"
    },
    {
        urls: "turn:global.relay.metered.ca:443",
        username: "ec4e996df1b54e5300c955bf",
        credential: "mElLDNWaGsNkqVSK"
    },
    {
        urls: "turns:global.relay.metered.ca:443?transport=tcp",
        username: "ec4e996df1b54e5300c955bf",
        credential: "mElLDNWaGsNkqVSK"
    }
];

// ====== Local Media ======
async function initLocalMedia() {
    if (localStream) return;

    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });

        localVideo.srcObject = localStream;
        localVideo.play();
    } catch (err) {
        console.error("❌ Camera/Mic error:", err);
    }
}

// ====== Doctor Starts Call ======
async function open_videocall() {
    const patientId = document.getElementById('patient_id').value;
    const doctorId = document.getElementById('doctor_id').value;

    if (!patientId) return console.error("❌ patient_id missing");

    // send call request
    socket.send(JSON.stringify({
        type: "call_request",
        from: doctorId,
        to: patientId,
        name: document.querySelector(".chat-user strong").textContent,
        picture: document.getElementById('my_passport2').src
    }));

    // Prepare doctor media and UI
    await initLocalMedia();
    createPeerConnection(patientId);
    openPopupUI(
        document.querySelector(".chat-user strong").textContent,
        document.getElementById('chatUserPicture').src
    );
}

// ====== Create RTCPeerConnection ======
function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

    // Send local tracks
    localStream.getTracks().forEach(track =>
        peerConnection.addTrack(track, localStream)
    );

    // Receive remote stream
    peerConnection.ontrack = event => {
        remoteStream = event.streams[0];
        remoteVideo.srcObject = remoteStream;
        remoteVideo.play();

        placeholder.style.display = "none";
        remoteVideo.style.display = "block";
    };

    // ICE candidates
    peerConnection.onicecandidate = event => {
        if (event.candidate) {
            socket.send(JSON.stringify({
                type: "ice_candidate",
                candidate: event.candidate,
                to: peer,
                from: document.getElementById('doctor_id').value
            }));
        }
    };
}

// ====== Display Popup UI ======
function openPopupUI(name, picture) {
    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Patient: " + name;

    placeholder.src = picture;
    placeholder.style.display = "block";
    remoteVideo.style.display = "none";
}

// ====== End Call ======
function closeVideoCall() {
    popup.style.display = "none";

    if (localStream) localStream.getTracks().forEach(t => t.stop());
    if (peerConnection) peerConnection.close();

    remoteVideo.srcObject = null;
    placeholder.style.display = "block";
}

// ====== Mic Toggle ======
function toggleMic() {
    micEnabled = !micEnabled;
    localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);

    document.getElementById("btnToggleMic").textContent =
        micEnabled ? "🎤" : "🔇";
}

// ====== Speaker Toggle ======
function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;

    document.getElementById("btnToggleSpeaker").textContent =
        speakerEnabled ? "🔊" : "🔈";
}

// ====== Draggable Popup ======
header.onmousedown = function (e) {
    e.preventDefault();

    let offsetX = e.clientX - popup.offsetLeft;
    let offsetY = e.clientY - popup.offsetTop;

    function move(e) {
        popup.style.left = `${e.clientX - offsetX}px`;
        popup.style.top = `${e.clientY - offsetY}px`;
    }
    function stop() {
        document.removeEventListener("mousemove", move);
        document.removeEventListener("mouseup", stop);
    }

    document.addEventListener("mousemove", move);
    document.addEventListener("mouseup", stop);
};

// ====== Resize Popup ======
const resizeHandle = document.querySelector(".resize-handle");
resizeHandle.addEventListener("mousedown", function (e) {
    e.preventDefault();

    const startWidth = parseInt(getComputedStyle(popup).width, 10);
    const startHeight = parseInt(getComputedStyle(popup).height, 10);
    const startX = e.clientX;
    const startY = e.clientY;

    function resize(e) {
        popup.style.width = `${startWidth + (e.clientX - startX)}px`;
        popup.style.height = `${startHeight + (e.clientY - startY)}px`;
    }
    function stop() {
        document.removeEventListener("mousemove", resize);
        document.removeEventListener("mouseup", stop);
    }

    document.addEventListener("mousemove", resize);
    document.addEventListener("mouseup", stop);
});
</script>
