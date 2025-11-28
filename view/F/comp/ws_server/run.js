// run.js   ← WS
const express = require('express');
const path = require('path');
const RTC = require('./class.js');

// ←←← WS
function getLocalIP() {
    const os = require('os');
    const nets = os.networkInterfaces();
    for (const name of Object.keys(nets)) {
        for (const net of nets[name]) {
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return '127.0.0.1';
}

const app = express();
const PORT = 3000;

// Serve the 3 files from the same folder
app.use(express.static(__dirname));

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// THIS LINE IS THE MOST IMPORTANT → listen on ALL interfaces
app.listen(PORT, '0.0.0.0', () => {
    const ip = getLocalIP();
    console.log('=====================================');
    console.log(`   OPEN ON YOUR PHONE NOW → http://${ip}:3000`);
    console.log('=====================================');
    console.log(`   (or directly: http://192.168.100.5:3000)`);
});

// Start the WebSocket server
new RTC(500).start();