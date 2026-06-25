<?php
session_start();
include '../koneksi.php';

/* CEK LOGIN ADMIN */

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

/* TOTAL PRODUK */

$q_produk = mysqli_query($koneksi,"
SELECT * FROM produk
");

$total_produk = mysqli_num_rows($q_produk);

/* TOTAL CUSTOMER */

$q_customer = mysqli_query($koneksi,"
SELECT * FROM users
WHERE role='customer'
");

$total_customer = mysqli_num_rows($q_customer);

/* TOTAL PENDAPATAN */

$q_pendapatan = mysqli_query($koneksi,"
SELECT SUM(total_harga) AS total
FROM riwayat_pesanan
WHERE payment_status='paid'
");

$data_pendapatan = mysqli_fetch_assoc($q_pendapatan);

$total_pendapatan = $data_pendapatan['total'] ?? 0;

/* PESANAN TERBARU */

$q_latest = mysqli_query($koneksi,"
SELECT *
FROM riwayat_pesanan
ORDER BY id_riwayat DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }

        .wrapper{
            display: flex;
        }

        /* SIDEBAR */

        .sidebar{
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg,#ff8c00,#ff6600);
            padding: 30px 20px;
        }

        .logo{
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .sidebar ul{
            list-style: none;
            padding: 0;
        }

        .sidebar ul li{
            margin-bottom: 12px;
        }

        .sidebar ul li a{
            display: block;
            color: white;
            text-decoration: none;
            padding: 14px 18px;
            border-radius: 12px;
            transition: 0.3s;
            font-size: 15px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active{
            background: rgba(255,255,255,0.2);
        }

        /* CONTENT */

        .content{
            flex: 1;
            padding: 30px;
        }

        .header{
            margin-bottom: 30px;
        }

        .header h2{
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header p{
            color: #666;
            font-size: 14px;
        }

        /* CARD */

        .card-dashboard{
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .card-dashboard small{
            color: #777;
            font-size: 14px;
        }

        .card-dashboard h3{
            margin-top: 10px;
            font-size: 28px;
            font-weight: 700;
            color: #ff6600;
        }

        /* TABLE */

        .table-box{
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .table-box h4{
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .table th{
            font-size: 14px;
            color: #666;
        }

        .table td{
            font-size: 14px;
            font-weight: 500;
        }

        /* STATUS */

        .paid{
            background: #28a745;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
        }

        .unpaid{
            background: #dc3545;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* RESPONSIVE */

        @media(max-width:991px){

            .wrapper{
                flex-direction: column;
            }

            .sidebar{
                width: 100%;
                min-height: auto;
            }

            .content{
                padding: 20px;
            }

        }

    </style>

</head>
<body>
<div class="wrapper">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">🍔 Dim's Admin</div>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="produk.php">Produk</a></li>
            <li><a href="pesanan_admin.php">Pesanan</a></li>
            <li><a href="users.php">Staff</a></li>
            <li><a href="users_customer.php">Customer</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    <!-- CONTENT -->
    <div class="content">
        <div class="header">
            <h2>Dashboard Admin</h2>
            <p>Selamat datang di panel admin Dim's Outlet</p>
        </div>
        <!-- CARD -->
        <div class="row">

    <div class="col-lg-4 col-md-6 mb-4">

        <div class="card-dashboard">

            <small>Total Produk</small>

            <h3>
                <?= $total_produk; ?>
            </h3>

        </div>

    </div>

    <div class="col-lg-4 col-md-6 mb-4">

        <div class="card-dashboard">

            <small>Total Customer</small>

            <h3>
                <?= $total_customer; ?>
            </h3>

        </div>

    </div>

    <div class="col-lg-4 col-md-6 mb-4">

        <div class="card-dashboard">

            <small>Total Pendapatan</small>

            <h3>
                Rp <?= number_format($total_pendapatan); ?>
            </h3>

        </div>

    </div>

</div>

        <!-- TABLE -->

        <div class="table-box">

            <h4>
                Pesanan Terbaru
            </h4>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>No Pesanan</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($d = mysqli_fetch_assoc($q_latest)) { ?>

                        <tr>

                            <td>
                                #<?= $d['nomor_pesanan']; ?>
                            </td>

                            <td>
                                Rp <?= number_format($d['total_harga']); ?>
                            </td>

                            <td>
                                <?= $d['metode_pembayaran']; ?>
                            </td>

                            <td>

                                <?php if($d['payment_status'] == 'paid'){ ?>

                                    <span class="paid">
                                        Paid
                                    </span>

                                <?php } else { ?>

                                    <span class="unpaid">
                                        Unpaid
                                    </span>

                                <?php } ?>

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