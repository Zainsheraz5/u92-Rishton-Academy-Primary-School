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

// Fetch parents/guardians for dropdown
$link = getDbConnection();
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
        $link = getDbConnection();


        if (empty($errors)) {
            $sql = "UPDATE ParentGuardian SET Name=?, Address=?, Email=?, PhoneNumber=? WHERE ID=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $data['parent_guardian_name'], $data['parent_guardian_address'], $data['parent_guardian_email'], $data['parent_guardian_telephone_number'], $id);
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
    
        // Check if the parent/guardian is referenced in the Pupil table
        $sql = "SELECT ID FROM Pupil WHERE Parent_Guardian_1_ID  = ? OR Parent_Guardian_2_ID  = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $id, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "This Parent/Guardian is associated with students. Therefore, Parents/Guardians cannot be removed without removing students.";
            } else {
                // Proceed with deletion if no students are associated
                $sql = "DELETE FROM ParentGuardian WHERE ID=?";
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
}

// Fetch the record to populate form
$link = getDbConnection();
$sql = "SELECT * FROM ParentGuardian WHERE ID=?";
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
    <title>Edit Parent/Guardian</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit Parent/Guardian</h2>
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
                <label>Parent/Guardian Name</label>
                <input type="text" name="parent_guardian_name" class="form-control" value="<?php echo htmlspecialchars($data['Name']); ?>">
            </div>
            <div class="form-group">
                <label>Parent/Guardian Address</label>
                <input type="text" name="parent_guardian_address" class="form-control" value="<?php echo htmlspecialchars($data['Address']); ?>">
            </div>
            <div class="form-group">
                <label>Parent/Guardian Email</label>
                <input type="email" name="parent_guardian_email" class="form-control" value="<?php echo htmlspecialchars($data['Email']); ?>">
            </div>
            <div class="form-group">
                <label>Parent/Guardian Telephone Number</label>
                <input type="text" name="parent_guardian_telephone_number" class="form-control" value="<?php echo htmlspecialchars($data['PhoneNumber']); ?>">
            </div>
            <div class="form-group">
                <input type="submit" name="update" class="btn-action btn-action-primary" value="Update">
                <input type="submit" name="delete" class="btn btn-danger" value="Delete">
                <a href="dashboard.php" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
