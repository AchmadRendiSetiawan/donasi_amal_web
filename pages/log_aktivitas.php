<?php include '../config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Aktivitas Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles for white background and black text */
        body {
            background-color: #f8f9fa; /* A very light grey, almost white */
            color: #212529; /* Dark charcoal, almost black */
        }

        /* Ensures table text is black */
        .table {
            color: #212529; /* Dark charcoal */
        }

        /* Table header background for orange and white theme */
        .table thead th {
            background-color: #FF8C00; /* A vibrant orange */
            color: #ffffff; /* White text for header */
            border-bottom: 2px solid #E67E00; /* Slightly darker orange border */
        }

        /* Table body rows for white background */
        .table tbody tr {
            background-color: #ffffff; /* White background for all rows */
        }

        /* Striped table rows for readability - still white but can be slightly off-white if desired */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fcfcfc; /* A very subtle off-white for odd rows to maintain stripe effect */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Log Aktivitas Sistem</h2>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tabel</th>
                <th>Aksi</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ensure config.php is included for database connection
            // The user's original code had this outside the PHP block,
            // but it's good practice to keep it within.
            // If config.php is already included at the very top of the file
            // as per the user's initial input, this line might be redundant
            // but is kept here for self-containment of the PHP logic.
            // include '../config.php'; // This line is already at the top of the file based on user's input.

            // Check if $conn is defined from config.php
            if (isset($conn)) {
                $query = $conn->query("SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 20");
                while ($row = $query->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['waktu']) ?></td>
                    <td><?= htmlspecialchars($row['tabel']) ?></td>
                    <td><?= htmlspecialchars($row['aksi']) ?></td>
                    <td><?= htmlspecialchars($row['detail']) ?></td>
                </tr>
            <?php
                endwhile;
            } else {
                echo "<tr><td colspan='4' class='text-center text-danger'>Error: Database connection not established. Please check config.php.</td></tr>";
            }
            ?>
        </tbody>
    </table>
<div class="d-flex justify-content-between align-items-center mt-4">
    <a href="../index.php" class="btn btn-outline-success">Kembali ke Beranda</a>
</div>
</div>
</body>
</html>
