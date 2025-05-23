<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Donasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles for white background and black text */
        body {
            background-color: #f8f9fa; /* A very light grey, almost white */
            color: #212529; /* Dark charcoal, almost black */
        }

        /* Styling for the orange buttons (e.g., "Tampilkan") */
        .btn-primary {
            background-color: #fd7e14; /* A vibrant orange */
            border-color: #fd7e14; /* Match the background color */
        }
        .btn-primary:hover {
            background-color: #e66a00; /* Slightly darker orange on hover */
            border-color: #e66a00;
        }

        /* Card header styling to match image */
        .card-header.bg-orange { /* Custom class for orange header */
            background-color: #fd7e14 !important; /* Force orange */
            color: #ffffff !important; /* White text for contrast */
            font-weight: bold;
        }
        .card-header.bg-yellow { /* Custom class for yellow header */
            background-color: #ffc107 !important; /* Bootstrap's default warning yellow */
            color: #212529 !important; /* Black text for contrast as seen in image */
            font-weight: bold;
        }

        /* Ensures card content and table text remain black */
        .card, .table {
            color: #212529; /* Dark charcoal */
        }

        /* Table header background for better visibility */
        .table-light th {
            background-color: #e9ecef; /* Light grey for table headers */
        }

        /* Progress bar color adjustments */
        /* Top Donatur progress bar (orange) */
        .progress-bar.bg-orange-main { /* Custom class for orange progress bar */
            background-color: #fd7e14 !important;
        }

        /* Program Belum Mencapai Target progress bar (based on their actual Bootstrap classes) */
        .progress-bar.bg-danger {
            background-color: #dc3545 !important;
        }
        .progress-bar.bg-warning {
            background-color: #ffc107 !important;
        }
        .progress-bar.bg-info {
            background-color: #0dcaf0 !important;
        }

        /* Badge colors (ensuring readability on light backgrounds) */
        .badge.bg-danger {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important; /* Black text for better readability on yellow */
        }
        .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #212529 !important; /* Black text for better readability on light blue */
        }

        /* Ensure input fields maintain black text */
        .form-control {
            color: #212529;
            background-color: #ffffff;
            border-color: #ced4da;
        }
        .form-control:focus {
            border-color: #fd7e14; /* Orange border on focus */
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25); /* Orange shadow on focus */
        }

        /* Specific styling for percentage text in progress bar */
        .progress-bar {
            color: #ffffff; /* Default text color for progress bar */
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Laporan Donasi</h2>
        
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-orange"> <h4 class="mb-0">🏆 Top Donatur</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Total Donasi</th>
                                <th>Persentase Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Calculate total donasi keseluruhan for percentage
                            $total_donasi_query = $conn->query("SELECT SUM(total_donasi) AS total_all FROM top_donatur");
                            if (!$total_donasi_query) {
                                die("Query error: " . $conn->error);
                            }
                            $total_donasi_row = $total_donasi_query->fetch_assoc();
                            $total_donasi = $total_donasi_row['total_all'] ?? 0; // Use null coalescing for safety

                            // Get top donatur data
                            $query_donatur = $conn->query("SELECT * FROM top_donatur ORDER BY total_donasi DESC LIMIT 10");
                            if (!$query_donatur) {
                                die("Query error: " . $conn->error);
                            }

                            $no = 1;
                            if ($query_donatur->num_rows > 0) {
                                while ($row = $query_donatur->fetch_assoc()):
                                    // Calculate percentage contribution
                                    $percentage = ($total_donasi > 0 && $row['total_donasi'] > 0)
                                        ? ($row['total_donasi'] / $total_donasi) * 100
                                        : 0;
                            ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td>Rp <?= number_format($row['total_donasi'], 0, ',', '.') ?></td>
                                        <td>
                                            <div class="progress" style="height: 25px; background-color: #e9ecef;"> <div class="progress-bar bg-orange-main" role="progressbar" style="width: <?= number_format($percentage, 2) ?>%" aria-valuenow="<?= number_format($percentage, 2) ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= number_format($percentage, 2) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                            <?php endwhile;
                            } else {
                                echo '<tr><td colspan="4" class="text-center">Tidak ada data top donatur.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-5">
            <div class="card-header bg-yellow"> <h4 class="mb-0">⚠️ Program Belum Mencapai Target</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Program</th>
                                <th>Target</th>
                                <th>Terkumpul</th>
                                <th>Sisa Target</th>
                                <th>Status & Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get programs that have not reached target
                            // Assuming 'program_belum_target' is a view or a table that correctly filters these programs
                            $query_program = $conn->query("SELECT * FROM program_belum_target");
                            if (!$query_program) {
                                die("Query error: " . $conn->error);
                            }

                            if ($query_program->num_rows == 0) {
                                echo '<tr><td colspan="5" class="text-center">Tidak ada program yang belum mencapai target.</td></tr>';
                            } else {
                                while ($row = $query_program->fetch_assoc()):
                                    // Calculate remaining target
                                    $sisa = $row['target_donasi'] - $row['total_terkumpul'];

                                    // Calculate achievement percentage
                                    $percentage = ($row['target_donasi'] > 0)
                                        ? ($row['total_terkumpul'] / $row['target_donasi']) * 100
                                        : 0;

                                    // Determine badge and progress bar class based on percentage
                                    $badge_class = '';
                                    $status_text = '';
                                    $progress_bar_class = ''; // This will control the progress bar color

                                    if ($percentage < 30) {
                                        $badge_class = 'bg-danger';
                                        $status_text = 'Rendah';
                                        $progress_bar_class = 'bg-danger';
                                    } elseif ($percentage < 70) {
                                        $badge_class = 'bg-warning text-dark';
                                        $status_text = 'Sedang';
                                        $progress_bar_class = 'bg-warning';
                                    } else {
                                        $badge_class = 'bg-info text-dark';
                                        $status_text = 'Mendekati';
                                        $progress_bar_class = 'bg-info';
                                    }
                            ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama_program']) ?></td>
                                        <td>Rp <?= number_format($row['target_donasi'], 0, ',', '.') ?></td>
                                        <td>Rp <?= number_format($row['total_terkumpul'], 0, ',', '.') ?></td>
                                        <td>Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= number_format($percentage, 2) ?>% (<?= $status_text ?>)
                                            </span>
                                            <div class="progress mt-1" style="height: 15px;">
                                                <div class="progress-bar <?= $progress_bar_class ?>" role="progressbar" style="width: <?= number_format($percentage, 2) ?>%" aria-valuenow="<?= number_format($percentage, 2) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                            <?php endwhile;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
            </div>
        <h2 class="mt-5">Laporan Donasi Harian</h2>

        <form method="GET" action="" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="date" name="tgl_mulai" class="form-control" required value="<?= htmlspecialchars($_GET['tgl_mulai'] ?? '') ?>">
                </div>
                <div class="col-md-5">
                    <input type="date" name="tgl_akhir" class="form-control" required value="<?= htmlspecialchars($_GET['tgl_akhir'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>

        <?php if (isset($_GET['tgl_mulai']) && isset($_GET['tgl_akhir'])):
            $tgl_mulai = $_GET['tgl_mulai'];
            $tgl_akhir = $_GET['tgl_akhir'];

            try {
                // Generate laporan donasi
                $stmt = $conn->prepare("CALL generate_laporan(?, ?)");
                $stmt->bind_param("ss", $tgl_mulai, $tgl_akhir);
                $stmt->execute();
                $result = $stmt->get_result();

                // IMPORTANT: Free the first result set before calling the next stored procedure
                $stmt->free_result();
                // If the stored procedure returns multiple result sets, you might need next_result() as well
                while ($conn->more_results() && $conn->next_result()) {
                    // Consume any additional result sets
                    if ($res = $conn->store_result()) {
                        $res->free();
                    }
                }


                // Update status semua program
                // You might need to re-prepare this statement if it's causing issues after cleaning up results
                // Or ensure your update_status_program stored procedure doesn't return any result sets.
                $stmt_update_all = $conn->prepare("CALL update_status_program(?)");
                // Check if the previous statement left any lingering results from a potential previous call to generate_laporan
                // This is crucial for avoiding "commands out of sync" if generate_laporan is indeed the culprit.
                // It's good practice to ensure all result sets are fetched or freed before a new query.
                if ($conn->more_results()) {
                    while ($conn->next_result()) {
                        if ($res = $conn->store_result()) {
                            $res->free();
                        }
                    }
                }

                $result_program_ids = $conn->query("SELECT id_program FROM program_amal");

                if ($result_program_ids) {
                    while ($row = $result_program_ids->fetch_assoc()) {
                        $stmt_update_all->bind_param("i", $row['id_program']);
                        $stmt_update_all->execute();
                        // If update_status_program also returns results, free them here too.
                        // However, a typical update procedure shouldn't return result sets.
                        if ($stmt_update_all->more_results()) {
                            while ($stmt_update_all->next_result()) {
                                if ($res_update = $stmt_update_all->store_result()) {

                                } // Free the result set if any
                                $stmt_update_all->free_result();
                            }
                        }
                    }
                    $result_program_ids->free(); // Free the result set from SELECT id_program
                } else {
                    error_log("Error fetching program IDs: " . $conn->error);
                }

                if ($stmt_update_all) {
                    $stmt_update_all->close();
                }

            } catch (Exception $e) {
                echo '<div class="alert alert-danger mt-4">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                $result = false; // Indicate that no results were retrieved
            }
        ?>
            <h4>Laporan Donasi dari **<?= htmlspecialchars($tgl_mulai) ?>** hingga **<?= htmlspecialchars($tgl_akhir) ?>**</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Total Donasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($result) && $result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_program']) ?></td>
                                <td>Rp <?= number_format($row['total_donasi'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center">Tidak ada data donasi untuk periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>