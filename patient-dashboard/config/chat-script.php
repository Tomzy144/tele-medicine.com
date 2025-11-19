<script>
/*
  Single merged script:
  - One shared WebSocket `ws` for chat + video signaling
  - refreshChat() preserved (rewritten to fit merged structure)
  - Chat + video handlers use same connection
  - No duplicate ws.onmessage
*/

/* ---------- Shared globals ---------- */
let ws = null;
const wsUrl = window.location.hostname === "localhost"
    ? "ws://localhost:8080"
    : "wss://tele-medicine-chat-server.onrender.com";

const chatMessages = $("#chatMessages");
const chatInput = $("#chatInput");
const doctorStatus = $("#doctorStatus");
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

/* ---------- Helpers used by refreshChat / global ---------- */
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

/* ---------- refreshChat() (kept & implemented) ---------- */
function refreshChat() {
    const patientId = $("#patient_id").val();
    const doctorId = $("#doctor_id").val();

    // --- Render one message ---
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

    // --- Doctor status update ---
    function updateDoctorStatus(online) {
        doctorStatus.text(online ? "Online" : "Offline").css("color", online ? "green" : "gray");
    }

    // --- Prescription response handler (kept local here) ---
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

        $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
    }

    // attach the global handler; if ws already exists, we won't recreate it
    let reconnectAttempts = 0;
    const MAX_RECONNECT_ATTEMPTS = 5;

    function ensureWebSocketConnected() {
        if (ws && ws.readyState === WebSocket.OPEN) return; // already connected or connecting

        ws = new WebSocket(wsUrl);

        ws.onopen = () => {
            console.log("✅ WebSocket connected");
            reconnectAttempts = 0;
            // register this patient & fetch initial data
            ws.send(JSON.stringify({ type: "patient_login", patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_history", doctor_id: doctorId, patient_id: patientId }));
            ws.send(JSON.stringify({ type: "get_status", doctor_id: doctorId }));
        };

        ws.onmessage = (event) => {
            // All messages (chat + video) are handled here. route to handlers below.
            let data;
            try { data = JSON.parse(event.data); }
            catch (e) { console.error("❌ Error parsing message:", e); return; }

            // ignore messages intended for other patients explicitly
            if (data.to && data.to !== "patient_" + patientId) return;

            // video/call types handled first
            if (["call_request","call_accept","call_reject","call_end","video_offer","video_answer","ice_candidate"].includes(data.type)) {
                // route to video handler
                handleVideoMessage(data, patientId);
                return;
            }

            // Chat & other messages
            switch (data.type) {
                case "history":
                case "missed_messages":
                    chatMessages.empty();
                    if (Array.isArray(data.data)) {
                        data.data.forEach(createMessage);
                        const unseenIds = data.data
                            .filter(m => m.sender === "doctor" && m.status !== "seen")
                            .map(m => m.sn);
                        if (unseenIds.length) {
                            ws.send(JSON.stringify({ type: "mark_seen", message_ids: unseenIds, doctor_id: doctorId, patient_id: patientId }));
                        }
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
            } else {
                alert("Connection lost. Please refresh the page to reconnect.");
            }
        };

        ws.onerror = (err) => console.error("⚠️ WebSocket error:", err);
    }

    // start socket & handlers
    ensureWebSocketConnected();

    // expose send_chat and addToPrescription globally (keeps old behavior)
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

    // bind UI events
    $("#chatInput").off("keydown").on("keydown", window.send_chat);
    $("#sendBtn").off("click").on("click", window.send_chat);
}

/* ---------- Video / signaling handler (shared) ---------- */
function handleVideoMessage(data, patientId) {
    try {
        // CALL REQUEST
        if (data.type === 'call_request') {
            peerId = data.from_key || data.from;
            showIncomingCallPopup(data.name, data.picture);
            return;
        }

        // VIDEO OFFER
        if (data.type === 'video_offer') {
            peerId = data.from_key || data.from;
            pendingOffer = data.sdp;
            (async () => {
                try {
                    await initLocalMedia();
                    createPeerConnection(peerId);
                    if (pendingOffer) {
                        await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
                        const answer = await peerConnection.createAnswer();
                        await peerConnection.setLocalDescription(answer);
                        if (ws && ws.readyState === WebSocket.OPEN) {
                            ws.send(JSON.stringify({ type: 'video_answer', sdp: answer, to: peerId, from: patientId }));
                        }
                        pendingOffer = null;
                    }
                    popup.style.display = 'flex';
                } catch (err) {
                    console.error('❌ Video offer handling failed:', err);
                }
            })();
            return;
        }

        // VIDEO ANSWER
        if (data.type === 'video_answer') {
            if (!peerConnection) return;
            peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp)).catch(console.error);
            return;
        }

        // ICE CANDIDATE
        if (data.type === 'ice_candidate') {
            if (peerConnection && data.candidate) {
                peerConnection.addIceCandidate(data.candidate).catch(console.error);
            }
            return;
        }

        // CALL END
        if (data.type === 'call_end') {
            closeVideoCall();
            alert('📞 Call ended by doctor');
            return;
        }

        // CALL ACCEPT / REJECT might be used by doctor side - no extra client action here except maybe UI but handle if needed
        if (data.type === 'call_accept') {
            // doctor accepted, could start offer flow from patient if patient initiated - handled elsewhere
            return;
        }
        if (data.type === 'call_reject') {
            // doctor rejected - close any popup
            popup.style.display = 'none';
            return;
        }

    } catch (err) {
        console.error('❌ Error in handleVideoMessage:', err);
    }
}

/* ---------- Local media & PeerConnection ---------- */
async function initLocalMedia() {
    if (localStream) return;
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video:true, audio:true });
        if (localVideo) {
            localVideo.srcObject = localStream;
            localVideo.style.display = "block";
        }
    } catch (err) {
        console.error("❌ Camera/Mic error:", err);
    }
}

