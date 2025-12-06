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

 const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://yemasconsults.co.uk";

const socket = new WebSocket(wsUrl);

socket.onopen = () => console.log("✅ WebSocket connected");
socket.onerror = err => console.error("❌ WebSocket error:", err);
socket.onclose = () => console.log("🔌 WebSocket closed");

// ====== Incoming WebSocket Messages ======
socket.onmessage = async event => {
    const data = JSON.parse(event.data);
    const doctorId = document.getElementById('doctor_id').value;
    if (data.to && data.to !== doctorId) return;

    switch (data.type) {

        // ======================= PATIENT ACCEPTED CALL =======================
        case "call_accept":
            peerId = data.from;

            openPopupUI("Patient", "Patient Picture URL");

            await initLocalMedia();
            createPeerConnection(peerId);

            // Doctor creates offer (send as `sdp` to match server/patient handlers)
            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);

            socket.send(JSON.stringify({
                type: "video_offer",
                sdp: offer,
                to: peerId,
                from: doctorId
            }));
            break;

        // ======================= VIDEO ANSWER =======================
        case "video_answer":
            if (peerConnection) {
                // server/patient send `sdp` field — use that as the remote description
                await peerConnection.setRemoteDescription(
                    new RTCSessionDescription(data.sdp)
                );
            }
            break;

        // ======================= ICE CANDIDATE =======================
        case "ice_candidate":
            try {
                if (peerConnection) await peerConnection.addIceCandidate(data.candidate);
            } catch (err) {
                console.error("ICE Error:", err);
            }
            break;

        // ======================= CALL END =======================
        case "call_end":
            closeVideoCall();
            alert('📞 Call ended by patient');
            break;
    }
};

// ====== ICE Servers ======
const ICE_SERVERS = [
    { urls: "stun:stun.relay.metered.ca:80" },
    { urls: "turn:global.relay.metered.ca:443", username:"ec4e996df1b54e5300c955bf", credential:"mElLDNWaGsNkqVSK" }
];

// ====== Local Media ======
async function initLocalMedia() {
    if (localStream) return;
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
        localVideo.play();
    } catch (err) {
        console.error("❌ Camera/Mic error:", err);
    }
}

// ====== Doctor Initiates Call ======
async function open_videocall() {
    const patientId = document.getElementById('patient_id').value;
    const doctorId = document.getElementById('doctor_id').value;
    if (!patientId) return console.error("❌ patient_id missing");

    socket.send(JSON.stringify({
        type: "call_request",
        from: doctorId,
        to: patientId,
        name: document.getElementById("doctor-name").textContent,
        picture: document.getElementById('my_passport2').src
    }));

    await initLocalMedia();
    createPeerConnection(patientId);
    openPopupUI(
        document.querySelector(".chat-user strong").textContent,
        document.getElementById('chatUserPicture').src
    );
}

// ====== Create Peer Connection ======
function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({ iceServers: ICE_SERVERS });

    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    peerConnection.ontrack = event => {
        remoteStream = event.streams[0];
        remoteVideo.srcObject = remoteStream;
        remoteVideo.play().catch(console.error);
        placeholder.style.display = "none";
        remoteVideo.style.display = "block";
    };

    peerConnection.onicecandidate = event => {
        if (event.candidate) {
            const doctorId = document.getElementById('doctor_id').value;
            socket.send(JSON.stringify({
                type: "ice_candidate",
                candidate: event.candidate,
                to: peer,
                from: doctorId
            }));
        }
    };
}

// ====== Popup UI ======
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
    peerConnection = null;
    remoteVideo.srcObject = null;
    placeholder.style.display = "block";
}

// ====== Mic & Speaker Toggle ======
function toggleMic() {
    micEnabled = !micEnabled;
    localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
}

function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;
    document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
}

// ====== Draggable & Resizable Popup ======
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

</script>
