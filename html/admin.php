<?php
include('db.php');
session_start();

// แก้ไขส่วนนี้ให้เข้มงวด
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'bigadmin')) {
    header("Location: admin_login.php");
    exit();
}
// ... โค้ดจัดการข้อมูลแอดมินอื่นๆ ...

// จัดการลบ/เพิ่มยอดเงิน และเปลี่ยนรหัสผ่าน (POST Handling)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_id = $_POST['user_id'];
    
    if (isset($_POST['update_balance'])) {
        $amount = floatval($_POST['amount']);
        mysqli_query($conn, "UPDATE users SET balance = balance + ($amount) WHERE id = '$target_id'");
    }
    
    if (isset($_POST['change_pw'])) {
        $new_pw = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$new_pw' WHERE id = '$target_id'");
    }
    echo "<script>alert('ดำเนินการสำเร็จ'); window.location='admin.php';</script>";
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY last_login_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-100 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">จัดการสมาชิก</h1>
            <div class="flex gap-4">
                <a href="admin_withdraw.php" class="bg-rose-500 text-white px-6 py-2 rounded-xl shadow-lg">อนุมัติการถอน</a>
                <a href="admin_slips.php" class="bg-emerald-500 text-white px-6 py-2 rounded-xl shadow-lg">เช็คสลิป</a>
            </div>
        </div>
        <div class="flex gap-4">
            <?php if ($_SESSION['role'] == 'bigadmin'): ?>
                <a href="manage_staff.php" class="bg-indigo-600 text-white px-6 py-2 rounded-xl shadow-lg font-bold">👑 จัดการแอดมิน</a>
            <?php endif; ?>
            <a href="admin_withdraw.php" class="bg-rose-500 text-white px-6 py-2 rounded-xl shadow-lg">อนุมัติการถอน</a>
            <a href="admin_slips.php" class="bg-emerald-500 text-white px-6 py-2 rounded-xl shadow-lg">เช็คสลิป</a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-slate-600">ชื่อลูกค้า / เบอร์</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">ธนาคาร / เลขบัญชี</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">ยอดเงิน</th>
                        <th class="p-4 text-sm font-semibold text-slate-600">IP ล่าสุด / เวลา</th>
                        <th class="p-4 text-sm font-semibold text-slate-600 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = mysqli_fetch_assoc($users)): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4">
                            <p class="font-bold text-slate-700"><?php echo $row['real_name']." ".$row['real_surname']; ?></p>
                            <p class="text-xs text-slate-400"><?php echo $row['username']; ?></p>
                        </td>
                        <td class="p-4">
                            <p class="text-sm text-indigo-600 font-medium"><?php echo $row['bank_name']; ?></p>
                            <p class="text-xs font-mono text-slate-500"><?php echo $row['account_number']; ?></p>
                        </td>
                        <td class="p-4">
                            <span class="text-lg font-bold text-emerald-600">฿<?php echo number_format($row['balance'],2); ?></span>
                        </td>
                        <td class="p-4">
                            <p class="text-xs text-slate-600 font-mono"><?php echo $row['last_ip'] ?? '-'; ?></p>
                            <p class="text-[10px] text-slate-400"><?php echo $row['last_login_at'] ?? 'ไม่เคยเข้าใช้งาน'; ?></p>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col gap-1">
                                <form method="POST" class="flex gap-1">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="amount" placeholder="+/- เงิน" class="w-20 text-xs p-1 border rounded">
                                    <button name="update_balance" class="bg-indigo-600 text-white text-[10px] px-2 py-1 rounded">ตกลง</button>
                                </form>
                                <form method="POST" class="flex gap-1">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="new_password" placeholder="รหัสใหม่" class="w-20 text-xs p-1 border rounded">
                                    <button name="change_pw" class="bg-slate-800 text-white text-[10px] px-2 py-1 rounded">เปลี่ยน PW</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>