function createPeerConnection(peer) {
    peerConnection = new RTCPeerConnection({
        iceServers: [
            { urls: "stun:stun.relay.metered.ca:80" },
            { urls:"turn:global.relay.metered.ca:443", username:"ec4e996df1b54e5300c955bf", credential:"mElLDNWaGsNkqVSK" }
        ]
    });

    if (localStream) localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    peerConnection.ontrack = e => {
        remoteStream = e.streams[0];
        if (remoteVideo) {
            remoteVideo.srcObject = remoteStream;
            remoteVideo.style.display = "block";
            if (placeholder) placeholder.style.display = "none";
        }
    };

    peerConnection.onicecandidate = e => {
        if (e.candidate && ws && ws.readyState === WebSocket.OPEN) {
            const patientId = document.getElementById("patient_id").value;
            ws.send(JSON.stringify({ type: "ice_candidate", candidate: e.candidate, to: peer, from: patientId }));
        }
    };
}

/* ---------- UI for incoming call ---------- */
function showIncomingCallPopup(name, picture) {
    if (!popup) return;
    popup.style.display = "flex";
    document.getElementById("videoCallUser").textContent = "Doctor: " + name;
    if (placeholder) { placeholder.src = picture; placeholder.style.display = "block"; }
    if (remoteVideo) remoteVideo.style.display = "none";
}

/* ---------- Accept / Reject / End ---------- */
btnAccept && (btnAccept.onclick = async () => {
    const patientId = document.getElementById("patient_id").value;
    if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify({ type: "call_accept", to: peerId, from: patientId }));

    await initLocalMedia();
    createPeerConnection(peerId);

    if (pendingOffer) {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify({ type: "video_answer", sdp: answer, to: peerId, from: patientId }));
        pendingOffer = null;
    }

    popup.style.display = "none";
});

btnReject && (btnReject.onclick = () => {
    const patientId = document.getElementById("patient_id").value;
    if (ws && ws.readyState === WebSocket.OPEN) ws.send(JSON.stringify({ type: "call_reject", to: peerId, from: patientId }));
    popup.style.display = "none";
});

function closeVideoCall() {
    if (popup) popup.style.display = "none";
    if (localStream) localStream.getTracks().forEach(t=>t.stop());
    if (peerConnection) peerConnection.close();
    peerConnection = null;
    if (remoteVideo) remoteVideo.srcObject = null;
    if (placeholder) placeholder.style.display = "block";

    if (peerId && ws && ws.readyState === WebSocket.OPEN) {
        const patientId = document.getElementById("patient_id").value;
        ws.send(JSON.stringify({ type: "call_end", to: peerId, from: patientId }));
        peerId = null;
    }
}

/* ---------- Mic & Speaker ---------- */
function toggleMic() {
    micEnabled = !micEnabled;
    if (localStream) localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    document.getElementById("btnToggleMic").textContent = micEnabled ? "🎤" : "🔇";
}
function toggleSpeaker() {
    speakerEnabled = !speakerEnabled;
    remoteVideo.muted = !speakerEnabled;
    document.getElementById("btnToggleSpeaker").textContent = speakerEnabled ? "🔊" : "🔈";
}

/* ---------- Draggable & Resizable popup (kept intact) ---------- */
header && (header.onmousedown = function(e) {
    e.preventDefault();
    let offsetX = e.clientX - popup.offsetLeft;
    let offsetY = e.clientY - popup.offsetTop;
    function move(e) { popup.style.left = `${e.clientX - offsetX}px`; popup.style.top = `${e.clientY - offsetY}px`; }
    function stop() { document.removeEventListener("mousemove", move); document.removeEventListener("mouseup", stop); }
    document.addEventListener("mousemove", move);
    document.addEventListener("mouseup", stop);
});

const resizeHandle = document.querySelector(".resize-handle");
if (resizeHandle) {
    resizeHandle.addEventListener("mousedown", e => {
        e.preventDefault();
        const startWidth = parseInt(getComputedStyle(popup).width,10);
        const startHeight = parseInt(getComputedStyle(popup).height,10);
        const startX = e.clientX, startY = e.clientY;
        function resize(e) { popup.style.width = `${startWidth + (e.clientX - startX)}px`; popup.style.height = `${startHeight + (e.clientY - startY)}px`; }
        function stop() { document.removeEventListener("mousemove", resize); document.removeEventListener("mouseup", stop); }
        document.addEventListener("mousemove", resize);
        document.addEventListener("mouseup", stop);
    });
}

/* ---------- Expose refreshChat globally so you can call it from page init ---------- */
window.refreshChat = refreshChat;

/* ---------- Optionally auto-run refreshChat if you want immediately ---------- */
/* Uncomment next line if you want it to auto-initialize on script load */
// refreshChat();

</script>
