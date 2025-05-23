<?php
// Aktifkan debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sertakan koneksi database
include 'config.php';

// Hitung statistik dengan penanganan error
try {
    $total_program = $conn->query("SELECT COUNT(*) FROM program_amal WHERE status = 'Aktif'")->fetch_array()[0];
} catch (Exception $e) {
    $total_program = 0;
}

try {
    $total_donatur = $conn->query("SELECT COUNT(*) FROM donatur")->fetch_array()[0];
} catch (Exception $e) {
    $total_donatur = 0;
}

try {
    $total_penerima = $conn->query("SELECT COUNT(*) FROM penerima_manfaat")->fetch_array()[0];
} catch (Exception $e) {
    $total_penerima = 0;
}

try {
    $total_donasi_result = $conn->query("SELECT SUM(total_terkumpul) FROM program_amal");
    $total_donasi = ($total_donasi_result && $row = $total_donasi_result->fetch_array()) ? $row[0] : 0;
} catch (Exception $e) {
    $total_donasi = 0;
}
?>
<!DOCTYPE html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Donasi Amal</title>
    <meta name="description" content="Sistem Donasi Amal untuk Bantuan Sosial">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="pages/css/bootstrap.min.css">
    <link rel="stylesheet" href="pages/css/owl.carousel.min.css">
    <link rel="stylesheet" href="pages/css/magnific-popup.css">
    <link rel="stylesheet" href="pages/css/font-awesome.min.css">
    <link rel="stylesheet" href="pages/css/themify-icons.css">
    <link rel="stylesheet" href="pages/css/nice-select.css">
    <link rel="stylesheet" href="pages/css/flaticon.css">
    <link rel="stylesheet" href="pages/css/gijgo.css">
    <link rel="stylesheet" href="pages/css/animate.css">
    <link rel="stylesheet" href="pages/css/slicknav.css">
    <link rel="stylesheet" href="pages/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ">
    <style>
        .navbar {
            padding: 1rem 0;
        }
        .navbar-brand img {
            margin-right: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .nav-link {
            color: #333 !important;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #198754 !important;
        }
        .slider_image {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; /* Ensure the container takes full height */
        }
        .slider_image img {
            max-height: 400px; /* Adjust as needed */
            object-fit: contain;
        }
    </style>
</head>
<body>

<header>
    <div class="header-area">
        <div class="header-top_area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <div class="short_contact_list">
                            <ul>
                                <li><a href="#">+62 812-2813-2556</a></li>
                                <li><a href="#">Senin - Minggu 09:00 - 18:00</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-8 text-center text-lg-end">
                        <div class="social_media_links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="pages/img/logo atas.png" alt="Logo Anipat" width="100" height="40" class="d-inline-block align-text-top">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/donatur.php">Donatur</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/program.php">Program</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/donasi.php">Donasi</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/laporan.php">Laporan</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/penyaluran.php">Penyaluran</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/penerima.php">Penerima</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/log_aktivitas.php">Log Aktivitas</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">LoginAdmin</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>

<div class="slider_area">
    <div class="single_slider slider_bg_1 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="slider_text">
                        <h3>Bantu Sesama<br><span>Donasi Amal</span></h3>
                        <p>Donasi untuk program amal yang sedang berjalan.</p>
                        <a href="pages/donasi.php" class="boxed-btn4">Donasi Sekarang</a>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1 col-md-6 d-none d-md-block">
                    <div class="slider_image">
                        <img src="pages/img/banner/orang_donasi.png" alt="Tangan Memberi Donasi" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="service_area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10">
                <div class="section_title text-center mb-95">
                    <h3>Program Amal Aktif</h3>
                    <p>Daftar program amal yang sedang berjalan.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php
            try {
                $query = $conn->query("SELECT * FROM program_amal WHERE status = 'Aktif'");
                if (!$query) throw new Exception("Query error: " . $conn->error);

                if ($query->num_rows == 0) {
                    echo '<div class="col-12 text-center py-4"><em>Tidak ada program aktif saat ini.</em></div>';
                } else {
                    while ($row = $query->fetch_assoc()):
            ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="single_service">
                                <div class="service_thumb service_icon_bg_1 d-flex align-items-center justify-content-center">
                                    <div class="service_icon">
                                        <img src="<?= file_exists('img/service/service_icon_1.png') ? 'img/service/service_icon_1.png' : 'https://via.placeholder.com/80?text=Icon ' ?>" alt="">
                                    </div>
                                </div>
                                <div class="service_content text-center">
                                    <h3><?= htmlspecialchars($row['nama_program']) ?></h3>
                                    <p><?= substr(htmlspecialchars($row['deskripsi']), 0, 100) ?>...</p>
                                    <a href="pages/program.php?id=<?= $row['id_program'] ?>" class="boxed-btn3">Detail</a>
                                </div>
                            </div>
                        </div>
            <?php
                    endwhile;
                }
            } catch (Exception $e) {
                echo '<div class="col-12 text-center py-4 alert alert-danger">' . $e->getMessage() . '</div>';
            }
            ?>
        </div>
    </div>
</div>

<div class="pet_care_area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-6">
                <div class="donasi_terkumpul">
                    <img src="<?= file_exists('pages/img/logo donasi.png') ? 'pages/img/logo donasi.png' : 'https://via.placeholder.com/300x200?text=Orang+Donasi ' ?>" alt="Gambar Donasi">
                </div>
            </div>
            <?php
            // Pastikan $total_donasi memiliki nilai default jika kosong
            $total_donasi = isset($total_donasi) && !is_null($total_donasi) ? $total_donasi : 0;
            ?>
            <div class="col-lg-6 offset-lg-1 col-md-6">
                <div class="pet_info">
                    <div class="section_title">
                        <h3><span>Donasi Terkumpul</span><br>Rp <?= number_format($total_donasi, 0, ',', '.') ?></h3>
                        <p>Total donasi yang berhasil terkumpul hingga saat ini.</p>
                        <a href="pages/laporan.php" class="boxed-btn3">Lihat Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adapt_area">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-lg-5">
                <div class="adapt_help">
                    <div class="section_title">
                        <h3><span>Kami butuh</span><br>bantuan Anda</h3>
                        <p>Donasi Anda bisa menyelamatkan banyak orang. Ayo bantu sesama!</p>
                        <a href="pages/donasi.php" class="boxed-btn3">Donasi</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="adapt_about">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="single_adapt text-center">
                                <div class="adapt_content">
                                    <h3 class="counter"><?= $total_program ?></h3>
                                    <p>Program Aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="single_adapt text-center">
                                <div class="adapt_content">
                                    <h3><span class="counter"><?= $total_donatur ?></span>+</h3>
                                    <p>Donatur</p>
                                </div>
                            </div>
                            <div class="single_adapt text-center">
                                <div class="adapt_content">
                                    <h3><span class="counter"><?= $total_penerima ?></span>+</h3>
                                    <p>Penerima Manfaat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="testmonial_area">
    <div class="container">
        <h2 class="text-center mb-5">Riwayat Donasi <span class="text-success">Donatur</span></h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="textmonial_active owl-carousel">
                    <?php
                    try {
                        // Using a simple view `riwayat_donasi_per_donatur` for demonstration.
                        // In a real application, you might join `donasi` and `donatur` tables.
                        // Assume riwayat_donasi_per_donatur has columns: nama_donatur, jumlah_donasi, nama_program
                        $query_testi = $conn->query("SELECT * FROM riwayat_donasi_per_donatur LIMIT 3");
                        if (!$query_testi) throw new Exception("Query error: " . $conn->error);

                        if ($query_testi->num_rows == 0) {
                            echo "<div class='text-center py-5'><em>Tidak ada riwayat donasi</em></div>";
                        } else {
                            while ($row = $query_testi->fetch_assoc()):
                    ?>
                                <div class="item">
                                    <div class="card bg-white shadow-sm mx-auto" style="max-width: 500px;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="<?= file_exists('pages/img/team/1.png') ? 'pages/img/team/1.png' : 'https://via.placeholder.com/60?text=' . substr(htmlspecialchars($row['nama_donatur']), 0, 1) ?>"
                                                        alt="Profil Donatur"
                                                        class="rounded-circle me-3"
                                                        style="width: 60px; height: 60px;">
                                                <div>
                                                    <h5><?= htmlspecialchars($row['nama_donatur']) ?></h5>
                                                    <small class="text-muted">Donatur</small>
                                                </div>
                                            </div>
                                            <p class="mb-0 mt-3">
                                                <i class="fas fa-donate text-success me-2"></i>
                                                Donasi Rp <?= number_format($row['jumlah_donasi'], 0, ',', '.') ?>
                                                untuk program "<strong><?= htmlspecialchars($row['nama_program']) ?></strong>".
                                            </p>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            endwhile;
                        }
                    } catch (Exception $e) {
                        echo "<div class='col-12 text-center py-4 alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="team_area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="section_title text-center mb-95">
                    <h3>Tim Kami</h3>
                    <p>Tim pengelola sistem donasi amal yang bertanggung jawab.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php
            try {
                // Fetching from 'donatur' for 'Tim Kami' as an example, adjust if you have a separate 'team' table
                $query_team = $conn->query("SELECT * FROM donatur ORDER BY id_donatur ASC LIMIT 3");
                while ($row = $query_team->fetch_assoc()):
            ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="single_team">
                            <div class="thumb">
                                <img src="<?= file_exists('pages/img/team/1.png') ? 'pages/img/team/1.png' : 'https://via.placeholder.com/200?text=Tim ' ?>" alt="Profil Tim">
                            </div>
                            <div class="member_name text-center">
                                <div class="mamber_inner">
                                    <h4><?= htmlspecialchars($row['nama']) ?></h4>
                                    <p>Anggota Tim</p>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                endwhile;
            } catch (Exception $e) {
                echo "<div class='col-12 text-center py-4 alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">Kontak Kami</h3>
                        <ul class="address_line">
                            <li>+62 812-2813-2556</li>
                            <li><a href="#">info@donasi-amal.com</a></li>
                            <li>Jl. Lamongan No. 30, jawatimur</li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">Program Kami</h3>
                        <ul class="links">
                            <li><a href="pages/program.php">Program Amal</a></li>
                            <li><a href="pages/laporan.php">Laporan Donasi</a></li>
                            <li><a href="pages/donatur.php">Data Donatur</a></li>
                            <li><a href="pages/penyaluran.php">Penyaluran Bantuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">Tentang Kami</h3>
                        <ul class="links">
                            <li><a href="pages/donasi.php">Donasi</a></li>
                            <li><a href="pages/laporan.php">Laporan</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <div class="footer_logo">
                            <a href="#"><img src="pages/img/logo bawah.png" alt="Logo"></a>
                        </div>
                        <p class="address_text">Jl. Lamongan No. 30, Jawatimur</p>
                        <div class="social_media_links">
                            <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copy-right_text">
        <div class="container">
            <div class="bordered_1px"></div>
            <div class="row">
                <div class="col-xl-12">
                    <p class="copy_right text-center">
                        &copy; 2025 Donasi Amal • Dibuat dengan rasa kasih sayang dan tertekan
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="pages/js/vendor/jquery-1.12.4.min.js"></script>
<script src="pages/js/popper.min.js"></script>
<script src="pages/js/bootstrap.min.js"></script>
<script src="pages/js/owl.carousel.min.js"></script>
<script src="pages/js/isotope.pkgd.min.js"></script>
<script src="pages/js/imagesloaded.pkgd.min.js"></script>
<script src="pages/js/wow.min.js"></script>
<script src="pages/js/nice-select.min.js"></script>
<script src="pages/js/jquery.slicknav.min.js"></script>
<script src="pages/js/jquery.magnific-popup.min.js"></script>
<script src="pages/js/plugins.js"></script>
<script src="pages/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        dots: true,
        nav: false,
        responsive: {
            768: { items: 2 },
            992: { items: 3 }
        }
    });
});
</script>

</body>
</html>