// view/F/comp/COMS/ws_server/class.js
const WebSocket = require('ws');

class ChatRTC {
    constructor(port = 8080) {
        this.port = port;
        this.clients = new Map(); // clientId → { ws, conversationId, userId, username }
        this.typingTimers = new Map(); // conversationId → Map<userId, timeout>
    }

    _generateID() {
        return Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
    }

    // Broadcast to all users in a conversation except sender
    broadcast(conversationId, data, excludeClientId = null) {
        const msg = typeof data === 'string' ? data : JSON.stringify(data);
        let sent = 0;

        console.log(`[BROADCAST] To conversation ${conversationId}, excluding ${excludeClientId}`);

        this.clients.forEach((client, clientId) => {
            const isInConversation = client.conversationId == conversationId;
            const isNotExcluded = clientId !== excludeClientId;
            const isOpen = client.ws.readyState === WebSocket.OPEN;

            if (isOpen && isInConversation && isNotExcluded) {
                client.ws.send(msg);
                sent++;
                console.log(`[BROADCAST] ✅ Sent to ${client.username} (${clientId})`);
            }
        });

        console.log(`[BROADCAST] Total sent: ${sent}`);
        return sent;
    }

    // Get all users currently in a conversation
    getUsersInConversation(conversationId) {
        const users = [];
        this.clients.forEach((client) => {
            if (client.conversationId == conversationId) {
                users.push({
                    userId: client.userId,
                    username: client.username
                });
            }
        });
        return users;
    }

    // Handle typing indicator with auto-stop after 3 seconds
    handleTyping(clientId, conversationId, isTyping) {
        const client = this.clients.get(clientId);
        if (!client || !conversationId) {
            console.log(`[TYPING] ❌ Invalid client or conversation:`, { clientId, conversationId });
            return;
        }

        console.log(`[TYPING] ${client.username} (${client.userId}) in conversation ${conversationId}: ${isTyping ? 'START' : 'STOP'}`);

        if (!this.typingTimers.has(conversationId)) {
            this.typingTimers.set(conversationId, new Map());
        }

        const convTyping = this.typingTimers.get(conversationId);

        // Clear existing timer
        if (convTyping.has(client.userId)) {
            clearTimeout(convTyping.get(client.userId));
            console.log(`[TYPING] Cleared existing timer for ${client.username}`);
        }

        if (isTyping) {
            // Set auto-stop timer (3 seconds)
            const timer = setTimeout(() => {
                console.log(`[TYPING] ⏱️ Auto-stop timer expired for ${client.username}`);
                convTyping.delete(client.userId);
                this.broadcast(conversationId, {
                    type: 'typing',
                    userId: client.userId,
                    username: client.username,
                    conversationId: conversationId,
                    isTyping: false
                }, clientId);
            }, 3000);

            convTyping.set(client.userId, timer);

            // Broadcast typing start
            const broadcastData = {
                type: 'typing',
                userId: client.userId,
                username: client.username,
                conversationId: conversationId,
                isTyping: true
            };
            console.log(`[TYPING] 📤 Broadcasting START:`, broadcastData);
            const sent = this.broadcast(conversationId, broadcastData, clientId);
            console.log(`[TYPING] 📊 Broadcast reached ${sent} clients`);
        } else {
            // Stop typing
            convTyping.delete(client.userId);
            const broadcastData = {
                type: 'typing',
                userId: client.userId,
                username: client.username,
                conversationId: conversationId,
                isTyping: false
            };
            console.log(`[TYPING] 📤 Broadcasting STOP:`, broadcastData);
            const sent = this.broadcast(conversationId, broadcastData, clientId);
            console.log(`[TYPING] 📊 Broadcast reached ${sent} clients`);
        }
    }

    // Clean up when user leaves conversation
    leaveConversation(clientId) {
        const client = this.clients.get(clientId);
        if (!client || !client.conversationId) return;

        const conversationId = client.conversationId;

        // Clear typing timer
        if (this.typingTimers.has(conversationId)) {
            const convTyping = this.typingTimers.get(conversationId);
            if (convTyping.has(client.userId)) {
                clearTimeout(convTyping.get(client.userId));
                convTyping.delete(client.userId);
            }
        }

        // Notify others
        this.broadcast(conversationId, {
            type: 'user_left',
            userId: client.userId,
            username: client.username,
            timestamp: new Date().toISOString()
        }, clientId);

        client.conversationId = null;
    }

