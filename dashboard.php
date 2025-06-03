<?php
session_start();
require_once 'db.php'; // Pastikan db.php ada dan berfungsi

if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php');
    exit;
}

$db = new DB();
$users = [];

// Mengambil data user dan mengurutkannya berdasarkan CreateTime terbaru
$sql = "SELECT id, username, CreateTime FROM tbl_user ORDER BY CreateTime DESC";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Daftar User</title>
    <style>
        /* Reset dan Font Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5; /* Warna latar belakang konsisten */
            color: #333;
            padding: 25px; /* Sedikit penyesuaian padding */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Halaman */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .page-header h1 {
            font-size: 26px; /* Sedikit penyesuaian ukuran font */
            color: #2c3e50;
            font-weight: 600;
        }
        .btn { /* Kelas dasar untuk tombol */
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }
        .btn-logout {
            background-color: #e74c3c; /* Merah untuk logout */
            color: #fff;
        }
        .btn-logout:hover {
            background-color: #c0392b;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);
        }
        .btn-add {
            background-color: #4CAF50; /* Hijau konsisten */
            color: white;
            padding: 11px 20px; /* Padding konsisten */
            margin-bottom: 25px; /* Penyesuaian margin */
        }
        .btn-add:hover {
            background-color: #45a049;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        }

        /* Kontainer Tabel */
        .table-container {
            background-color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Bayangan konsisten */
            border-radius: 8px; /* Radius konsisten */
            overflow: hidden; /* Untuk border-radius pada tabel */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background-color: #4a5568; /* Warna header tabel yang lebih netral gelap */
            color: white;
        }
        th, td {
            padding: 15px 20px; /* Padding sel konsisten */
            text-align: left;
            font-size: 15px;
        }
        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        tbody tr {
            border-bottom: 1px solid #e8ebee; /* Border baris yang lebih halus */
            transition: background-color 0.2s ease;
        }
        tbody tr:last-child {
            border-bottom: none;
        }
        tbody tr:hover {
            background-color: #f7f9fa; /* Hover yang lebih halus */
        }
        .action-links a {
            margin-right: 12px; /* Penyesuaian jarak */
            text-decoration: none;
            font-weight: 500; /* Sedikit lebih ringan */
            padding: 5px 8px;
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .action-links a.edit-link {
            color: #2980b9; /* Biru untuk edit */
        }
        .action-links a.edit-link:hover {
            background-color: rgba(41, 128, 185, 0.1);
            color: #1c5980;
        }
        .action-links a.delete-link {
            color: #c0392b; /* Merah untuk hapus */
        }
        .action-links a.delete-link:hover {
            background-color: rgba(192, 57, 43, 0.1);
            color: #a52a1a;
        }
        .no-users-message td {
            text-align:center;
            padding: 30px 20px; /* Padding lebih besar untuk pesan kosong */
            color: #777;
            font-style: italic;
        }

        /* Responsif */
        @media (max-width: 768px) { /* Penyesuaian breakpoint */
            body {
                padding: 15px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 20px;
            }
            .page-header h1 {
                margin-bottom: 15px;
            }
            .btn-add {
                width: 100%; /* Tombol tambah jadi full width */
                margin-bottom: 20px;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px; /* Sembunyikan header tabel asli */
            }
            tr {
                border: 1px solid #e0e0e0; /* Border untuk setiap 'kartu' baris */
                border-radius: 6px; /* Radius untuk 'kartu' */
                margin-bottom: 15px;
                background-color: white; /* Pastikan background putih untuk setiap kartu */
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                padding: 0; /* Hapus padding default tr jika ada */
            }
            td {
                padding: 12px 15px; /* Padding dalam sel responsif */
                padding-left: 45%; /* Ruang untuk label data */
                position: relative;
                text-align: right; /* Data rata kanan */
                border-bottom: 1px dotted #e8ebee; /* Garis putus antar data */
                display: flex; /* Untuk align item jika konten kompleks */
                justify-content: space-between; /* Label kiri, nilai kanan */
                align-items: center;
            }
            td:before {
                content: attr(data-label); /* Ambil dari atribut data-label */
                position: absolute;
                left: 15px; /* Posisi label */
                width: 40%; /* Lebar label */
                white-space: nowrap;
                font-weight: 600; /* Label tebal */
                color: #555;
                text-align: left; /* Label rata kiri */
            }
            td:last-child {
                border-bottom: 0;
            }
            .action-links {
                padding-top: 15px; /* Beri jarak untuk tombol aksi */
                padding-bottom: 15px;
            }
            .action-links a {
                display: inline-block;
                margin-bottom: 5px; /* Tombol aksi mungkin perlu baris baru */
            }
        }
         @media (max-width: 480px) {
            .page-header h1 {
                font-size: 22px;
            }
            th, td {
                font-size: 14px;
            }
            td {
                padding-left: 40%; /* Sesuaikan jika label terlalu panjang */
            }
            td:before{
                width: 35%;
                font-size: 13px;
            }
         }
    </style>
</head>
<body>
    <header class="page-header"> <h1>Daftar User</h1>
        <a href="logout.php" class="btn btn-logout">Logout</a> </header>

    <a href="add_user.php" class="btn btn-add">Tambah User Baru</a> <div class="table-container"> <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td data-label="No"><?php echo $index + 1; ?></td>
                        <td data-label="Username"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td data-label="Tanggal Dibuat"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['CreateTime']))); ?></td>
                        <td data-label="Aksi" class="action-links">
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit-link">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus user ini: <?php echo htmlspecialchars(addslashes($user['username'])); ?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="no-users-message"><td colspan="4">Belum ada user terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>