<?php
session_start();
include 'config/db.php';
include 'includes/sidebar.php';

// Count totals
$total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_customers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0];
$total_invoices = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM invoices"))[0];
$total_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_amount) FROM invoices"))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zithex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a2e, #16213e);
            position: fixed;
            top: 0; left: 0;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 25px 20px;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu { list-style: none; padding: 15px 0; }
        .sidebar-menu a {
            display: block;
            padding: 13px 25px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            color: #fff;
            background: rgba(233,69,96,0.2);
            border-left: 3px solid #e94560;
            padding-left: 22px;
        }
        .sidebar-menu a i { margin-right: 10px; width: 20px; }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .toggle-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }
        .user-info {
            color: #333;
            font-weight: 600;
        }
        .page-content { padding: 30px; }
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }
        .stat-icon.red { background: linear-gradient(135deg, #e94560, #c23152); }
        .stat-icon.blue { background: linear-gradient(135deg, #0f3460, #1a6090); }
        .stat-icon.green { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-icon.orange { background: linear-gradient(135deg, #f7971e, #ffd200); }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }
        .stat-label {
            font-size: 14px;
            color: #888;
            margin-top: 5px;
        }
        .welcome-card {
            background: linear-gradient(135deg, #1a1a2e, #e94560);
            border-radius: 15px;
            padding: 30px;
            color: #fff;
            margin-bottom: 25px;
        }
        .welcome-card h3 { font-size: 22px; font-weight: 700; }
        .welcome-card p { opacity: 0.8; margin: 0; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/header.php'; ?>

    <div class="page-content">
        <div class="welcome-card">
            <h3>👋 Welcome back, <?php echo $_SESSION['user_name']; ?>!</h3>
            <p>Here's what's happening with your business today.</p>
        </div>

        <h4 class="page-title">Dashboard Overview</h4>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $total_products; ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $total_customers; ?></div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $total_invoices; ?></div>
                        <div class="stat-label">Total Invoices</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div>
                        <div class="stat-number">₹<?php echo number_format($total_revenue ?? 0, 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggleBtn').addEventListener('click', function() {
        document.getElementById('sidebar').style.width =
            document.getElementById('sidebar').style.width === '0px' ? '250px' : '0px';
    });
</script>
</body>
</html>