    start() {
        const wss = new WebSocket.Server({
            port: this.port,
            perMessageDeflate: false
        });

        wss.on('connection', (ws, req) => {
            const clientId = this._generateID();
            const clientIp = req.socket.remoteAddress;

            this.clients.set(clientId, {
                ws,
                conversationId: null,
                userId: null,
                username: null
            });

            console.log(`[WS] 🟢 Client connected: ${clientId} from ${clientIp}`);
            console.log(`[WS] Total clients: ${this.clients.size}`);

            // Send connection confirmation
            ws.send(JSON.stringify({
                type: 'connected',
                clientId,
                timestamp: new Date().toISOString()
            }));

            // Heartbeat
            ws.isAlive = true;
            ws.on('pong', () => {
                ws.isAlive = true;
            });

            ws.on('message', (raw) => {
                let msg;
                try {
                    msg = JSON.parse(raw.toString());
                } catch(e) {
                    console.error('[WS] ❌ Invalid JSON:', e.message);
                    return;
                }

                console.log(`[WS] 📨 Received from ${clientId}:`, msg.type, msg);

                const client = this.clients.get(clientId);
                if (!client) return;

                // Handle message types
                switch(msg.type) {
                    case 'join':
                        // User joins a conversation
                        const { conversationId, userId, username } = msg;

                        if (!conversationId || !userId || !username) {
                            console.error('[WS] ❌ Missing join data');
                            return;
                        }

                        // Leave previous conversation if any
                        if (client.conversationId) {
                            this.leaveConversation(clientId);
                        }

                        client.conversationId = String(conversationId);
                        client.userId = String(userId);
                        client.username = username;

                        console.log(`[WS] 👤 ${username} (${userId}) joined conversation ${conversationId}`);

                        // Send user list to joiner
                        const users = this.getUsersInConversation(conversationId);
                        ws.send(JSON.stringify({
                            type: 'users_list',
                            users,
                            timestamp: new Date().toISOString()
                        }));

                        // Notify others
                        this.broadcast(conversationId, {
                            type: 'user_joined',
                            userId,
                            username,
                            timestamp: new Date().toISOString()
                        }, clientId);
                        break;

                    case 'leave':
                        // User explicitly leaves conversation
                        this.leaveConversation(clientId);
                        console.log(`[WS] 👋 ${client.username} left conversation ${client.conversationId}`);
                        break;

                    case 'message':
                        // New message sent
                        if (!client.conversationId || !msg.content?.trim()) {
                            console.error('[WS] ❌ Invalid message data');
                            return;
                        }

                        const messageData = {
                            type: 'new_message',
                            messageId: msg.messageId || this._generateID(),
                            userId: client.userId,
                            username: client.username,
                            content: msg.content.trim(),
                            conversationId: client.conversationId,
                            replyToId: msg.replyToId || null,
                            timestamp: new Date().toISOString()
                        };

                        console.log(`[WS] 💬 Message in ${client.conversationId} from ${client.username}`);

                        // Stop typing indicator
                        this.handleTyping(clientId, client.conversationId, false);

                        // Broadcast to all except sender
                        const recipientCount = this.broadcast(client.conversationId, messageData, clientId);

                        // Send confirmation to sender
                        ws.send(JSON.stringify({
                            type: 'message_sent',
                            messageId: messageData.messageId,
                            recipientCount,
                            timestamp: messageData.timestamp
                        }));
                        break;

                    case 'typing':
                        // Typing indicator
                        console.log(`[WS] ⌨️ Typing indicator from ${client.username}:`, {
                            conversationId: msg.conversationId,
                            isTyping: msg.isTyping,
                            clientConv: client.conversationId
                        });

                        if (client.conversationId) {
                            this.handleTyping(clientId, client.conversationId, msg.isTyping === true);
                        } else {
                            console.warn(`[WS] ⚠️ Typing indicator received but client not in conversation`);
                        }
                        break;

                    case 'message_edit':
                        // Message edited
                        if (client.conversationId && msg.messageId) {
                            console.log(`[WS] ✏️ Message ${msg.messageId} edited by ${client.username}`);

                            this.broadcast(client.conversationId, {
                                type: 'message_edited',
                                messageId: msg.messageId,
                                content: msg.content,
                                userId: client.userId,
                                conversationId: client.conversationId,
                                timestamp: new Date().toISOString()
                            }, clientId);
                        }
                        break;

                    case 'message_delete':
                        // Message deleted
                        if (client.conversationId && msg.messageId) {
                            console.log(`[WS] 🗑️ Message ${msg.messageId} deleted by ${client.username}`);

                            this.broadcast(client.conversationId, {
                                type: 'message_deleted',
                                messageId: msg.messageId,
                                userId: client.userId,
                                conversationId: client.conversationId,
                                timestamp: new Date().toISOString()
                            }, clientId);
                        }
                        break;

                    case 'reaction_update':
                        // NEW: Reaction updated
                        if (client.conversationId && msg.messageId) {
                            console.log(`[WS] 😊 Reaction on message ${msg.messageId} by ${client.username}`);

                            this.broadcast(client.conversationId, {
                                type: 'reaction_updated',
                                messageId: msg.messageId,
                                reactions: msg.reactions,
                                userId: client.userId,
                                conversationId: client.conversationId,
                                timestamp: new Date().toISOString()
                            }, clientId);
                        }
                        break;

                    case 'ping':
                        // Respond to ping
                        ws.send(JSON.stringify({ type: 'pong', timestamp: new Date().toISOString() }));
                        break;

                    default:
                        console.log(`[WS] ❓ Unknown message type: ${msg.type}`);
                }
            });

            ws.on('close', () => {
                const client = this.clients.get(clientId);

                if (client) {
                    if (client.conversationId) {
                        this.leaveConversation(clientId);
                    }
                    console.log(`[WS] 🔴 Client disconnected: ${clientId} (${client.username || 'unknown'})`);
                }

                this.clients.delete(clientId);
                console.log(`[WS] Total clients: ${this.clients.size}`);
            });

            ws.on('error', (error) => {
                console.error(`[WS] ⚠️ Error for client ${clientId}:`, error.message);
            });
        });

        // Heartbeat interval to detect dead connections
        const heartbeat = setInterval(() => {
            wss.clients.forEach((ws) => {
                if (ws.isAlive === false) {
                    return ws.terminate();
                }
                ws.isAlive = false;
                ws.ping();
            });
        }, 30000);

        wss.on('close', () => {
            clearInterval(heartbeat);
        });

        console.log('╔═══════════════════════════════════╗');
        console.log('║   🚀 TuniSpace Chat Server         ║');
        console.log('║                                    ║');
        console.log(`║   Port: ${this.port}                       ║`);
        console.log(`║   URL: ws://localhost:${this.port}         ║`);
        console.log('║                                    ║');
        console.log('║   Status: ✅ Running                ║');
        console.log('╚═══════════════════════════════════╝');
    }
}

module.exports = ChatRTC;