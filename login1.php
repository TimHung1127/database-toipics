<?php
session_start(); // 启动会话

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户输入的学生ID和姓名
    $student_id = $_POST["student_id"];
    $student_name = $_POST["student_name"];

    // 数据库连接和查询逻辑
    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 查询数据库验证用户身份
    $sql = "SELECT * FROM students WHERE student_id = '$student_id' AND student_name = '$student_name'";
    $result = mysqli_query($conn, $sql) or die('MySQL query error');
    $count = mysqli_num_rows($result);

    if ($count == 1) {
        // 用户身份验证通过，将学生ID存储在会话中
        $_SESSION['student_id'] = $student_id;



        // 重定向到加退选页面
        header("Location: course_selection.php");
        exit();
    } else {
        // 学生ID或姓名不正确，提示用户重新登录
        echo "找不到该用户，请检查输入的学生ID和姓名是否正确。";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
    }

    // 关闭数据库连接
    mysqli_close($conn);
}
?>
