<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

// ตรวจสอบสิทธิ์: ต้องเป็น admin หรือ bigadmin เท่านั้น
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'bigadmin')) {
    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location='member.php';</script>";
    exit();
}

// ดึงข้อมูลลูกค้าทุกคน (ไม่รวม Admin คนอื่นเพื่อความปลอดภัยเบื้องต้น)
$result = mysqli_query($conn, "SELECT id, firstname, lastname, phone, created_at, role FROM users WHERE role = 'user' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการหลังบ้าน | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: #f8f9fa; color: #333; }

        /* Navbar */
        nav {
            background: #2d3436;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            opacity: 0.8;
        }
        .nav-links a:hover { opacity: 1; }

        /* Main Content */
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header-flex h2 { color: #2d3436; font-weight: 500; }

        /* Table Style */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        th {
            text-align: left;
            padding: 15px;
            background: #f1f3f5;
            color: #495057;
            font-weight: 500;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: middle;
        }
        tr:hover { background: #fdfdfd; }

        /* Buttons & Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #e9ecef;
            color: #495057;
        }
        .btn-manage {
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            transition: 0.3s;
        }
        .btn-manage:hover { background: #5a67d8; box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3); }
        
        .role-user { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>

<nav>
    <div style="font-size: 20px; font-weight: 500;">Admin Dashboard</div>
    <div class="nav-links">
        <span>สวัสดี Admin: <strong><?php echo $_SESSION['username']; ?></strong></span>
        <a href="member.php">หน้าสมาชิก</a>
        <a href="logout.php" style="color: #ff7675;">ออกจากระบบ</a>
    </div>
</nav>

<div class="container">
    <div class="header-flex">
        <h2>จัดการข้อมูลลูกค้า</h2>
        <div style="font-size: 14px; color: #777;">จำนวนสมาชิกทั้งหมด: <?php echo mysqli_num_rows($result); ?> คน</div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>วันที่สมัคร</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td><span class="badge role-user">CUSTOMER</span></td>
                        <td>
                            <a href="edit_password.php?id=<?php echo $row['id']; ?>" class="btn-manage">เปลี่ยนรหัสผ่าน</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #999;">ยังไม่มีข้อมูลลูกค้าในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>