<script>
const popup = document.getElementById("videoCallPopup");
const header = document.getElementById("videoCallHeader");
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const placeholder = document.getElementById("remotePlaceholder");

let localStream;
let remoteStream;
let peerConnection;
let peerId; // doctor id
let micEnabled = true;
let speakerEnabled = true;

// ====== WebSocket ======
const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://tele-medicine-chat-server.onrender.com";

const socket = new WebSocket(wsUrl);

socket.onopen = () => console.log("✅ WebSocket connected");
socket.onclose = () => console.log("WebSocket closed");
socket.onerror = err => console.error("❌ WebSocket error:", err);

socket.onmessage = async event => {
    const data = JSON.parse(event.data);
    console.log("📥 WS message:", data);

    if (data.to !== document.getElementById('patient_id').value) return;

    switch (data.type) {
        case "call_request":
            peerId = data.from;
            showIncomingCallPopup(data.from, data.name, data.picture);
            // Auto-answer
            await initLocalMedia();
            createPeerConnection(peerId);
            break;

        case "video_offer":
            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            socket.send(JSON.stringify({ type: "video_answer", sdp: answer, to: peerId }));
            openPopupUI(data.name, data.picture);
            break;

        case "video_answer":
            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
            break;

        case "ice_candidate":
            try { await peerConnection.addIceCandidate(data.candidate); }
            catch (err) { console.error("❌ ICE candidate error:", err); }
            break;
    }
};

// ====== Metered ICE servers ======
const ICE_SERVERS = [
    { urls: "stun:stun.relay.metered.ca:80" },
    { urls: "turn:global.relay.metered.ca:80", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turn:global.relay.metered.ca:443", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turns:global.relay.metered.ca:443?transport=tcp", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" }
];

// ====== Initialize local media ======
async function initLocalMedia() {
    if (localStream) return;
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
        localVideo.play();
    } catch (err) {
        console.error("❌ Cannot access camera/mic:", err);
    }
}

// ====== Create peer connection ======
function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    peerConnection.ontrack = event => {
        remoteStream = event.streams[0];
        remoteVideo.srcObject = remoteStream;
        remoteVideo.play();
        placeholder.style.display = "none";
        remoteVideo.style.display = "block";
    };

    peerConnection.onicecandidate = event => {
        if (event.candidate) {
            socket.send(JSON.stringify({
                type: "ice_candidate",
                candidate: event.candidate,
                to: peer
            }));
        }
    };
}

// ====== Open popup UI ======
function openPopupUI(name, picture) {
    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Doctor: " + name;
    placeholder.src = picture;
    placeholder.style.display = "block";
    remoteVideo.style.display = "none";
}

// ====== Incoming call popup ======
function showIncomingCallPopup(fromId, name, picture) {
    openPopupUI(name, picture);
    alert(`Incoming call from ${name}`); // Replace with better UI if needed
}

// ====== Close call ======
function closeVideoCall() {
    popup.style.display = "none";
    if (localStream) localStream.getTracks().forEach(track => track.stop());
    if (peerConnection) peerConnection.close();
    remoteVideo.srcObject = null;
    placeholder.style.display = "block";
}

// ====== Mic toggle ======
function toggleMic() {
    micEnabled = !micEnabled;
    if (localStream) localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
}

// ====== Speaker toggle ======
function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;
    document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
}

// ====== Draggable popup ======
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

// ====== Resizable popup ======
const resizeHandle = document.querySelector(".resize-handle");
resizeHandle.addEventListener("mousedown", function (e) {
    e.preventDefault();
    const startX = e.clientX;
    const startY = e.clientY;
    const startWidth = parseInt(window.getComputedStyle(popup).width, 10);
    const startHeight = parseInt(window.getComputedStyle(popup).height, 10);

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
