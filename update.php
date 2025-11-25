<?php
session_start();
require_once 'db.php';

$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);

$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit($id)) {
    die("ID tidak valid.");
}
$id = (int)$id;

$sql = "SELECT id, mata_kuliah, deskripsi_tugas, status, dosen, deadline 
        FROM tugas 
        WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Prepare statement gagal: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$task) {
    die("Data tidak ditemukan.");
}

$matkul_val    = isset($old['mata_kuliah']) ? $old['mata_kuliah'] : $task['mata_kuliah'];
$deskripsi_val = isset($old['deskripsi_tugas']) ? $old['deskripsi_tugas'] : $task['deskripsi_tugas'];
$status_val    = isset($old['status']) ? $old['status'] : $task['status'];
$dosen_val     = isset($old['dosen']) ? $old['dosen'] : $task['dosen'];
$deadline_val  = isset($old['deadline']) ? $old['deadline'] : $task['deadline'];

$matkul       = htmlspecialchars($matkul_val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$deskripsi    = htmlspecialchars($deskripsi_val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$status       = htmlspecialchars($status_val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$dosen        = htmlspecialchars($dosen_val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$deadline     = htmlspecialchars($deadline_val, ENT_QUOTES | ENT_HTML5, 'UTF-8');

function isInvalidClass($errors, $field) {
    return isset($errors[$field]) ? 'is-invalid' : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Tugas</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <h2 class="text-center mb-4">Edit Tugas</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Tampilkan pesan error general jika ada -->
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <!-- Form tetap sama, namun tiap input mendukung Bootstrap validation -->
                <form action="process/update_action.php" method="POST" class="p-4 bg-white shadow rounded" novalidate>

                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="mb-3">
                        <label class="form-label">Matkul</label>
                        <input type="text" name="mata_kuliah" class="form-control <?= isInvalidClass($errors, 'mata_kuliah') ?>"
                            value="<?= $matkul ?>" required>
                        <?php if (isset($errors['mata_kuliah'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['mata_kuliah']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi_tugas" class="form-control <?= isInvalidClass($errors, 'deskripsi_tugas') ?>" rows="3"
                            required><?= $deskripsi ?></textarea>
                        <?php if (isset($errors['deskripsi_tugas'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['deskripsi_tugas']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select <?= isInvalidClass($errors, 'status') ?>" required>
                            <option value="Belum"   <?= $status === "Belum" ? "selected" : "" ?>>Belum</option>
                            <option value="Sedang" <?= $status === "Sedang" ? "selected" : "" ?>>Sedang</option>
                            <option value="Selesai" <?= $status === "Selesai" ? "selected" : "" ?>>Selesai</option>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['status']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dosen</label>
                        <input type="text" name="dosen" class="form-control <?= isInvalidClass($errors, 'dosen') ?>"
                            value="<?= $dosen ?>" required>
                        <?php if (isset($errors['dosen'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['dosen']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" class="form-control <?= isInvalidClass($errors, 'deadline') ?>"
                            value="<?= $deadline ?>" required>
                        <?php if (isset($errors['deadline'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['deadline']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>

                </form>

            </div>
        </div>

    </div>

</body>

</html>
