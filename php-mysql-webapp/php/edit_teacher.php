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

// Fetch classes for dropdown
$link = getDbConnection();
$class_options = [];
$sql_classes = "SELECT ID, Name FROM Classes";
if ($result_classes = mysqli_query($link, $sql_classes)) {
    while ($row_class = mysqli_fetch_assoc($result_classes)) {
        $class_options[] = $row_class;
    }
}
mysqli_free_result($result_classes);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Handle Update Logic
        $data = $_POST;
        $link = getDbConnection();

        if (empty($errors)) {
            $sql = "UPDATE Teacher SET Name=?, Address=?, PhoneNumber=?, AnnualSalary=?, BackgroundCheck=?, BackgroundSummary=? WHERE ID=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssiisi", $data['teacher_name'], $data['teacher_address'], $data['teacher_phone_number'], $data['teacher_annual_salary'], $data['background_check'], $data['background_summary'], $id);
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
}
    if (isset($_POST['delete'])) {
        // Handle Delete Logic
        $link = getDbConnection();
    
        // Check if the teacher is assigned to any class
        $sql = "SELECT ID FROM Classes WHERE TeacherID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
    
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "This teacher cannot be deleted because they are currently teaching one or more classes.";
            } else {
                // Check if any teaching assistants are assigned to the teacher
                $sql = "SELECT ID FROM TeachingAssistant WHERE AssignedToTeacherID = ?";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
    
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        $errors[] = "This teacher cannot be deleted because a teaching assistant is assigned to them.";
                    } else {
                        // Proceed with deletion if no classes or TAs are associated
                        $sql = "DELETE FROM Teacher WHERE ID=?";
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_bind_param($stmt, "i", $id);
                            if (mysqli_stmt_execute($stmt)) {
                                $success_msg = "Data deleted successfully.";
                                header("Location: dashboard.php");
                                exit();
                            } else {
                                $errors[] = "Something went wrong. Please try again later.";
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_close($link);
    }

// Fetch the record to populate form
$link = getDbConnection();
$sql = "SELECT * FROM Teacher WHERE ID=?";
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
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Teacher</h2>
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
                <label>Teacher Name</label>
                <input type="text" name="teacher_name" class="form-control" value="<?php echo htmlspecialchars($data['Name']); ?>">
            </div>
            <div class="form-group">
                <label>Teacher Address</label>
                <input type="text" name="teacher_address" class="form-control" value="<?php echo htmlspecialchars($data['Address']); ?>">
            </div>
            <div class="form-group">
                <label>Teacher Phone Number</label>
                <input type="text" name="teacher_phone_number" class="form-control" value="<?php echo htmlspecialchars($data['PhoneNumber']); ?>">
            </div>
            <div class="form-group">
                <label>Teacher Annual Salary</label>
                <input type="text" name="teacher_annual_salary" class="form-control" value="<?php echo htmlspecialchars($data['AnnualSalary']); ?>">
            </div>
            <div class="form-group">
                <label>Background Check (1 for Approved, 0 for Not Approved)</label>
                <input type="text" name="background_check" class="form-control" value="<?php echo htmlspecialchars($data['BackgroundCheck']); ?>">
            </div>
            <div class="form-group">
                <label>Background Summary</label>
                <textarea name="background_summary" class="form-control"><?php echo htmlspecialchars($data['BackgroundSummary']); ?></textarea>
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
            