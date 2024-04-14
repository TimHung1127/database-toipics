<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection System - Login</title>
</head>
<body>
    <h2>Login to Course Selection System</h2>
    <form action="login1.php" method="POST">
        <label for="student_id">Student ID:</label><br>
        <input type="text" id="student_id" name="student_id"><br>
        <label for="student_name">Student Name:</label><br>
        <input type="text" id="student_name" name="student_name"><br><br>
        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Create one here</a>.</p>
</body>
</html>
