<?php
include 'database.php';

if (isset($_POST['add'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $photo = NULL;
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $photo = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $document = NULL;
    if (!empty($_FILES['document']['name'])) {
        $doc_dir = "documents/";
        if (!file_exists($doc_dir)) {
            mkdir($doc_dir, 0777, true);
        }
        $doc_name = time() . "_" . basename($_FILES['document']['name']);
        $document = $doc_dir . $doc_name;
        move_uploaded_file($_FILES['document']['tmp_name'], $document);
    }

    $stmt = $conn->prepare("INSERT INTO guest_info (first_name, last_name, email, photo, document) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $photo, $document);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-lg mx-auto bg-white p-6 shadow-md rounded-md">
        <h2 class="text-xl font-semibold text-center mb-4">Add Guest</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="first_name" placeholder="First Name" required class="w-full p-2 border mb-2">
            <input type="text" name="last_name" placeholder="Last Name" required class="w-full p-2 border mb-2">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border mb-2">
            <input type="file" name="photo" class="w-full p-2 border mb-2">
            <input type="file" name="document" class="w-full p-2 border mb-2">
            <button type="submit" name="add" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Submit</button>
        </form>
    </div>

    <div class="max-w-4xl mx-auto mt-8 bg-white p-6 shadow-md rounded-md">
        <h2 class="text-xl font-semibold text-center mb-4">Guest List</h2>
        <table class="w-full border-collapse border text-left">
            <thead>
                <tr class="bg-blue-500 text-white">
                    <th class="p-2">ID</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT id, first_name, last_name, email FROM guest_info");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='border-b'>
                            <td class='p-2'>{$row['id']}</td>
                            <td class='p-2'>{$row['first_name']} {$row['last_name']}</td>
                            <td class='p-2'>{$row['email']}</td>
                            <td class='p-2'>
                                <a href='edit.php?id={$row['id']}' class='text-blue-500'>Edit</a> |
                                <a href='delete.php?id={$row['id']}' class='text-red-500' onclick='return confirm("Are you sure?")'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='p-2 text-center'>No guests found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
