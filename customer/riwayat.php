<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi,"
SELECT *
FROM riwayat_pesanan
WHERE id_user='$id_user'
ORDER BY id_riwayat DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Pesanan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/riwayat.css">

<style>

body{
    background:#f5f6fa;
    font-family:'Poppins',sans-serif;
}

.navbar-custom{
    background:#ff6600;
    padding:15px 0;
}

.navbar-flex{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    color:white;
    font-size:24px;
    font-weight:700;
    text-decoration:none;
}

.btn-kembali{
    background:white;
    color:#ff6600;
    text-decoration:none;
    padding:10px 18px;
    border-radius:10px;
    font-weight:600;
}

.header-section{
    padding:40px 0;
    text-align:center;
}

.header-content h1{
    font-weight:700;
    color:#222;
}

.header-content p{
    color:#777;
}

.history-card{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    height:100%;
}

.top-history{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.top-history h3{
    font-size:20px;
    margin:0;
    font-weight:700;
}

.status-paid{
    background:#d1fae5;
    color:#059669;
    padding:8px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.status-unpaid{
    background:#fee2e2;
    color:#dc2626;
    padding:8px 15px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.history-body{
    margin-bottom:20px;
}

.detail-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:12px;
}

.text-orange{
    color:#ff6600;
}

.btn-detail{
    display:block;
    text-align:center;
    background:#ff6600;
    color:white;
    text-decoration:none;
    padding:12px;
    border-radius:12px;
    font-weight:600;
}

.btn-detail:hover{
    background:#e65c00;
    color:white;
}

.empty-box{
    text-align:center;
    padding:80px 20px;
}

.empty-box img{
    width:180px;
    margin-bottom:20px;
}

.btn-belanja{
    display:inline-block;
    margin-top:15px;
    background:#ff6600;
    color:white;
    text-decoration:none;
    padding:12px 25px;
    border-radius:12px;
    font-weight:600;
}

</style>

</head>
<body>

<nav class="navbar-custom">

    <div class="container navbar-flex">

        <a href="index.php" class="logo">
            🍔 Dim's Outlet
        </a>

        <a href="index.php" class="btn-kembali">
            ← Kembali
        </a>

    </div>

</nav>

<section class="header-section">

    <div class="container">

        <div class="header-content">

            <h1>Riwayat Pesanan</h1>

            <p>
                Semua pesanan yang telah selesai akan ditampilkan di halaman ini.
            </p>

        </div>

    </div>

</section>

<section class="content-section">

    <div class="container">

        <?php if(mysqli_num_rows($query) > 0){ ?>

        <div class="row">

            <?php while($data = mysqli_fetch_assoc($query)){ ?>

            <div class="col-lg-6 mb-4">

                <div class="history-card">

                    <div class="top-history">

                        <div>

                            <small>Nomor Pesanan</small>

                            <h3>
                                #<?= $data['nomor_pesanan']; ?>
                            </h3>

                        </div>

                        <div>

                            <?php if($data['payment_status'] == 'paid'){ ?>

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

                    <div class="history-body">

                        <div class="detail-item">

                            <span>Total Pembayaran</span>

                            <strong>
                                Rp <?= number_format($data['total_harga']); ?>
                            </strong>

                        </div>

                        <div class="detail-item">

                            <span>Metode Pembayaran</span>

                            <strong>
                                <?= strtoupper($data['metode_pembayaran']); ?>
                            </strong>

                        </div>

                        <div class="detail-item">

                            <span>Status Pesanan</span>

                            <strong class="text-success">
                                Selesai
                            </strong>

                        </div>

                        <div class="detail-item">

                            <span>Tanggal</span>

                            <strong>
                                <?= date('d M Y H:i', strtotime($data['created_at'])); ?>
                            </strong>

                        </div>

                    </div>

                    <a href="detail_riwayat.php?id=<?= $data['id_riwayat']; ?>"
                    class="btn-detail">
                        Lihat Detail Pesanan
                    </a>

                </div>

            </div>

            <?php } ?>

        </div>

        <?php } else { ?>

        <div class="empty-box">

            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png">

            <h2>
                Belum Ada Riwayat Pesanan
            </h2>

            <p>
                Pesanan yang sudah selesai akan muncul di sini.
            </p>

            <a href="index.php" class="btn-belanja">
                Pesan Sekarang
            </a>

        </div>

        <?php } ?>

    </div>

</section>

</body>
</html>