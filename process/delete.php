<?php

require_once __DIR__ . '/../db.php';

function redirect_index() {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$id = isset($_POST['id']) ? $_POST['id'] : '';

if ($id === '' || !ctype_digit((string)$id)) {
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light"><div class="container mt-5">';
    echo '<div class="alert alert-danger">ID tidak valid.</div>';
    echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
    echo '</div></body></html>';
    exit;
}

$id = (int)$id;

$sql = "DELETE FROM tugas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die('Prepare statement gagal: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);

try {
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected > 0) {
        redirect_index();
    } else {
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Not Found</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '</head><body class="bg-light"><div class="container mt-5">';
        echo '<div class="alert alert-warning">Data dengan ID tersebut tidak ditemukan atau sudah dihapus.</div>';
        echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
        echo '</div></body></html>';
        exit;
    }

} catch (mysqli_sql_exception $e) {
    mysqli_stmt_close($stmt);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body class="bg-light"><div class="container mt-5">';
    echo '<div class="alert alert-danger">Terjadi kesalahan saat menghapus data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<a href="../index.php" class="btn btn-secondary">Kembali</a>';
    echo '</div></body></html>';
    exit;
}
