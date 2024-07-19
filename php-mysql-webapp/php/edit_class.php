<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once 'db.php';

$id = $_GET['id'];

// Initialize variables and errors
$data = [];
$errors = [];
$success_msg = "";
$teacher_id = 0;


function fetchData($table) {
    $link = getDbConnection();
    $sql = "SELECT * FROM $table";  
    $result = mysqli_query($link, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($link);
    return $data;
}
$teacher = fetchData('Teacher');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Handle Update Logic
        if (empty(trim($_POST["teacher_id"]))) {
            $errors = "Please select a valid Teacher ID"; 
        } else {
            $teacher_id = trim($_POST["teacher_id"]);
            $link = getDbConnection();
            $sql = "SELECT ID FROM Teacher WHERE ID = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $teacher_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 0) {
                    $errors[] = "Teacher ID does not exist.";
                }
                
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
        if($link = getDbConnection()){
            
            $teacher_id = trim($_POST["teacher_id"]);
            $link = getDbConnection();
            
            // Check if the teacher is already assigned to another class
            $sql = "SELECT ID FROM Classes WHERE TeacherID = ? AND ID != ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $teacher_id, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors[] = "This teacher is already assigned to another class. Please select another teacher.";
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
        if(empty($errors)){

            $data = $_POST;
            $link = getDbConnection();
            
            $sql = "UPDATE Classes SET Classtype = ?, Name = ?, Capacity = ?, PupilAmount = ?, TeacherID = ? WHERE ID = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssiiii", $data['ClassType'], $data['Name'], $data['Capacity'], $data['PupilAmount'], $teacher_id, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Data updated successfully.";
                } else {
                    $errors[] = "Something went wrong. Please try again later.";
                }
            }
            mysqli_stmt_close($stmt);
            mysqli_close($link);
        }
    }
}


    if (isset($_POST['delete'])) {
         // Handle Delete Logic
         $link = getDbConnection();
        
         // Check if the class is referenced in the Pupil table
    $sql = "SELECT ID FROM Pupil WHERE ClassEnrolledID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Students are a part of this class, therefore the class cannot be removed.";
        } else {
                    $sql = "DELETE FROM Classes WHERE ID=?";
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
            mysqli_close($link);
        }


// Fetch the record to populate form
$link = getDbConnection();
$sql = "SELECT * FROM Classes WHERE ID=?";
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
    <title>Edit Class</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Class</h2>
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <label>Class Type</label>
                <input placeholder = "Reception Year, Year One, Year Two, ...." type="text" name="ClassType" class="form-control" value="<?php echo htmlspecialchars($data['ClassType']); ?>">
            </div>
            <div class="form-group">
                <label>Class Name</label>
                <input type="text" name="Name" class="form-control" value="<?php echo htmlspecialchars($data['Name']); ?>">
            </div>
            <div class="form-group">
                <label>Class Capacity</label>
                <input type="text" name="Capacity" class="form-control" value="<?php echo htmlspecialchars($data['Capacity']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Amount</label>
                <input type="text" name="PupilAmount" class="form-control" value="<?php echo htmlspecialchars($data['PupilAmount']); ?>">
            </div>
            <div class="form-group">
                <label>Assigned Teacher</label>
                <select name="teacher_id" class="form-control">
                    <option value="">Select a Teacher ID</option>
                    <?php foreach ($teacher as $teach): ?>
                        <option value="<?php echo htmlspecialchars($teach['ID']); ?>" <?php echo ($data['TeacherID'] == $teach['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teach['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" name="update" class="btn-action btn-action-primary" value="Update" >
                <input type="submit" name="delete" class="btn btn-danger" value="Delete">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
