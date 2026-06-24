<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'kasir'){
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi,"
SELECT transaksi.*, users.nama_lengkap, users.email
FROM transaksi
JOIN users ON transaksi.id_user = users.id_user
WHERE transaksi.id_transaksi='$id'
");

$data = mysqli_fetch_assoc($query);

/* DETAIL PRODUK */

$detail = mysqli_query($koneksi,"
SELECT detail_transaksi.*, produk.nama_produk, produk.gambar
FROM detail_transaksi
JOIN produk ON detail_transaksi.id_produk = produk.id_produk
WHERE detail_transaksi.id_transaksi='$id'
");

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detail Pesanan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/kasir.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        💳 Kasir Panel
    </div>

    <ul>

        <li>
            <a href="index.php">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="pesanan.php" class="active">
                <i class="fa-solid fa-bag-shopping"></i>
                Pesanan
            </a>
        </li>

        <li>
            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>
        </li>

    </ul>

</div>

<!-- CONTENT -->

<div class="content">

    <div class="topbar">

        <div>

            <h2>
                Detail Pesanan
            </h2>

            <p>
                Informasi lengkap pesanan customer
            </p>

        </div>

    </div>

    <!-- CARD INFO -->

    <div class="detail-card">

        <div class="row">

            <div class="col-lg-6">

                <div class="info-box">

                    <h5>
                        Informasi Customer
                    </h5>

                    <div class="info-item">

                        <span>Nama</span>

                        <strong>
                            <?= $data['nama_lengkap']; ?>
                        </strong>

                    </div>

                    <div class="info-item">

                        <span>Email</span>

                        <strong>
                            <?= $data['email']; ?>
                        </strong>

                    </div>

                    <div class="info-item">

                        <span>No Pesanan</span>

                        <strong>
                            <?= $data['nomor_pesanan']; ?>
                        </strong>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="info-box">

                    <h5>
                        Informasi Pembayaran
                    </h5>

                    <div class="info-item">

                        <span>Metode</span>

                        <strong>
                            <?= strtoupper($data['metode_pembayaran']); ?>
                        </strong>

                    </div>

                    <div class="info-item">

                        <span>Status Bayar</span>

                        <strong class="text-success">
                            <?= strtoupper($data['payment_status']); ?>
                        </strong>

                    </div>

                    <div class="info-item">

                        <span>Total</span>

                        <strong class="harga">
                            Rp <?= number_format($data['total_harga']); ?>
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- PRODUK -->

    <div class="table-box mt-4">

        <div class="table-header">

            <h4>
                Produk Pesanan
            </h4>

        </div>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Produk</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>

                    </tr>

                </thead>

                <tbody>

                <?php while($item = mysqli_fetch_assoc($detail)){ ?>

                    <tr>

                        <td>

                            <img src="../uploads/<?= $item['gambar']; ?>"
                            class="img-detail">

                        </td>

                        <td>

                            <?= $item['nama_produk']; ?>

                        </td>

                        <td>

                            Rp <?= number_format($item['harga']); ?>

                        </td>

                        <td>

                            <?= $item['qty']; ?>

                        </td>

                        <td>

                            <strong>
                                Rp <?= number_format($item['subtotal']); ?>
                            </strong>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- BUTTON -->

    <div class="button-area">

        <a href="pesanan.php"
        class="btn-kembali">

            <i class="fa-solid fa-arrow-left"></i>
            Kembali

        </a>

        <button onclick="window.print()"
        class="btn-print">

            <i class="fa-solid fa-print"></i>
            Cetak Struk

        </button>

    </div>

</div>

</div>

</body>
</html>