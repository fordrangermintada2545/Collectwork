<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$sql = "SELECT balance, bank_name, account_number, account_name FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// ประมวลผลเมื่อกดปุ่มถอน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    
    if ($amount <= 0) {
        $error = "กรุณาระบุจำนวนเงินที่ถูกต้อง";
    } elseif ($amount > $user['balance']) {
        $error = "ยอดเงินคงเหลือไม่เพียงพอ";
    } else {
        // หักเงินในระบบทันที
        $new_balance = $user['balance'] - $amount;
        $update = mysqli_query($conn, "UPDATE users SET balance = '$new_balance' WHERE id = '$user_id'");
        
        if ($update) {
            // บันทึกประวัติการถอน (ใช้ trans_id เป็นรหัสถอนเงินแบบสุ่ม)
            $trans_id = "WD" . time() . rand(10, 99);
            mysqli_query($conn, "INSERT INTO transactions (user_id, amount, trans_id, detail) 
                                VALUES ('$user_id', '-$amount', '$trans_id', 'แจ้งถอนเงิน (รอดำเนินการ)')");
            
            echo "<script>alert('✅ ส่งคำขอถอนเงินสำเร็จ! แอดมินจะดำเนินการโอนเงินให้ท่านโดยเร็วที่สุด'); window.location='member.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ถอนเงิน | MyWebsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white max-w-md w-full rounded-3xl shadow-xl p-8 border border-slate-100">
        <a href="member.php" class="text-slate-400 hover:text-indigo-600 mb-4 inline-block">← กลับหน้าสมาชิก</a>
        
        <h2 class="text-2xl font-bold text-slate-800 mb-2">ถอนเงินคงเหลือ</h2>
        <p class="text-slate-500 text-sm mb-6">ระบุจำนวนเงินที่คุณต้องการถอนเข้าบัญชี</p>

        <div class="bg-indigo-50 p-4 rounded-2xl mb-6 border border-indigo-100">
            <div class="text-xs text-indigo-400 mb-1 uppercase font-bold">โอนเข้าบัญชีที่ลงทะเบียนไว้</div>
            <div class="text-slate-700 font-bold"><?php echo $user['bank_name']; ?></div>
            <div class="text-indigo-600 text-lg tracking-wider"><?php echo $user['account_number']; ?></div>
            <div class="text-slate-500 text-sm"><?php echo $user['account_name']; ?></div>
        </div>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="text-sm font-semibold text-slate-600 ml-1">ยอดเงินคงเหลือ: ฿<?php echo number_format($user['balance'], 2); ?></label>
                <input type="number" name="amount" step="0.01" min="1" placeholder="ระบุจำนวนเงิน" required 
                    class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none text-2xl font-bold text-center mt-2">
            </div>

            <?php if (isset($error)): ?>
                <div class="text-red-500 text-sm text-center font-bold"><?php echo $error; ?></div>
            <?php endif; ?>

            <button type="submit" onclick="return confirm('ยืนยันการถอนเงิน?')"
                class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95">
                ถอนเงินทันที
            </button>
        </form>

        <p class="text-center text-[11px] text-slate-400 mt-6">
            * การถอนเงินจะใช้เวลาดำเนินการโดยเจ้าหน้าที่ประมาณ 15-30 นาที
        </p>
    </div>

</body>
</html>