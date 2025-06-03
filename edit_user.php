<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$db = new DB();

// Get user data
$user = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM tbl_user WHERE id = $id";
    $result = $db->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        // If user not found, redirect to dashboard or show an error
        $_SESSION['error_message'] = 'User tidak ditemukan.';
        header('Location: dashboard.php');
        exit;
    }
} else {
    // If no ID is provided, redirect
    $_SESSION['error_message'] = 'ID User tidak valid.';
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->escapeString($_POST['username']);
    $password = $_POST['password']; // No need to escape, will be hashed
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username)) {
        $error = 'Username tidak boleh kosong.';
    } elseif (strlen($username) > 128) {
        $error = 'Username maksimal 128 karakter.';
    } elseif (!empty($password) && (strlen($password) < 5 || strlen($password) > 8)) {
        $error = 'Password harus 5-8 karakter jika diisi.';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama.';
    } else {
        // Check if username exists for other users
        $check_sql = "SELECT id FROM tbl_user WHERE username = '$username' AND id != $id";
        $check_result = $db->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $error = 'Username sudah digunakan oleh user lain.';
        } else {
            if (empty($password)) {
                // Update username only
                $update_sql = "UPDATE tbl_user SET username = '$username' WHERE id = $id";
            } else {
                // Update username and password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $update_sql = "UPDATE tbl_user SET username = '$username', password = '$hashed_password' WHERE id = $id";
            }
            
            if ($db->query($update_sql)) {
                $success = 'User berhasil diperbarui!';
                // Refresh user data after update
                $result = $db->query("SELECT * FROM tbl_user WHERE id = $id");
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc(); // Update $user array with new data
                }
                // Optionally, clear password fields after successful update if they were filled
                // $_POST['password'] = '';
                // $_POST['confirm_password'] = '';
            } else {
                $error = 'Gagal memperbarui user: ' . $db->getConnection()->error;
            }
        }
    }
}

$db->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>EDIT USER</title>
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
            justify-content: center;
            align-items: flex-start; /* Align to top to see content when it's longer */
            min-height: 100vh;
            padding-top: 50px; /* Add padding to prevent content from sticking to the top */
            padding-bottom: 50px; /* Add padding to bottom for scrollable content */
        }
        .container {
            background: #fff;
            width: 100%;
            max-width: 420px;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            letter-spacing: 1.2px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            font-size: 15px;
            border: 1.8px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.4);
        }
        small {
            display: block;
            margin-top: 6px;
            color: #666;
            font-style: italic;
            font-size: 13px;
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .error, .success {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        .error {
            background-color: #fddede;
            color: #d32f2f;
            border: 1.5px solid #d32f2f;
        }
        .success {
            background-color: #d4edda;
            color: #2e7d32;
            border: 1.5px solid #2e7d32;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #4CAF50;
            font-weight: 600;
            text-decoration: none;
            letter-spacing: 0.03em;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
                margin-left: 15px; /* Add some margin for smaller screens */
                margin-right: 15px;
            }
            button {
                padding: 12px;
            }
            h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>EDIT USER</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($user): // Pastikan $user ada sebelum menampilkan form ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" 
                       required maxlength="128" autocomplete="off" autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password Baru (opsional)</label>
                <input type="password" id="password" name="password" minlength="5" maxlength="8" autocomplete="new-password">
                <small>Kosongkan jika tidak ingin mengubah password. Jika diisi, harus 5-8 karakter.</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="5" maxlength="8" autocomplete="new-password">
                <small>Ulangi password baru jika Anda mengisinya.</small>
            </div>
            
            <button type="submit">Simpan Perubahan</button>
        </form>
        <?php else: ?>
            <div class="error">User tidak ditemukan atau ID tidak valid.</div>
        <?php endif; ?>
        
        <a href="dashboard.php" class="back-link">← Kembali ke Daftar User</a>
    </div>
</body>
</html>