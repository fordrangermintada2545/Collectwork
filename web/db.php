<?php
$host = "localhost";
$db   = "konradfa_gtlq1"; // ชื่อจากคอลัมน์ Database ในรูปของคุณ
$user = "konradfa_gtlq1"; // โดยปกติจะชื่อเดียวกับ Database
$pass = "6hjhUZvXzNncx2HGasCH"; // **สำคัญ** ต้องเป็นรหัสที่ตั้งตอนสร้าง Database

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลไม่ได้: " . mysqli_connect_error());
}
?>