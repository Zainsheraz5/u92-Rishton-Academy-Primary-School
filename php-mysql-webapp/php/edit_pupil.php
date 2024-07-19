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

// Fetch parents/guardians for dropdown
$parent_options = [];
$sql_parents = "SELECT ID, Name FROM ParentGuardian";
if ($result_parents = mysqli_query($link, $sql_parents)) {
    while ($row_parent = mysqli_fetch_assoc($result_parents)) {
        $parent_options[] = $row_parent;
    }
}
mysqli_free_result($result_parents);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Handle Update Logic
        $data = $_POST;
        
        // Validate class enrolled
        $class_ids = array_column($class_options, 'ID');
        if (!in_array($data['class_enrolled'], $class_ids)) {
            $errors[] = "Selected class is not valid.";
        }

        // Validate parent/guardian IDs
        $parent_ids = array_column($parent_options, 'ID');
        if (!in_array($data['parent_guardian_1'], $parent_ids) || !in_array($data['parent_guardian_2'], $parent_ids)) {
            $errors[] = "Selected parent/guardian is not valid.";
        }
        // Check that both parents are not the same
        if ($data['parent_guardian_1'] == $data['parent_guardian_2']) {
            $errors[] = "Parent/Guardian 1 and Parent/Guardian 2 cannot be the same person.";
        }

        if (empty($errors)) {
            $link = getDbConnection();
            $sql = "UPDATE Pupil SET Name=?, Address=?, Age=?, Height=?, Weight=?, BloodGroup=?, ClassEnrolledID=?, Parent_Guardian_1_ID=?, Parent_Guardian_2_ID=? WHERE ID=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssiddsiiii", $data['pupil_name'], $data['pupil_address'], $data['pupil_age'], $data['pupil_height'], $data['pupil_weight'], $data['pupil_blood_group'], $data['class_enrolled'], $data['parent_guardian_1'], $data['parent_guardian_2'], $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Data updated successfully.";
                } else {
                    $errors[] = "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
    }

    if (isset($_POST['delete'])) {
        // Handle Delete Logic
        $link = getDbConnection();
        $sql = "DELETE FROM Pupil WHERE ID=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Data deleted successfully.";
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    }
}

// Fetch the record to populate form
$link = getDbConnection();
$sql = "SELECT * FROM Pupil WHERE ID=?";
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
    <title>Edit Pupil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Pupil</h2>
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
                <label>Pupil Name</label>
                <input type="text" name="pupil_name" class="form-control" value="<?php echo htmlspecialchars($data['Name']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Address</label>
                <input type="text" name="pupil_address" class="form-control" value="<?php echo htmlspecialchars($data['Address']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Age</label>
                <input type="text" name="pupil_age" class="form-control" value="<?php echo htmlspecialchars($data['Age']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Height</label>
                <input type="text" name="pupil_height" class="form-control" value="<?php echo htmlspecialchars($data['Height']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Weight</label>
                <input type="text" name="pupil_weight" class="form-control" value="<?php echo htmlspecialchars($data['Weight']); ?>">
            </div>
            <div class="form-group">
                <label>Pupil Blood Group</label>
                <input type="text" name="pupil_blood_group" class="form-control" value="<?php echo htmlspecialchars($data['BloodGroup']); ?>">
            </div>
            <div class="form-group">
                <label>Class Enrolled</label>
                <select name="class_enrolled" class="form-control">
                    <?php foreach ($class_options as $class): ?>
                        <option value="<?php echo $class['ID']; ?>" <?php echo $class['ID'] == $data['ClassEnrolledID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Parent/Guardian 1</label>
                <select name="parent_guardian_1" class="form-control">
                    <?php foreach ($parent_options as $parent): ?>
                        <option value="<?php echo $parent['ID']; ?>" <?php echo $parent['ID'] == $data['Parent_Guardian_1_ID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Parent/Guardian 2</label>
                <select name="parent_guardian_2" class="form-control">
                    <?php foreach ($parent_options as $parent): ?>
                        <option value="<?php echo $parent['ID']; ?>" <?php echo $parent['ID'] == $data['Parent_Guardian_2_ID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['Name']); ?>
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
