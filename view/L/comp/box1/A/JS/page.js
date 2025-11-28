
<!-- PAGE SWITCHER -->
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
}

// Example: auto show reset page if token in URL
if (window.location.hash === '#reset') showPage('resetPage');
if (window.location.hash === '#profile') showPage('profilePage');