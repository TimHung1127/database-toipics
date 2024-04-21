<?php
session_start(); // 啟動session

// 檢查用戶是否已登入
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// 檢查是否收到正確的課程ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfollowed_course_id'])) {
    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 獲取學生ID與要取消關注的課程ID
    $student_id = $_SESSION['student_id'];
    $unfollowed_course_id = $_POST['unfollowed_course_id'];

    // 從關注列表中移除
    $unfollow_query = "DELETE FROM follow_list WHERE student_id = '$student_id' AND course_id = '$unfollowed_course_id'";
    $result = mysqli_query($conn, $unfollow_query) or die('MySQL query error');

    mysqli_close($conn);

    // 重定向回選課頁面
    header("Location: course_selection.php");
    exit();
} else {
    // 未收到正確ID則重定向回選課頁面
    header("Location: course_selection.php");
    exit();
}
?>
