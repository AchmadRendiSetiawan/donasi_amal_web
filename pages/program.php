<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Program Amal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles for white background and black text */
        body {
            background-color: #f8f9fa; /* A very light grey, almost white */
            color: #212529; /* Dark charcoal, almost black */
        }

        /* Styling for the orange buttons */
        .btn-primary {
            background-color: #fd7e14; /* A vibrant orange */
            border-color: #fd7e14; /* Match the background color */
        }
        .btn-primary:hover {
            background-color: #e66a00; /* Slightly darker orange on hover */
            border-color: #e66a00;
        }

        /* Targets Bootstrap's default success button style (used for "Simpan") */
        .btn-success {
            background-color: #fd7e14; /* A vibrant orange */
            border-color: #fd7e14; /* Match the background color */
        }
        .btn-success:hover {
            background-color: #e66a00; /* Slightly darker orange on hover */
            border-color: #e66a00;
        }

        /* Ensures modal content has a white background and black text */
        .modal-content {
            background-color: #ffffff; /* Pure white */
            color: #212529; /* Dark charcoal */
        }

        /* Ensures table text is black */
        .table {
            color: #212529; /* Dark charcoal */
        }

        /* Table header background for better visibility */
        .table thead th {
            background-color: #e9ecef; /* Light grey for table headers */
            border-bottom: 2px solid #dee2e6; /* Add a subtle border for separation */
        }

        /* Input field styling */
        .form-control, .form-select {
            color: #212529; /* Dark charcoal for input text */
            background-color: #ffffff; /* White background for input fields */
            border-color: #ced4da; /* Default Bootstrap border color */
        }
        .form-control:focus, .form-select:focus {
            border-color: #fd7e14; /* Orange border on focus */
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25); /* Orange shadow on focus */
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
        <h2>Daftar Program Amal</h2>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahProgram">Tambah Program</a>
    </div>

        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Program</th>
                    <th>Target Donasi</th>
                    <th>Total Terkumpul</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $conn->query("SELECT * FROM program_amal");
                while ($row = $query->fetch_assoc()) {
                    // Check if 'status' key exists before accessing it
                    $status_display = isset($row['status']) ? htmlspecialchars($row['status']) : 'N/A';

                    echo "<tr>
                                <td>" . htmlspecialchars($row['nama_program']) . "</td>
                                <td>Rp " . number_format($row['target_donasi'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($row['total_terkumpul'], 0, ',', '.') . "</td>
                                <td>" . $status_display . "</td>
                            </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="tambahProgram" tabindex="-1" aria-labelledby="tambahProgramLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="proses/proses_tambah_program.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahProgramLabel">Tambah Program Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_program" class="form-label">Nama Program</label>
                            <input type="text" name="nama_program" id="nama_program" class="form-control" placeholder="Nama Program" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" placeholder="Deskripsi"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="target_donasi" class="form-label">Target Donasi</label>
                            <input type="number" name="target_donasi" id="target_donasi" class="form-control" placeholder="Target Donasi" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>