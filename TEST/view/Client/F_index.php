<script>
    // ===== CONFIGURATION =====
    const WS_URL = `ws://${window.location.hostname}:9090`;
    const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?? 1001 ?>;
    const CURRENT_USERNAME = '<?= addslashes($_SESSION['username'] ?? 'alice') ?>';

    console.log('[CONFIG] WebSocket URL:', WS_URL);
    console.log('[CONFIG] User ID:', CURRENT_USER_ID);
    console.log('[CONFIG] Username:', CURRENT_USERNAME);
</script>
<?php
//FrontOffice





require __DIR__ . '/F.html';

//require __DIR__ . '/comp/ws_server/index.html';
?>

