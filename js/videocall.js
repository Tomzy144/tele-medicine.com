// ======= CONFIGURATION =======
const myUserId = sessionStorage.getItem("userId"); // e.g., "doctor_123" or "patient_456"
let peerId = null; // Set this to the ID of the person you want to call

// Metered ICE servers
const rtcConfig = {
  iceServers: [
    { urls: "stun:stun.relay.metered.ca:80" },
    { urls: "turn:global.relay.metered.ca:80", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turn:global.relay.metered.ca:80?transport=tcp", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turn:global.relay.metered.ca:443", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" },
    { urls: "turns:global.relay.metered.ca:443?transport=tcp", username: "ec4e996df1b54e5300c955bf", credential: "mElLDNWaGsNkqVSK" }
  ]
};

// ======= GLOBAL VARIABLES =======
let localStream;
let remoteStream;
let peerConnection;

// ======= VIDEO ELEMENTS =======
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");

// ======= WEBSOCKET CONNECTION =======
const wsProtocol = location.protocol === "https:" ? "wss" : "ws";
const socketUrl = `${wsProtocol}://${location.host}`;
const socket = new WebSocket(socketUrl);

socket.onopen = () => console.log("✅ WebSocket connected");
socket.onmessage = async (msg) => {
  const data = JSON.parse(msg.data);
  handleSignal(data);
};

// ======= INITIALIZE MEDIA =======
async function initMedia() {
  try {
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    localVideo.srcObject = localStream;
  } catch (err) {
    console.error("❌ Error accessing camera/mic:", err);
  }
}

// ======= CREATE PEER CONNECTION =======
function createPeerConnection() {
  peerConnection = new RTCPeerConnection(rtcConfig);

  localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

  peerConnection.ontrack = event => {
    if (!remoteVideo.srcObject) {
      remoteStream = event.streams[0];
      remoteVideo.srcObject = remoteStream;
    }
  };

  peerConnection.onicecandidate = event => {
    if (event.candidate && peerId) {
      sendSignal({ type: "ice_candidate", candidate: event.candidate, to: peerId });
    }
  };
}

// ======= SEND SIGNAL =======
function sendSignal(data) {
  data.from = myUserId;
  socket.send(JSON.stringify(data));
}

// ======= HANDLE SIGNALS =======
async function handleSignal(data) {
  // Only handle signals meant for me
  if (data.to && data.to !== myUserId) return;

  switch (data.type) {
    case "video_offer":
      peerId = data.from;
      if (!peerConnection) createPeerConnection();
      await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
      const answer = await peerConnection.createAnswer();
      await peerConnection.setLocalDescription(answer);
      sendSignal({ type: "video_answer", sdp: answer, to: peerId });
      break;

    case "video_answer":
      await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
      break;

    case "ice_candidate":
      try {
        if (!peerConnection) createPeerConnection();
        await peerConnection.addIceCandidate(data.candidate);
      } catch (err) {
        console.error("❌ Error adding ICE candidate:", err);
      }
      break;
  }
}

// ======= START CALL (CALLER) =======
async function startCall(targetId) {
  peerId = targetId;
  createPeerConnection();
  const offer = await peerConnection.createOffer();
  await peerConnection.setLocalDescription(offer);
  sendSignal({ type: "video_offer", sdp: offer, to: peerId });
}

// ======= INITIALIZE =======
initMedia();

// ======= EXPORT START CALL =======
// Use this function to trigger a call from your UI
window.startCall = startCall;
