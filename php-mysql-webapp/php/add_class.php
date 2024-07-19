<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once 'db.php';

$class_type = $class_name = $class_capacity = $pupils_amount = $teacher_id = "" ;
$class_type_err = $class_name_err = $class_capacity_err = $pupils_amount_err = $teacher_id_err = $success_msg = "";
$allowed_classes = ['Reception Year', 'Year One', 'Year Two', 'Year Three', 'Year Four', 'Year Five', 'Year Six'];

//Get data to display table
function fetchData($table) {
    $link = getDbConnection();
    $sql = "SELECT * FROM $table";
    $result = mysqli_query($link, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($link);
    return $data;
}
$teacher = fetchData('Teacher');


if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Validating All Inputs
    if(empty(trim($_POST["class_type"]))){
        $class_type_err = "Please enter the class type.";
    } else{
        if (!in_array(trim($_POST["class_type"]), $allowed_classes)) {
            $class_type_err = "Invalid class type. Please select a valid class.";
        }
        else{
            $class_type = trim($_POST["class_type"]);
        }
    }
    
    if(empty(trim($_POST["class_name"]))){
        $class_name_err = "Please enter the class name.";
    } else{
        $class_name = trim($_POST["class_name"]);
    }
    
    if(empty(trim($_POST["class_capacity"]))){
        $class_capacity_err = "Please enter the class capacity.";
    } // The input must be a digit
    elseif(!ctype_digit($_POST["class_capacity"])){
        $class_capacity_err = "Please enter a valid number.";
    } else{
        $class_capacity = trim($_POST["class_capacity"]);
    }

    if(empty(trim($_POST["pupils_amount"]))){
        $pupils_amount_err = "Please enter the number of pupils.";
    } elseif(!ctype_digit($_POST["pupils_amount"])){
        $pupils_amount_err = "Please enter a valid number for pupils.";
    }//Number of pupils cannot exceed capacity
     elseif((int)$_POST["pupils_amount"] > (int)$_POST["class_capacity"]){
        $pupils_amount_err = "Number of pupils cannot exceed class capacity.";
    } else{
        $pupils_amount = trim($_POST["pupils_amount"]);
    }

    if(empty(trim($_POST["teacher_id"])) || $_POST["teacher_id"] == "NULL") {
        $teacher_id = NULL; 
    } else {
        if($link = getDbConnection()){
        $sql = "SELECT TeacherID FROM Classes";
        $stmt = mysqli_prepare($link, $sql);
        $valid = true;
    
        if($stmt) {
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_bind_result($stmt, $teacher_id_db);
    
                while (mysqli_stmt_fetch($stmt)) {
                    if ($teacher_id_db == trim($_POST["teacher_id"])) {
                        $valid = false;
                        break;
                    }
                }
                
                if (!$valid) {
                     $teacher_id_err = "This teacher is already teaching another class. Please select another teacher.";;
                } else {
                    $teacher_id = trim($_POST["teacher_id"]);
                }
            } else {
                echo "Failed to execute the SQL statement.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to prepare the SQL statement.";
        }
        mysqli_close($link);
        }
    }

    if(empty($class_type_err) && empty($class_name_err) && empty($class_capacity_err) && empty($pupils_amount_err) && empty($teacher_id_err)){
        $link = getDbConnection();
        $sql = "INSERT INTO Classes (ClassType, Name, Capacity, PupilAmount,TeacherID) VALUES (?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssiii", $param_class_type, $param_class_name, $param_class_capacity, $param_pupils_amount,$param_teacher_id);
            $param_class_type = $class_type;
            $param_class_name = $class_name;
            $param_class_capacity = $class_capacity;
            $param_pupils_amount = $pupils_amount;
            $param_teacher_id = $teacher_id;
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Class added successfully.";
            } else{
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to prepare the SQL insert statement.";
        }

        mysqli_close($link);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Class</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Class</h2>
        <p>Please fill this form to add a new class.</p>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($class_type_err)) ? 'has-error' : ''; ?>">
                <label>Class Type</label>
                <input type="text" name="class_type" class="form-control" value="<?php echo $class_type; ?>" placeholder = "Reception Year, Year One, Year Two, ....">
                <span class="help-block"><?php echo $class_type_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($class_name_err)) ? 'has-error' : ''; ?>">
                <label>Class Name</label>
                <input type="text" name="class_name" class="form-control" value="<?php echo $class_name; ?>">
                <span class="help-block"><?php echo $class_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($class_capacity_err)) ? 'has-error' : ''; ?>">
                <label>Class Capacity</label>
                <input type="text" name="class_capacity" class="form-control" value="<?php echo $class_capacity; ?>">
                <span class="help-block"><?php echo $class_capacity_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupils_amount_err)) ? 'has-error' : ''; ?>">
                <label>Number of Pupils</label>
                <input type="text" name="pupils_amount" class="form-control" value="<?php echo $pupils_amount; ?>">
                <span class="help-block"><?php echo $pupils_amount_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($teacher_id_err)) ? 'has-error' : ''; ?>">
                <label>Assigned Teacher ID</label>
                <select name="teacher_id" class="form-control">
                    <option value="">Select a Teacher ID</option>
                    <?php foreach ($teacher as $teach): ?>
                        <option value="<?php echo htmlspecialchars($teach['ID']); ?>" <?php echo ($teacher_id == $teach['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teach['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value = "NULL">
                        NULL
                    </option>
                </select>
                <span class="help-block"><?php echo $teacher_id_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-action btn-action-primary" value="Add Class">
            </div>
        </form>
        <p><a class = "btn btn-primary" href="dashboard.php">Back to Dashboard</a></p>
    </div>    
</body>
</html>
