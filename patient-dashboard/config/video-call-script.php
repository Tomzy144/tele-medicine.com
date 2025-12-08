<script>
const popup = document.getElementById("videoCallPopup");
const header = document.getElementById("videoCallHeader");
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const placeholder = document.getElementById("remotePlaceholder");
const btnAccept = document.getElementById("btnAcceptCall");
const btnReject = document.getElementById("btnRejectCall");

let localStream, remoteStream, peerConnection, peerId, pendingOffer;
let micEnabled = true, speakerEnabled = true;


const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://tele-medicine-serverjs--main.autogen.nodeops.network/tele-medicine-chat-server";


const socket = new WebSocket(wsUrl);

socket.onopen = () => {
    const patientId = document.getElementById("patient_id").value;
    socket.send(JSON.stringify({ type:"patient_login", patient_id: patientId }));
    console.log("✅ WS connected as patient", patientId);
};

socket.onmessage = async event => {
    const data = JSON.parse(event.data);
    const patientId = document.getElementById("patient_id").value;

    // Ignore messages for other patients
    if (data.to && data.to !== "patient_" + patientId) return;

    console.log("📩 WS message:", data);

    // --------- CALL REQUEST ----------
    if (data.type === "call_request") {
        peerId = data.from_key || data.from;
        showIncomingCallPopup(data.name, data.picture);
        return; // important
    }

    switch(data.type) {
        case "video_offer":
            peerId = data.from_key || data.from;
            pendingOffer = data.sdp;
            await initLocalMedia();
            createPeerConnection(peerId);

            if (pendingOffer) {
                await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
                const answer = await peerConnection.createAnswer();
                await peerConnection.setLocalDescription(answer);

                socket.send(JSON.stringify({
                    type:"video_answer",
                    sdp: answer,
                    to: peerId,
                    from: patientId
                }));
                pendingOffer = null;
            }
            popup.style.display = "flex";
            break;

        case "video_answer":
            if (!peerConnection) return;
            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
            break;

        case "ice_candidate":
            if(peerConnection && data.candidate) {
                try { await peerConnection.addIceCandidate(data.candidate); }
                catch(err){ console.error("❌ ICE error:", err); }
            }
            break;

        case "call_end":
            closeVideoCall();
            alert("📞 Call ended by doctor");
            break;

        default:
            console.warn("📩 Unhandled message type:", data.type);
    }
};

async function initLocalMedia() {
    if(localStream) return;
    localStream = await navigator.mediaDevices.getUserMedia({ video:true, audio:true });
    localVideo.srcObject = localStream;
    localVideo.style.display = "block";
}

function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({
        iceServers: [
            { urls: "stun:stun.relay.metered.ca:80" },
            { urls:"turn:global.relay.metered.ca:443", username:"ec4e996df1b54e5300c955bf", credential:"mElLDNWaGsNkqVSK" }
        ]
    });

    if(localStream) localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    peerConnection.ontrack = e => {
        remoteStream = e.streams[0];
        remoteVideo.srcObject = remoteStream;
        remoteVideo.style.display = "block";
        placeholder.style.display = "none";
    };

    peerConnection.onicecandidate = e => {
        if(e.candidate) {
            const patientId = document.getElementById("patient_id").value;
            socket.send(JSON.stringify({ type:"ice_candidate", candidate:e.candidate, to:peer, from:patientId }));
        }
    };
}

function showIncomingCallPopup(name, picture) {
    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Doctor: " + name;
    placeholder.src = picture;
    placeholder.style.display = "block";
    remoteVideo.style.display = "none";
}

btnAccept.onclick = async () => {
    const patientId = document.getElementById("patient_id").value;
    socket.send(JSON.stringify({ type:"call_accept", to:peerId, from:patientId }));

    await initLocalMedia();
    createPeerConnection(peerId);

    if(pendingOffer) {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        socket.send(JSON.stringify({ type:"video_answer", sdp: answer, to:peerId, from:patientId }));
        pendingOffer = null;
    }

    popup.style.display = "none";
};

btnReject.onclick = () => {
    const patientId = document.getElementById("patient_id").value;
    socket.send(JSON.stringify({ type:"call_reject", to:peerId, from:patientId }));
    popup.style.display = "none";
};

function closeVideoCall() {
    popup.style.display = "none";
    if(localStream) localStream.getTracks().forEach(t=>t.stop());
    if(peerConnection) peerConnection.close();
    peerConnection = null;
    remoteVideo.srcObject = null;
    placeholder.style.display = "block";

    if(peerId) {
        const patientId = document.getElementById("patient_id").value;
        socket.send(JSON.stringify({ type:"call_end", to:peerId, from:patientId }));
        peerId = null;
    }
}

// ===== Mic & Speaker =====
function toggleMic() {
    micEnabled = !micEnabled;
    if(localStream) localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
}

function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;
    document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
}

// ===== Draggable Popup =====
header.onmousedown = function(e) {
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

// ===== Resizable Popup =====
const resizeHandle = document.querySelector(".resize-handle");
resizeHandle.addEventListener("mousedown", e => {
    e.preventDefault();
    const startWidth = parseInt(getComputedStyle(popup).width,10);
    const startHeight = parseInt(getComputedStyle(popup).height,10);
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
