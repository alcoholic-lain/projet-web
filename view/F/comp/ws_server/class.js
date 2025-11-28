// class.js
const WebSocket = require('ws');

class RTC {
    constructor(port = 500) {
        this.port = port;
        this.clients = new Map(); // id → { ws, channel }
    }

    _generateID() {
        return Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
    }

    broadcast(channel, data, exclude = null) {
        const msg = typeof data === 'string' ? data : JSON.stringify(data);
        this.clients.forEach((client, id) => {
            if (client.ws.readyState === WebSocket.OPEN &&
                client.channel === channel &&
                client.ws !== exclude) {
                client.ws.send(msg);
            }
        });
    }

    start() {
        const wss = new WebSocket.Server({ port: this.port });

        wss.on('connection', (ws) => {
            const id = this._generateID();
            this.clients.set(id, { ws, channel: '' }); // '' = public

            console.log(`User connected → ${id}`);

            ws.on('message', (raw) => {
                let msg;
                try { msg = JSON.parse(raw.toString()); }
                catch { return; }

                const client = this.clients.get(id);

                // Switch channel
                if (msg.type === 'canal') {
                    client.channel = msg.canal || '';
                    console.log(`${id} → channel "${client.channel}"`);
                    return;
                }

                // Chat message
                if (msg.type === 'message') {
                    const { username, message } = msg;
                    if (!username?.trim() || !message?.trim()) return;

                    const payload = {
                        type: 'message',
                        username: username.trim(),
                        message: message.trim(),
                        canal: client.channel
                    };

                    this.broadcast(client.channel, payload);
                }
            });

            ws.on('close', () => {
                console.log(`User disconnected → ${id}`);
                this.clients.delete(id);
            });
        });

        console.log(`RTC server running on ws://localhost:${this.port}`);
    }
}

module.exports = RTC;