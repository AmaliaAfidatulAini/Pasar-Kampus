<?php
destroy_session();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout - Pasar Kampus</title>
</head>
<body>
    <script>
        localStorage.setItem('pk_logged_in', 'false');
        localStorage.removeItem('pk_admin_logged_in');
        window.location.href = '<?= route('/login.php') ?>';
    </script>
</body>
</html>
