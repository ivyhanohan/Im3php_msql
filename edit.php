<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $old_photo = $_POST['old_photo'] ?? '';
    $old_document = $_POST['old_document'] ?? '';

    $photo = $old_photo;
    $document = $old_document;

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/photos/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $photo = $target_dir . $photo_name;

        $imageFileType = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $photo)) {
                $photo = $old_photo;
            }
        } else {
            $photo = $old_photo;
        }
    }

    if (!empty($_FILES['document']['name'])) {
        $doc_dir = "uploads/documents/";
        if (!file_exists($doc_dir)) {
            mkdir($doc_dir, 0777, true);
        }
        $doc_name = time() . "_" . basename($_FILES["document"]["name"]);
        $document = $doc_dir . $doc_name;

        $docFileType = strtolower(pathinfo($document, PATHINFO_EXTENSION));
        $allowed_docs = ["pdf", "doc", "docx", "ppt", "pptx"];

        if (in_array($docFileType, $allowed_docs)) {
            if (!move_uploaded_file($_FILES["document"]["tmp_name"], $document)) {
                $document = $old_document;
            }
        } else {
            $document = $old_document;
        }
    }

    if (!empty($id) && !empty($first_name) && !empty($last_name) && !empty($email)) {
        $sql = "UPDATE guest_info SET first_name=?, last_name=?, email=?, photo=?, document=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $photo, $document, $id);

        if ($stmt->execute()) {
            echo "<script>window.location='index.php';</script>";
        } else {
            echo "<p class='text-red-500 text-center'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

$edit_first_name = $edit_last_name = $edit_email = $edit_photo = $edit_document = "";
if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql = "SELECT * FROM guest_info WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $edit_first_name = $row['first_name'];
        $edit_last_name = $row['last_name'];
        $edit_email = $row['email'];
        $edit_photo = $row['photo'];
        $edit_document = $row['document'];
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Edit Guest</h1>

        <form method="post" action="edit.php" enctype="multipart/form-data" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_id) ?>">
            <input type="hidden" name="old_photo" value="<?= htmlspecialchars($edit_photo) ?>">
            <input type="hidden" name="old_document" value="<?= htmlspecialchars($edit_document) ?>">

            <label class="block mb-2 font-semibold">First Name:</label>
            <input type="text" name="first_name" required value="<?= htmlspecialchars($edit_first_name) ?>" class="w-full p-2 border rounded-md mb-4">

            <label class="block mb-2 font-semibold">Last Name:</label>
            <input type="text" name="last_name" required value="<?= htmlspecialchars($edit_last_name) ?>" class="w-full p-2 border rounded-md mb-4">

            <label class="block mb-2 font-semibold">Email:</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($edit_email) ?>" class="w-full p-2 border rounded-md mb-4">

            <label class="block mb-2 font-semibold">Current Photo:</label>
            <?php if (!empty($edit_photo)): ?>
                <img src="<?= htmlspecialchars($edit_photo) ?>" alt="Guest Photo" class="w-32 h-32 object-cover rounded-md mb-4">
            <?php else: ?>
                <p class="text-gray-500 mb-4">No photo uploaded.</p>
            <?php endif; ?>

            <label class="block mb-2 font-semibold">Upload New Photo:</label>
            <input type="file" name="photo" class="w-full p-2 border rounded-md mb-4">

            <label class="block mb-2 font-semibold">Uploaded Document:</label>
            <?php if (!empty($edit_document)): ?>
                <a href="<?= htmlspecialchars($edit_document) ?>" target="_blank" class="text-blue-500 underline">View Document</a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">No document uploaded.</p>
            <?php endif; ?>

            <label class="block mb-2 font-semibold">Upload New Document:</label>
            <input type="file" name="document" class="w-full p-2 border rounded-md mb-4">

            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                Update Guest
            </button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
