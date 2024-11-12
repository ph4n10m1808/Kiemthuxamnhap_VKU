<?php
session_start();
include __DIR__ . '/db/db.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['login'])) {
    header("Location: ./auth/login.php");
    exit();
}
// Check id in URL
if (isset($_GET['id'])) {
    if ($_GET['id'] !== '') {
        $id = intval($_GET['id']);
    } else {
        session_destroy();
        header("Location: ./auth/login.php");
        exit();
    }
} else {
    $id = intval($_SESSION['id']);
}

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Tính tuổi
    $dob = new DateTime($user['dob']);
    $today = new DateTime('today');
    $age = $today->diff($dob)->y;
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
    <title>User Profile</title>
</head>

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <div class="flex items-center space-x-4">
                <!-- Avatar -->
                <img src="uploads/<?php echo $user['avatar']; ?>" alt="Avatar" class="w-16 h-16 rounded-full">
                <div>
                    <h1 class="text-2xl font-bold"><?php echo $user['full_name']; ?></h1>
                    <p class="text-gray-500">Username: <?php echo $user['username']; ?></p>
                </div>
            </div>
            <div class="mt-6">
                <p class="text-gray-700"><strong>Date of Birth:</strong> <?php echo date('d-m-Y', strtotime($user['dob'])); ?></p>
                <p class="text-gray-700"><strong>Age:</strong> <?php echo $age; ?> years</p>
                <p class="text-gray-700"><strong>Address:</strong> <?php echo $user['address']; ?></p>
                <p class="text-gray-700"><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
            </div>
            <div class="mt-8 flex justify-between">
                <a href="logout.php" class="text-blue-500 hover:underline">Logout</a>
                <a href="edit.php?id=<?php echo $id ?>" class="text-blue-500 hover:underline">Edit Profile</a>
            </div>
        </div>
    </div>
</body>

</html>