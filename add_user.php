<!DOCTYPE html>
<html>
<head>
    <title>TAMBAH USER</title>
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
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 50px;
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
            }
            button {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>TAMBAH USER BARU</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                       required maxlength="128" autocomplete="off" autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required minlength="5" maxlength="8" autocomplete="new-password">
                <small>Password harus 5-8 karakter</small>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required minlength="5" maxlength="8" autocomplete="new-password">
            </div>

            <button type="submit">Simpan</button>
        </form>

        <a href="dashboard.php" class="back-link">← Kembali ke Daftar User</a>
    </div>
</body>
</html>
