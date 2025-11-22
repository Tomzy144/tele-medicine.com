<script>
/* =========================
   CHAT SECTION
========================= */

/* ---------- Shared globals for chat ---------- */
let ws = null;
const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://tele-medicine-chat-server.onrender.com";

const chatMessages = $("#chatMessages");
const chatInput = $("#chatInput");
const doctorStatus = $("#doctorStatus");

/* ---------- Helpers ---------- */
const getTickHTML = status => ({
    sent: "✓",
    delivered: "✓✓",
    seen: '<span style="color:blue">✓✓</span>'
}[status] || "✓");

function censorMessage(text) {
    return text
        .replace(/\+?\d[\d\s-]{6,}\d/g, "****")
        .replace(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/gi, "****")
        .replace(/\b(password|pass|pwd|secret)\b/gi, "****");
}

/* ---------- refreshChat() ---------- */
function refreshChat() {
    const patientId = $("#patient_id").val();
    const doctorId = $("#doctor_id").val();

    function createMessage(msgData) {
        const msgClass = msgData.sender === "patient" ? "message sent" : "message received";
        let innerHTML = `<span class="text">${censorMessage(msgData.message)}</span>`;

        if (msgData.sender === "patient") {
            innerHTML += `<span class="ticks">${getTickHTML(msgData.status)}</span>`;
        } else {
            innerHTML += `<span class="reaction-btn" onclick="addToPrescription(this)">➕</span>`;
        }

        $("<div>").addClass(msgClass).html(innerHTML).appendTo(chatMessages);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    function updateDoctorStatus(online) {
        doctorStatus.text(online ? "Online" : "Offline").css("color", online ? "green" : "gray");
    }

    function handlePrescriptionResponse(data) {
        const { success, duplicate, error, tempId } = data;
        const targetBtn = $(`.reaction-btn[data-tempid="${tempId}"]`);
        const feedback = $(`.message.sent[data-tempid="${tempId}"]`);

        if (success) {
            targetBtn.text("✅").prop("disabled", true);
            feedback.html("✅ Prescription added successfully.");
        } else if (duplicate) {
            targetBtn.text("➕").prop("disabled", false);
            feedback.html("⚠️ Prescription already exists for today.");
        } else if (error) {
            targetBtn.text("➕").prop("disabled", false);
            feedback.html("❌ Error saving prescription. Try again.");
        }

        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    let reconnectAttempts = 0;
    const MAX_RECONNECT_ATTEMPTS = 5;

    function ensureWebSocketConnected() {
        if (ws && ws.readyState === WebSocket.OPEN) return;

        ws = new WebSocket(wsUrl);

        ws.onopen = () => {
            console.log("✅ WebSocket connected");
            reconnectAttempts = 0;
            ws.send(JSON.stringify({ type: "patient_login", patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_history", doctor_id: doctorId, patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_status", doctor_id: doctorId }));
        };

        ws.onmessage = (event) => {
            let data;
            try { data = JSON.parse(event.data); } 
            catch (e) { console.error("❌ Error parsing message:", e); return; }

            if (data.to && data.to !== "patient_" + patientId) return;

            if (["call_request","call_accept","call_reject","call_end","video_offer","video_answer","ice_candidate"].includes(data.type)) {
                handleVideoMessage(data, patientId);
                return;
            }

            switch (data.type) {
                case "history":
                case "missed_messages":
                    chatMessages.empty();
                    if (Array.isArray(data.data)) {
                        data.data.forEach(createMessage);
                        const unseenIds = data.data.filter(m => m.sender === "doctor" && m.status !== "seen").map(m => m.sn);
                        if (unseenIds.length) ws.send(JSON.stringify({ type: "mark_seen", message_ids: unseenIds, doctor_id: doctorId, patient_id: patientId }));
                    }
                    break;
                case "new_message":
                    createMessage(data);
                    if (data.sender === "doctor") {
                        new Audio('/assets/notification.mp3').play().catch(()=>{});
                        ws.send(JSON.stringify({ type: "mark_seen", message_ids: [data.sn], doctor_id: doctorId, patient_id: patientId }));
                    }
                    break;
                case "doctor_status_update":
                    if (data.doctor_id == doctorId) updateDoctorStatus(data.status === "online");
                    break;
                case "message_delivered":
                    if (data.message_id) $(`.message[data-id="${data.message_id}"] .ticks`).html(getTickHTML("delivered"));
                    break;
                case "messages_seen":
                    $(`.message[data-sender="patient"] .ticks`).html(getTickHTML("seen"));
                    break;
                case "prescription_added":
                    handlePrescriptionResponse(data);
                    break;
                case "error":
                    console.error("⚠️ Server error:", data.message);
                    break;
                default:
                    console.log("📩 Unhandled message type:", data.type);
            }
        };

        ws.onclose = (e) => {
            console.log("❌ WebSocket closed.", e.reason || "No reason");
            if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                const timeout = Math.min(1000 * Math.pow(2, reconnectAttempts), 10000);
                console.log(`🔄 Reconnecting in ${timeout/1000}s... (Attempt ${reconnectAttempts+1}/${MAX_RECONNECT_ATTEMPTS})`);
                setTimeout(()=>{ reconnectAttempts++; ensureWebSocketConnected(); }, timeout);
            } else alert("Connection lost. Please refresh the page to reconnect.");
        };

        ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
    }

    ensureWebSocketConnected();

    window.send_chat = function(event) {
        if (event && event.type === "keydown" && event.key !== "Enter") return;
        event?.preventDefault();
        const text = $("#chatInput").val().trim();
        if (!text) return;
        const msgData = { type: "chat", sender: "patient", message: text, patient_id: patientId, doctor_id: doctorId, message_type: "text", status: "sent" };
        if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify(msgData));
        $("#chatInput").val("");
    };

    window.addToPrescription = function(btn) {
        const messageText = $(btn).siblings(".text").text().trim();
        const tempId = Date.now() + "_" + Math.random().toString(36).substr(2, 5);
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({ type: "prescription_added", patient_id: patientId, doctor_id: doctorId, prescription: messageText, tempId }));
            $(btn).attr("data-tempid", tempId).text("⏳");
            $("<div>").addClass("message sent").attr("data-tempid", tempId).html("⏳ Adding '" + messageText + "' to prescription list...").appendTo(chatMessages);
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        } else alert("⚠️ Connection lost. Prescription not saved.");
    };

    $("#chatInput").off("keydown").on("keydown", window.send_chat);
    $("#sendBtn").off("click").on("click", window.send_chat);
}

