<?php
session_start(); // 啟用session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 獲取輸入的ID與姓名
    $student_id = $_POST["student_id"];
    $student_name = $_POST["student_name"];

    // 數據庫連接
    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 查詢數據庫驗證身分
    $sql = "SELECT * FROM students WHERE student_id = '$student_id' AND student_name = '$student_name'";
    $result = mysqli_query($conn, $sql) or die('MySQL query error');
    $count = mysqli_num_rows($result);

    if ($count == 1) {
		// 驗證通過，將資料存在session
        $_SESSION['student_id'] = $student_id;

        // 重定向到選課頁面
        header("Location: course_selection.php");
        exit();
    } else {
        // ID或姓名不正確
        echo "找不到該用戶";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
    }

    mysqli_close($conn);
}
?>
