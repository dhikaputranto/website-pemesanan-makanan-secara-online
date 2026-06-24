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

/* TOTAL PESANAN */
$total_pesanan = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT COUNT(*) as total FROM transaksi
"));

/* TOTAL PENDAPATAN */
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT SUM(total_harga) as total FROM transaksi
WHERE payment_status='paid'
"));

/* PESANAN HARI INI */
$hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT COUNT(*) as total FROM transaksi
WHERE DATE(waktu_pesanan)=CURDATE()
"));

/* DATA PESANAN */
$query = mysqli_query($koneksi,"
SELECT transaksi.*, users.nama_lengkap
FROM transaksi
JOIN users ON transaksi.id_user = users.id_user
ORDER BY id_transaksi DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Kasir - Dim's Outlet</title>

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
            <a href="index.php" class="active">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="pesanan.php">
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
                Dashboard Kasir
            </h2>

            <p>
                Selamat datang <?= $_SESSION['nama_lengkap']; ?>
            </p>

        </div>

    </div>

    <!-- CARD -->

    <div class="row">

        <div class="col-lg-4 mb-4">

            <div class="card-box">

                <div class="card-icon orange">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <div>

                    <h3>
                        <?= $total_pesanan['total']; ?>
                    </h3>

                    <p>Total Pesanan</p>

                </div>

            </div>

        </div>

        <div class="col-lg-4 mb-4">

            <div class="card-box">

                <div class="card-icon green">
                    <i class="fa-solid fa-wallet"></i>
                </div>

                <div>

                    <h3>
                        Rp <?= number_format($total_pendapatan['total'] ?? 0); ?>
                    </h3>

                    <p>Total Pendapatan</p>

                </div>

            </div>

        </div>

        <div class="col-lg-4 mb-4">

            <div class="card-box">

                <div class="card-icon blue">
                    <i class="fa-solid fa-calendar"></i>
                </div>

                <div>

                    <h3>
                        <?= $hari_ini['total']; ?>
                    </h3>

                    <p>Pesanan Hari Ini</p>

                </div>

            </div>

        </div>

    </div>

    <!-- TABLE -->

    <div class="table-box">

        <div class="table-header">

            <h4>
                Pesanan Terbaru
            </h4>

        </div>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php
                $no=1;
                while($data = mysqli_fetch_assoc($query)){
                ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <?= $data['nomor_pesanan']; ?>
                        </td>

                        <td>
                            <?= $data['nama_lengkap']; ?>
                        </td>

                        <td>
                            Rp <?= number_format($data['total_harga']); ?>
                        </td>

                        <td>

                            <span class="badge bg-success">
                                <?= $data['payment_status']; ?>
                            </span>

                        </td>

                        <td>

                            <span class="badge bg-warning text-dark">
                                <?= $data['status_pesanan']; ?>
                            </span>

                        </td>

                        <td>

                            <a href="detail_pesanan.php?id=<?= $data['id_transaksi']; ?>"
                            class="btn-detail">

                                Detail

                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>

</body>
</html>