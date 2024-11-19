<?php
session_start();
include '../db/db.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Mã hóa mật khẩu
    $full_name = $_POST['full_name'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $image = $_FILES['image'];

    // Kiểm tra lỗi upload
    if ($image['error'] !== UPLOAD_ERR_OK) {
        die("File upload error: " . $image['error']);
    }

    // Đường dẫn upload
    $uploadDir = "../uploads/";
    // Đặt lại tên file tải lên
    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $picturename = pathinfo($image['name'], PATHINFO_FILENAME) . '-' . time() . '.' . $extension;
    $uploadFile = $uploadDir . $picturename;

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username has been register";
    } else {
        // Di chuyển tệp đã tải lên
        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            // Chuẩn bị câu lệnh SQL để chèn dữ liệu
            $sql = "INSERT INTO users (username, password, full_name, dob, address, phone, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $username, $password, $full_name, $birthdate, $address, $phone, $picturename);
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Error uploading file.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Register</title>
</head>

<body>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="relative flex flex-col m-6 space-y-8 bg-white shadow-2xl rounded-2xl md:flex-row md:space-y-0">
            <!-- left side -->
            <div class="flex flex-col justify-center p-8 md:p-14">
                <span class="mb-3 text-4xl font-bold">Create Account</span>
                <span class="font-light text-gray-400 mb-8">Please fill in the details below</span>

                <form method="post" enctype="multipart/form-data">
                    <div class="py-4">
                        <span class="mb-2 text-md">Full Name</span>
                        <input
                            type="text"
                            name="full_name"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Username</span>
                        <input
                            type="text"
                            name="username"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Password</span>
                        <input
                            type="password"
                            name="password"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Birthdate</span>
                        <input
                            type="date"
                            name="birthdate"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Address</span>
                        <input
                            type="text"
                            name="address"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Phone Number</span>
                        <input
                            type="tel"
                            name="phone"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <div class="py-4">
                        <span class="mb-2 text-md">Profile Image</span>
                        <input
                            type="file"
                            name="image"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>

                    <button
                        type="submit"
                        name="submit"
                        class="w-full bg-black text-white p-2 rounded-lg mb-6 hover:bg-white hover:text-black hover:border hover:border-gray-300">
                        Register
                    </button>
                    <!-- <?php if (isset($error)) echo '<><div class="text-red-500">' . $error . '</div>'; ?>
                    <?php if (isset($success)) echo '<div class="text-green-500">' . $success . '</div>'; ?> -->
                    <?php if (isset($error)) : ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                alert("<?php echo $error; ?>");
                            });
                        </script>
                    <?php endif; ?>

                    <?php if (isset($success)) : ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                if (confirm("<?php echo $success; ?>")) {
                                    window.location.href = 'login.php';
                                } else {
                                    setTimeout(function() {
                                        window.location.href = 'login.php';
                                    }, 2000);
                                }
                            });
                        </script>
                    <?php endif; ?>
                </form>

                <div class="text-center text-gray-400">
                    Already have an account?
                    <a href="login.php" class="font-bold text-black">Login here</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>