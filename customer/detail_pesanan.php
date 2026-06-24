<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
}

$id_transaksi = $_GET['id'];

$transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi
WHERE id_transaksi='$id_transaksi'");

$data = mysqli_fetch_assoc($transaksi);

$detail = mysqli_query($koneksi, "SELECT * FROM detail_transaksi
JOIN produk ON detail_transaksi.id_produk = produk.id_produk
WHERE detail_transaksi.id_transaksi='$id_transaksi'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/detail_pesanan.css">

</head>
<body>

<!-- NAVBAR -->

<nav class="navbar-custom">

    <div class="container navbar-flex">

        <a href="index.php" class="logo">
            🍔 Dim's Outlet
        </a>

        <a href="riwayat.php" class="btn-kembali">
            ← Kembali
        </a>

    </div>

</nav>

<!-- CONTENT -->

<div class="container py-5">

    <!-- HEADER -->

    <div class="header-detail">

        <h2>Detail Pesanan</h2>

        <p>
            Informasi lengkap pesanan makanan kamu
        </p>

    </div>

    <!-- CARD -->

    <div class="detail-card">

        <!-- TOP -->

        <div class="top-detail">

            <div>

                <small>Nomor Pesanan</small>

                <h3>
                    #<?= $data['nomor_pesanan']; ?>
                </h3>

            </div>

            <div>

                <?php if($data['status_pembayaran'] == 'paid') { ?>

                    <span class="status-paid">
                        Paid
                    </span>

                <?php } else { ?>

                    <span class="status-unpaid">
                        Unpaid
                    </span>

                <?php } ?>

            </div>

        </div>

        <!-- INFO -->

        <div class="info-box">

            <div class="info-item">

                <span>Metode Pembayaran</span>

                <strong>
                    <?= $data['metode_pembayaran']; ?>
                </strong>

            </div>

            <div class="info-item">

                <span>Status Pesanan</span>

                <strong class="text-orange">
                    <?= ucfirst($data['status_pesanan']); ?>
                </strong>

            </div>

            <div class="info-item">

                <span>Total Pembayaran</span>

                <strong>
                    Rp <?= number_format($data['total_harga']); ?>
                </strong>

            </div>

        </div>

        <!-- PRODUK -->

        <div class="produk-title">

            <h4>Daftar Pesanan</h4>

        </div>

        <?php while($p = mysqli_fetch_assoc($detail)) { ?>

        <div class="produk-item">

            <div class="produk-left">

                <img src="../uploads/<?= $p['gambar']; ?>">

                <div>

                    <h5>
                        <?= $p['nama_produk']; ?>
                    </h5>

                    <small>
                        Qty : <?= $p['qty']; ?>
                    </small>

                </div>

            </div>

            <div class="produk-right">

                Rp <?= number_format($p['subtotal']); ?>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

</body>
</html>