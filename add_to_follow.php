<?php
session_start(); // 啟動session

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $course_id = $_POST["course_id"];

    $dbhost = '127.0.0.1';
    $dbuser = 'hj';
    $dbpass = 'test1234';
    $dbname = 'testdb';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_select_db($conn, $dbname);

    // 從資料庫中獲取學生信息
    $get_grade_query = "SELECT grade FROM students WHERE student_id = '$student_id'";
    $grade_result = mysqli_query($conn, $get_grade_query);
    $grade_row = mysqli_fetch_assoc($grade_result);
    $student_grade = $grade_row['grade'];
	
    // 檢查課程是否與學生科系相同
    $check_department_query = "SELECT COUNT(*) AS count FROM course_department 
                           INNER JOIN departments ON course_department.department_id = departments.department_id 
                           INNER JOIN courses ON course_department.course_id = courses.course_id
                           WHERE course_department.course_id = '$course_id' 
                           AND departments.department_name = (SELECT department_name FROM students WHERE student_id = '$student_id')
                           AND courses.grade = '$student_grade'";

    $check_department_result = mysqli_query($conn, $check_department_query) or die('MySQL query error');
    $row = mysqli_fetch_assoc($check_department_result);
    $count = $row['count'];

    if ($count > 0) {
        // 加入關注列表
        $insert_query = "INSERT INTO follow_list (student_id, course_id) VALUES ('$student_id', '$course_id')";
        mysqli_query($conn, $insert_query) or die('MySQL query error');

        mysqli_close($conn);

        // 重定向回選課頁面
        header("Location: course_selection.php");
        exit();
    } else {
		// 如果課程不是本系，顯示錯誤信息
		echo "該課程不屬於本系或年級不符";
        echo "<script>setTimeout(function(){ window.location.href = 'course_selection.php'; }, 1000);</script>";
    }
} else {
    // 如果未登入
    header("Location: index.php");
    exit();
}
?>
