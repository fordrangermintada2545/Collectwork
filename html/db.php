<?php
$host = "localhost";
$user = "root";  // XAMPP ทุกเครื่องใช้ username คือ root
$pass = "";      // XAMPP ทุกเครื่อง "ไม่มี" รหัสผ่าน (เว้นว่างไว้)
$db   = "user"; // ชื่อ Database ที่คุณสร้างไว้ใน localhost/phpmyadmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}
?>