// window.refreshChat = refreshChat;

/* =========================
   VIDEO CALL SECTION
========================= */

const popup = document.getElementById("videoCallPopup");
const header = document.getElementById("videoCallHeader");
const localVideo = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");
const placeholder = document.getElementById("remotePlaceholder");
const btnAccept = document.getElementById("btnAcceptCall");
const btnReject = document.getElementById("btnRejectCall");

let localStream = null, remoteStream = null, peerConnection = null;
let peerId = null, pendingOffer = null;
let micEnabled = true, speakerEnabled = true;

// ---------- Video / signaling handler ----------
function handleVideoMessage(data, patientId) {
    try {
        if (data.type === 'call_request') {
            peerId = data.from_key || data.from;
            showIncomingCallPopup(data.name, data.picture);
            
            // play ringing
            const audio = new Audio('/assets/ring.mp3');
            audio.loop = true;
            audio.play();
            
            return;
        }

        if (data.type === 'video_offer') {
            peerId = data.from_key || data.from;
            pendingOffer = data.sdp;

            // Show popup with caller info
            showIncomingCallPopup(data.name, data.picture);

            // play ringing
            const audio = new Audio('/assets/ring.mp3');
            audio.loop = true;
            audio.play();

            // DO NOT start camera yet — wait for Accept button
        }

        if (data.type === 'video_answer') {
            peerConnection?.setRemoteDescription(new RTCSessionDescription(data.sdp)).catch(console.error);
            return;
        }

        if (data.type === 'ice_candidate') {
            peerConnection?.addIceCandidate(data.candidate).catch(console.error);
            return;
        }

        if (data.type === 'call_end') {
            closeVideoCall();
            alert('📞 Call ended by doctor');
            return;
        }

        if (data.type === 'call_reject') {
            popup.style.display = 'none';
            return;
        }
    } catch (err) {
        console.error('❌ Error in handleVideoMessage:', err);
    }
}

/* ---------- Local media & PeerConnection ---------- */
async function initLocalMedia() {
    if (localStream) return localStream; // already initialized
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        if (localVideo && localStream) {
            localVideo.srcObject = localStream;
            localVideo.play().catch(err => console.error("❌ Video play error:", err));
            localVideo.style.display = "block";
        }
    } catch (err) {
        console.error("❌ Camera/Mic error:", err);
        localStream = null;
    }
    return localStream;
}



function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({
        iceServers: [
            { urls: "stun:stun.relay.metered.ca:80" },
            { urls:"turn:global.relay.metered.ca:443", username:"ec4e996df1b54e5300c955bf", credential:"mElLDNWaGsNkqVSK" }
        ]
    });
    localStream?.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
    peerConnection.ontrack = e => { remoteVideo.srcObject = e.streams[0]; remoteVideo.style.display="block"; placeholder && (placeholder.style.display="none"); };
    peerConnection.onicecandidate = e => { 
        if (e.candidate && ws && ws.readyState === WebSocket.OPEN) {
            const patientId = document.getElementById("patient_id").value;
            ws.send(JSON.stringify({ type: "ice_candidate", candidate: e.candidate, to: peer, from: patientId }));
        }
    };
}

