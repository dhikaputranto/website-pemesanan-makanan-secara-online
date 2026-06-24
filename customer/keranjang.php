<?php
session_start();
include '../koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/* ================= HAPUS MULTIPLE ================= */

if(isset($_POST['hapus_produk'])){

    if(isset($_POST['pilih'])){

        foreach($_POST['pilih'] as $id_keranjang){

            mysqli_query($koneksi,"
            DELETE FROM keranjang
            WHERE id_keranjang='$id_keranjang'
            AND id_user='$id_user'
            ");

        }

    }

}

/* ================= DATA KERANJANG ================= */

$query = mysqli_query($koneksi,"
SELECT * FROM keranjang
JOIN produk ON keranjang.id_produk = produk.id_produk
WHERE keranjang.id_user='$id_user'
AND keranjang.status='belum'
ORDER BY keranjang.id_keranjang DESC
");

$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Keranjang</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<link rel="stylesheet" href="../assets/css/keranjang.css">

</head>

<body>

<!-- HEADER -->

<div class="header">

    <a href="index.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h3>Keranjang</h3>

    <button class="btn-edit" id="btnEdit">
        Ubah
    </button>

</div>

<!-- CONTENT -->

<form method="POST" id="formKeranjang">

<div class="container">

<?php if(mysqli_num_rows($query) > 0){ ?>

    <?php while($data = mysqli_fetch_assoc($query)){

        $total += $data['subtotal'];

    ?>

    <div class="cart-item">

        <div class="cart-top">

            <!-- CHECKBOX -->

            <input type="checkbox"
            name="pilih[]"
            value="<?= $data['id_keranjang']; ?>"
            class="check-item">

            <!-- IMAGE -->

            <img src="../uploads/<?= $data['gambar']; ?>"
            class="cart-image">

            <!-- INFO -->

            <div class="cart-info">

                <h4>
                    <?= $data['nama_produk']; ?>
                </h4>

                <p>
                    <?= substr($data['deskripsi'],0,45); ?>...
                </p>

                <div class="price">
                    Rp <?= number_format($data['subtotal']); ?>
                </div>

                <!-- BOTTOM -->

                <div class="qty-box">

    <a href="update_qty.php?id=<?= $data['id_keranjang']; ?>&aksi=kurang"
    class="qty-btn">
        -
    </a>

    <span>
        <?= $data['qty']; ?>
    </span>

    <a href="update_qty.php?id=<?= $data['id_keranjang']; ?>&aksi=tambah"
    class="qty-btn">
        +
    </a>

</div>

                </div>

            </div>

        </div>

    </div>

    <?php } ?>

<?php } else { ?>

    <!-- EMPTY -->

    <div class="empty-cart">

        <i class="fa-solid fa-cart-shopping"></i>

        <span>
            Keranjang Kamu Kosong
        </span>

    </div>

<?php } ?>

</div>

<!-- FOOTER -->

<div class="footer-checkout">

    <div class="total">

        Total Dipilih:
        <span>
            Rp <?= number_format($total); ?>
        </span>

    </div>

    <button type="button"
    class="btn-checkout"
    id="actionButton">

        Checkout

    </button>

</div>

</form>

<script>

const btnEdit = document.getElementById('btnEdit');
const actionButton = document.getElementById('actionButton');
const formKeranjang = document.getElementById('formKeranjang');

let editMode = false;

/* SEMBUNYIKAN CHECKBOX AWAL */

document.querySelectorAll('.check-item').forEach((item)=>{
    item.style.display = 'none';
});

/* BUTTON UBAH */

btnEdit.addEventListener('click', function(){

    editMode = !editMode;

    if(editMode){

        btnEdit.innerHTML = 'Selesai';

        actionButton.innerHTML = 'Hapus';

        actionButton.classList.add('btn-delete-mode');

        document.querySelectorAll('.check-item').forEach((item)=>{
            item.style.display = 'block';
        });

    }else{

        btnEdit.innerHTML = 'Ubah';

        actionButton.innerHTML = 'Checkout';

        actionButton.classList.remove('btn-delete-mode');

        document.querySelectorAll('.check-item').forEach((item)=>{
            item.style.display = 'none';
            item.checked = false;
        });

    }

});

/* BUTTON BAWAH */

actionButton.addEventListener('click', function(){

    if(editMode){

        let checked =
        document.querySelectorAll('.check-item:checked');

        if(checked.length < 1){

            alert('Pilih produk yang ingin dihapus');

            return;

        }

        let konfirmasi =
        confirm('Yakin ingin menghapus produk yang dipilih?');

        if(konfirmasi){

            let input =
            document.createElement('input');

            input.type = 'hidden';
            input.name = 'hapus_produk';
            input.value = '1';

            formKeranjang.appendChild(input);

            formKeranjang.submit();

        }

    }else{

        window.location = 'checkout.php';

    }

});

</script>

</body>
</html>