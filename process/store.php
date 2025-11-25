<?php
// process/store.php
session_start();

// include koneksi (pastikan path relatif benar)
require_once __DIR__ . '/../db.php';

// Ambil input dan trim
$mata_kuliah     = isset($_POST['mata_kuliah']) ? trim($_POST['mata_kuliah']) : '';
$deskripsi_tugas = isset($_POST['deskripsi_tugas']) ? trim($_POST['deskripsi_tugas']) : '';
$deadline        = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$dosen           = isset($_POST['dosen']) ? trim($_POST['dosen']) : '';
$status          = isset($_POST['status']) ? trim($_POST['status']) : '';

// Simpan old input supaya form bisa menampilkan kembali nilai jika ada error
$_SESSION['old'] = [
    'mata_kuliah' => $mata_kuliah,
    'deskripsi_tugas' => $deskripsi_tugas,
    'deadline' => $deadline,
    'dosen' => $dosen,
    'status' => $status,
];

// Validasi server-side (associative array per-field untuk Bootstrap feedback)
$errors = [];

// Mata kuliah
if ($mata_kuliah === '') {
    $errors['mata_kuliah'] = 'Mata kuliah wajib diisi.';
} elseif (mb_strlen($mata_kuliah) > 100) {
    $errors['mata_kuliah'] = 'Mata kuliah maksimal 100 karakter.';
}

// Deskripsi
if ($deskripsi_tugas === '') {
    $errors['deskripsi_tugas'] = 'Deskripsi tugas wajib diisi.';
}

// Deadline (format YYYY-MM-DD)
if ($deadline === '') {
    $errors['deadline'] = 'Deadline wajib diisi.';
} else {
    $d = DateTime::createFromFormat('Y-m-d', $deadline);
    $d_errors = DateTime::getLastErrors();
    if (!($d && $d_errors['warning_count'] == 0 && $d_errors['error_count'] == 0)) {
        $errors['deadline'] = 'Format deadline tidak valid. Gunakan YYYY-MM-DD.';
    }
}

// Dosen
if ($dosen === '') {
    $errors['dosen'] = 'Dosen wajib diisi.';
} elseif (mb_strlen($dosen) > 100) {
    $errors['dosen'] = 'Nama dosen maksimal 100 karakter.';
}

// Status
$allowed_status = ['Belum', 'Sedang', 'Selesai'];
if ($status === '') {
    $errors['status'] = 'Status wajib dipilih.';
} elseif (!in_array($status, $allowed_status, true)) {
    $errors['status'] = 'Status tidak valid.';
}

// Jika ada error, simpan associative errors ke session dan redirect kembali ke form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../add.php'); // gunakan URL relatif, bukan filesystem path
    exit;
}

// Jika lolos validasi — lakukan INSERT dengan prepared statement
$sql = "INSERT INTO tugas (mata_kuliah, deskripsi_tugas, deadline, dosen, status) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    // prepare gagal
    $_SESSION['errors'] = ['general' => 'Gagal menyiapkan query.'];
    header('Location: ../add.php');
    exit;
}

// bind dan execute
mysqli_stmt_bind_param($stmt, "sssss", $mata_kuliah, $deskripsi_tugas, $deadline, $dosen, $status);

try {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Bersihkan old/errors, set pesan sukses
    unset($_SESSION['old']);
    $_SESSION['success'] = "Tugas berhasil ditambahkan.";

    // Redirect ke index
    header('Location: ../index.php');
    exit;
} catch (mysqli_sql_exception $e) {
    // simpan error umum ke session agar tampil di form
    mysqli_stmt_close($stmt);
    $_SESSION['errors'] = ['general' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()];
    header('Location: ../add.php');
    exit;
}
