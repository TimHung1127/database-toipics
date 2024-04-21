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

// 檢查選課請求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // 檢查是否已選過該課程
    $check_query = "SELECT * FROM student_courses WHERE student_id = '$student_id' AND course_id = '$course_id'";
    $check_result = mysqli_query($conn, $check_query) or die('MySQL query error');
    $num_rows = mysqli_num_rows($check_result);

    if ($num_rows == 0) {
        // 查詢已選學分
        $total_credits_query = "SELECT SUM(credits) AS total_credits FROM student_courses WHERE student_id = '$student_id'";
        $total_credits_result = mysqli_query($conn, $total_credits_query) or die('MySQL query error');
        $total_credits_row = mysqli_fetch_assoc($total_credits_result);
        $total_credits = $total_credits_row['total_credits'];

        // 查詢已選人數
		$course_info_query = "SELECT credits, selected_count, capacity FROM courses WHERE course_id = '$course_id'";
		$course_info_result = mysqli_query($conn, $course_info_query) or die('MySQL query error');
		$course_info = mysqli_fetch_assoc($course_info_result);
		$credits = $course_info['credits'];
		$selected_count = $course_info['selected_count'];
		$capacity = $course_info['capacity'];

        // 檢查總學分是否超過30
        if (($total_credits + $credits) > 30) {
            echo "已修課程總學分超過30，無法選修";
            echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
        } elseif ($selected_count >= $capacity) {
            // 如果課程已滿
            echo "課程已達人數上限，無法選修";
            echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
        } else {
            // 查詢要加選課程的時間信息
            $course_info_query = "SELECT day_of_week, time_slot FROM courses WHERE course_id = '$course_id'";
            $course_info_result = mysqli_query($conn, $course_info_query);
            $course_info = mysqli_fetch_assoc($course_info_result);
            $course_day_of_week = $course_info['day_of_week'];
            $course_time_slot = $course_info['time_slot'];

            // 檢查是否有時間衝突
            $conflicting_course_query = "SELECT * FROM student_courses 
                                         INNER JOIN courses ON student_courses.course_id = courses.course_id 
                                         WHERE student_id = '$student_id' 
                                         AND day_of_week = '$course_day_of_week' 
                                         AND time_slot = '$course_time_slot'";
            $conflicting_course_result = mysqli_query($conn, $conflicting_course_query);
            if (mysqli_num_rows($conflicting_course_result) > 0) {
                // 存在時間衝突
                echo "已選課程與該課程衝堂";
                echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
            } else {
                // 未超過30學分且未滿人數限制且不存在時間衝突則加選
                // 更新已選人數
                $update_enrollment_query = "UPDATE courses SET selected_count = selected_count + 1 WHERE course_id = '$course_id'";
                mysqli_query($conn, $update_enrollment_query) or die('MySQL query error');

                // 將課程從關注列表中移除
                $unfollow_query = "DELETE FROM follow_list WHERE student_id = '$student_id' AND course_id = '$course_id'";
                mysqli_query($conn, $unfollow_query) or die('MySQL query error');

                // 將課程添加到課表
                $enroll_query = "INSERT INTO student_courses (student_id, course_id, credits) VALUES ('$student_id', '$course_id', '$credits')";
                mysqli_query($conn, $enroll_query) or die('MySQL query error');

                echo "成功選課!";

                echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
            }
        }
    } else {
        // 如果已選過該課程
        echo "已選過該課程";
        echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
    }
}


mysqli_close($conn);
?>
