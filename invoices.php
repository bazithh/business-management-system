<?php
session_start();
include 'config/db.php';
include 'includes/sidebar.php';

// Create Invoice
if(isset($_POST['create_invoice'])) {
    $customer_id = $_POST['customer_id'];
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    $total_amount = 0;

    // Calculate total
    foreach($product_ids as $index => $product_id) {
        $qty = $quantities[$index];
        $price_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id=$product_id"));
        $total_amount += $price_row['price'] * $qty;
    }

    // Insert invoice
    mysqli_query($conn, "INSERT INTO invoices (customer_id, total_amount) VALUES ('$customer_id', '$total_amount')");
    $invoice_id = mysqli_insert_id($conn);

    // Insert invoice items
    foreach($product_ids as $index => $product_id) {
        $qty = $quantities[$index];
        $price_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE id=$product_id"));
        $price = $price_row['price'];
        mysqli_query($conn, "INSERT INTO invoice_items (invoice_id, product_id, quantity, price) VALUES ('$invoice_id', '$product_id', '$qty', '$price')");

        // Reduce stock
        mysqli_query($conn, "UPDATE products SET stock=stock-$qty WHERE id=$product_id");
    }

    header('Location: invoices.php');
}

// Delete Invoice
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM invoice_items WHERE invoice_id=$id");
    mysqli_query($conn, "DELETE FROM invoices WHERE id=$id");
    header('Location: invoices.php');
}

// Fetch all invoices
$invoices = mysqli_query($conn, "
    SELECT invoices.*, customers.name as customer_name
    FROM invoices
    LEFT JOIN customers ON invoices.customer_id = customers.id
    ORDER BY invoices.created_at DESC
");

// Fetch customers and products for form
$customers = mysqli_query($conn, "SELECT * FROM customers");
$products = mysqli_query($conn, "SELECT * FROM products WHERE stock > 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices | Zithex</title>
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
        .invoice-badge {
            background: rgba(15,52,96,0.1);
            color: #0f3460;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
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
        .btn-delete:hover { background: #e94560; color: #fff; }
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
        .product-row {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .btn-remove-row {
            background: rgba(233,69,96,0.1);
            color: #e94560;
            border: none;
            padding: 5px 10px;
            border-radius: 8px;
            cursor: pointer;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.3; }
        #total-display {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #fff;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include 'includes/header.php'; ?>

    <div class="page-content">
        <div class="page-header">
            <h4 class="page-title">🧾 Invoices</h4>
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="fas fa-plus me-2"></i>Create Invoice
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if(mysqli_num_rows($invoices) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($invoices)): ?>
                            <tr>
                                <td><span class="invoice-badge">INV-00<?php echo $row['id']; ?></span></td>
                                <td><strong><?php echo $row['customer_name']; ?></strong></td>
                                <td><strong style="color:#11998e;">₹<?php echo number_format($row['total_amount'], 2); ?></strong></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="invoices.php?delete=<?php echo $row['id']; ?>"
                                       onclick="return confirm('Delete this invoice?')"
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
                    <i class="fas fa-file-invoice"></i>
                    <p>No invoices yet. Create your first invoice!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>Create New Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            <?php while($c = mysqli_fetch_assoc($customers)): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <label class="form-label">Products</label>
                    <div id="product-rows">
                        <div class="product-row">
                            <div class="row g-2 align-items-center">
                                <div class="col-7">
                                    <select name="product_id[]" class="form-select product-select" required>
                                        <option value="">-- Select Product --</option>
                                        <?php
                                        // Reset products pointer
                                        mysqli_data_seek($products, 0);
                                        while($p = mysqli_fetch_assoc($products)):
                                        ?>
                                        <option value="<?php echo $p['id']; ?>"
                                                data-price="<?php echo $p['price']; ?>">
                                            <?php echo $p['name']; ?> (₹<?php echo $p['price']; ?>)
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="quantity[]"
                                           class="form-control qty-input"
                                           placeholder="Qty" min="1" value="1" required>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2 mb-3"
                            onclick="addProductRow()">
                        <i class="fas fa-plus me-1"></i> Add Another Product
                    </button>

                    <div id="total-display" class="mb-3">
                        Total: ₹0.00
                    </div>

                    <button type="submit" name="create_invoice" class="btn-add w-100">
                        <i class="fas fa-file-invoice me-2"></i>Create Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Product row template
    const productOptions = `<?php
        mysqli_data_seek($products, 0);
        while($p = mysqli_fetch_assoc($products)) {
            echo '<option value="'.$p['id'].'" data-price="'.$p['price'].'">'.$p['name'].' (₹'.$p['price'].')</option>';
        }
    ?>`;

    function addProductRow() {
        const row = document.createElement('div');
        row.className = 'product-row';
        row.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-7">
                    <select name="product_id[]" class="form-select product-select" required>
                        <option value="">-- Select Product --</option>
                        ${productOptions}
                    </select>
                </div>
                <div class="col-3">
                    <input type="number" name="quantity[]"
                           class="form-control qty-input"
                           placeholder="Qty" min="1" value="1" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        document.getElementById('product-rows').appendChild(row);
        attachListeners();
        calculateTotal();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.product-row');
        if(rows.length > 1) {
            btn.closest('.product-row').remove();
            calculateTotal();
        }
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const qty = row.querySelector('.qty-input');
            if(select.value && qty.value) {
                const price = parseFloat(select.options[select.selectedIndex].dataset.price) || 0;
                total += price * parseInt(qty.value);
            }
        });
        document.getElementById('total-display').textContent = 'Total: ₹' + total.toFixed(2);
    }

    function attachListeners() {
        document.querySelectorAll('.product-select, .qty-input').forEach(el => {
            el.addEventListener('change', calculateTotal);
            el.addEventListener('input', calculateTotal);
        });
    }

    attachListeners();
</script>
</body>
</html>