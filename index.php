<?php
require_once 'db.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

if ($q !== '' && $status_filter !== '') {
    $sql = "SELECT id, mata_kuliah, deskripsi_tugas, status, dosen, deadline
            FROM tugas
            WHERE (mata_kuliah LIKE ? OR dosen LIKE ?)
              AND status = ?
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%{$q}%";
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $status_filter);
} elseif ($q !== '') {
    $sql = "SELECT id, mata_kuliah, deskripsi_tugas, status, dosen, deadline
            FROM tugas
            WHERE mata_kuliah LIKE ? OR dosen LIKE ?
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%{$q}%";
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
} elseif ($status_filter !== '') {
    $sql = "SELECT id, mata_kuliah, deskripsi_tugas, status, dosen, deadline
            FROM tugas
            WHERE status = ?
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
} else {
    $sql = "SELECT id, mata_kuliah, deskripsi_tugas, status, dosen, deadline
            FROM tugas
            ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <h2 class="text-center mb-4">Daftar Tugas</h2>

        <!-- Tambah Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="add.php" class="btn btn-success">Tambah</a>

            <!-- Search / Filter form -->
            <form method="get" class="d-flex" style="gap:8px;">
                <input type="text" name="q" value="<?= htmlspecialchars($q); ?>" class="form-control"
                    placeholder="Cari Mata Kuliah atau Dosen...">
                <select name="status_filter" class="form-select" style="max-width:140px;">
                    <option value="" <?= $status_filter === '' ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="Belum" <?= $status_filter === 'Belum' ? 'selected' : ''; ?>>Belum</option>
                    <option value="Sedang" <?= $status_filter === 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                    <option value="Selesai" <?= $status_filter === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                </select>
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </form>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10" style="width: 100%;">

                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Matkul</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Dosen</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>

                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['mata_kuliah']); ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi_tugas']); ?></td>
                                    <td>
                                        <?php
                                        $st = $row['status'];
                                        if ($st === 'Belum') {
                                            echo '<span class="badge bg-warning text-dark">Belum</span>';
                                        } elseif ($st === 'Sedang') {
                                            echo '<span class="badge bg-info text-dark">Sedang</span>';
                                        } else {
                                            echo '<span class="badge bg-success">Selesai</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['dosen']); ?></td>
                                    <td><?= htmlspecialchars($row['deadline']); ?></td>
                                    <td>
                                        <a href="update.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>

                                        <!-- Delete pakai POST + konfirmasi -->
                                        <form action="process/delete.php" method="POST" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Tidak ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

</body>

</html>