<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once 'db.php';

$ta_name = $ta_address = $ta_phone = $ta_salary = $assigned_teacher_id = "";
$ta_name_err = $ta_address_err = $ta_phone_err = $ta_salary_err = $assigned_teacher_id_err = "";
$success_msg = "";

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
    // Validate teaching assistant name
    $input_ta_name = trim($_POST["ta_name"]);
    if (empty($input_ta_name)) {
        $ta_name_err = "Please enter a name.";
    } else {
        $ta_name = $input_ta_name;
    }
    
    // Validate teaching assistant address
    $input_ta_address = trim($_POST["ta_address"]);
    if (empty($input_ta_address)) {
        $ta_address_err = "Please enter an address.";
    } else {
        $ta_address = $input_ta_address;
    }
    
    // Validate teaching assistant phone
    $input_ta_phone = trim($_POST["ta_phone"]);
    if (empty($input_ta_phone)) {
        $ta_phone_err = "Please enter a phone number.";
    } elseif (!ctype_digit($input_ta_phone)) {
        $ta_phone_err = "Please enter a valid phone number.";
    } else {
        $ta_phone = $input_ta_phone;
    }
    
    // Validate teaching assistant salary
    $input_ta_salary = trim($_POST["ta_salary"]);
    if (empty($input_ta_salary)) {
        $ta_salary_err = "Please enter a salary.";
    } elseif (!ctype_digit($input_ta_salary)) {
        $ta_salary_err = "Please enter a valid salary.";
    } else {
        $ta_salary = $input_ta_salary;
    }
    
    // Validate assigned teacher ID
    $input_assigned_teacher_id = trim($_POST["assigned_teacher_id"]);
    if (empty($input_assigned_teacher_id)) {
        $assigned_teacher_id_err = "Please select a teacher.";
    } elseif (!ctype_digit($input_assigned_teacher_id)) {
        $assigned_teacher_id_err = "Please select a valid teacher.";
    } else {
        $assigned_teacher_id = $input_assigned_teacher_id;
    }

    // Check input errors before inserting in database
    if (empty($ta_name_err) && empty($ta_address_err) && empty($ta_phone_err) && empty($ta_salary_err) && empty($assigned_teacher_id_err)) {
        $link = getDbConnection();
        $sql = "INSERT INTO TeachingAssistant (Name, Address, Phone, Salary,AssignedToTeacherID ) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssii", $param_ta_name, $param_ta_address, $param_ta_phone, $param_ta_salary, $param_assigned_teacher_id);
            
            $param_ta_name = $ta_name;
            $param_ta_address = $ta_address;
            $param_ta_phone = $ta_phone;
            $param_ta_salary = $ta_salary;
            $param_assigned_teacher_id = $assigned_teacher_id;
            
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Teaching Assistant added successfully.";
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
    <title>Add Teaching Assistant</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Teaching Assistant</h2>
        <p>Please fill this form to add a teaching assistant.</p>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($ta_name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="ta_name" class="form-control" value="<?php echo $ta_name; ?>">
                <span class="help-block"><?php echo $ta_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($ta_address_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="ta_address" class="form-control" value="<?php echo $ta_address; ?>">
                <span class="help-block"><?php echo $ta_address_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($ta_phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone Number</label>
                <input type="text" name="ta_phone" class="form-control" value="<?php echo $ta_phone; ?>">
                <span class="help-block"><?php echo $ta_phone_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($ta_salary_err)) ? 'has-error' : ''; ?>">
                <label>Salary</label>
                <input type="text" name="ta_salary" class="form-control" value="<?php echo $ta_salary; ?>">
                <span class="help-block"><?php echo $ta_salary_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($assigned_teacher_id_err)) ? 'has-error' : ''; ?>">
                <label>Assigned Teacher ID</label>
                <select name="assigned_teacher_id" class="form-control">
                    <option value="">Select a Teacher ID</option>
                    <?php foreach ($teacher as $teach): ?>
                        <option value="<?php echo htmlspecialchars($teach['ID']); ?>" <?php echo ($assigned_teacher_id == $teach['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teach['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block"><?php echo $assigned_teacher_id_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-action btn-action-primary" value="Submit">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
