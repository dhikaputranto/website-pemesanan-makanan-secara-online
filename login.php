<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM users 
    WHERE email='$email' 
    AND password='$password'");

    $cek = mysqli_num_rows($query);

    if($cek > 0){

        $data = mysqli_fetch_assoc($query);

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'admin'){
            header("Location: admin/");
        }

        elseif($data['role'] == 'kasir'){
            header("Location: kasir/");
        }

        elseif($data['role'] == 'dapur'){
            header("Location: dapur/");
        }

        else{
            header("Location: customer/");
        }

    }else{
        $error = "Email atau Password salah!";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dim's Outlet</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/login.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body>

    <div class="login-container">

        <div class="login-card">

            <div class="login-left">

                <h2>🍔 Dim's Outlet</h2>

                <h1>Selamat Datang</h1>

                <p>
                    Login untuk memesan makanan favoritmu dengan cepat dan mudah.
                </p>

                <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=1200&auto=format&fit=crop">

            </div>

            <div class="login-right">

                <h3>Login Akun</h3>

                <?php if(isset($error)) { ?>
                    <div class="alert alert-danger">
                        <?= $error; ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="mb-3">
                        <label>Email</label>

                        <input type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        required>
                    </div>

                    <div class="mb-4">
                        <label>Password</label>

                        <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required>
                    </div>

                    <button type="submit"
                    name="login"
                    class="btn-login">
                        Login
                    </button>

                </form>

                <div class="register-link">

                    Belum punya akun?

                    <a href="register.php">
                        Registrasi
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>
</html>