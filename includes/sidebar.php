<?php if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); } ?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-store me-2"></i>
        <span> Zithex</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='products.php'?'active':''; ?>">
                <i class="fas fa-box"></i> Products
            </a>
        </li>
        <li>
            <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='customers.php'?'active':''; ?>">
                <i class="fas fa-users"></i> Customers
            </a>
        </li>
        <li>
            <a href="invoices.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='invoices.php'?'active':''; ?>">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
        </li>
        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>