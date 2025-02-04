<?php
// proses_logup.php
include("../koneksi.php");
session_start();

$username = trim($_POST["username"]);
$password = $_POST["password"];
$email = $_POST["email"];
$role = $_POST["role"];

// Validasi input
if (empty($username) || empty($password) || empty($email) || empty($role)) {
    die("Semua kolom harus diisi. <a href='logup.php'>Kembali</a>");
}

// Validasi panjang password
if (strlen($password) < 6) {
    die("Password harus memiliki minimal 6 karakter. <a href='logup.php'>Kembali</a>");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Validasi apakah username atau email sudah digunakan
$stmt = mysqli_prepare($koneksi, "SELECT user_name, email FROM user WHERE user_name = ? OR email = ?");
mysqli_stmt_bind_param($stmt, "ss", $username, $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    die("Username atau email sudah digunakan. <a href='logup.php'>Kembali</a>");
}
mysqli_stmt_close($stmt);

// Insert ke tabel users
$stmt = mysqli_prepare($koneksi, "INSERT INTO user (user_name, password, email, role) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $role);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    // Set sesi untuk pengguna yang berhasil mendaftar
    $_SESSION['id_user'] = mysqli_insert_id($koneksi);
    $_SESSION['user'] = $username;
    $_SESSION['email_user'] = $email;
    $_SESSION['role'] = $role;

    header("Location: ../home/home.php");
    exit;
} else {
    die("Pendaftaran gagal: " . mysqli_error($koneksi));
}
?>
