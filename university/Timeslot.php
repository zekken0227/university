<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Slot Management</title>
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

// Create or Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $timeSlotId = $_POST['time_slot_id'] ?? null;

    // Use prepared statements to prevent SQL injection
    if ($timeSlotId) {
        // Update existing time slot
        $stmt = $conn->prepare("UPDATE TimeSlot SET start_time = ?, end_time = ? WHERE time_slot_id = ?");
        $stmt->bind_param("ssi", $startTime, $endTime, $timeSlotId);
    } else {
        // Insert new time slot
        $stmt = $conn->prepare("INSERT INTO TimeSlot (start_time, end_time) VALUES (?, ?)");
        $stmt->bind_param("ss", $startTime, $endTime);
    }
    
    if ($stmt->execute()) {
        echo "Time slot saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Read time slots
$result = $conn->query("SELECT time_slot_id, start_time, end_time FROM TimeSlot");

// Display time slots
echo "<h2>Time Slots</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Start Time</th><th>End Time</th><th>Actions</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['time_slot_id'] . "</td>";
    echo "<td>" . $row['start_time'] . "</td>";
    echo "<td>" . $row['end_time'] . "</td>";
    echo "<td>
            <a href='?edit=" . $row['time_slot_id'] . "'>Edit</a> | 
            <a href='?delete=" . $row['time_slot_id'] . "'>Delete</a>
          </td>";
    echo "</tr>";
}
echo "</table>";

// Delete time slot
if (isset($_GET['delete'])) {
    $timeSlotId = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM TimeSlot WHERE time_slot_id = ?");
    $deleteStmt->bind_param("i", $timeSlotId);
    if ($deleteStmt->execute()) {
        echo "Time slot deleted successfully.";
        header("Location: timeslot.php"); // Redirect to avoid re-submission
        exit;
    } else {
        echo "Error: " . $deleteStmt->error;
    }
    $deleteStmt->close();
}

// Edit time slot
$timeSlotToEdit = null;
if (isset($_GET['edit'])) {
    $timeSlotId = $_GET['edit'];
    $editStmt = $conn->prepare("SELECT * FROM TimeSlot WHERE time_slot_id = ?");
    $editStmt->bind_param("i", $timeSlotId);
    $editStmt->execute();
    $timeSlotToEdit = $editStmt->get_result()->fetch_assoc();
    $editStmt->close();
}
?>

<!-- Form for adding or editing a time slot -->
<form method="POST">
    <input type="hidden" name="time_slot_id" value="<?php echo $timeSlotToEdit['time_slot_id'] ?? ''; ?>">
    <input type="time" name="start_time" placeholder="Start Time" value="<?php echo $timeSlotToEdit['start_time'] ?? ''; ?>" required>
    <input type="time" name="end_time" placeholder="End Time" value="<?php echo $timeSlotToEdit['end_time'] ?? ''; ?>" required>
    <button type="submit"><?php echo $timeSlotToEdit ? 'Update Time Slot' : 'Add Time Slot'; ?></button>
</form>

<!-- Back button -->
<button onclick="window.location.href='index.php';">Back</button>

<?php
// Close the connection
$conn->close();
?>

</body>
</html>
