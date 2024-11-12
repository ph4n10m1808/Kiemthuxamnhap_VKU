<?php
session_start();
include '../db/db.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $results = $result->fetch_assoc();

        if (password_verify($password, $results['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['username_login'] = $results['username'];
            $_SESSION['id'] = $results['id'];
            header("Location: ../index.php?id=" . $results['id']);
            exit();
        } else {
            $error = "Invalid login. Please try again!";
        }
    } else {
        $error = "Invalid login. Please try again!";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login</title>
</head>

<body>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="relative flex flex-col m-6 space-y-8 bg-white shadow-2xl rounded-2xl md:flex-row md:space-y-0">
            <!-- left side -->
            <div class="flex flex-col justify-center p-8 md:p-14">
                <span class="mb-3 text-4xl font-bold">Welcome back</span>
                <span class="font-light text-gray-400 mb-8">Welcome back! Please enter your details</span>

                <!-- Form đăng nhập -->
                <form method="post">
                    <div class="py-4">
                        <span class="mb-2 text-md">Username</span>
                        <input
                            type="text"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            name="username"
                            id="username"
                            required />
                    </div>
                    <div class="py-4">
                        <span class="mb-2 text-md">Password</span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="w-full p-2 border border-gray-300 rounded-md placeholder:font-light placeholder:text-gray-500"
                            required />
                    </div>
                    <div class="flex justify-between w-full py-4">
                        <div class="mr-24">
                            <input type="checkbox" name="remember" id="remember" class="mr-2" />
                            <span class="text-md">Remember for 30 days</span>
                        </div>
                        <span class="font-bold text-md">Forgot password?</span>
                    </div>
                    <button
                        type="submit"
                        name="submit"
                        class="w-full bg-black text-white p-2 rounded-lg mb-6 hover:bg-white hover:text-black hover:border hover:border-gray-300">
                        Sign in
                    </button>
                </form>

                <!-- Hiển thị thông báo lỗi nếu có -->
                <?php if (isset($error)) echo '<div class="text-red-500">' . $error . '</div>'; ?>

                <div class="text-center text-gray-400">
                    Don't have an account?
                    <a href="register.php" class="font-bold text-black">Sign up for free</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>