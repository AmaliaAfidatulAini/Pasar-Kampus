<?php
require_once __DIR__ . '/../core/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email       = $_POST['email'] ?? '';
    $password    = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
    $role        = 'user'; 


    $name        = $_POST['name'] ?? '';
    $phone       = $_POST['phone'] ?? '';
    $campus      = $_POST['campus'] ?? '';
    $subdistrict = $_POST['subdistrict'] ?? '';
    $district    = $_POST['district'] ?? '';
    $preferences = $_POST['preferences'] ?? '';

    global $pdo; 

    try {
        $pdo->beginTransaction();

        $stmtUser = executeQuery("INSERT INTO users (email, password, role) VALUES (?, ?, ?) RETURNING user_id", [$email, $password, $role]);
        $userId = fetchColumn($stmtUser)[0] ?? $stmtUser->fetchColumn();

        executeQuery("INSERT INTO user_profiles (user_id, name, phone, campus, subdistrict, district, preferences) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $userId,
            $name,
            $phone,
            $campus,
            $subdistrict,
            $district,
            $preferences
        ]);

        $pdo->commit();
        header("Location: login.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Registrasi gagal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - Pasar Kampus</title>
</head>

<body>
    <h2>Daftar Akun Pasar Kampus</h2>
    <?php if ($message): ?><p style="color: red;"><?= $message ?></p><?php endif; ?>

    <form action="register.php" method="POST">
        <h3>Informasi Akun</h3>
        <label>Email:</label><br>
        <input type="email" name="email" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <h3>Biodata & Kampus</h3>
        <label>Nama Lengkap:</label><br>
        <input type="text" name="name" required><br>
        <label>No Telepon:</label><br>
        <input type="text" name="phone"><br>
        <label>Asal Kampus (cth: POLINEMA):</label><br>
        <input type="text" name="campus"><br>
        <label>Kecamatan:</label><br>
        <input type="text" name="subdistrict"><br>
        <label>Kabupaten/Kota (District):</label><br>
        <input type="text" name="district"><br>
        <label>Preferensi Barang:</label><br>
        <input type="text" name="preferences" placeholder="Buku, Elektronik, dll"><br><br>

        <button type="submit">Daftar Sekarang</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
</body>

</html>