<?php
include_once "db_config.php";

$message = "";

/* ------------------------
   INSERT MANUFACTURER
------------------------ */
if (isset($_POST['insert'])) {
    $name       = trim($_POST['name']);
    $address    = trim($_POST['address']);
    $contact_no = trim($_POST['contact_no']);

    $stmt = $db->prepare("CALL insert_manufacturer(?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . $db->error);

    $stmt->bind_param("sss", $name, $address, $contact_no);
    if ($stmt->execute()) {
        $message = "✅ Manufacturer inserted successfully.";
    } else {
        $message = "❌ Insert failed: " . $stmt->error;
    }

    // Clear extra result sets (important for stored procedures)
    while ($db->more_results() && $db->next_result()) { }
    $stmt->close();
}

/* ------------------------
   DELETE MANUFACTURER
------------------------ */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM manufacturer WHERE id=?");
    if (!$stmt) die("Prepare failed: " . $db->error);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "✅ Manufacturer deleted successfully.";
    } else {
        $message = "❌ Delete failed: " . $stmt->error;
    }
    $stmt->close();
}

/* ------------------------
   FETCH RECORD FOR EDIT
------------------------ */
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM manufacturer WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editData = $result->fetch_object();
    $stmt->close();
}

/* ------------------------
   UPDATE MANUFACTURER
------------------------ */
if (isset($_POST['update'])) {
    $id         = (int)$_POST['id'];
    $name       = trim($_POST['name']);
    $address    = trim($_POST['address']);
    $contact_no = trim($_POST['contact_no']);

    $stmt = $db->prepare("UPDATE manufacturer SET name=?, address=?, contact_no=? WHERE id=?");
    if (!$stmt) die("Prepare failed: " . $db->error);

    $stmt->bind_param("sssi", $name, $address, $contact_no, $id);
    if ($stmt->execute()) {
        $message = "✅ Manufacturer updated successfully.";
    } else {
        $message = "❌ Update failed: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manufacturer Management</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
form { margin-bottom: 20px; }
label { display: block; margin-top: 10px; }
input { padding: 5px; width: 250px; }
button { margin-top: 10px; padding: 5px 12px; cursor: pointer; }
table { border-collapse: collapse; width: 80%; margin-top: 20px; }
th, td { border: 1px solid #000; padding: 8px; text-align: center; }
th { background: #f2f2f2; }
.success-msg { color: green; margin-bottom: 10px; }
.action-btn { padding: 5px 12px; color: white; text-decoration: none; display:inline-block; }
.edit-btn { background: teal; }
.delete-btn { background: crimson; }
.action-btn:hover { opacity: 0.9; }
</style>
</head>
<body>

<h2><?php echo $editData ? 'Edit Manufacturer' : 'Insert New Manufacturer'; ?></h2>

<?php if ($message): ?>
    <p class="success-msg"><?php echo $message; ?></p>
<?php endif; ?>

<form action="" method="post">
    <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?php echo $editData->id; ?>">
    <?php endif; ?>

    <label>Name:</label>
    <input type="text" name="name" required value="<?php echo $editData->name ?? ''; ?>">

    <label>Address:</label>
    <input type="text" name="address" required value="<?php echo $editData->address ?? ''; ?>">

    <label>Contact No:</label>
    <input type="text" name="contact_no" required value="<?php echo $editData->contact_no ?? ''; ?>">

    <?php if ($editData): ?>
        <button type="submit" name="update">Update Manufacturer</button>
    <?php else: ?>
        <button type="submit" name="insert">Insert Manufacturer</button>
    <?php endif; ?>
</form>

<h2>All Manufacturers</h2>
<?php
$stmt = $db->query("SELECT * FROM manufacturer");
echo "<table>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Contact No</th>
            <th>Action</th>
        </tr>";

while ($row = $stmt->fetch_object()) {
    echo "<tr>
            <td>{$row->name}</td>
            <td>{$row->address}</td>
            <td>{$row->contact_no}</td>
            <td>
                <a href='?edit={$row->id}' class='action-btn edit-btn'>Edit</a>
                <a href='insert_manufacturer.php?delete={$row->id}' class='action-btn delete-btn' onclick=\"return confirm('Are you sure you want to delete this manufacturer?');\">Delete</a>
            </td>
          </tr>";
}
echo "</table>";
?>
</body>
</html>
