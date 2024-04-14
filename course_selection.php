<?php
session_start();

// 檢查是否登入
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

// 連接數據庫
$dbhost = '127.0.0.1';
$dbuser = 'hj';
$dbpass = 'test1234';
$dbname = 'testdb';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_select_db($conn, $dbname);

// 獲取ID
$student_id = $_SESSION['student_id'];

// 檢查取消關注或退選
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['course_id'])) {
    $action = $_POST['action'];
    $course_id = $_POST['course_id'];

    if ($action === 'unfollow') {
        // 取消關注
        $unfollow_query = "DELETE FROM follow_list WHERE student_id = '$student_id' AND course_id = '$course_id'";
        mysqli_query($conn, $unfollow_query) or die('MySQL query error');
    } elseif ($action === 'withdraw') {
        // 退选
        $withdraw_query = "DELETE FROM student_courses WHERE student_id = '$student_id' AND course_id = '$course_id'";
        mysqli_query($conn, $withdraw_query) or die('MySQL query error');

        // 將課程重新加入可選課程表和關注列表
        $add_to_available_query = "INSERT INTO follow_list (student_id, course_id) VALUES ('$student_id', '$course_id')";
        mysqli_query($conn, $add_to_available_query) or die('MySQL query error');
    } elseif ($action === 'follow') {
        // 加選
        $follow_query = "INSERT INTO follow_list (student_id, course_id) VALUES ('$student_id', '$course_id')";
        mysqli_query($conn, $follow_query) or die('MySQL query error');
    }

    // 刷新頁面
    header("Location: course_selection.php");
    exit();
}

// 獲取課程列表
$followed_courses_query = "SELECT follow_list.course_id, courses.course_name FROM follow_list INNER JOIN courses ON follow_list.course_id = courses.course_id WHERE follow_list.student_id = '$student_id'";
$followed_courses_result = mysqli_query($conn, $followed_courses_query) or die('MySQL query error');

// 獲取所在系所
$student_department_query = "SELECT department_name FROM students WHERE student_id = '$student_id'";
$student_department_result = mysqli_query($conn, $student_department_query) or die('MySQL query error');
$row = mysqli_fetch_assoc($student_department_result);
$student_department_name = $row['department_name'];
// 获取学生的年级信息
$student_grade_query = "SELECT grade FROM students WHERE student_id = '$student_id'";
$student_grade_result = mysqli_query($conn, $student_grade_query) or die('MySQL query error');
$row = mysqli_fetch_assoc($student_grade_result);
$student_grade = $row['grade'];

// 获取可选课程表，排除已选课程并且只选择同一年级的课程
$available_courses_query = "SELECT DISTINCT courses.course_id, courses.course_name 
                            FROM courses 
                            INNER JOIN course_department ON courses.course_id = course_department.course_id 
                            INNER JOIN departments ON course_department.department_id = departments.department_id 
                            WHERE departments.department_name = '$student_department_name' 
                            AND courses.course_id NOT IN (SELECT course_id FROM follow_list WHERE student_id = '$student_id')
                            AND courses.course_id NOT IN (SELECT course_id FROM student_courses WHERE student_id = '$student_id')
                            AND courses.grade = '$student_grade'";

$available_courses_result = mysqli_query($conn, $available_courses_query) or die('MySQL query error');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection System - Course Selection</title>
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
	<a href="logout.php">Logout</a>
</div>
<h2>Course Selection</h2>

<!-- 已關注課程表 -->
<h3>Followed Courses:</h3>
<ul>
    <?php
    while ($row = mysqli_fetch_assoc($followed_courses_result)) {
        echo "<li>" . $row['course_name'] . " (" . $row['course_id'] . ") <form name='unfollow_form' action='course_selection.php' method='POST'>
                <input type='hidden' name='course_id' value='" . $row['course_id'] . "'>
                <input type='hidden' name='action' value='unfollow'>
                <input type='submit' value='Unfollow'>
              </form>
              <form name='withdraw_form' action='enroll_course.php' method='POST'>
                <input type='hidden' name='course_id' value='" . $row['course_id'] . "'>
                <input type='hidden' name='action' value='withdraw'>
                <input type='submit' value='Withdraw'>
              </form>
            </li>";
    }
    ?>
</ul>


<!-- 可選課程表 -->
<h3>Available Courses:</h3>
<ul>
    <?php
    while ($row = mysqli_fetch_assoc($available_courses_result)) {
        echo "<li>" . $row['course_name'] . " (" . $row['course_id'] . ") <form name='follow_form' action='course_selection.php' method='POST'><input type='hidden' name='course_id' value='" . $row['course_id'] . "'><input type='hidden' name='action' value='follow'><input type='submit' name='follow_button' value='Follow'></form></li>";
    }
    ?>
</ul>

<br>

<!-- 輸入課程ID關注 -->
<form action="add_to_follow.php" method="POST">
    <label for="course_id">Enter Course ID to Follow:</label><br>
    <input type="text" id="course_id" name="course_id" required><br>
    <input type="submit" value="Follow">
</form>

<br>

<!-- 登出 -->
<a href="logout.php">Logout</a>
</body>
</html>
