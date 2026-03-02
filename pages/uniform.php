<?php
// uniform.php - Uniforms List with Add & Edit Modal, Sold, Pagination, History & Total Amount
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];

$current_page = 'uniforms';

$message = '';

// Handle Sold Action
if (isset($_GET['action']) && $_GET['action'] === 'sold' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, quantity FROM uniform WHERE uniform_id = $id"));
    if ($get && $get['quantity'] > 0) {
        $price_at_sale = $get['price'];
        mysqli_query($conn, "UPDATE uniform SET quantity = quantity - 1 WHERE uniform_id = $id");
        mysqli_query($conn, "INSERT INTO uniform_sales_history (uniform_id, quantity_sold, price_at_sale) 
                             VALUES ($id, 1, $price_at_sale)");
    }
    header("Location: uniform.php?msg=sold");
    exit;
}

// Handle Add Uniform (via modal POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_uniform') {
    $uniform_name      = mysqli_real_escape_string($conn, trim($_POST['uniform_name'] ?? ''));
    $category          = $_POST['category'] ?? '';
    $size              = mysqli_real_escape_string($conn, trim($_POST['size'] ?? ''));
    $color             = mysqli_real_escape_string($conn, trim($_POST['color'] ?? ''));
    $price             = floatval($_POST['price'] ?? 0);
    $quantity          = (int)($_POST['quantity'] ?? 0);
    $low_stock_limit   = (int)($_POST['low_stock_limit'] ?? 10);
    $supplier_id       = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    if (empty($uniform_name) || empty($category) || empty($size) || empty($color) || $price <= 0 || $quantity < 0) {
        $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                        <strong>Error:</strong> Please fill all required fields correctly.
                    </div>';
    } else {
        $supplier_sql = $supplier_id ? $supplier_id : 'NULL';
        $query = "INSERT INTO uniform 
                  (uniform_name, category, size, color, price, quantity, low_stock_limit, supplier_id, date_added)
                  VALUES 
                  ('$uniform_name', '$category', '$size', '$color', $price, $quantity, $low_stock_limit, $supplier_sql, NOW())";

        if (mysqli_query($conn, $query)) {
            $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-check-circle" style="font-size:20px;"></i>
                            Uniform added successfully!
                        </div>';
        } else {
            $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                            <strong>Error adding uniform:</strong> ' . mysqli_error($conn) . '
                        </div>';
        }
    }
}

// Handle Edit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_uniform') {
    $id                = (int)$_POST['uniform_id'];
    $uniform_name      = mysqli_real_escape_string($conn, trim($_POST['uniform_name']));
    $category          = $_POST['category'];
    $size              = mysqli_real_escape_string($conn, trim($_POST['size']));
    $color             = mysqli_real_escape_string($conn, trim($_POST['color']));
    $price             = floatval($_POST['price']);
    $quantity          = (int)$_POST['quantity'];
    $low_stock_limit   = (int)$_POST['low_stock_limit'];
    $supplier_id       = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    $supplier_sql = $supplier_id ? $supplier_id : 'NULL';

    $query = "UPDATE uniform SET 
                uniform_name      = '$uniform_name',
                category          = '$category',
                size              = '$size',
                color             = '$color',
                price             = $price,
                quantity          = $quantity,
                low_stock_limit   = $low_stock_limit,
                supplier_id       = $supplier_sql,
                updated_at        = NOW()
              WHERE uniform_id = $id";

    if (mysqli_query($conn, $query)) {
        $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-check-circle" style="font-size:20px;"></i>
                        Uniform updated successfully!
                    </div>';
    } else {
        $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                        <strong>Error updating:</strong> ' . mysqli_error($conn) . '
                    </div>';
    }
}

// Pagination (5 per page)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$total_query = "SELECT COUNT(*) as total FROM uniform";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'] ?? 0;
$pages = ceil($total / $limit);

// Fetch uniforms with pagination
$query = "SELECT u.*, s.supplier_name 
          FROM uniform u 
          LEFT JOIN suppliers s ON u.supplier_id = s.id 
          ORDER BY u.uniform_name ASC 
          LIMIT $offset, $limit";
$uniforms = mysqli_fetch_all(mysqli_query($conn, $query), MYSQLI_ASSOC);

