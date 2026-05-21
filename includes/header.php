<nav class="top-navbar">
    <button class="toggle-btn" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="navbar-right">
        <div class="user-info">
            <i class="fas fa-user-circle me-2"></i>
            <?php echo $_SESSION['user_name']; ?>
        </div>
    </div>
</nav>

<footer style="position:fixed; bottom:0; left:250px; right:0; text-align:center; padding:12px; font-size:13px; color:#aaa; border-top:1px solid #eee; background:#fff; z-index:999;">
    © 2026 <strong>Abdul Bazith (Zithex)</strong> | Full Stack Developer
</footer>
