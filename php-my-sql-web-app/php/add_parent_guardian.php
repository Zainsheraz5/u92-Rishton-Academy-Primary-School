<?php
require_once 'db.php';

$parent_guardian_name = $parent_guardian_address = $parent_guardian_email = $parent_guardian_phone = "";
$parent_guardian_name_err = $parent_guardian_address_err = $parent_guardian_email_err = $parent_guardian_phone_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate parent/guardian name
    $input_parent_guardian_name = trim($_POST["parent_guardian_name"]);
    if (empty($input_parent_guardian_name)) {
        $parent_guardian_name_err = "Please enter a name.";
    } else {
        $parent_guardian_name = $input_parent_guardian_name;
    }
    
    // Validate parent/guardian address
    $input_parent_guardian_address = trim($_POST["parent_guardian_address"]);
    if (empty($input_parent_guardian_address)) {
        $parent_guardian_address_err = "Please enter an address.";
    } else {
        $parent_guardian_address = $input_parent_guardian_address;
    }
    
    // Validate parent/guardian email
    $input_parent_guardian_email = trim($_POST["parent_guardian_email"]);
    if (empty($input_parent_guardian_email)) {
        $parent_guardian_email_err = "Please enter an email.";
    } elseif (!filter_var($input_parent_guardian_email, FILTER_VALIDATE_EMAIL)) {
        $parent_guardian_email_err = "Please enter a valid email address.";
    } else {
        $parent_guardian_email = $input_parent_guardian_email;
    }

    // Validate parent/guardian phone
    $input_parent_guardian_phone = trim($_POST["parent_guardian_phone"]);
    if (empty($input_parent_guardian_phone)) {
        $parent_guardian_phone_err = "Please enter a phone number.";
    } elseif (!ctype_digit($input_parent_guardian_phone)) {
        $parent_guardian_phone_err = "Please enter a valid phone number.";
    } else {
        $parent_guardian_phone = $input_parent_guardian_phone;
    }

    // Check input errors before inserting in database
    if (empty($parent_guardian_name_err) && empty($parent_guardian_address_err) && empty($parent_guardian_email_err) && empty($parent_guardian_phone_err)) {
        $link = getDbConnection();
        $sql = "INSERT INTO ParentGuardian (Name,Address,Email,PhoneNumber) VALUES (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_parent_guardian_name, $param_parent_guardian_address, $param_parent_guardian_email, $param_parent_guardian_phone);
            
            $param_parent_guardian_name = $parent_guardian_name;
            $param_parent_guardian_address = $parent_guardian_address;
            $param_parent_guardian_email = $parent_guardian_email;
            $param_parent_guardian_phone = $parent_guardian_phone;
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Parent/Guardian added successfully.";
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
    <title>Add Parent/Guardian</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Parent/Guardian</h2>
        <p>Please fill this form to add a parent/guardian.</p>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($parent_guardian_name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="parent_guardian_name" class="form-control" value="<?php echo $parent_guardian_name; ?>">
                <span class="help-block"><?php echo $parent_guardian_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($parent_guardian_address_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="parent_guardian_address" class="form-control" value="<?php echo $parent_guardian_address; ?>">
                <span class="help-block"><?php echo $parent_guardian_address_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($parent_guardian_email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="parent_guardian_email" class="form-control" value="<?php echo $parent_guardian_email; ?>">
                <span class="help-block"><?php echo $parent_guardian_email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($parent_guardian_phone_err)) ? 'has-error' : ''; ?>">
                <label>Phone Number</label>
                <input type="text" name="parent_guardian_phone" class="form-control" value="<?php echo $parent_guardian_phone; ?>">
                <span class="help-block"><?php echo $parent_guardian_phone_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-action btn-action-primary" value="Submit">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
