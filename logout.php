<?php
session_start();

// 清除session
$_SESSION = array();

// 將cookie的到期時間設置圍過去來刪除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// 刪除session
session_destroy();

// 重定向回登入頁面
header("Location: index.php");
exit();
?>
