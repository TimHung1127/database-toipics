<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取用户输入的学生ID、姓名、系所名称和年级
    $student_id = $_POST["student_id"];
    $student_name = $_POST["student_name"];
    $department_name = $_POST["department_name"];
    $grade = $_POST["grade"]; // 新添加的年级字段

    // 数据库连接信息
    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';

    // 建立数据库连接
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 检查学生ID是否已经存在于数据库中
    $check_query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query) or die('MySQL query error');
    $num_rows = mysqli_num_rows($check_result);

    if ($num_rows == 0) {
		$student_grade = $_POST["grade"];
        // 学生ID不存在，可以进行注册
        // 插入新学生记录到数据库中，包括年级信息
        $insert_query = "INSERT INTO students (student_id, student_name, department_name, grade) VALUES ('$student_id', '$student_name', '$department_name', '$grade')";
        mysqli_query($conn, $insert_query) or die('MySQL query error');

        // 提示用户注册成功
        echo "注册成功！";

        // 查询该系所的必修课程
        $required_courses_query = "SELECT course_id FROM courses WHERE required = 1 AND grade = '$student_grade' AND course_id IN (SELECT course_id FROM course_department WHERE department_id = 
                                   (SELECT department_id FROM departments WHERE department_name = '$department_name'))";

        $required_courses_result = mysqli_query($conn, $required_courses_query) or die('MySQL query error');

        // 将必修课程添加到学生课程表中
        while ($row = mysqli_fetch_assoc($required_courses_result)) {
            $course_id = $row['course_id'];
            $insert_student_course_query = "INSERT INTO student_courses (student_id, course_id) VALUES ('$student_id', '$course_id')";
            mysqli_query($conn, $insert_student_course_query) or die('MySQL query error');
        }

        // 重定向到登录页面
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
    } else {
        // 学生ID已经存在，提醒用户选择其他ID
        echo "学生ID已经存在，请选择其他ID。";
        echo "<script>setTimeout(function(){ window.location.href = 'register.php'; }, 1000);</script>"; // 重定向到注册页面
    }

    // 关闭数据库连接
    mysqli_close($conn);
}
?>
