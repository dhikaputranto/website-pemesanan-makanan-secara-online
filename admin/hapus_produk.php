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

/* CEK ID */
if(isset($_GET['id'])){

    $id = (int) $_GET['id'];

    /* AMBIL DATA GAMBAR */
    $get = mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id_produk='$id'");
    $data = mysqli_fetch_assoc($get);

    if($data){

        /* HAPUS GAMBAR DARI FOLDER */
        if(!empty($data['gambar']) && file_exists("../uploads/".$data['gambar'])){
            unlink("../uploads/".$data['gambar']);
        }

        /* HAPUS DATA PRODUK */
        $delete = mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");

        if($delete){
            echo "<script>
                alert('Produk berhasil dihapus!');
                window.location='produk.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus produk!');
                window.location='produk.php';
            </script>";
        }

    } else {
        echo "<script>
            alert('Produk tidak ditemukan!');
            window.location='produk.php';
        </script>";
    }

} else {
    echo "<script>
        alert('ID tidak valid!');
        window.location='produk.php';
    </script>";
}
?>