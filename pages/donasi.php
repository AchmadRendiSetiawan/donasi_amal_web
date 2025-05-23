<?php
session_start();
include '../config.php'; // Pastikan path ke config.php benar

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(50));
}
$csrf_token = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Donasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        .btn-success {
            background-color: #fd7e14;
            border-color: #fd7e14;
        }
        .btn-success:hover {
            background-color: #e66a00;
            border-color: #e66a00;
        }
        .form-control {
            color: #212529;
            background-color: #ffffff;
            border-color: #ced4da;
        }
        .form-control:focus {
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
        }
        .btn-outline-success {
        border-color: #fd7e14;
        color: #fd7e14;
        }

        .btn-outline-success:hover {
        background-color: #fd7e14;
        color: white;
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Form Donasi</h2>

        <?php
        // Tampilkan pesan status (sukses/error)
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'sukses') {
                echo '<div class="alert alert-success" role="alert">Donasi berhasil disimpan. Terima kasih!</div>';
            } elseif ($_GET['status'] == 'error') {
                $pesan_error = isset($_GET['pesan']) ? htmlspecialchars($_GET['pesan']) : 'Terjadi kesalahan saat memproses donasi.';
                echo '<div class="alert alert-danger" role="alert">Error: ' . $pesan_error . '</div>';
            } elseif ($_GET['status'] == 'input_tidak_valid') {
                echo '<div class="alert alert-warning" role="alert">Input tidak valid. Harap periksa kembali data donasi.</div>';
            } elseif ($_GET['status'] == 'csrf_invalid') {
                echo '<div class="alert alert-danger" role="alert">Kesalahan keamanan: Token CSRF tidak valid. Silakan coba lagi.</div>';
            } elseif ($_GET['status'] == 'program_tidak_aktif') {
                echo '<div class="alert alert-warning" role="alert">Program donasi yang dipilih tidak aktif atau tidak ditemukan.</div>';
            }
        }
        ?>

        <form method="POST" action="proses/proses_donasi.php">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="mb-2">
                <label for="id_donatur" class="form-label">Nama Donatur</label>
                <select name="id_donatur" id="id_donatur" class="form-control" required>
                    <?php
                    if (isset($conn)) {
                        $query = $conn->prepare("SELECT id_donatur, nama FROM donatur");
                        $query->execute();
                        $result = $query->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_donatur']}'>{$row['nama']}</option>";
                            }
                        } else {
                            echo "<option value=''>Tidak ada donatur tersedia</option>";
                        }
                        $query->close();
                    } else {
                        echo "<option value=''>Error: Koneksi database tidak tersedia</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-2">
                <label for="id_program" class="form-label">Program Amal</label>
                <select name="id_program" id="id_program" class="form-control" required>
                    <?php
                    // Ambil hanya program yang aktif
                    $query = $conn->prepare("SELECT id_program, nama_program FROM program_amal WHERE status = 'Aktif'");
                    $query->execute();
                    $result = $query->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <option value="<?= $row['id_program'] ?>"><?= htmlspecialchars($row['nama_program']) ?></option>
                    <?php
                        endwhile;
                    } else {
                        echo "<option value=''>Tidak ada program aktif tersedia</option>";
                    }
                    $query->close();
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="jumlah_donasi" class="form-label">Jumlah Donasi (Rp)</label>
                <input type="number" name="jumlah_donasi" id="jumlah_donasi" class="form-control" placeholder="Jumlah Donasi" required min="1000">
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="submit" class="btn btn-success">Donasi</button>
            <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Tutup koneksi setelah selesai digunakan di halaman ini
if (isset($conn)) {
    $conn->close();
}
?>