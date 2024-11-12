<?php
session_start();
include './db/db.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['login']) ) {
    header("Location: auth/login.php");
    exit();
}

// Lấy ID người dùng từ URL
$userId = $_GET['id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Cập nhật thông tin người dùng
    $updateSql = "UPDATE users SET full_name = ?, dob = ?, address = ?, phone = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('ssssi', $name, $date_of_birth, $address, $phone, $userId);

    if ($updateStmt->execute()) {
        header("Location: index.php?id=" . $userId);
        exit();
    } else {
        $error = "Error updating user information.";
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit User</title>
</head>

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <h1 class="text-2xl font-bold mb-6">Edit User Information</h1>
            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white p-2 rounded mb-4"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo $user['full_name']; ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" />
                </div>
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo date('Y-m-d', strtotime($user['dob'])); ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" />
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo $user['address']; ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" />
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="<?php echo $user['phone']; ?>" required class="mt-1 p-2 border border-gray-300 rounded-md w-full" />
                </div>
                <button type="submit" name="update" class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">Update</button>
            </form>
            <div class="mt-4">
                <a href="index.php?id=<?php echo $userId; ?>" class="text-blue-500 hover:underline">Cancel</a>
            </div>
        </div>
    </div>
</body>

</html>