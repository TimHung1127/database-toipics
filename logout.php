<?php
// 开始会话
session_start();

// 清除所有会话数据
$_SESSION = array();

// 如果存在会话 cookie，通过将到期时间设置为过去来删除它
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// 彻底销毁会话
session_destroy();

// 重定向到登录页面
header("Location: index.php");
exit();
?>
