<?php
session_start();

// 檢查是否登入
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$dbhost = '127.0.0.1';
$dbuser = 'hj';
$dbpass = 'test1234';
$dbname = 'testdb';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_select_db($conn, $dbname);

// 獲取學生ID
$student_id = $_SESSION['student_id'];

// 檢查課程ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // 查詢課程信息
    $course_info_query = "SELECT credits, required FROM courses WHERE course_id = '$course_id'";
    $course_info_result = mysqli_query($conn, $course_info_query) or die('MySQL query error');
    $row = mysqli_fetch_assoc($course_info_result);
    $withdraw_credit = $row['credits'];
    $required = $row['required'];

    // 查詢學生總學分
    $total_credits_query = "SELECT SUM(courses.credits) AS total_credits
                            FROM student_courses
                            INNER JOIN courses ON student_courses.course_id = courses.course_id
                            WHERE student_courses.student_id = '$student_id'";
    $total_credits_result = mysqli_query($conn, $total_credits_query) or die('MySQL query error');
    $row = mysqli_fetch_assoc($total_credits_result);
    $total_credits = $row['total_credits'];

    // 檢查是否為必修課
    if ($required == 1) {
        echo "警告，此課程為必修課，欲退選請洽各系系辦";
		echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
		exit();
    }
	
	if ($total_credits - $withdraw_credit < 9) {
        // 如果退選後小於9學分
        echo "警告，退選後少於9學分，無法退選";
        echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
        exit();
    }

    // 刪除課表中的此課程
    $withdraw_query = "DELETE FROM student_courses WHERE student_id = '$student_id' AND course_id = '$course_id'";
    mysqli_query($conn, $withdraw_query) or die('MySQL query error');
	
	// 更新課程的已選人數
    $update_enrollment_query = "UPDATE courses SET selected_count = selected_count - 1 WHERE course_id = '$course_id'";
    mysqli_query($conn, $update_enrollment_query) or die('MySQL query error');
    
    echo "成功退選！";
    echo "<script>setTimeout(function(){ window.location.href = 'schedule.php'; }, 1000);</script>";
}

mysqli_close($conn);
?>