// Fetch suppliers for add & edit modal (only uniform or both)
$suppliers = mysqli_fetch_all(mysqli_query($conn, "
    SELECT id, supplier_name 
    FROM suppliers 
    WHERE status = 'active' 
      AND supplier_type IN ('uniforms', 'both') 
    ORDER BY supplier_name ASC
"), MYSQLI_ASSOC);

// Fetch sales history (latest 20)
$history = mysqli_fetch_all(mysqli_query($conn, "
    SELECT h.*, u.uniform_name 
    FROM uniform_sales_history h 
    JOIN uniform u ON h.uniform_id = u.uniform_id 
    ORDER BY h.sold_at DESC LIMIT 20
"), MYSQLI_ASSOC);

// Total amount sold (quantity × price_at_sale)
$total_amount_query = "SELECT SUM(quantity_sold * price_at_sale) as total_amount FROM uniform_sales_history";
$total_amount = mysqli_fetch_assoc(mysqli_query($conn, $total_amount_query))['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Uniforms</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">

    <style>
        body { background:#f0f7f2; font-family:'Inter',sans-serif; color:#1e3c2c; margin:0; }
        .main-content { padding:24px 32px; }
        .dashboard { max-width:1440px; margin:0 auto; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; flex-wrap:wrap; gap:16px; }
        h1 { font-family:'Outfit',sans-serif; color:#1a4d2e; display:flex; align-items:center; gap:12px; }
        .btn-primary { background:#2e7d5e; color:white; border:none; border-radius:40px; padding:10px 24px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; }
        .btn-primary:hover { background:#1a4d2e; }
        .filter-bar { display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
        .filter-btn { padding:8px 16px; border-radius:40px; background:white; border:1px solid #d4e8da; cursor:pointer; }
        .filter-btn.active { background:#2e7d5e; color:white; border-color:#1a4d2e; }
        .table-container { background:white; border-radius:24px; overflow:hidden; border:1px solid #d4e8da; box-shadow:0 8px 20px rgba(0,0,0,0.06); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:14px 16px; text-align:left; }
        th { background:#e8f5e9; font-weight:600; color:#1a4d2e; }
        tr:hover { background:#f8fdfa; }
        .action-buttons { display:flex; gap:8px; }
        .edit-btn { background:#0288d1; color:white; border:none; padding:6px 12px; border-radius:30px; cursor:pointer; font-size:13px; }
        .sold-btn { background:#d32f2f; color:white; border:none; padding:6px 12px; border-radius:30px; cursor:pointer; font-size:13px; }
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center; }
        .modal-overlay.active { display:flex; }
        .modal-container { background:white; border-radius:24px; width:90%; max-width:700px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.2); }
        .modal-header { padding:24px; border-bottom:1px solid #e2f0e6; display:flex; justify-content:space-between; align-items:center; }
        .modal-body { padding:32px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:500; color:#1a4d2e; }
        .form-control { width:100%; padding:10px 14px; border:1px solid #d4e8da; border-radius:12px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-actions { display:flex; justify-content:flex-end; gap:12px; margin-top:24px; }
        .history-section { margin-top:40px; background:white; border-radius:24px; padding:24px; border:1px solid #d4e8da; }
        .pagination { margin-top:20px; text-align:center; }
        .pagination a { margin:0 8px; padding:8px 16px; border-radius:30px; background:#e8f5e9; text-decoration:none; color:#1a4d2e; }
        .pagination a.active { background:#2e7d5e; color:white; }
        .total-amount { font-size:20px; font-weight:600; color:#1a4d2e; text-align:center; margin:20px 0; }
    </style>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard">
        <div class="header">
            <h1><i class="fas fa-tshirt"></i> Uniform Management</h1>
            <button class="btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="fas fa-plus"></i> Add Uniform
            </button>
        </div>

        <?= $message ?>

        <!-- Filter -->
        <div class="filter-bar">
            <a href="?filter=all" class="filter-btn <?= $filter==='all'?'active':'' ?>">All</a>
            <a href="?filter=boys" class="filter-btn <?= $filter==='boys'?'active':'' ?>">Boys</a>
            <a href="?filter=girls" class="filter-btn <?= $filter==='girls'?'active':'' ?>">Girls</a>
            <a href="?filter=pe" class="filter-btn <?= $filter==='pe'?'active':'' ?>">PE Uniform</a>
            <a href="?filter=others" class="filter-btn <?= $filter==='others'?'active':'' ?>">Others</a>
        </div>

        <!-- Uniforms Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($uniforms)): ?>
                        <tr><td colspan="8" style="text-align:center; padding:60px;">No uniforms yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($uniforms as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['uniform_name']) ?></td>
                                <td><?= htmlspecialchars($u['category']) ?></td>
                                <td><?= htmlspecialchars($u['size']) ?></td>
                                <td><?= htmlspecialchars($u['color']) ?></td>
                                <td>₱<?= number_format($u['price'], 2) ?></td>
                                <td><?= number_format($u['quantity']) ?> pcs</td>
                                <td><?= htmlspecialchars($u['supplier_name'] ?? '—') ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="openEditModal(
                                        <?= $u['uniform_id'] ?>,
                                        '<?= addslashes($u['uniform_name']) ?>',
                                        '<?= $u['category'] ?>',
                                        '<?= addslashes($u['size']) ?>',
                                        '<?= addslashes($u['color']) ?>',
                                        <?= $u['price'] ?>,
                                        <?= $u['quantity'] ?>,
                                        <?= $u['low_stock_limit'] ?>,
                                        <?= $u['supplier_id'] ?? 'null' ?>
                                    )">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="sold-btn" onclick="if(confirm('Sold 1 piece?')) location.href='?action=sold&id=<?= $u['uniform_id'] ?>'">
                                        Sold
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $pages): ?>
                <a href="?page=<?= $page+1 ?>">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Total Amount Sold -->
        <div class="total-amount">
            Total Amount from Sales: <span style="color:#2e7d5e;">₱<?= number_format($total_amount, 2) ?></span>
        </div>

        <!-- Sales History -->
        <div class="history-section">
            <h3 style="margin-bottom:16px; color:#1a4d2e;">Recent Sales History</h3>
            <?php if (empty($history)): ?>
                <p style="color:#4a6b57;">No sales recorded yet.</p>
            <?php else: ?>
                <ul style="list-style:none; padding:0;">
                    <?php foreach ($history as $h): ?>
                        <li style="padding:12px 0; border-bottom:1px solid #e2f0e6;">
                            <strong><?= htmlspecialchars($h['uniform_name']) ?></strong> —
                            <span style="color:#d32f2f;">Sold <?= $h['quantity_sold'] ?> pcs</span>
                            (₱<?= number_format($h['price_at_sale'] * $h['quantity_sold'], 2) ?>) on 
                            <?= date('M d, Y g:i A', strtotime($h['sold_at'])) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Uniform Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Uniform</h2>
            <button onclick="document.getElementById('addModal').classList.remove('active')" style="background:none;border:none;font-size:28px;cursor:pointer;color:#4a6b57;">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_uniform">

                <div class="form-group">
                    <label>Uniform Name</label>
                    <input type="text" name="uniform_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Male">Boys</option>
                            <option value="Female">Girls</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size</label>
                        <input type="text" name="size" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" min="1" value="10" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">No Supplier</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['supplier_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="document.getElementById('addModal').classList.remove('active')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Add Uniform</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Uniform</h2>
            <button onclick="document.getElementById('editModal').classList.remove('active')" style="background:none;border:none;font-size:28px;cursor:pointer;color:#4a6b57;">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_uniform">
                <input type="hidden" name="uniform_id" id="edit_uniform_id">

                <div class="form-group">
                    <label>Uniform Name</label>
                    <input type="text" name="uniform_name" id="edit_uniform_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="edit_category" class="form-control" required>
                            <option value="Male">Boys</option>
                            <option value="Female">Girls</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size</label>
                        <input type="text" name="size" id="edit_size" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" id="edit_color" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" id="edit_low_stock_limit" min="1" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" id="edit_supplier_id" class="form-control">
                        <option value="">No Supplier</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['supplier_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="document.getElementById('editModal').classList.remove('active')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, cat, size, color, price, qty, limit, sup) {
    document.getElementById('edit_uniform_id').value = id;
    document.getElementById('edit_uniform_name').value = name;
    document.getElementById('edit_category').value = cat;
    document.getElementById('edit_size').value = size;
    document.getElementById('edit_color').value = color;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_quantity').value = qty;
    document.getElementById('edit_low_stock_limit').value = limit;
    document.getElementById('edit_supplier_id').value = sup || '';
    document.getElementById('editModal').classList.add('active');
}
</script>

</body>
</html>