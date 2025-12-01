// view/F/comp/COMS/ws_server/run.js
const ChatRTC = require('./class.js');

// Create and start WebSocket server on port 8080
const chatServer = new ChatRTC(8080);
chatServer.start();

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\n[WS] 🛑 Shutting down server...');
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n[WS] 🛑 Shutting down server...');
    process.exit(0);
});

console.log('\n💡 Server is ready for connections!');
console.log('📱 Connect from your chat at: ws://localhost:8080');
console.log('⏹️  Press Ctrl+C to stop\n');