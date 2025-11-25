<?php
// process/delete.php
// Pastikan file ini berada di folder 'process/' dan db.php ada satu level atas.

require_once __DIR__ . '/../db.php';

// Helper redirect
function redirect_index() {
    header('Location: ../index.php');
    exit;
}

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

// Ambil id dan validasi
$id = isset($_POST['id']) ? $_POST['id'] : '';

if ($id === '' || !ctype_digit((string)$id)) {
    // Invalid id
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light"><div class="container mt-5">';
    echo '<div class="alert alert-danger">ID tidak valid.</div>';
    echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
    echo '</div></body></html>';
    exit;
}

$id = (int)$id;

// Lakukan delete dengan prepared statement
$sql = "DELETE FROM tugas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    // prepare gagal
    die('Prepare statement gagal: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);

try {
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected > 0) {
        // Sukses - redirect
        redirect_index();
    } else {
        // ID tidak ditemukan
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Not Found</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '</head><body class="bg-light"><div class="container mt-5">';
        echo '<div class="alert alert-warning">Data dengan ID tersebut tidak ditemukan atau sudah dihapus.</div>';
        echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
        echo '</div></body></html>';
        exit;
    }

} catch (mysqli_sql_exception $e) {
    // Error eksekusi
    mysqli_stmt_close($stmt);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light"><div class="container mt-5">';
    echo '<div class="alert alert-danger">Terjadi kesalahan saat menghapus data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
    echo '</div></body></html>';
    exit;
}
