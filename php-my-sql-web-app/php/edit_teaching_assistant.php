<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';

$id = $_GET['id'];

// Initialize variables and errors
$data = [];
$errors = [];
$success_msg = "";

// Fetch teachers for dropdown
$link = getDbConnection();
$teacher_options = [];
$sql_teachers = "SELECT ID, Name FROM Teacher";
if ($result_teachers = mysqli_query($link, $sql_teachers)) {
    while ($row_teacher = mysqli_fetch_assoc($result_teachers)) {
        $teacher_options[] = $row_teacher;
    }
}
mysqli_free_result($result_teachers);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Handle Update Logic
        $data = $_POST;
        $link = getDbConnection();

        // Validate teacher assigned
        $teacher_ids = array_column($teacher_options, 'ID');
        if (!in_array($data['assigned_to_teacher_id'], $teacher_ids)) {
            $errors[] = "Selected teacher is not valid.";
        }

        if (empty($errors)) {
            $sql = "UPDATE TeachingAssistant SET Name=?, Address=?, Phone=?, Salary=?, AssignedToTeacherID=? WHERE ID=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssiii", $data['teaching_assistant_name'], $data['teaching_assistant_address'], $data['teaching_assistant_phone_number'], $data['teaching_assistant_salary'], $data['assigned_to_teacher_id'], $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Data updated successfully.";
                } else {
                    $errors[] = "Something went wrong. Please try again later.";
                }
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    }

    if (isset($_POST['delete'])) {
        // Handle Delete Logic
        $link = getDbConnection();
        $sql = "DELETE FROM TeachingAssistant WHERE ID=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                header("location: dashboard.php");
                exit();
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }
}

// Fetch the record to populate form
$link = getDbConnection();
$sql = "SELECT * FROM TeachingAssistant WHERE ID=?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teaching Assistant</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Teaching Assistant</h2>
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php 
        if (!empty($success_msg)) {
            echo '<div class="success">' . $success_msg . '</div>';
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label>Teaching Assistant Name</label>
                <input type="text" name="teaching_assistant_name" class="form-control" value="<?php echo htmlspecialchars($data['Name']); ?>">
            </div>
            <div class="form-group">
                <label>Teaching Assistant Address</label>
                <input type="text" name="teaching_assistant_address" class="form-control" value="<?php echo htmlspecialchars($data['Address']); ?>">
            </div>
            <div class="form-group">
                <label>Teaching Assistant Phone Number</label>
                <input type="text" name="teaching_assistant_phone_number" class="form-control" value="<?php echo htmlspecialchars($data['Phone']); ?>">
            </div>
            <div class="form-group">
                <label>Teaching Assistant Salary</label>
                <input type="text" name="teaching_assistant_salary" class="form-control" value="<?php echo htmlspecialchars($data['Salary']); ?>">
            </div>
            <div class="form-group">
                <label>Assigned To Teacher</label>
                <select name="assigned_to_teacher_id" class="form-control">
                    <?php foreach ($teacher_options as $teacher): ?>
                        <option value="<?php echo $teacher['ID']; ?>" <?php echo $teacher['ID'] == $data['AssignedToTeacherID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" name="update" class="btn-action btn-action-primary" value="Update">
                <input type="submit" name="delete" class="btn btn-danger" value="Delete">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
