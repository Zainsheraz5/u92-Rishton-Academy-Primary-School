<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once 'db.php';

$teacher_name = $teacher_address = $teacher_phone = $teacher_salary = $background_check = $background_summary = "";
$teacher_name_err = $teacher_address_err = $teacher_phone_err = $teacher_salary_err = $background_check_err = $background_summary_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate teacher name
    $input_teacher_name = trim($_POST["teacher_name"]);
    if (empty($input_teacher_name)) {
        $teacher_name_err = "Please enter a name.";
    } else {
        $teacher_name = $input_teacher_name;
    }
    
    // Validate teacher address
    $input_teacher_address = trim($_POST["teacher_address"]);
    if (empty($input_teacher_address)) {
        $teacher_address_err = "Please enter an address.";
    } else {
        $teacher_address = $input_teacher_address;
    }
    
    // Validate teacher phone
    $input_teacher_phone = trim($_POST["teacher_phone"]);
    if (empty($input_teacher_phone)) {
        $teacher_phone_err = "Please enter a phone number.";
    } elseif (!ctype_digit($input_teacher_phone)) {
        $teacher_phone_err = "Please enter a valid phone number.";
    } else {
        $teacher_phone = $input_teacher_phone;
    }
    
    // Validate teacher salary
    $input_teacher_salary = trim($_POST["teacher_salary"]);
    if (empty($input_teacher_salary)) {
        $teacher_salary_err = "Please enter a salary.";
    } elseif (!ctype_digit($input_teacher_salary)) {
        $teacher_salary_err = "Please enter a valid salary.";
    } else {
        $teacher_salary = $input_teacher_salary;
    }
    
    // Validate background check
    $input_background_check = trim($_POST["background_check"]);
    if($_POST["background_check"] == "0"){
        $background_check = $input_background_check;
    }
    else if (empty($input_background_check)) {
        $background_check_err = "Please select background check status.";
    } else {
        $background_check = $input_background_check;
    }
    
    // Validate background summary
    $input_background_summary = trim($_POST["background_summary"]);
    if (empty($input_background_summary) ) {
        $background_summary_err = "Please enter a background summary.";
    } else {
        $background_summary = $input_background_summary;
    }

    // Check input errors before inserting in database
    if (empty($teacher_name_err) && empty($teacher_address_err) && empty($teacher_phone_err) && empty($teacher_salary_err) && empty($background_check_err) && empty($background_summary_err)) {
        $link = getDbConnection();
        $sql = "INSERT INTO Teacher (Name, Address, PhoneNumber, AnnualSalary, BackgroundCheck, BackgroundSummary) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssiis", $param_teacher_name, $param_teacher_address, $param_teacher_phone, $param_teacher_salary, $param_background_check, $param_background_summary);
            
            $param_teacher_name = $teacher_name;
            $param_teacher_address = $teacher_address;
            $param_teacher_phone = $teacher_phone;
            $param_teacher_salary = $teacher_salary;
            $param_background_check = $background_check;
            $param_background_summary = $background_summary;
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Teacher added successfully.";
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
    <title>Add Teacher</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Teacher</h2>
        <p>Please fill this form to add a teacher.</p>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($teacher_name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="teacher_name" class="form-control" value="<?php echo $teacher_name; ?>">
                <span class="help-block"><?php echo $teacher_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($teacher_address_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="teacher_address" class="form-control" value="<?php echo $teacher_address; ?>">
                <span class="help-block"><?php echo $teacher_address_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($teacher_phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone Number</label>
                <input type="text" name="teacher_phone" class="form-control" value="<?php echo $teacher_phone; ?>">
                <span class="help-block"><?php echo $teacher_phone_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($teacher_salary_err)) ? 'has-error' : ''; ?>">
                <label>Salary</label>
                <input type="text" name="teacher_salary" class="form-control" value="<?php echo $teacher_salary; ?>">
                <span class="help-block"><?php echo $teacher_salary_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($background_check_err)) ? 'has-error' : ''; ?>">
                <label>Background Check</label>
                <select name="background_check" class="form-control">
                    <option value="">Select status</option>
                    <option value="1" <?php echo ($background_check == '1') ? 'selected' : ''; ?>>Approved</option>
                    <option value="0" <?php echo ($background_check == '0') ? 'selected' : ''; ?>>Not Approved</option>
                </select>
                <span class="help-block"><?php echo $background_check_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($background_summary_err)) ? 'has-error' : ''; ?>">
                <label>Background Summary</label>
                <textarea name="background_summary" class="form-control"><?php echo $background_summary; ?></textarea>
                <span class="help-block"><?php echo $background_summary_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-action btn-action-primary" value="Submit">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
