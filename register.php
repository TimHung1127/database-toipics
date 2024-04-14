<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection System - Register</title>
</head>
<body>
    <h2>Register for Course Selection System</h2>
    <form action="register1.php" method="POST">
        <label for="student_id">Student ID:</label><br>
        <input type="text" id="student_id" name="student_id" required><br>
        <label for="student_name">Student Name:</label><br>
        <input type="text" id="student_name" name="student_name" required><br><br>
        <label for="department_name">Department Name:</label><br>
        <input type="text" id="department_name" name="department_name" required><br><br>
        <label for="grade">Grade:</label><br>
        <input type="number" id="grade" name="grade" min="1" max="4" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