/* ---------- Incoming call UI ---------- */
function showIncomingCallPopup(name, picture) {
    if (!popup) return;

    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Doctor: " + name;
    
    if (placeholder) { 
        placeholder.src = picture; 
        placeholder.style.display = "block"; 
    }

    // Show accept/reject buttons
    const buttonsDiv = document.getElementById("callActionButtons");
    if (buttonsDiv) buttonsDiv.style.display = "flex";
}


// Accept button
// ---------- Accept button ----------
btnAccept && (btnAccept.onclick = async () => {
    // stop ringing
    document.querySelectorAll('audio').forEach(a => a.pause());

    const patientId = document.getElementById("patient_id").value;
    if (ws && ws.readyState === WebSocket.OPEN)
        ws.send(JSON.stringify({ type: "call_accept", to: peerId, from: patientId }));

    // START camera after user accepts
    await initLocalMedia();
    createPeerConnection(peerId);

    // attach remote stream via pending offer
    if (pendingOffer) {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        if (ws && ws.readyState === WebSocket.OPEN)
            ws.send(JSON.stringify({ type: "video_answer", sdp: answer, to: peerId, from: patientId }));
        pendingOffer = null;
    }

    // show remote video
    remoteVideo.style.display = "block";
    placeholder.style.display = "none";

    // hide accept/reject buttons
    document.getElementById("callActionButtons").style.display = "none";
});


// Reject button
btnReject && (btnReject.onclick = () => {
    document.querySelectorAll('audio').forEach(a => a.pause());

    const patientId = document.getElementById("patient_id").value;
    if (ws && ws.readyState === WebSocket.OPEN)
        ws.send(JSON.stringify({ type: "call_reject", to: peerId, from: patientId }));

    popup.style.display = "none";
    document.getElementById("callActionButtons").style.display = "none";
});


// Close video call (also hides buttons)
function closeVideoCall() {
    if (popup) popup.style.display = "none";
    if (localStream) localStream.getTracks().forEach(t=>t.stop());
    if (peerConnection) peerConnection.close();
    peerConnection = null;
    if (remoteVideo) remoteVideo.srcObject = null;
    if (placeholder) placeholder.style.display = "block";

    const buttonsDiv = document.getElementById("callActionButtons");
    if (buttonsDiv) buttonsDiv.style.display = "none";

    if (peerId && ws && ws.readyState === WebSocket.OPEN) {
        const patientId = document.getElementById("patient_id").value;
        ws.send(JSON.stringify({ type: "call_end", to: peerId, from: patientId }));
        peerId = null;
    }
}


/* ---------- Mic & Speaker ---------- */
function toggleMic() { micEnabled=!micEnabled; localStream?.getAudioTracks().forEach(t=>t.enabled=micEnabled); document.getElementById("btnToggleMic").textContent = micEnabled?"🎤":"🔇"; }
function toggleSpeaker() { speakerEnabled=!speakerEnabled; remoteVideo.muted=!speakerEnabled; document.getElementById("btnToggleSpeaker").textContent = speakerEnabled?"🔊":"🔈"; }

/* ---------- Draggable & Resizable popup ---------- */
header && (header.onmousedown = function(e){ e.preventDefault();
    let offsetX = e.clientX - popup.offsetLeft, offsetY = e.clientY - popup.offsetTop;
    function move(e){ popup.style.left=`${e.clientX-offsetX}px`; popup.style.top=`${e.clientY-offsetY}px`; }
    function stop(){ document.removeEventListener("mousemove",move); document.removeEventListener("mouseup",stop); }
    document.addEventListener("mousemove",move); document.addEventListener("mouseup",stop);
});

const resizeHandle = document.querySelector(".resize-handle");
resizeHandle && resizeHandle.addEventListener("mousedown", e => {
    e.preventDefault();
    const startWidth=parseInt(getComputedStyle(popup).width,10), startHeight=parseInt(getComputedStyle(popup).height,10);
    const startX=e.clientX, startY=e.clientY;
    function resize(e){ popup.style.width=`${startWidth+(e.clientX-startX)}px`; popup.style.height=`${startHeight+(e.clientY-startY)}px`; }
    function stop(){ document.removeEventListener("mousemove",resize); document.removeEventListener("mouseup",stop); }
    document.addEventListener("mousemove",resize); document.addEventListener("mouseup",stop);
});

</script>
