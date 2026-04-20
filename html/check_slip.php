<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['slip_image'])) {
    $secret_key = "HvI0EtarCDf32XSqBJM+X2109x9XizFn+9bh+Ifi8_k=";
    $api_url = "https://connect.slip2go.com/api/verify-slip/qr-image/info";

    $slip_tmp  = $_FILES['slip_image']['tmp_name'];
    $file_name = $_FILES['slip_image']['name'];
    $file_type = $_FILES['slip_image']['type'];

    $curl = curl_init();
    $cfile = new CURLFile($slip_tmp, $file_type, $file_name);

    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'file' => $cfile // ส่งแค่ไฟล์อย่างเดียว เพื่อตัดปัญหา Request Invalid
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $secret_key,
            'Accept: application/json'
        ),
        CURLOPT_SSL_VERIFYPEER => false,
    ));

    $response = curl_exec($curl);
    $data = json_decode($response, true);
    curl_close($curl);

    if (isset($data['code']) && $data['code'] == "200000") {
        $res = isset($data['data'][0]) ? $data['data'][0] : $data['data'];
        
        $amount         = $res['amount'];
        $trans_id       = $res['transRef'];
        $receiver_name  = $res['receiver']['account']['name'] ?? '';
        
        // --- 🛡️ การตรวจสอบฉบับปรับปรุง (เน้นชื่อที่ API ตรวจพบจริง) ---
        
        // 1. เช็คชื่อ: ต้องมีคำว่า "มินทร์ธาดา" 
        // วิธีนี้ปลอดภัยพอสมควร เพราะโอกาสที่จะมีคนชื่อ "มินทร์ธาดา" คนอื่นมาใช้กสิกรรับเงินเหมือนกันนั้นน้อยมาก
        $is_name_ok = (mb_strpos($receiver_name, "มินทร์ธาดา", 0, 'UTF-8') !== false);
        
        // 2. เช็คยอดเงิน: ต้องมากกว่า 0
        $is_amount_ok = ($amount > 0);

        if ($is_name_ok && $is_amount_ok) {
            $user_id = $_SESSION['user_id'];
            
            // 3. เช็คสลิปซ้ำ (ด่านนี้สำคัญที่สุด! กันคนเอาสลิปเดิมมาใช้)
            $check_dup = mysqli_query($conn, "SELECT id FROM transactions WHERE trans_id = '$trans_id'");
            
            if (mysqli_num_rows($check_dup) == 0) {
                // ทำการเพิ่มเงิน
                $update = mysqli_query($conn, "UPDATE users SET balance = balance + $amount WHERE id = '$user_id'");
                
                if ($update) {
                    $detail = "เติมเงินอัตโนมัติ (Ref: $trans_id)";
                    mysqli_query($conn, "INSERT INTO transactions (user_id, amount, trans_id, detail) VALUES ('$user_id', '$amount', '$trans_id', '$detail')");
                    echo "<script>alert('✅ เติมเงินสำเร็จ $amount บาท'); window.location='member.php';</script>";
                }
            } else {
                echo "<script>alert('⚠️ สลิปนี้เคยใช้งานไปแล้ว'); window.location='recharge.php';</script>";
            }
        } else {
            $msg = "❌ ข้อมูลผู้รับไม่ถูกต้อง!\\nตรวจพบ: $receiver_name";
            echo "<script>alert('$msg'); window.location='recharge.php';</script>";
        }
    }
}
?>