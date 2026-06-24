<?php
session_start();
include 'koneksi.php';

if(isset($_POST['register'])){

    $nama_lengkap      = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email             = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password          = md5($_POST['password']);
    $konfirmasi        = md5($_POST['konfirmasi_password']);

    // Cek email
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($cek_email) > 0){

        $error = "Email sudah digunakan!";

    }elseif($password != $konfirmasi){

        $error = "Konfirmasi password tidak cocok!";

    }else{

        $insert = mysqli_query($koneksi, "INSERT INTO users(
            nama_lengkap,
            email,
            password,
            role
        ) VALUES (
            '$nama_lengkap',
            '$email',
            '$password',
            'customer'
        )");

        if($insert){

            echo "
            <script>
                alert('Registrasi berhasil!');
                window.location='login.php';
            </script>
            ";

        }else{

            $error = "Registrasi gagal!";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dim's Outlet</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/register.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>

    <div class="register-container">

        <div class="register-card">

            <!-- LEFT -->

            <div class="register-left">

                <h2>🍔 Dim's Outlet</h2>

                <h1>Buat Akun Baru</h1>

                <p>
                    Daftar sekarang dan nikmati pengalaman memesan makanan dengan cepat dan modern.
                </p>

                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1200&auto=format&fit=crop">

            </div>

            <!-- RIGHT -->

            <div class="register-right">

                <h3>Registrasi</h3>

                <?php if(isset($error)) { ?>
                    <div class="alert alert-danger">
                        <?= $error; ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="mb-3">

                        <label>Nama Lengkap</label>

                        <input type="text"
                        name="nama_lengkap"
                        class="form-control"
                        placeholder="Masukkan nama lengkap"
                        required>

                    </div>

                    <div class="mb-3">

                        <label>Email</label>

                        <input type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        required>

                    </div>

                    <div class="mb-3">

                        <label>Password</label>

                        <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required>

                    </div>

                    <div class="mb-4">

                        <label>Konfirmasi Password</label>

                        <input type="password"
                        name="konfirmasi_password"
                        class="form-control"
                        placeholder="Masukkan ulang password"
                        required>

                    </div>

                    <button type="submit"
                    name="register"
                    class="btn-register">
                        Registrasi
                    </button>

                </form>

                <div class="login-link">

                    Sudah punya akun?

                    <a href="login.php">
                        Login
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>
</html>