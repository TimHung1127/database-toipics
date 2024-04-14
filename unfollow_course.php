<?php
session_start(); // 启动会话

// 检查用户是否已登录，如果未登录则重定向到登录页面
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// 检查是否收到正确的取消关注课程ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollowed_course_id'])) {
    // 连接数据库
    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 获取学生ID和要取消关注的课程ID
    $student_id = $_SESSION['student_id'];
    $unfollowed_course_id = $_POST['unfollowed_course_id'];

    // 执行取消关注操作，从关注列表中移除指定课程
    $unfollow_query = "DELETE FROM follow_list WHERE student_id = '$student_id' AND course_id = '$unfollowed_course_id'";
    $result = mysqli_query($conn, $unfollow_query) or die('MySQL query error');

    // 关闭数据库连接
    mysqli_close($conn);

    // 重定向回关注列表页面
    header("Location: course_selection.php");
    exit();
} else {
    // 如果未收到正确的课程ID，则重定向回关注列表页面
    header("Location: course_selection.php");
    exit();
}
?>
