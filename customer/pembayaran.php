<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

/* =========================
   AMBIL ID TRANSAKSI
========================= */

if(!isset($_GET['id'])){

    die("ID transaksi tidak ditemukan");

}

$id_transaksi = $_GET['id'];

/* =========================
   QUERY TRANSAKSI
========================= */

$query = mysqli_query($koneksi,"
SELECT * FROM transaksi
WHERE id_transaksi='$id_transaksi'
");

$data = mysqli_fetch_assoc($query);

/* =========================
   JIKA DATA TIDAK ADA
========================= */

if(!$data){

    die("Data transaksi tidak ditemukan");

}

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pembayaran</title>

<!-- Bootstrap -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- Font -->

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f6fa;
    font-family:'Poppins',sans-serif;
}

/* CARD */

.payment-card{
    max-width:700px;
    margin:auto;
    background:white;
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

/* HEADER */

.header-payment{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.header-payment h2{
    font-size:32px;
    font-weight:700;
    color:#222;
}

.btn-kembali{
    background:linear-gradient(135deg,#ff8c00,#ff6600);
    color:white;
    text-decoration:none;
    padding:11px 18px;
    border-radius:12px;
    font-weight:600;
    font-size:14px;
}

.btn-kembali:hover{
    color:white;
}

/* NOMOR */

.nomor-box{
    background:#fff4e6;
    padding:18px 20px;
    border-radius:16px;
    margin-bottom:25px;
    font-size:16px;
    font-weight:500;
}

.nomor-box span{
    color:#ff6600;
    font-weight:700;
}

/* TOTAL */

.total-box{
    background:#fff8f0;
    padding:30px;
    border-radius:20px;
    text-align:center;
    margin-bottom:25px;
}

.total-box h1{
    font-size:42px;
    color:#ff6600;
    font-weight:800;
    margin-bottom:5px;
}

.total-box p{
    color:#666;
    margin:0;
}

/* METODE */

.metode-box{
    background:#f9f9f9;
    padding:18px 20px;
    border-radius:16px;
    margin-bottom:25px;
    font-size:15px;
}

.metode-box strong{
    color:#ff6600;
}

/* QRIS */

.qris-box{
    text-align:center;
    margin-bottom:30px;
}

.qris-box img{
    width:260px;
    max-width:100%;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
    margin-bottom:15px;
}

.qris-box p{
    color:#666;
    margin:0;
}

/* STATUS */

.status-box{
    background:#f8f8f8;
    padding:18px;
    border-radius:16px;
    text-align:center;
    font-weight:600;
    margin-bottom:25px;
}

.paid{
    color:#16a34a;
}

.unpaid{
    color:#dc2626;
}

/* BUTTON */

.btn-bayar{
    width:100%;
    display:block;
    text-align:center;
    border:none;
    background:linear-gradient(135deg,#ff8c00,#ff6600);
    color:white;
    padding:16px;
    border-radius:14px;
    font-size:17px;
    font-weight:700;
    text-decoration:none;
    transition:0.3s;
}

.btn-bayar:hover{
    color:white;
    transform:translateY(-2px);
}

/* MOBILE */

@media(max-width:768px){

    .payment-card{
        padding:25px;
    }

    .header-payment{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .header-payment h2{
        font-size:26px;
    }

    .total-box h1{
        font-size:34px;
    }

}

</style>

</head>
<body>

<div class="container py-5">

    <div class="payment-card">

        <!-- HEADER -->

        <div class="header-payment">

            <h2>Pembayaran</h2>

            <a href="index.php"
            class="btn-kembali">

                ← Kembali

            </a>

        </div>

        <!-- NOMOR -->

        <div class="nomor-box">

            Nomor Pesanan :
            <span><?= $data['nomor_pesanan']; ?></span>

        </div>

        <!-- TOTAL -->

        <div class="total-box">

            <h1>
                Rp <?= number_format($data['total_harga']); ?>
            </h1>

            <p>
                Total pembayaran pesanan kamu
            </p>

        </div>

        <!-- METODE -->

        <div class="metode-box">

            Metode Pembayaran :

            <strong>
                <?= strtoupper($data['metode_pembayaran']); ?>
            </strong>

        </div>

        <!-- QRIS -->

        <?php if($data['metode_pembayaran'] == 'qris'){ ?>

        <div class="qris-box">

            <img src="../assets/img/qris.jpg">

            <p>
                Silahkan scan QRIS untuk melakukan pembayaran
            </p>

        </div>

        <?php } ?>

        <!-- STATUS -->

        <div class="status-box">

            Status Pembayaran :

            <?php if($data['payment_status'] == 'paid'){ ?>

                <span class="paid">
                    Paid
                </span>

            <?php } else { ?>

                <span class="unpaid">
                    Unpaid
                </span>

            <?php } ?>

        </div>

        <!-- TOMBOL LANJUT -->

        <?php if($data['payment_status'] == 'paid'){ ?>

        <a href="status_pesanan.php?id=<?= $id_transaksi; ?>"
        class="btn-bayar">

            Lanjut

        </a>

        <?php } ?>

    </div>

</div>

</body>
</html>