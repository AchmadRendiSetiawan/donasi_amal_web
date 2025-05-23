<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Penerima Manfaat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles for white background and black text */
        body {
            background-color: #f8f9fa; /* A very light grey, almost white */
            color: #212529; /* Dark charcoal, almost black */
        }

        /* Styling for the orange buttons */
        /* Targets Bootstrap's default success button style (used for "Simpan") */
        .btn-success {
            background-color: #fd7e14; /* A vibrant orange */
            border-color: #fd7e14; /* Match the background color */
        }
        .btn-success:hover {
            background-color: #e66a00; /* Slightly darker orange on hover */
            border-color: #e66a00;
        }

        /* Ensures table text is black */
        .table {
            color: #212529; /* Dark charcoal */
        }

        /* Table header background for better visibility */
        .table-bordered thead th {
            background-color: #e9ecef; /* Light grey for table headers */
            border-color: #dee2e6; /* Default Bootstrap border color */
        }
        
        /* Ensure input and select fields maintain black text and white background */
        .form-control, .form-select {
            color: #212529;
            background-color: #ffffff;
            border-color: #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #fd7e14; /* Orange border on focus */
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25); /* Orange shadow on focus */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Penerima Manfaat</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Total Bantuan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM penerima_manfaat_dengan_penyaluran");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_penerima']) ?></td>
                    <td><?= htmlspecialchars($row['kontak']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td>Rp <?= number_format($row['total_bantuan'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 class="mt-5">Tambah Penerima Baru</h3>
    <form method="POST" action="proses/proses_tambah_penerima.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] = bin2hex(random_bytes(50)) ?>">
        <input type="text" name="nama" placeholder="Nama Lengkap" class="form-control mb-2" required>
        <select name="jenis_penerima" class="form-control mb-2" required>
            <option value="">Pilih Jenis</option>
            <option value="Anak Yatim">Anak Yatim</option>
            <option value="Lansia">Lansia</option>
            <option value="Fakir Miskin">Fakir Miskin</option>
            <option value="Lainnya">Lainnya</option>
        </select>
        <input type="text" name="kontak" placeholder="Kontak (Opsional)" class="form-control mb-2">
        <textarea name="alamat" class="form-control mb-2" placeholder="Alamat" required></textarea>
        <div class="d-flex justify-content-between align-items-center mt-4">
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
        </div>
    </form>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>