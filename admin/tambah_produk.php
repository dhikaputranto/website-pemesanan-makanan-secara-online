<?php
session_start();
include '../koneksi.php';

/* CEK LOGIN */

if(!isset($_SESSION['id_user'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

/* TAMBAH PRODUK */

if(isset($_POST['simpan'])){

    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    /* GAMBAR */

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    move_uploaded_file($tmp, "../uploads/".$gambar);

    /* INSERT DATABASE */

    mysqli_query($koneksi, "INSERT INTO produk VALUES(
        NULL,
        '$nama_produk',
        '$kategori',
        '$harga',
        '$stok',
        '$deskripsi',
        '$gambar'
    )");

    echo "
    <script>
        alert('Produk berhasil ditambahkan!');
        window.location='produk.php';
    </script>
    ";

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/tambah_produk.css">

</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <div class="logo">
            🍔 Dim's Admin
        </div>

        <ul>

            <li>
                <a href="index.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="produk.php" class="active">
                    Produk
                </a>
            </li>

            <li>
                <a href="pesanan.php">
                    Pesanan
                </a>
            </li>

            <li>
                <a href="users.php">
                    Users
                </a>
            </li>

            <li>
                <a href="laporan.php">
                    Laporan
                </a>
            </li>

            <li>
                <a href="../logout.php">
                    Logout
                </a>
            </li>

        </ul>

    </div>

    <!-- CONTENT -->

    <div class="content">

        <!-- HEADER -->

        <div class="header">

            <div>

                <h2>
                    Tambah Produk
                </h2>

                <p>
                    Tambahkan menu makanan atau minuman baru
                </p>

            </div>

            <a href="produk.php" class="btn-kembali">
                ← Kembali
            </a>

        </div>

        <!-- FORM -->

        <div class="form-box">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">

                    <label>Nama Produk</label>

                    <input type="text"
                    name="nama_produk"
                    class="form-control"
                    required>

                </div>

                <div class="mb-3">

                    <label>Kategori</label>

                    <select name="kategori"
                    class="form-control"
                    required>

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        <option value="Makanan">
                            Makanan
                        </option>

                        <option value="Minuman">
                            Minuman
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label>Harga</label>

                    <input type="number"
                    name="harga"
                    class="form-control"
                    required>

                </div>

                <div class="mb-3">

                    <label>Stok</label>

                    <input type="number"
                    name="stok"
                    class="form-control"
                    required>

                </div>

                <div class="mb-3">

                    <label>Deskripsi</label>

                    <textarea name="deskripsi"
                    class="form-control"
                    rows="5"
                    required></textarea>

                </div>

                <div class="mb-4">

                    <label>Upload Gambar</label>

                    <input type="file"
                    name="gambar"
                    class="form-control"
                    required>

                </div>

                <button type="submit"
                name="simpan"
                class="btn-simpan">

                    Simpan Produk

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>