// Auto-scroll to bottom
const msgArea = document.getElementById('msgArea');
if (msgArea) {
    msgArea.scrollTop = msgArea.scrollHeight;
}

// Only refresh if we're in a conversation
setInterval(() => {
    if (location.search.includes('a=view&id=')) {
        location.reload();
    }
}, 3000);