<header class="bg-gray-800 p-4 fixed top-0 w-full">
    <a href="index.php" class="text-white hover:text-gray-300 px-4 py-2">HOME</a>
    <a href="createUser.php" class="text-white hover:text-gray-300 px-4 py-2">REGISTER</a>
    <a href="loginUser.php" class="text-white hover:text-gray-300 px-4 py-2">LOGIN</a>
    <a href="profile.php" class="text-white hover:text-gray-300 px-4 py-2">PROFILE</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="statistics.php" class="text-white hover:text-gray-300 px-4 py-2">STATISTIQUES</a>
    <?php endif; ?>
</header>
