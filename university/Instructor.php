<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Management</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>

<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'university');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $middleInitial = $_POST['middle_initial'];
    $lastName = $_POST['last_name'];
    $streetNumber = $_POST['street_number'];
    $streetName = $_POST['street_name'];
    $aptNumber = $_POST['apt_number'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postalCode = $_POST['postal_code'];
    $dateOfBirth = $_POST['date_of_birth'];
    $departmentId = $_POST['department_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO Instructor (first_name, middle_initial, last_name, street_number, street_name, apt_number, city, state, postal_code, date_of_birth, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssssssssi", $firstName, $middleInitial, $lastName, $streetNumber, $streetName, $aptNumber, $city, $state, $postalCode, $dateOfBirth, $departmentId);
    
    if ($stmt->execute()) {
        echo "Instructor added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Read instructors
$result = $conn->query("SELECT * FROM Instructor");

// Display instructors
echo "<h2>Instructors</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>First Name</th><th>Middle Initial</th><th>Last Name</th><th>Address</th><th>City</th><th>State</th><th>Postal Code</th><th>Date of Birth</th><th>Department ID</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['instructor_id'] . "</td>"; // Change 'ID' to 'instructor_id'
    echo "<td>" . $row['first_name'] . "</td>";
    echo "<td>" . $row['middle_initial'] . "</td>";
    echo "<td>" . $row['last_name'] . "</td>";
    echo "<td>" . $row['street_number'] . " " . $row['street_name'] . " " . $row['apt_number'] . "</td>";
    echo "<td>" . $row['city'] . "</td>";
    echo "<td>" . $row['state'] . "</td>";
    echo "<td>" . $row['postal_code'] . "</td>";
    echo "<td>" . $row['date_of_birth'] . "</td>";
    echo "<td>" . $row['department_id'] . "</td>"; // Display department_id
    echo "</tr>";
}
echo "</table>";
?>

<!-- Form for adding an instructor -->
<form method="POST">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="middle_initial" placeholder="Middle Initial">
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="street_number" placeholder="Street Number">
    <input type="text" name="street_name" placeholder="Street Name">
    <input type="text" name="apt_number" placeholder="Apt Number">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="state" placeholder="State">
    <input type="text" name="postal_code" placeholder="Postal Code">
    <input type="date" name="date_of_birth" required>
    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php
        // Populate department options
        $departments = $conn->query("SELECT department_id, department_name FROM Department");
        while ($dept = $departments->fetch_assoc()) {
            echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
        }
        ?>
    </select>
    <button type="submit">Add Instructor</button>
</form>

<!-- Back button -->
<button onclick="window.location.href='index.php';">Back</button>

<?php
// Close the connection
$conn->close();
?>

</body>
</html>
