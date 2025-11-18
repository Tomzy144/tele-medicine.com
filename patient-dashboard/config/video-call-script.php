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
let pendingOffer = null; // store incoming SDP until patient accepts
let micEnabled = true;
let speakerEnabled = true;

//--------------------------------------
// WEBSOCKET
//--------------------------------------
const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://tele-medicine-chat-server.onrender.com";

const socket = new WebSocket(wsUrl);

socket.onopen = () => console.log("✅ WebSocket connected");
socket.onclose = () => console.log("WebSocket closed");
socket.onerror = err => console.error("❌ WebSocket error:", err);

//--------------------------------------
// PATIENT SIDE LOGIC
//--------------------------------------
socket.onmessage = async event => {
    const data = JSON.parse(event.data);
    console.log("📥 WS message:", data);

    if (data.to !== document.getElementById('patient_id').value) return;

    switch (data.type) {

        // Doctor is calling → Patient receives request
        case "call_request":
            peerId = data.from;
            showIncomingCallPopup(data.from, data.name, data.picture);
            break;

        // Doctor sent SDP offer → store it until patient accepts
        case "video_offer":
            pendingOffer = data.sdp;
            break;

        case "ice_candidate":
            try {
                if (peerConnection)
                    await peerConnection.addIceCandidate(data.candidate);
            } catch (err) {
                console.error("❌ ICE error:", err);
            }
            break;

        case "call_end":
            closeVideoCall();
            alert("📞 Call ended by doctor");
            break;
    }
};

//--------------------------------------
// ICE SERVERS
//--------------------------------------
const ICE_SERVERS = [
    { urls: "stun:stun.relay.metered.ca:80" },
    { urls: "turn:global.relay.metered.ca:80", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turn:global.relay.metered.ca:443", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turns:global.relay.metered.ca:443?transport=tcp", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" }
];

//--------------------------------------
// MEDIA
//--------------------------------------
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

//--------------------------------------
// WEBRTC PEER CONNECTION
//--------------------------------------
function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

    localStream.getTracks().forEach(track =>
        peerConnection.addTrack(track, localStream)
    );

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

//--------------------------------------
// OPEN CALL POPUP
//--------------------------------------
function openPopupUI(name, picture) {
    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Doctor: " + name;
    placeholder.src = picture;
    placeholder.style.display = "block";
    remoteVideo.style.display = "none";
}

//--------------------------------------
// INCOMING CALL POPUP (PATIENT ONLY)
//--------------------------------------
async function showIncomingCallPopup(fromId, name, picture) {
    peerId = fromId;
    openPopupUI(name, picture);

    const accept = confirm(`📞 Incoming call from Dr. ${name}\n\nAccept call?`);

    if (accept) {
        socket.send(JSON.stringify({ type: "call_accept", to: fromId }));

        // Start media and peer connection AFTER accept
        await initLocalMedia();
        createPeerConnection(peerId);

        if (pendingOffer) {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);

            socket.send(JSON.stringify({
                type: "video_answer",
                sdp: answer,
                to: peerId
            }));

            pendingOffer = null; // clear stored offer
        }
    } else {
        socket.send(JSON.stringify({ type: "call_reject", to: fromId }));
    }
}

//--------------------------------------
// END CALL
//--------------------------------------
function closeVideoCall() {
    popup.style.display = "none";
    if (localStream) localStream.getTracks().forEach(track => track.stop());
    if (peerConnection) peerConnection.close();

    if (peerId) {
        socket.send(JSON.stringify({
            type: "call_end",
            to: peerId
        }));
    }

    remoteVideo.srcObject = null;
    placeholder.style.display = "block";
}

//--------------------------------------
// MIC
//--------------------------------------
function toggleMic() {
    micEnabled = !micEnabled;
    if (localStream)
        localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
}

//--------------------------------------
// SPEAKER
//--------------------------------------
function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;
    document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
}

//--------------------------------------
// DRAGGABLE POPUP
//--------------------------------------
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

//--------------------------------------
// RESIZABLE POPUP
//--------------------------------------
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
