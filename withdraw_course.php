<?php
session_start();

// 检查是否登录
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// 连接数据库
$dbhost = '127.0.0.1';
$dbuser = 'hj';
$dbpass = 'test1234';
$dbname = 'testdb';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_select_db($conn, $dbname);

// 获取学生ID
$student_id = $_SESSION['student_id'];

// 检查课程ID是否存在
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // 查询课程信息，包括是否为必修课程
    $course_info_query = "SELECT credits, required FROM courses WHERE course_id = '$course_id'";
    $course_info_result = mysqli_query($conn, $course_info_query) or die('MySQL query error');
    $row = mysqli_fetch_assoc($course_info_result);
    $withdraw_credit = $row['credits'];
    $required = $row['required'];

    // 查询学生总学分
    $total_credits_query = "SELECT SUM(courses.credits) AS total_credits
                            FROM student_courses
                            INNER JOIN courses ON student_courses.course_id = courses.course_id
                            WHERE student_courses.student_id = '$student_id'";
    $total_credits_result = mysqli_query($conn, $total_credits_query) or die('MySQL query error');
    $row = mysqli_fetch_assoc($total_credits_result);
    $total_credits = $row['total_credits'];

    // 检查是否退选的是必修课程
    if ($required == 1) {
        echo "警告：此課程為必修課";
		echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
    }
	
	if ($total_credits - $withdraw_credit < 9) {
        // 如果退选的是必修课程且退选后总学分小于 9 学分，则显示警告消息
        echo "警告：退选此课程将导致总学分少于 9 学分！";
        echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
        exit();
    }

    // 删除学生课程表中的课程记录
    $withdraw_query = "DELETE FROM student_courses WHERE student_id = '$student_id' AND course_id = '$course_id'";
    mysqli_query($conn, $withdraw_query) or die('MySQL query error');
	
	// 更新课程的已选人数
    $update_enrollment_query = "UPDATE courses SET selected_count = selected_count - 1 WHERE course_id = '$course_id'";
    mysqli_query($conn, $update_enrollment_query) or die('MySQL query error');
    
    // 显示成功消息
    echo "成功退选课程！";
    echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
}

// 关闭数据库连接
mysqli_close($conn);
?>
