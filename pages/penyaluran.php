<?php
session_start();
include '../config.php'; // Ensure this path is correct for your config file

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(50));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyaluran Donasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* Dominant white background */
            color: #212529; /* Dark text for general font (almost black) */
        }
        .btn-orange {
            background-color: #fd7e14; /* Orange button background */
            border-color: #fd7e14; /* Orange button border */
            color: white; /* White text on orange buttons */
        }
        .btn-orange:hover {
            background-color: #e66a00; /* Darker orange on hover */
            border-color: #e66a00;
        }
        .table thead th {
            background-color: #e9ecef; /* Light grey for table headers */
            color: #212529; /* Dark text for table headers */
        }
        .card {
            border: none; /* Keep card border minimal */
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); /* Subtle shadow for cards */
        }
        .card-header {
            background-color: #fd7e14; /* Orange for card header */
            color: white; /* White text on orange header */
            font-weight: bold; /* Make header text bold */
        }
        .form-control {
            color: #212529; /* Ensure form input text is dark */
            border-color: #ced4da; /* Default Bootstrap border color */
        }
        /* Specific styling for table body text to ensure it's dark */
        .table tbody {
            color: #212529;
        }
        /* Styling for the badge in the table */
        .badge.bg-success {
            background-color: #198754 !important; /* Bootstrap success green */
            color: white !important;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important; /* Bootstrap warning yellow */
            color: #212529 !important; /* Dark text for warning badge */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Distribusi Donasi ke Penerima Manfaat</h1>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Form Penyaluran</div>
        <div class="card-body">
            <form method="POST" action="proses/proses_distribusi.php">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="mb-3">
                    <label for="programSelect" class="form-label">Pilih Program Amal</label>
                    <select name="id_program" id="programSelect" class="form-control" required>
                        <option value="">-- Pilih Program --</option>
                        <?php
                        // MODIFIED: Fetch programs that are 'Aktif' OR 'Selesai'
                        // This allows distribution from programs that have reached their target but still have funds
                        $query = $conn->prepare("SELECT id_program, nama_program FROM program_amal WHERE status IN ('Aktif', 'Selesai') ORDER BY nama_program ASC");
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id_program'] ?>"><?= htmlspecialchars($row['nama_program']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="penerimaSelect" class="form-label">Pilih Penerima Manfaat</label>
                    <select name="id_penerima" id="penerimaSelect" class="form-control" required>
                        <option value="">-- Pilih Penerima --</option>
                        <?php
                        $query = $conn->prepare("SELECT id_penerima, nama FROM penerima_manfaat ORDER BY nama ASC");
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id_penerima'] ?>"><?= htmlspecialchars($row['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlahTotal" class="form-label">Jumlah Total yang Disalurkan</label>
                    <input type="number" name="jumlah_total" id="jumlahTotal" class="form-control" placeholder="Masukkan jumlah donasi yang akan disalurkan" required min="0.01" step="0.01">
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="submit" class="btn btn-orange">Distribusikan</button>
                <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Riwayat Penyaluran</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0"> <thead>
                    <tr>
                        <th>Program</th>
                        <th>Penerima</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $conn->query("
                        SELECT 
                            pd.id_penyaluran,
                            pa.nama_program,
                            pm.nama AS nama_penerima,
                            pd.jumlah_disalurkan,
                            pd.tanggal_penyaluran,
                            pd.status_distribusi
                        FROM penyaluran_donasi pd
                        JOIN program_amal pa ON pd.id_program = pa.id_program
                        JOIN penerima_manfaat pm ON pd.id_penerima = pm.id_penerima
                        ORDER BY pd.tanggal_penyaluran DESC
                    ");
                    if ($query && $query->num_rows > 0) {
                        while ($row = $query->fetch_assoc()):
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_program']) ?></td>
                                <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                                <td>Rp <?= number_format($row['jumlah_disalurkan'], 0, ',', '.') ?></td>
                                <td><?= $row['tanggal_penyaluran'] ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= $row['status_distribusi'] == 'Selesai' ? 'bg-success' : 'bg-warning' ?>">
                                        <?= htmlspecialchars($row['status_distribusi']) ?>
                                    </span>
                                </td>
                            </tr>
                    <?php endwhile; } else { ?>
                        <tr><td colspan="5" class="text-center">Tidak ada riwayat penyaluran.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Penyaluran donasi berhasil dilakukan.',
    confirmButtonColor: '#fd7e14' // Optional: Match SweetAlert button to your theme
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>