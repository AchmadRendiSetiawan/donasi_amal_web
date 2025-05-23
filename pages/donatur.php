<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Donatur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            color: #212529;
        }
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
        }
        .btn-orange:hover {
            background-color: #e66a00;
            border-color: #e66a00;
        }
        .modal-content {
            background-color: #ffffff;
            color: #212529;
        }
        .table {
            color: #212529;
        }
        .table thead th {
            background-color: #e9ecef;
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
            <h2 class="mb-0 text-center flex-grow-1">Daftar Donatur</h2>
            <a href="#" class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#tambahDonatur">Tambah Donatur</a>
        </div>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $conn->query("SELECT * FROM donatur");
                while ($row = $query->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['nama']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['no_hp']) . "</td>
                            <td>" . htmlspecialchars($row['alamat']) . "</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Donatur -->
    <div class="modal fade" id="tambahDonatur" tabindex="-1" aria-labelledby="tambahDonaturLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="proses/proses_tambah_donatur.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahDonaturLabel">Tambah Donatur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_new" class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama_new" placeholder="Nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_new" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email_new" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_hp_new" class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" id="no_hp_new" placeholder="No HP" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_new" class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" id="alamat_new" placeholder="Alamat" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-orange">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
