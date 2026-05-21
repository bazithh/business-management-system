<?php
session_start();
include 'config/db.php';
include 'includes/sidebar.php';

// Add Customer
if(isset($_POST['add_customer'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    mysqli_query($conn, "INSERT INTO customers (name, phone, email, address) VALUES ('$name', '$phone', '$email', '$address')");
    header('Location: customers.php');
}

// Delete Customer
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    header('Location: customers.php');
}

// Fetch all customers
$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | Zithex</title>
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
        .user-info { color: #333; font-weight: 600; }
        .page-content { padding: 30px; }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .btn-add {
            background: linear-gradient(135deg, #e94560, #c23152);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(233,69,96,0.4);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
        }
        .card-body { padding: 25px; }
        .table th {
            background: #f8f9fa;
            color: #555;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px;
        }
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f0f2f5;
            color: #333;
        }
        .table tbody tr:hover { background: #f8f9fa; }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e94560, #c23152);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
        }
        .btn-delete {
            background: rgba(233,69,96,0.1);
            color: #e94560;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-delete:hover {
            background: #e94560;
            color: #fff;
        }
        .modal-content { border-radius: 15px; border: none; }
        .modal-header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #fff;
            border-radius: 15px 15px 0 0;
            padding: 20px 25px;
        }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 25px; }
        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #e94560;
            box-shadow: 0 0 0 3px rgba(233,69,96,0.15);
        }
        .form-label { font-weight: 600; color: #555; font-size: 14px; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.3; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/header.php'; ?>

    <div class="page-content">
        <div class="page-header">
            <h4 class="page-title">👥 Customers</h4>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="fas fa-plus me-2"></i>Add Customer
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if(mysqli_num_rows($customers) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($row = mysqli_fetch_assoc($customers)): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div class="avatar">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <strong><?php echo $row['name']; ?></strong>
                                    </div>
                                </td>
                                <td><i class="fas fa-phone me-2 text-muted"></i><?php echo $row['phone']; ?></td>
                                <td><i class="fas fa-envelope me-2 text-muted"></i><?php echo $row['email']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td>
                                    <a href="customers.php?delete=<?php echo $row['id']; ?>"
                                       onclick="return confirm('Delete this customer?')"
                                       class="btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No customers yet. Add your first customer!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Enter customer name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Enter email address">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control"
                                  placeholder="Enter address" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_customer" class="btn-add w-100">
                        <i class="fas fa-plus me-2"></i>Add Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>