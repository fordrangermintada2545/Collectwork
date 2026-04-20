<?php
// check_api.php
include('db.php');
session_start();

// ล็อคให้เฉพาะ Big Admin เท่านั้นที่ดูหน้านี้ได้ (เพื่อความปลอดภัยของ Secret Key)
if ($_SESSION['role'] !== 'bigadmin') {
    die("คุณไม่มีสิทธิ์เข้าถึงหน้านี้");
}

$secretKey = "HvI0EtarCDf32XSqBJM+X2109x9XizFn+9bh+Ifi8_k="; // *** สำคัญมาก ห้ามบอกคนอื่น ***

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.slip2go.com/api/account/info', 
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบ API | Slip2go</title>
    <style>
        body { font-family: 'Kanit', sans-serif; background: #1a1a2e; color: white; padding: 20px; }
        .info-box { background: #16213e; padding: 20px; border-radius: 10px; border-left: 5px solid #e94560; }
        pre { background: #0f3460; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h2>สถานะการเชื่อมต่อ Slip2go</h2>
    <div class="info-box">
        <?php if (isset($data['success']) && $data['success'] == true): ?>
            <p style="color: #2ed573;">● เชื่อมต่อสำเร็จ</p>
            <p>ชื่อบัญชี: <?php echo $data['data']['name']; ?></p>
            <p>ยอดเงินคงเหลือในระบบ API: <?php echo $data['data']['balance']; ?> บาท</p>
        <?php else: ?>
            <p style="color: #ff4757;">○ เชื่อมต่อล้มเหลว: <?php echo $data['message'] ?? 'ตรวจสอบ Secret Key ของคุณ'; ?></p>
        <?php endif; ?>
    </div>

    <h4>ข้อมูลดิบจาก API (Raw Data):</h4>
    <pre><?php print_r($data); ?></pre>

    <br>
    <a href="bigadmin.php" style="color: #888; text-decoration: none;">← กลับหน้า Big Admin</a>
</body>
</html>