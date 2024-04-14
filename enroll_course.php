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

// 检查选课请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // 检查是否已经选过该课程
    $check_query = "SELECT * FROM student_courses WHERE student_id = '$student_id' AND course_id = '$course_id'";
    $check_result = mysqli_query($conn, $check_query) or die('MySQL query error');
    $num_rows = mysqli_num_rows($check_result);

    if ($num_rows == 0) {
        // 查询已选学分
        $total_credits_query = "SELECT SUM(credits) AS total_credits FROM student_courses WHERE student_id = '$student_id'";
        $total_credits_result = mysqli_query($conn, $total_credits_query) or die('MySQL query error');
        $total_credits_row = mysqli_fetch_assoc($total_credits_result);
        $total_credits = $total_credits_row['total_credits'];

        // 查询课程学分
        $course_info_query = "SELECT * FROM courses WHERE course_id = '$course_id'";
        $course_info_result = mysqli_query($conn, $course_info_query) or die('MySQL query error');
        $course_info = mysqli_fetch_assoc($course_info_result);
        $credits = $course_info['credits'];

        // 检查总学分是否超过30
        if (($total_credits + $credits) > 30) {
            // 如果超过30学分，显示消息
            echo "您已选修的课程总学分超过30学分，无法再选修该课程。";

            // 重定向到课程选择页面
            echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
        } else {
            // 查询要加选课程的时间信息
            $course_info_query = "SELECT day_of_week, time_slot FROM courses WHERE course_id = '$course_id'";
            $course_info_result = mysqli_query($conn, $course_info_query);
            $course_info = mysqli_fetch_assoc($course_info_result);
            $course_day_of_week = $course_info['day_of_week'];
            $course_time_slot = $course_info['time_slot'];

            // 检查是否有时间冲突的课程
            $conflicting_course_query = "SELECT * FROM student_courses 
                                         INNER JOIN courses ON student_courses.course_id = courses.course_id 
                                         WHERE student_id = '$student_id' 
                                         AND day_of_week = '$course_day_of_week' 
                                         AND time_slot = '$course_time_slot'";
            $conflicting_course_result = mysqli_query($conn, $conflicting_course_query);
            if (mysqli_num_rows($conflicting_course_result) > 0) {
                // 存在时间冲突的课程，显示消息
                echo "您已选修的课程与该课程存在时间冲突，无法再选修该课程。";

                // 重定向到课程选择页面
                echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
            } else {
                // 如果未超过30学分且不存在时间冲突的课程，则执行加选操作
                // 将课程从关注列表中移除
                $unfollow_query = "DELETE FROM follow_list WHERE student_id = '$student_id' AND course_id = '$course_id'";
                mysqli_query($conn, $unfollow_query) or die('MySQL query error');

                // 将课程添加到学生课程表中
                $enroll_query = "INSERT INTO student_courses (student_id, course_id, credits) VALUES ('$student_id', '$course_id', '$credits')";
                mysqli_query($conn, $enroll_query) or die('MySQL query error');

                // 成功选课消息
                echo "成功選課!";

                // 重定向到课程选择页面
                echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
            }
        }
    } else {
        // 如果已经选过该课程，则显示消息
        echo "已選過該課程";

        // 重定向到课程选择页面
        echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
    }
}


mysqli_close($conn);
?>
