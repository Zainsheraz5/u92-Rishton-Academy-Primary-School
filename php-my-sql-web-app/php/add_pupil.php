<?php

require_once 'db.php';


$pupil_name = $pupil_address = $pupil_age = $pupil_height = $pupil_weight = $pupil_blood_group = $class_enrolled = $parent_guardian_1_id = $parent_guardian_2_id = "";
$pupil_name_err = $pupil_address_err = $pupil_age_err = $pupil_height_err = $pupil_weight_err = $pupil_blood_group_err = $class_enrolled_err = $parent_guardian_1_id_err = $parent_guardian_2_id_err = "";
$success_msg = "";

function fetchData($table) {
    $link = getDbConnection();
    $sql = "SELECT * FROM $table";
    $result = mysqli_query($link, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($link);
    return $data;
}

$classes = fetchData('Classes');
$parents = fetchData('ParentGuardian');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate pupil name
    $input_pupil_name = trim($_POST["pupil_name"]);
    if (empty($input_pupil_name)) {
        $pupil_name_err = "Please enter a name.";
    } else {
        $pupil_name = $input_pupil_name;
    }
    
    // Validate pupil address
    $input_pupil_address = trim($_POST["pupil_address"]);
    if (empty($input_pupil_address)) {
        $pupil_address_err = "Please enter an address.";
    } else {
        $pupil_address = $input_pupil_address;
    }
    
    // Validate pupil age
    $input_pupil_age = trim($_POST["pupil_age"]);
    if (empty($input_pupil_age)) {
        $pupil_age_err = "Please enter an age.";
    } elseif (!ctype_digit($input_pupil_age)) {
        $pupil_age_err = "Please enter a positive integer value.";
    } else {
        $pupil_age = $input_pupil_age;
    }

    // Validate pupil height
    $input_pupil_height = trim($_POST["pupil_height"]);
    if (empty($input_pupil_height)) {
        $pupil_height_err = "Please enter height.";
    } elseif (!is_numeric($input_pupil_height)) {
        $pupil_height_err = "Please enter a valid height.";
    } else {
        $pupil_height = $input_pupil_height;
    }

    // Validate pupil weight
    $input_pupil_weight = trim($_POST["pupil_weight"]);
    if (empty($input_pupil_weight)) {
        $pupil_weight_err = "Please enter weight.";
    } elseif (!is_numeric($input_pupil_weight)) {
        $pupil_weight_err = "Please enter a valid weight.";
    } else {
        $pupil_weight = $input_pupil_weight;
    }

    // Validate pupil blood group
    $input_pupil_blood_group = trim($_POST["pupil_blood_group"]);
    if (empty($input_pupil_blood_group)) {
        $pupil_blood_group_err = "Please enter a blood group.";
    } else {
        $pupil_blood_group = $input_pupil_blood_group;
    }

    // Validate class enrolled
    $input_class_enrolled = trim($_POST["class_enrolled"]);
    if (empty($input_class_enrolled)) {
        $class_enrolled_err = "Please select a class OR Enter NULL.";
    } elseif (!ctype_digit($input_class_enrolled)) {
        $class_enrolled_err = "Please enter valid number.";
        } else {
        if($link = getDbConnection()){
            $sql = "SELECT ID FROM Classes";
            $stmt = mysqli_prepare($link, $sql);
            $valid = false;
        
            if($stmt) {
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $class_enrolled_id_db);
        
                    while (mysqli_stmt_fetch($stmt)) {
                        if ($class_enrolled_id_db == trim($_POST["class_enrolled"])) {
                            $valid = true;
                            break;
                        }
                    }
        
                    if (!$valid) {
                        $class_enrolled_err = "Class ID does not exist";
                    } else {
                        $class_enrolled = trim($_POST["class_enrolled"]);
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

    // Validate parent/guardian 1 ID
    $input_parent_guardian_1_id = trim($_POST["parent_guardian_1_id"]);
    if (empty($input_parent_guardian_1_id)) {
        $parent_guardian_1_id_err = "Please select a parent/guardian OR Enter NULL .";
    } elseif(trim($_POST["parent_guardian_1_id"]) == "NULL"){
        $parent_guardian_1_id = NULL;
    } elseif (!ctype_digit($input_parent_guardian_1_id)) {
    }  else {
        $parent_guardian_1_id_err = "Please enter valid number.";
        if($link = getDbConnection()){
            $sql = "SELECT ID FROM ParentGuardian";
            $stmt = mysqli_prepare($link, $sql);
            $valid = false;
        
            if($stmt) {
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $parent_guardian_1_id_db);
        
                    while (mysqli_stmt_fetch($stmt)) {
                        if ($parent_guardian_1_id_db == trim($_POST["parent_guardian_1_id"])) {
                            $valid = true;
                            break;
                        }
                    }
        
                    if (!$valid) {
                        $$parent_guardian_1_id_err = "Parent ID does not exist";
                    } else {
                        $parent_guardian_1_id = trim($_POST["parent_guardian_1_id"]);
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

    // Validate parent/guardian 2 ID
    $input_parent_guardian_2_id = trim($_POST["parent_guardian_2_id"]);
    if (empty($input_parent_guardian_2_id)) {
        $parent_guardian_2_id_err = "Please select a parent/guardian OR Enter NULL.";
    }  elseif(trim($_POST["parent_guardian_2_id"]) == "NULL"){
        $parent_guardian_2_id = NULL;
    }elseif (!ctype_digit($input_parent_guardian_2_id)) {
        $parent_guardian_2_id_err = "Please enter valid number.";
    }  else {
        if($link = getDbConnection()){
            $sql = "SELECT ID FROM ParentGuardian";
            $stmt = mysqli_prepare($link, $sql);
            $valid = false;
        
            if($stmt) {
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_bind_result($stmt, $parent_guardian_2_id_db);
        
                    while (mysqli_stmt_fetch($stmt)) {
                        if ($parent_guardian_2_id_db == trim($_POST["parent_guardian_2_id"])) {
                            $valid = true;
                            break;
                        }
                    }
        
                    if (!$valid) {
                        $$parent_guardian_2_id_err = "Parent ID does not exist";
                    } else {
                        $parent_guardian_2_id = trim($_POST["parent_guardian_2_id"]);
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

    // Check input errors before inserting in database
    if (empty($pupil_name_err) && empty($pupil_address_err) && empty($pupil_age_err) && empty($pupil_height_err) && empty($pupil_weight_err) && empty($pupil_blood_group_err) && empty($class_enrolled_err) && empty($parent_guardian_1_id_err) && empty($parent_guardian_2_id_err)) {
        $link = getDbConnection();
        $sql = "INSERT INTO Pupil (Name,Address,Age,Height,Weight,BloodGroup,ClassEnrolledID,Parent_Guardian_1_ID,Parent_Guardian_2_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssiddsiii", $param_pupil_name, $param_pupil_address, $param_pupil_age, $param_pupil_height, $param_pupil_weight, $param_pupil_blood_group, $param_class_enrolled, $param_parent_guardian_1_id, $param_parent_guardian_2_id);
            
            $param_pupil_name = $pupil_name;
            $param_pupil_address = $pupil_address;
            $param_pupil_age = $pupil_age;
            $param_pupil_height = $pupil_height;
            $param_pupil_weight = $pupil_weight;
            $param_pupil_blood_group = $pupil_blood_group;
            $param_class_enrolled = $class_enrolled;
            $param_parent_guardian_1_id = $parent_guardian_1_id;
            $param_parent_guardian_2_id = $parent_guardian_2_id;
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Pupil added successfully.";
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
    <title>Add Pupil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/add_pages.css">
    
</head>
<body>
    <div class="wrapper">
        <h2>Add Pupil</h2>
        <p>Please fill this form to add a pupil.</p>
        <?php 
        if(!empty($success_msg)){
            echo '<div class="success">' . $success_msg . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($pupil_name_err)) ? 'has-error' : ''; ?>">
                <label>Name</label>
                <input type="text" name="pupil_name" class="form-control" value="<?php echo $pupil_name; ?>">
                <span class="help-block"><?php echo $pupil_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupil_address_err)) ? 'has-error' : ''; ?>">
                <label>Address</label>
                <input type="text" name="pupil_address" class="form-control" value="<?php echo $pupil_address; ?>">
                <span class="help-block"><?php echo $pupil_address_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupil_age_err)) ? 'has-error' : ''; ?>">
                <label>Age</label>
                <input type="text" name="pupil_age" class="form-control" value="<?php echo $pupil_age; ?>">
                <span class="help-block"><?php echo $pupil_age_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupil_height_err)) ? 'has-error' : ''; ?>">
                <label>Height</label>
                <input type="text" name="pupil_height" class="form-control" value="<?php echo $pupil_height; ?>">
                <span class="help-block"><?php echo $pupil_height_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupil_weight_err)) ? 'has-error' : ''; ?>">
                <label>Weight</label>
                <input type="text" name="pupil_weight" class="form-control" value="<?php echo $pupil_weight; ?>">
                <span class="help-block"><?php echo $pupil_weight_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($pupil_blood_group_err)) ? 'has-error' : ''; ?>">
                <label>Blood Group</label>
                <input type="text" name="pupil_blood_group" class="form-control" value="<?php echo $pupil_blood_group; ?>">
                <span class="help-block"><?php echo $pupil_blood_group_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($class_enrolled_err)) ? 'has-error' : ''; ?>">
                <label>Class Enrolled</label>
                <select name="class_enrolled" class="form-control">
                    <option value="">Select a class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class['ID']); ?>" <?php echo ($class_enrolled == $class['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                   
                </select>
                <span class="help-block"><?php echo $class_enrolled_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($parent_guardian_1_id_err)) ? 'has-error' : ''; ?>">
                <label>Parent/Guardian 1</label>
                <select name="parent_guardian_1_id" class="form-control">
                    <option value="">Select a parent/guardian</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?php echo htmlspecialchars($parent['ID']); ?>" <?php echo ($parent_guardian_1_id == $parent['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value = "NULL">
                        NULL
                    </option>
                </select>
                <span class="help-block"><?php echo $parent_guardian_1_id_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($parent_guardian_2_id_err)) ? 'has-error' : ''; ?>">
                <label>Parent/Guardian 2</label>
                <select name="parent_guardian_2_id" class="form-control">
                    <option value="">Select a parent/guardian</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?php echo htmlspecialchars($parent['ID']); ?>" <?php echo ($parent_guardian_2_id == $parent['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value = "NULL">
                        NULL
                    </option>
                </select>
                <span class="help-block"><?php echo $parent_guardian_2_id_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-action btn-action-primary" value="Submit">
            </div>
            <a href="dashboard.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
