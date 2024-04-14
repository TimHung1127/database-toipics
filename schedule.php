<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection System - Course Selection - Logout</title>
    <style>
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="course_selection.php">Course Selection</a>
    <a href="schedule.php">My Course Schedule</a>
    <a href="logout.php">Logout</a> <!-- 添加 Logout 按钮，链接到 logout.php 文件 -->
</div>

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

// 定义星期几的数字值
$days_of_week = array(1, 2, 3, 4, 5); // 星期一到星期五分别对应数字 1 到 5

// 计算已选学分
$total_credits_query = "SELECT SUM(courses.credits) AS total_credits
                        FROM student_courses
                        INNER JOIN courses ON student_courses.course_id = courses.course_id
                        WHERE student_courses.student_id = '$student_id'";
$total_credits_result = mysqli_query($conn, $total_credits_query) or die('MySQL query error');
$row = mysqli_fetch_assoc($total_credits_result);
$total_credits = $row['total_credits'];

// 输出已选学分
echo "<h2>Total Credits: " . $total_credits . "</h2>";

// 输出课程表
echo "<h2>My Course Schedule</h2>";
foreach ($days_of_week as $day) {
	// 查询已选课程信息
	$course_schedule_query = "SELECT student_courses.course_id, courses.course_name, courses.day_of_week, courses.time_slot
							  FROM student_courses
							  INNER JOIN courses ON student_courses.course_id = courses.course_id
							  WHERE student_courses.student_id = '$student_id'
							  AND courses.day_of_week = $day
							  ORDER BY courses.time_slot";
    $course_schedule_result = mysqli_query($conn, $course_schedule_query) or die('MySQL query error');

    // 输出星期几的课程表
    echo "<h3>";
    switch ($day) {
        case 1:
            echo "Monday";
            break;
        case 2:
            echo "Tuesday";
            break;
        case 3:
            echo "Wednesday";
            break;
        case 4:
            echo "Thursday";
            break;
        case 5:
            echo "Friday";
            break;
        default:
            echo "Unknown";
            break;
    }
    echo "</h3>";
    echo "<ul>";
	
    while ($row = mysqli_fetch_assoc($course_schedule_result)) {
        echo "<li>" . $row['time_slot'] . ": " . $row['course_name'] . " <form name='withdraw_form' action='withdraw_course.php' method='POST'>
                <input type='hidden' name='course_id' value='" . $row['course_id'] . "'>
                <input type='submit' value='Withdraw'>
              </form></li>";
    }
    echo "</ul>";
}

// 关闭数据库连接
mysqli_close($conn);
?>
</body>
</html>
