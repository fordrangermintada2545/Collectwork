<?php
include('db.php');
session_start();
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'bigadmin') die("No access");

// เมื่อ Admin กดยืนยันการถอน
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE transactions SET detail = 'ถอนเงินสำเร็จ (โอนแล้ว)' WHERE id = '$id'");
    header("Location: admin_withdraw.php");
}

// เมื่อ Admin กดยกเลิก (คืนเงินให้ลูกค้า)
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $trans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transactions WHERE id = '$id'"));
    $user_id = $trans['user_id'];
    $refund = abs($trans['amount']); // แปลงค่าลบเป็นบวกเพื่อคืนเงิน
    
    mysqli_query($conn, "UPDATE users SET balance = balance + $refund WHERE id = '$user_id'");
    mysqli_query($conn, "UPDATE transactions SET detail = 'การถอนถูกปฏิเสธ (คืนเงินแล้ว)' WHERE id = '$id'");
    header("Location: admin_withdraw.php");
}

$withdraws = mysqli_query($conn, "SELECT t.*, u.real_name, u.bank_name, u.account_number FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.detail LIKE '%แจ้งถอน%' ORDER BY t.created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อนุมัติการถอน</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-8">
    <div class="max-w-5xl mx-auto">
        <a href="admin.php" class="text-slate-400 mb-4 inline-block">← กลับหน้าหลัก</a>
        <h1 class="text-2xl font-bold mb-6">รายการรอโอนเงิน (แจ้งถอน)</h1>
        
        <div class="space-y-4">
            <?php while($row = mysqli_fetch_assoc($withdraws)): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-400">ชื่อ: <?php echo $row['real_name']; ?></p>
                    <p class="font-bold text-indigo-600"><?php echo $row['bank_name']; ?> : <?php echo $row['account_number']; ?></p>
                    <p class="text-2xl font-bold text-rose-500">ยอดถอน: ฿<?php echo number_format(abs($row['amount']), 2); ?></p>
                </div>
                <div class="flex gap-2">
                    <a href="?approve=<?php echo $row['id']; ?>" onclick="return confirm('โอนเงินให้ลูกค้าเรียบร้อยแล้ว?')" class="bg-emerald-500 text-white px-6 py-2 rounded-xl">โอนแล้ว</a>
                    <a href="?reject=<?php echo $row['id']; ?>" class="bg-slate-200 text-slate-600 px-6 py-2 rounded-xl">ปฏิเสธ</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>