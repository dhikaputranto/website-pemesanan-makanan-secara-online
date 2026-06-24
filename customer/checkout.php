<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* =========================
   DATA KERANJANG
========================= */

$query = mysqli_query($koneksi,"
SELECT * FROM keranjang
JOIN produk 
ON keranjang.id_produk = produk.id_produk
WHERE keranjang.id_user='$id_user'
AND keranjang.status='belum'
");


$total = 0;
$items = [];


while($data = mysqli_fetch_assoc($query)){

    $items[] = $data;

    $total += $data['subtotal'];

}



/* =========================
   NOMOR PESANAN
========================= */


$ambil = mysqli_query($koneksi,"
SELECT nomor_pesanan 
FROM transaksi
ORDER BY id_transaksi DESC
LIMIT 1
");


$data_nomor = mysqli_fetch_assoc($ambil);


if($data_nomor){


    $nomor_terakhir = $data_nomor['nomor_pesanan'];


    $angka = intval($nomor_terakhir);


    $angka++;


    $nomor_pesanan = str_pad(
        $angka,
        2,
        "0",
        STR_PAD_LEFT
    );


}else{


    $nomor_pesanan = "01";


}



/* =========================
   CHECKOUT
========================= */


if(isset($_POST['checkout'])){


    $metode = strtolower($_POST['metode_pembayaran']);



    /*
    STATUS PEMBAYARAN

    TUNAI = LANGSUNG BAYAR
    QRIS = MENUNGGU KONFIRMASI
    */


    if($metode=="tunai"){

        $payment_status="paid";

    }else{

        $payment_status="unpaid";

    }




    /* =========================
       INSERT TRANSAKSI
    ========================= */


    $insert = mysqli_query($koneksi,"
    INSERT INTO transaksi
    (
        nomor_pesanan,
        id_user,
        total_harga,
        metode_pembayaran,
        payment_status,
        status_pesanan
    )
    VALUES
    (
        '$nomor_pesanan',
        '$id_user',
        '$total',
        '$metode',
        '$payment_status',
        'pending'
    )
    ");



    if(!$insert){

        die("Gagal transaksi : ".mysqli_error($koneksi));

    }




    /* ID TRANSAKSI */

    $id_transaksi = mysqli_insert_id($koneksi);




    /* =========================
       DETAIL TRANSAKSI
    ========================= */


    foreach($items as $item){


        mysqli_query($koneksi,"
        INSERT INTO detail_transaksi
        (
            id_transaksi,
            id_produk,
            qty,
            subtotal
        )
        VALUES
        (
            '$id_transaksi',
            '".$item['id_produk']."',
            '".$item['qty']."',
            '".$item['subtotal']."'
        )
        ");


    }




    /* =========================
       UPDATE KERANJANG
    ========================= */


    mysqli_query($koneksi,"
    UPDATE keranjang
    SET status='checkout'
    WHERE id_user='$id_user'
    AND status='belum'
    ");





    /* =========================
       REDIRECT
    ========================= */


    if($metode=="tunai"){


        header(
            "Location: status_pesanan.php?id=".$id_transaksi
        );


    }else{


        header(
            "Location: pembayaran.php?id=".$id_transaksi
        );


    }


    exit;


}

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

/* CONTAINER */

.container{
    padding-top:30px;
    padding-bottom:30px;
}

/* CARD */

.checkout-card{
    max-width:750px;
    margin:auto;
    background:white;
    border-radius:25px;
    padding:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    overflow:hidden;
}

/* HEADER */

.header-checkout{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    margin-bottom:30px;
}

.header-checkout h2{
    font-size:32px;
    font-weight:700;
    color:#222;
    margin-bottom:5px;
}

.header-checkout p{
    color:#777;
    font-size:14px;
    margin:0;
}

.btn-kembali{
    background:linear-gradient(135deg,#ff8c00,#ff6600);
    color:white;
    text-decoration:none;
    padding:11px 18px;
    border-radius:12px;
    font-weight:600;
    font-size:14px;
    transition:0.3s;
    white-space:nowrap;
}

.btn-kembali:hover{
    color:white;
    transform:translateY(-2px);
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

/* MENU */

.menu-list{
    width:100%;
    margin-bottom:30px;
}

.menu-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    padding:18px 0;
    border-bottom:1px solid #eee;
}

.menu-left{
    display:flex;
    align-items:center;
    gap:15px;
    flex:1;
    min-width:0;
}

/* GAMBAR */

.gambar-menu{
    width:90px !important;
    height:90px !important;
    min-width:90px !important;
    max-width:90px !important;
    object-fit:cover;
    border-radius:15px;
    flex-shrink:0;
    display:block;
    background:#f1f1f1;
}

/* INFO */

.menu-info{
    flex:1;
    min-width:0;
}

.menu-info h4{
    font-size:18px;
    font-weight:700;
    color:#222;
    margin-bottom:5px;

    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.menu-info p{
    font-size:14px;
    color:#777;
    margin:0;
}

/* HARGA */

.menu-price{
    color:#ff6600;
    font-size:18px;
    font-weight:700;
    white-space:nowrap;
}

/* TOTAL */

.total-box{
    background:#fff8f0;
    padding:25px;
    border-radius:20px;
    text-align:center;
    margin-bottom:30px;
}

.total-box h3{
    font-size:40px;
    color:#ff6600;
    font-weight:800;
    margin-bottom:5px;
}

.total-box p{
    color:#666;
    margin:0;
}

/* FORM */

.form-label{
    font-weight:600;
    margin-bottom:10px;
}

.form-select{
    height:55px;
    border-radius:14px;
    border:1px solid #ddd;
    box-shadow:none !important;
}

.form-select:focus{
    border-color:#ff8c00;
}

.btn-checkout{
    width:100%;
    border:none;
    background:linear-gradient(135deg,#ff8c00,#ff6600);
    color:white;
    padding:16px;
    border-radius:14px;
    font-size:17px;
    font-weight:700;
    transition:0.3s;
}

.btn-checkout:hover{
    transform:translateY(-2px);
}

/* MOBILE */

@media(max-width:768px){

    .checkout-card{
        padding:25px;
    }

    .header-checkout{
        flex-direction:column;
        align-items:flex-start;
    }

    .header-checkout h2{
        font-size:26px;
    }

    .menu-price{
        font-size:16px;
    }

    .total-box h3{
        font-size:32px;
    }

}

@media(max-width:480px){

    .checkout-card{
        padding:20px;
        border-radius:20px;
    }

    .gambar-menu{
        width:75px !important;
        height:75px !important;
        min-width:75px !important;
        max-width:75px !important;
    }

    .menu-info h4{
        font-size:16px;
    }

    .menu-info p{
        font-size:13px;
    }

    .menu-price{
        font-size:15px;
    }

}

</style>

</head>
<body>

<div class="container">

    <div class="checkout-card">

        <!-- HEADER -->

        <div class="header-checkout">

            <div>

                <h2>Checkout Pesanan</h2>

                <p>
                    Pastikan pesanan kamu sudah benar
                </p>

            </div>

            <a href="keranjang.php" class="btn-kembali">
                ← Kembali
            </a>

        </div>

        <!-- NOMOR -->

        <div class="nomor-box">

            Nomor Pesanan :
            <span><?= $nomor_pesanan; ?></span>

        </div>

        <!-- LIST MENU -->

        <div class="menu-list">

            <?php foreach($items as $item){ ?>

            <div class="menu-item">

                <div class="menu-left">

                    <img 
                    src="../uploads/<?= $item['gambar']; ?>" 
                    class="gambar-menu">

                    <div class="menu-info">

                        <h4>
                            <?= $item['nama_produk']; ?>
                        </h4>

                        <p>
                            Qty : <?= $item['qty']; ?>
                        </p>

                    </div>

                </div>

                <div class="menu-price">

                    Rp <?= number_format($item['subtotal']); ?>

                </div>

            </div>

            <?php } ?>

        </div>

        <!-- TOTAL -->

        <div class="total-box">

            <h3>
                Rp <?= number_format($total); ?>
            </h3>

            <p>
                Total pembayaran pesanan kamu
            </p>

        </div>

        <!-- FORM -->

        <form method="POST">

            <div class="mb-4">

                <label class="form-label">
                    Metode Pembayaran
                </label>

                <select 
                name="metode_pembayaran"
                class="form-select"
                required>

                    <option value="">
                        -- Pilih Pembayaran --
                    </option>

                    <option value="qris">
                        QRIS
                    </option>

                    <option value="tunai">
                        Tunai
                    </option>

                </select>

            </div>

            <button 
            type="submit"
            name="checkout"
            class="btn-checkout">

                Pesan Sekarang

            </button>

        </form>

    </div>

</div>

</body>
</html>