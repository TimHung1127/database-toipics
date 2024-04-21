<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //獲取用戶數據
    $student_id = $_POST["student_id"];
    $student_name = $_POST["student_name"];
    $department_name = $_POST["department_name"];
    $grade = $_POST["grade"];

    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 檢查學生是否已存在數據庫中
    $check_query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $check_result = mysqli_query($conn, $check_query) or die('MySQL query error');
    $num_rows = mysqli_num_rows($check_result);
	
	//ID不存在，可以註冊
    if ($num_rows == 0) {
		$student_grade = $_POST["grade"];
        // 插入新學生資料到數據庫
        $insert_query = "INSERT INTO students (student_id, student_name, department_name, grade) VALUES ('$student_id', '$student_name', '$department_name', '$grade')";
        mysqli_query($conn, $insert_query) or die('MySQL query error');

        echo "註冊成功！";

        // 查詢必修課
        $required_courses_query = "SELECT course_id FROM courses WHERE required = 1 AND grade = '$student_grade' AND course_id IN (SELECT course_id FROM course_department WHERE department_id = 
                                   (SELECT department_id FROM departments WHERE department_name = '$department_name'))";

        $required_courses_result = mysqli_query($conn, $required_courses_query) or die('MySQL query error');

        // 添加必修課到課程表中
        while ($row = mysqli_fetch_assoc($required_courses_result)) {
            $course_id = $row['course_id'];
            $insert_student_course_query = "INSERT INTO student_courses (student_id, course_id) VALUES ('$student_id', '$course_id')";
            mysqli_query($conn, $insert_student_course_query) or die('MySQL query error');
        }

        // 重定向到登入頁面
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
    } else {
        // ID已存在
        echo "學生ID已存在";
        echo "<script>setTimeout(function(){ window.location.href = 'register.php'; }, 1000);</script>"; // 重定向到註冊頁
    }

    mysqli_close($conn);
}
?>
