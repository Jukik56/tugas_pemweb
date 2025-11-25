<?php

session_start();

require_once __DIR__ . '/../db.php';

function redirect_index()
{
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$id               = isset($_POST['id']) ? trim($_POST['id']) : '';
$mata_kuliah      = isset($_POST['mata_kuliah']) ? trim($_POST['mata_kuliah']) : '';
$deskripsi_tugas  = isset($_POST['deskripsi_tugas']) ? trim($_POST['deskripsi_tugas']) : '';
$deadline         = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
$dosen            = isset($_POST['dosen']) ? trim($_POST['dosen']) : '';
$status           = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id === '' || !ctype_digit($id)) {
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light"><div class="container mt-5">';
    echo '<div class="alert alert-danger">ID tidak valid.</div>';
    echo '<a href="../index.php" class="btn btn-secondary">Kembali ke daftar</a>';
    echo '</div></body></html>';
    exit;
}

$id = (int) $id;

$_SESSION['old'] = [
    'mata_kuliah' => $mata_kuliah,
    'deskripsi_tugas' => $deskripsi_tugas,
    'deadline' => $deadline,
    'dosen' => $dosen,
    'status' => $status,
];

$errors = [];

if ($mata_kuliah === '') {
    $errors['mata_kuliah'] = "Mata kuliah wajib diisi.";
} elseif (mb_strlen($mata_kuliah) > 100) {
    $errors['mata_kuliah'] = "Mata kuliah maksimal 100 karakter.";
}

if ($deskripsi_tugas === '') {
    $errors['deskripsi_tugas'] = "Deskripsi tugas wajib diisi.";
}

if ($deadline === '') {
    $errors['deadline'] = "Deadline wajib diisi.";
} else {
    $d = DateTime::createFromFormat('Y-m-d', $deadline);
    $d_errors = DateTime::getLastErrors();
    if (!($d && $d_errors['warning_count'] == 0 && $d_errors['error_count'] == 0)) {
        $errors['deadline'] = "Format deadline tidak valid. Gunakan YYYY-MM-DD.";
    }
}

if ($dosen === '') {
    $errors['dosen'] = "Dosen wajib diisi.";
} elseif (mb_strlen($dosen) > 100) {
    $errors['dosen'] = "Nama dosen maksimal 100 karakter.";
}

$allowed_status = ["Belum", "Sedang", "Selesai"];
if ($status === '') {
    $errors['status'] = "Status wajib dipilih.";
} elseif (!in_array($status, $allowed_status, true)) {
    $errors['status'] = "Status tidak valid.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../update.php?id=' . $id);
    exit;
}

$sql = "UPDATE tugas 
        SET mata_kuliah = ?, deskripsi_tugas = ?, deadline = ?, dosen = ?, status = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    $_SESSION['errors'] = ['general' => 'Gagal menyiapkan query: ' . mysqli_error($conn)];
    header('Location: ../update.php?id=' . $id);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssi",
    $mata_kuliah,
    $deskripsi_tugas,
    $deadline,
    $dosen,
    $status,
    $id
);

try {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    unset($_SESSION['old']);
    unset($_SESSION['errors']);
    $_SESSION['success'] = "Tugas berhasil diperbarui.";

    redirect_index();
} catch (mysqli_sql_exception $e) {
    mysqli_stmt_close($stmt);
    $_SESSION['errors'] = ['general' => 'Gagal mengupdate data: ' . $e->getMessage()];
    header('Location: ../update.php?id=' . $id);
    exit;
}
