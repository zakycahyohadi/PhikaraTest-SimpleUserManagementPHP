<?php
session_start();
require_once 'db.php'; // Pastikan db.php ada dan berfungsi

if (isset($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->escapeString($_POST['username']);
    $password = $_POST['password']; // password plain text dari input user
    $security_code_input = $_POST['security_code'];
    
    if (empty($username) || empty($password) || empty($security_code_input)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Pastikan session security_code ada sebelum dibandingkan
        if (!isset($_SESSION['security_code']) || strtoupper($security_code_input) !== strtoupper($_SESSION['security_code'])) { // Case-insensitive comparison
            $error = 'Kode keamanan tidak sesuai!';
        } else {
            $sql = "SELECT * FROM tbl_user WHERE username = '$username'";
            $result = $db->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // Cek password plain text, tidak pakai hash
                // PERINGATAN KEAMANAN: Menyimpan dan membandingkan password sebagai plain text sangat tidak aman.
                // Sebaiknya gunakan password_hash() saat registrasi/update dan password_verify() saat login.
                if ($password === $user['password']) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;
                    unset($_SESSION['security_code']); // Hapus kode keamanan setelah berhasil login
                    header('Location: dashboard.php');
                    exit;
                }
            }
            $error = 'LOGIN GAGAL: Username atau password salah.'; // Pesan error lebih umum
        }
    }
}

// Buat kode keamanan baru setiap kali halaman dimuat (jika belum ada atau jika POST gagal)
// atau jika tidak ada POST request (beban halaman awal)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($error)) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $_SESSION['security_code'] = substr(str_shuffle($chars), 0, 5);
}
$current_security_code = $_SESSION['security_code'] ?? '';


// Ambil daftar username untuk ditampilkan
$usernames = [];
$sqlUsernames = "SELECT username FROM tbl_user ORDER BY username ASC";
$resultUsernames = $db->query($sqlUsernames);
if ($resultUsernames && $resultUsernames->num_rows > 0) {
    while ($row = $resultUsernames->fetch_assoc()) {
        $usernames[] = $row['username'];
    }
}

$db->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>FORM LOGIN</title>
    <style>
        /* Reset dan font */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Allow scrolling if content overflows */
            align-items: center;
            min-height: 100vh;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .container {
            background: #fff;
            width: 100%;
            max-width: 400px; /* Slightly smaller for login form */
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px; /* Space for user list */
        }
        h1 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            color: #333;
            letter-spacing: 1px;
            font-size: 1.8em;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #444;
            font-size: 0.95em;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            font-size: 15px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px; /* Space above button */
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 0.03em;
            background-color: #fddede;
            color: #d32f2f;
            border: 1.5px solid #d32f2f;
            text-align: center;
        }
        
        .security-image-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .security-image {
            background: #e9ecef;
            padding: 12px 15px;
            margin-bottom: 8px;
            font-size: 22px; 
            letter-spacing: 8px; /* Increased letter spacing */
            text-align: center;
            font-weight: bold;
            border-radius: 5px;
            color: #495057;
            user-select: none; /* Prevent text selection */
            border: 1px solid #ced4da;
            display: inline-block; /* To fit content */
            text-transform: uppercase; /* Ensure code is displayed uppercase */
        }
        .security-input-label {
            font-size: 0.9em;
            color: #555;
            text-align: center;
            display: block; /* Make it full width for centering text */
            margin-bottom: 8px;
        }

        .user-list-container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .user-list-container h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            font-size: 1.2em;
            font-weight: 600;
        }
        .user-list-container ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
            max-height: 150px; /* Max height for scrollbar if many users */
            overflow-y: auto; /* Add scroll if needed */
        }
        .user-list-container li {
            margin-bottom: 8px;
            padding: 8px 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 0.95em;
            color: #495057;
            border: 1px solid #e9ecef;
        }
        .system-message p { /* Style for paragraphs in the system message box */
            text-align: center; 
            color: #495057; 
            font-size: 0.95em;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .system-message p:first-child {
            margin-top: 0;
        }


        /* Responsive */
        @media (max-width: 480px) {
            .container, .user-list-container {
                padding: 25px 20px;
                margin-left: 15px;
                margin-right: 15px;
                width: auto;
            }
            body {
                padding-top: 20px;
                padding-bottom: 20px;
            }
            h1 {
                font-size: 1.6em;
            }
            .security-image {
                font-size: 20px;
                letter-spacing: 6px;
                padding: 10px 12px;
            }
            input[type="text"],
            input[type="password"] {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>FORM LOGIN</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required autocomplete="username" autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <div class="form-group">
                <label>Kode Keamanan</label>
                <div class="security-image-container">
                    <div class="security-image">
                        <?php echo htmlspecialchars($current_security_code); ?>
                    </div>
                </div>
                <label for="security_code" class="security-input-label">Masukkan kode di atas (tidak case sensitive)</label>
                <input type="text" id="security_code" name="security_code" required autocomplete="off" maxlength="5" style="text-transform: uppercase;">
            </div>
            
            <button type="submit">Login</button>
        </form>
    </div>

    <?php if (!empty($usernames)): ?>
    <div class="user-list-container">
        <h2>Username Terdaftar</h2>
        <ul>
            <?php foreach ($usernames as $u): ?>
                <li><?php echo htmlspecialchars($u); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php else: ?>
    <div class="user-list-container system-message"> <h2 style="color: #6c757d;">Informasi Sistem</h2>
        <p>Belum ada akun pengguna yang terdaftar di sistem.</p>
        <p>Untuk dapat login, silakan buat akun pengguna terlebih dahulu secara manual melalui database MySQL Anda.</p>
    </div>
    <?php endif; ?>

</body>
</html>