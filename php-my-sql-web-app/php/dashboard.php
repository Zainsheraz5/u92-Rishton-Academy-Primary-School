<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once 'db.php';

function fetchData($table) {
    $link = getDbConnection();
    $sql = "SELECT * FROM $table";
    $result = mysqli_query($link, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($link);
    return $data;
}

$classes = fetchData('Classes');
$pupils = fetchData('Pupil');
$parents_guardians = fetchData('ParentGuardian');
$teachers = fetchData('Teacher');
$teaching_assistants = fetchData('TeachingAssistant');

// Fetch additional data for displaying names
$link = getDbConnection();

$teachers_lookup = [];
$teachers_result = mysqli_query($link, "SELECT ID, Name FROM Teacher");
while ($row = mysqli_fetch_assoc($teachers_result)) {
    $teachers_lookup[$row['ID']] = $row['Name'];
}

$parent_guardians_lookup = [];
$parents_result = mysqli_query($link, "SELECT ID, Name FROM ParentGuardian");
while ($row = mysqli_fetch_assoc($parents_result)) {
    $parent_guardians_lookup[$row['ID']] = $row['Name'];
}

$class_lookup = [];
$classes_result = mysqli_query($link, "SELECT ID, Name FROM Classes");
while ($row = mysqli_fetch_assoc($classes_result)) {
    $class_lookup[$row['ID']] = $row['Name'];
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">

    <script src="js/scripts.js" defer></script>
    <script>
        function toggleTable(tableId){
            var table = document.getElementById(tableId);
            if(table.style.display === "none" || table.style.display === ""){
                table.style.display = "block";
            } else if(table.style.display === "block"){
                table.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <h1 id="MainTitle">Welcome to U92 Rishton Academy Management System</h1>

    <div class="wrapper">
        <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
        <h2>Dashboard</h2>
        <a href="logout.php" class="btn btn-danger signout-btn">Sign Out</a>
        <h3>Manage Records</h3>
        
        <div>
            <button onclick="toggleTable('classes')">Toggle Classes</button>
            <div id="classes" class="table-container">
                <h4>Classes</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Class Type</th>
                            <th>Class Name</th>
                            <th>Class Capacity</th>
                            <th>Pupils Amount</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['ID']); ?></td>
                                <td><?php echo htmlspecialchars($class['ClassType']); ?></td>
                                <td><?php echo htmlspecialchars($class['Name']); ?></td>
                                <td><?php echo htmlspecialchars($class['Capacity']); ?></td>
                                <td><?php echo htmlspecialchars($class['PupilAmount']); ?></td>
                                <td><?php echo htmlspecialchars($teachers_lookup[$class['TeacherID']] ?? 'Unknown'); ?></td>
                                <td>
                                    <a href="edit_class.php?id=<?php echo $class['ID']; ?>" class="btn-action btn-action-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div>
                    <a href="add_class.php" class="btn-action btn-action-primary">Add Class</a>
                </div>
            </div>
        </div>

        <div>
            <button onclick="toggleTable('pupils')">Toggle Pupils</button>
            <div id="pupils" class="table-container">
                <h4>Pupils</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Age</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Blood Group<th>
                            <th>Class Enrolled</th>
                            <th>Parent/Guardian 1</th>
                            <th>Parent/Guardian 2</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pupils as $pupil): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pupil['ID']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['Name']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['Address']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['Age']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['Height']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['Weight']); ?></td>
                                <td><?php echo htmlspecialchars($pupil['BloodGroup']); ?> </td>
                                <td><?php echo htmlspecialchars($class_lookup[$pupil['ClassEnrolledID']] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardians_lookup[$pupil['Parent_Guardian_1_ID']] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardians_lookup[$pupil['Parent_Guardian_2_ID']] ?? 'Unknown'); ?></td>
                                <td>
                                    <a href="edit_pupil.php?id=<?php echo $pupil['ID']; ?>" class="btn-action btn-action-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div>
                    <a href="add_pupil.php" class="btn-action btn-action-primary">Add Pupil</a>
                </div>
            </div>
        </div>

        <div>
            <button onclick="toggleTable('parents_guardians')">Toggle Parents/Guardians</button>
            <div id="parents_guardians" class="table-container">
                <h4>Parents/Guardians</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parents_guardians as $parent_guardian): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($parent_guardian['ID']); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardian['Name']); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardian['Address']); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardian['Email']); ?></td>
                                <td><?php echo htmlspecialchars($parent_guardian['PhoneNumber']); ?></td>
                                <td>
                                    <a href="edit_parent_guardian.php?id=<?php echo $parent_guardian['ID']; ?>" class="btn-action btn-action-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div>
                    <a href="add_parent_guardian.php" class="btn-action btn-action-primary">Add Parent/Guardian</a>
                </div>
            </div>
        </div>

        <div>
            <button onclick="toggleTable('teachers')">Toggle Teachers</button>
            <div id="teachers" class="table-container">
                <h4>Teachers</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Annual Salary</th>
                            <th>Background Check</th>
                            <th>Background Summary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['ID']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['Name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['Address']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['PhoneNumber']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['AnnualSalary']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['BackgroundCheck']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['BackgroundSummary']); ?></td>
                                <td>
                                    <a href="edit_teacher.php?id=<?php echo $teacher['ID']; ?>" class="btn-action btn-action-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div>
                    <a href="add_teacher.php" class="btn-action btn-action-primary">Add Teacher</a>
                </div>
            </div>
        </div>

        <div>
            <button onclick="toggleTable('teaching_assistants')">Toggle Teaching Assistants</button>
            <div id="teaching_assistants" class="table-container">
                <h4>Teaching Assistants</h4>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Salary</th>
                            <th>Teacher Assigned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teaching_assistants as $teaching_assistant): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teaching_assistant['ID']); ?></td>
                                <td><?php echo htmlspecialchars($teaching_assistant['Name']); ?></td>
                                <td><?php echo htmlspecialchars($teaching_assistant['Address']); ?></td>
                                <td><?php echo htmlspecialchars($teaching_assistant['Phone']); ?></td>
                                <td><?php echo htmlspecialchars($teaching_assistant['Salary']); ?></td>
                                <td><?php echo htmlspecialchars($teachers_lookup[$teaching_assistant['AssignedToTeacherID']] ?? 'Unknown'); ?></td>
                                <td>
                                    <a href="edit_teaching_assistant.php?id=<?php echo $teaching_assistant['ID']; ?>" class="btn-action btn-action-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div>
                    <a href="add_teaching_assistant.php" class="btn-action btn-action-primary">Add Teaching Assistant</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
