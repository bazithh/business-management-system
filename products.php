<?php
session_start();
include 'config/db.php';
include 'includes/sidebar.php';

// Add Product
if(isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    mysqli_query($conn, "INSERT INTO products (name, category, price, stock) VALUES ('$name', '$category', '$price', '$stock')");
    header('Location: products.php');
}

// Delete Product
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header('Location: products.php');
}

// Fetch all products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Zithex</title>
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
        .badge-category {
            background: rgba(15,52,96,0.1);
            color: #0f3460;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-stock {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .stock-ok { background: rgba(17,153,142,0.1); color: #11998e; }
        .stock-low { background: rgba(233,69,96,0.1); color: #e94560; }
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
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
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
            <h4 class="page-title">📦 Products</h4>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i>Add Product
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if(mysqli_num_rows($products) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($row = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><span class="badge-category"><?php echo $row['category']; ?></span></td>
                                <td><strong>₹<?php echo number_format($row['price'], 2); ?></strong></td>
                                <td>
                                    <span class="badge-stock <?php echo $row['stock'] > 10 ? 'stock-ok' : 'stock-low'; ?>">
                                        <?php echo $row['stock']; ?> units
                                    </span>
                                </td>
                                <td>
                                    <a href="products.php?delete=<?php echo $row['id']; ?>"
                                       onclick="return confirm('Delete this product?')"
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
                    <i class="fas fa-box"></i>
                    <p>No products yet. Add your first product!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Enter product name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control"
                               placeholder="e.g. Electronics, Food, Clothing" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" name="price" class="form-control"
                               placeholder="0.00" step="0.01" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock" class="form-control"
                               placeholder="0" required>
                    </div>
                    <button type="submit" name="add_product" class="btn-add w-100">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>