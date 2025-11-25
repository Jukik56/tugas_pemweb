<?php 
session_start(); 

$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <h2 class="text-center mb-4">Tambah Tugas</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success']); ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form action="process/store.php" method="POST" class="p-4 bg-white shadow rounded" novalidate>

                    <div class="mb-3">
                        <label class="form-label">Matkul</label>
                        <input 
                            type="text" 
                            name="mata_kuliah" 
                            class="form-control <?= isset($errors['mata_kuliah']) ? 'is-invalid' : '' ?>" 
                            value="<?= htmlspecialchars($old['mata_kuliah'] ?? '') ?>"
                        >
                        <?php if (isset($errors['mata_kuliah'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['mata_kuliah']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea 
                            name="deskripsi_tugas" 
                            class="form-control <?= isset($errors['deskripsi_tugas']) ? 'is-invalid' : '' ?>" 
                            rows="3"
                        ><?= htmlspecialchars($old['deskripsi_tugas'] ?? '') ?></textarea>
                        <?php if (isset($errors['deskripsi_tugas'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['deskripsi_tugas']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select 
                            name="status" 
                            class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                        >
                            <option value="Belum" selected>Belum</option> 
                            <option value="Sedang"  <?= (isset($old['status']) && $old['status']=='Sedang')?'selected':'' ?>>Sedang</option>
                            <option value="Selesai" <?= (isset($old['status']) && $old['status']=='Selesai')?'selected':'' ?>>Selesai</option>
                        </select>
                        <?php if (isset($errors['status'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['status']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dosen</label>
                        <input 
                            type="text" 
                            name="dosen" 
                            class="form-control <?= isset($errors['dosen']) ? 'is-invalid' : '' ?>" 
                            value="<?= htmlspecialchars($old['dosen'] ?? '') ?>"
                        >
                        <?php if (isset($errors['dosen'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['dosen']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input 
                            type="date" 
                            name="deadline" 
                            class="form-control <?= isset($errors['deadline']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($old['deadline'] ?? '') ?>"
                        >
                        <?php if (isset($errors['deadline'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($errors['deadline']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>

                </form>

            </div>
        </div>

    </div>

</body>

</html>
