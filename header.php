<header>
    <div class="logo">
        <img src="virtual3.png" alt="">
        <a style="text-decoration: none;" href="index.php">VIRTUAL</a>
    </div>
    <div class="username">
        <?php 
        if (isset($_SESSION['username'])) {
            echo '<a href="profile.php">Welcome, ' . htmlspecialchars($_SESSION['username']) . '</a>';
        }
        ?>
    </div>
</header>