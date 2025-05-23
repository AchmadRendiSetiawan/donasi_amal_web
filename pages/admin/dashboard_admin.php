<?php
session_start();
include '../../config.php'; // Sesuaikan path jika berbeda

// Pastikan hanya admin yang login yang bisa mengakses halaman ini
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../login.php"); // Sesuaikan path ke halaman login Anda
    exit();
}

// Handle alert messages
$alert_message = '';
$alert_type = '';

if (isset($_SESSION['alert_message'])) {
    $alert_message = $_SESSION['alert_message'];
    $alert_type = $_SESSION['alert_type'];
    unset($_SESSION['alert_message']);
    unset($_SESSION['alert_type']);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Program & Donatur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-orange: #fd7e14;
            --darker-orange: #e66a00;
            --light-gray: #f8f9fa;
            --dark-text: #212529;
            --light-border: #e9ecef;
        }

        body {
            background-color: var(--light-gray);
            color: var(--dark-text);
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .btn-orange {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-orange:hover {
            background-color: var(--darker-orange);
            border-color: var(--darker-orange);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: var(--light-border);
            color: var(--dark-text);
            font-weight: 600;
        }

        .badge-status {
            font-weight: bold;
            padding: 0.5em 0.75em;
            border-radius: 0.375rem; /* Bootstrap's default rounded */
            display: inline-block; /* Ensures padding works */
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            color: var(--dark-text);
            border: 1px solid transparent;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            margin-right: 0.25rem;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary-orange);
            background-color: white;
            border-color: var(--light-border) var(--light-border) white;
            font-weight: bold;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            border-color: var(--light-border) var(--light-border) var(--light-border);
            background-color: #f0f2f5;
        }

        .modal-content {
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 2rem 1rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 2rem 1.5rem;
        }

        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
        }

        .hero-section {
            background: linear-gradient(45deg, #fce5d4, #ffedd5); /* Lighter, warm gradient */
            color: var(--dark-text);
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .hero-section h1 {
            color: var(--dark-text);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hero-section p {
            font-size: 1.15rem;
            opacity: 0.9;
        }

        /* Specific overrides for button colors for consistency */
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: var(--dark-text);
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: var(--dark-text);
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        /* Style for close button in danger modal header */
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="hero-section">
        <h1><i class="fas fa-hand-holding-heart"></i> Dashboard Admin</h1>
        <p class="lead">Kelola Program Amal dan Data Donatur dengan Mudah</p>
    </div>

    <?php if ($alert_message): ?>
        <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
            <?= $alert_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="crudTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="program-tab" data-bs-toggle="tab" data-bs-target="#program" type="button" role="tab" aria-controls="program" aria-selected="true"><i class="fas fa-hands-helping"></i> Program Amal</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="donatur-tab" data-bs-toggle="tab" data-bs-target="#donatur" type="button" role="tab" aria-controls="donatur" aria-selected="false"><i class="fas fa-users"></i> Donatur</button>
        </li>
        <li class="nav-item ms-auto"> <a class="nav-link text-danger" href="../../login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>

    <div class="tab-content" id="crudTabsContent">
        <div class="tab-pane fade show active" id="program" role="tabpanel" aria-labelledby="program-tab">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0">Daftar Program Amal</h5>
                    <button type="button" class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#tambahProgramModal">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Program
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Program</th>
                                    <th>Target Donasi</th>
                                    <th>Total Terkumpul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM program_amal ORDER BY status = 'Aktif' DESC, id_program DESC");
                            if ($result->num_rows > 0):
                                while ($row = $result->fetch_assoc()):
                                    $status = $row['status'] ?? 'Tidak Diketahui';
                                    $status_badge = match($status) {
                                        'Aktif' => 'bg-success',
                                        'Selesai' => 'bg-info text-dark',
                                        default => 'bg-secondary' // Default for 'Tidak Aktif' or unknown
                                    };
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_program']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_program']) ?></td>
                                    <td>Rp <?= number_format($row['target_donasi'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($row['total_terkumpul'], 0, ',', '.') ?></td>
                                    <td><span class="badge <?= $status_badge ?> badge-status"><?= htmlspecialchars($status) ?></span></td>
                                    <td class="d-flex flex-column flex-md-row gap-2">
                                        <button class="btn btn-sm btn-warning flex-fill" data-bs-toggle="modal" data-bs-target="#editProgram<?= $row['id_program'] ?>">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger flex-fill delete-btn"
                                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                data-id="<?= $row['id_program'] ?>"
                                                data-name="<?= htmlspecialchars($row['nama_program']) ?>"
                                                data-type="program"
                                                data-action="proses_admin/proses_hapus_program.php">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editProgram<?= $row['id_program'] ?>" tabindex="-1" aria-labelledby="editProgramLabel<?= $row['id_program'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="proses_admin/proses_edit_program.php">
                                            <input type="hidden" name="id_program" value="<?= $row['id_program'] ?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editProgramLabel<?= $row['id_program'] ?>">Edit Program</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" name="nama_program" class="form-control" id="nama_program<?= $row['id_program'] ?>" value="<?= htmlspecialchars($row['nama_program']) ?>" placeholder="Nama Program" required>
                                                        <label for="nama_program<?= $row['id_program'] ?>">Nama Program</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <textarea name="deskripsi" class="form-control" id="deskripsi<?= $row['id_program'] ?>" rows="3" placeholder="Deskripsi Program" style="height: 100px;" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                        <label for="deskripsi<?= $row['id_program'] ?>">Deskripsi</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="number" name="target_donasi" class="form-control" id="target_donasi<?= $row['id_program'] ?>" value="<?= $row['target_donasi'] ?>" step="0.01" min="0.01" placeholder="Target Donasi" required>
                                                        <label for="target_donasi<?= $row['id_program'] ?>">Target Donasi</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <select name="status" class="form-select" id="status_program<?= $row['id_program'] ?>" required>
                                                            <option value="Aktif" <?= $status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                                            <option value="Tidak Aktif" <?= $status == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                                            <option value="Selesai" <?= $status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                                        </select>
                                                        <label for="status_program<?= $row['id_program'] ?>">Status</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-orange">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                            else:
                                echo '<tr><td colspan="6" class="text-center py-4"><i class="fas fa-info-circle me-2"></i>Belum ada data program amal.</td></tr>';
                            endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="donatur" role="tabpanel" aria-labelledby="donatur-tab">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0">Daftar Donatur</h5>
                    <button type="button" class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#tambahDonaturModal">
                        <i class="fas fa-user-plus me-1"></i> Tambah Donatur
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Donatur</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $result_donatur = $conn->query("SELECT * FROM donatur ORDER BY id_donatur DESC");
                            if ($result_donatur->num_rows > 0):
                                while ($row_donatur = $result_donatur->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row_donatur['id_donatur']) ?></td>
                                    <td><?= htmlspecialchars($row_donatur['nama']) ?></td>
                                    <td><?= htmlspecialchars($row_donatur['email']) ?></td>
                                    <td><?= htmlspecialchars($row_donatur['no_hp']) ?></td>
                                    <td><?= htmlspecialchars($row_donatur['alamat'] ?? '-') ?></td>
                                    <td class="d-flex flex-column flex-md-row gap-2">
                                        <button class="btn btn-sm btn-warning flex-fill" data-bs-toggle="modal" data-bs-target="#editDonatur<?= $row_donatur['id_donatur'] ?>">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger flex-fill delete-btn"
                                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                data-id="<?= $row_donatur['id_donatur'] ?>"
                                                data-name="<?= htmlspecialchars($row_donatur['nama']) ?>"
                                                data-type="donatur"
                                                data-action="proses_admin/proses_hapus_donatur.php">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editDonatur<?= $row_donatur['id_donatur'] ?>" tabindex="-1" aria-labelledby="editDonaturLabel<?= $row_donatur['id_donatur'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="proses_admin/proses_edit_donatur.php">
                                            <input type="hidden" name="id_donatur" value="<?= $row_donatur['id_donatur'] ?>">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editDonaturLabel<?= $row_donatur['id_donatur'] ?>">Edit Donatur</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" name="nama" class="form-control" id="nama_donatur<?= $row_donatur['id_donatur'] ?>" value="<?= htmlspecialchars($row_donatur['nama']) ?>" placeholder="Nama Donatur" required>
                                                        <label for="nama_donatur<?= $row_donatur['id_donatur'] ?>">Nama Donatur</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="email" name="email" class="form-control" id="email_donatur<?= $row_donatur['id_donatur'] ?>" value="<?= htmlspecialchars($row_donatur['email']) ?>" placeholder="Email Donatur" required>
                                                        <label for="email_donatur<?= $row_donatur['id_donatur'] ?>">Email</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="text" name="no_hp" class="form-control" id="no_hp_donatur<?= $row_donatur['id_donatur'] ?>" value="<?= htmlspecialchars($row_donatur['no_hp']) ?>" placeholder="Nomor HP">
                                                        <label for="no_hp_donatur<?= $row_donatur['id_donatur'] ?>">No HP</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <textarea name="alamat" class="form-control" id="alamat_donatur<?= $row_donatur['id_donatur'] ?>" rows="3" placeholder="Alamat Donatur" style="height: 100px;"><?= htmlspecialchars($row_donatur['alamat'] ?? '') ?></textarea>
                                                        <label for="alamat_donatur<?= $row_donatur['id_donatur'] ?>">Alamat</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-orange">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php
                                endwhile;
                            else:
                                echo '<tr><td colspan="6" class="text-center py-4"><i class="fas fa-info-circle me-2"></i>Belum ada data donatur.</td></tr>';
                            endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahProgramModal" tabindex="-1" aria-labelledby="tambahProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="../proses/proses_tambah_program.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahProgramModalLabel">Tambah Program Amal Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nama_program" class="form-control" id="nama_program_new" placeholder="Nama Program" required>
                            <label for="nama_program_new">Nama Program</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea name="deskripsi" class="form-control" id="deskripsi_new" rows="3" placeholder="Deskripsi Program" style="height: 100px;" required></textarea>
                            <label for="deskripsi_new">Deskripsi</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" name="target_donasi" class="form-control" id="target_donasi_new" step="0.01" min="0.01" placeholder="Target Donasi" required>
                            <label for="target_donasi_new">Target Donasi</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select name="status" class="form-select" id="status_new_program" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <label for="status_new_program">Status</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-orange">Simpan Program</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="tambahDonaturModal" tabindex="-1" aria-labelledby="tambahDonaturModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="../proses/proses_tambah_donatur.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahDonaturModalLabel">Tambah Donatur Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="nama" class="form-control" id="nama_donatur_new" placeholder="Nama Donatur" required>
                            <label for="nama_donatur_new">Nama Donatur</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="email_donatur_new" placeholder="Email Donatur" required>
                            <label for="email_donatur_new">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="no_hp" class="form-control" id="no_hp_donatur_new" placeholder="Nomor HP">
                            <label for="no_hp_donatur_new">No HP</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea name="alamat" class="form-control" id="alamat_donatur_new" rows="3" placeholder="Alamat Donatur" style="height: 100px;"></textarea>
                            <label for="alamat_donatur_new">Alamat</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-orange">Simpan Donatur</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus <strong id="deleteItemType"></strong> "<strong id="deleteItemName"></strong>"?
                    <p class="text-danger mt-2"><i class="fas fa-info-circle me-1"></i> Perhatian: Tindakan ini tidak dapat dibatalkan!</p>
                    <div id="additionalWarning" class="alert alert-warning mt-3" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="">
                        <input type="hidden" name="id_program" id="deleteIdProgram">
                        <input type="hidden" name="id_donatur" id="deleteIdDonatur">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-1"></i> Hapus Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    if (confirmDeleteModal) { // Ensure the modal exists before adding listener
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            // Extract info from data-* attributes
            var itemId = button.getAttribute('data-id');
            var itemName = button.getAttribute('data-name');
            var itemType = button.getAttribute('data-type'); // 'program' or 'donatur'
            var formAction = button.getAttribute('data-action');

            // Update the modal's content.
            var modalBodyType = confirmDeleteModal.querySelector('#deleteItemType');
            var modalBodyName = confirmDeleteModal.querySelector('#deleteItemName');
            var additionalWarning = confirmDeleteModal.querySelector('#additionalWarning');
            var deleteForm = confirmDeleteModal.querySelector('#deleteForm');
            var deleteIdProgram = confirmDeleteModal.querySelector('#deleteIdProgram');
            var deleteIdDonatur = confirmDeleteModal.querySelector('#deleteIdDonatur');

            // Set the item name
            modalBodyName.textContent = itemName;

            // Set the form action
            deleteForm.setAttribute('action', formAction);

            // Reset hidden IDs and set the correct one
            deleteIdProgram.value = ''; // Always clear both first
            deleteIdDonatur.value = '';

            if (itemType === 'program') {
                modalBodyType.textContent = 'program';
                deleteIdProgram.value = itemId; // Set program ID
                additionalWarning.textContent = 'Semua donasi yang terkait dengan program ini juga akan terhapus.';
                additionalWarning.style.display = 'block';
            } else if (itemType === 'donatur') {
                modalBodyType.textContent = 'donatur';
                deleteIdDonatur.value = itemId; // Set donatur ID
                additionalWarning.textContent = 'Semua donasi yang terkait dengan donatur ini juga akan dihapus.';
                additionalWarning.style.display = 'block';
            } else {
                modalBodyType.textContent = 'item ini'; // Fallback
                additionalWarning.style.display = 'none'; // Hide warning if type is unknown
            }
        });
    }
});
</script>
</body>
</html>
<?php
$conn->close();
?>