
<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
}

/* NOTIFIKASI */
$notif = false;

if(isset($_GET['success'])){
    $notif = true;
}

$query_produk = mysqli_query($koneksi, "
SELECT * FROM produk
ORDER BY id_produk DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Customer - Dim's Outlet</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="../assets/css/customer.css">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- ICON -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

</head>

<body>

<!-- TOAST NOTIFICATION -->

<div class="toast-container position-fixed top-0 end-0 p-4">

    <div id="liveToast"
    class="toast custom-toast border-0 shadow-lg"
    role="alert">

        <div class="toast-body">

            <div class="toast-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div class="toast-content">
                <h5>Berhasil</h5>
                <p>Produk berhasil ditambahkan ke keranjang</p>
            </div>

        </div>

    </div>

</div>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark">

    <div class="container">

        <a class="navbar-brand" href="#">
            🍔 Dim's Outlet
        </a>

        <button class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse"
        id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link active" href="">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="keranjang.php">
                        Keranjang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="status_pesanan.php">
                        Status pesanan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="riwayat.php">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item ms-lg-3">

                    <div class="user-box">
                        <?= $_SESSION['nama_lengkap']; ?>
                    </div>

                </li>

                <li class="nav-item ms-lg-2">

                    <a href="../logout.php"
                    class="btn-logout">
                        Logout
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- HERO -->

<section class="hero-section">

    <div class="container">

        <div class="hero-content">

            <h1>
                Pesan Makanan Favoritmu 🍕
            </h1>

            <p>
                Nikmati makanan lezat dengan pembayaran QRIS dan Tunai secara mudah dan cepat.
            </p>

        </div>

    </div>

</section>

<!-- PRODUK -->

<section class="produk-section">

    <div class="container">

        <div class="section-title">

            <h2>Daftar Menu</h2>

        </div>

        <div class="row">

            <?php while($produk = mysqli_fetch_assoc($query_produk)) { ?>

            <div class="col-lg-3 col-md-6 mb-4">

                <div class="produk-card">

                    <img src="../uploads/<?= $produk['gambar']; ?>"
                    class="produk-image">

                    <div class="produk-body">

                        <h4>
                            <?= $produk['nama_produk']; ?>
                        </h4>

                        <p>
                            <?= substr($produk['deskripsi'],0,60); ?>...
                        </p>

                        <div class="produk-footer">

                            <h5>
                                Rp <?= number_format($produk['harga']); ?>
                            </h5>

                            <a href="tambah_keranjang.php?id=<?= $produk['id_produk']; ?>"
                            class="btn-cart">
                                + Keranjang
                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <?php } ?>

        </div>

    </div>

</section>

<!-- BOOTSTRAP JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if($notif == true){ ?>

<script>

const toastLiveExample =
document.getElementById('liveToast');

const toastBootstrap =
bootstrap.Toast.getOrCreateInstance(toastLiveExample);

toastBootstrap.show();

</script>

<?php } ?>

</body>
</html>
