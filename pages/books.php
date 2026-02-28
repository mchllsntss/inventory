<?php
// books.php - Books List with Add & Edit Modal, Sold, Pagination, History & Total Amount
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];

$current_page = 'books';

$message = '';

// Handle Sold Action
if (isset($_GET['action']) && $_GET['action'] === 'sold' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, quantity FROM books WHERE book_id = $id"));
    if ($get && $get['quantity'] > 0) {
        $price_at_sale = $get['price'];
        mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE book_id = $id");
        mysqli_query($conn, "INSERT INTO book_sales_history (book_id, quantity_sold, price_at_sale) 
                             VALUES ($id, 1, $price_at_sale)");
    }
    header("Location: books.php?msg=sold");
    exit;
}

// Handle Add Book (via modal POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_book') {
    $book_name         = mysqli_real_escape_string($conn, trim($_POST['book_name'] ?? ''));
    $grade_level       = $_POST['grade_level'] ?? '';
    $subject           = mysqli_real_escape_string($conn, trim($_POST['subject'] ?? ''));
    $price             = floatval($_POST['price'] ?? 0);
    $quantity          = (int)($_POST['quantity'] ?? 0);
    $low_stock_limit   = (int)($_POST['low_stock_limit'] ?? 10);
    $supplier_id       = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    if (empty($book_name) || empty($grade_level) || empty($subject) || $price <= 0 || $quantity < 0) {
        $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                        <strong>Error:</strong> Please fill all required fields correctly.
                    </div>';
    } else {
        $supplier_sql = $supplier_id ? $supplier_id : 'NULL';
        $query = "INSERT INTO books 
                  (book_name, grade_level, subject, price, quantity, low_stock_limit, supplier_id, date_added)
                  VALUES 
                  ('$book_name', '$grade_level', '$subject', $price, $quantity, $low_stock_limit, $supplier_sql, NOW())";

        if (mysqli_query($conn, $query)) {
            $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-check-circle" style="font-size:20px;"></i>
                            Book added successfully!
                        </div>';
        } else {
            $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                            <strong>Error adding book:</strong> ' . mysqli_error($conn) . '
                        </div>';
        }
    }
}

// Handle Edit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_book') {
    $id                = (int)$_POST['book_id'];
    $book_name         = mysqli_real_escape_string($conn, trim($_POST['book_name']));
    $grade_level       = $_POST['grade_level'];
    $subject           = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $price             = floatval($_POST['price']);
    $quantity          = (int)$_POST['quantity'];
    $low_stock_limit   = (int)$_POST['low_stock_limit'];
    $supplier_id       = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    $supplier_sql = $supplier_id ? $supplier_id : 'NULL';

    $query = "UPDATE books SET 
                book_name         = '$book_name',
                grade_level       = '$grade_level',
                subject           = '$subject',
                price             = $price,
                quantity          = $quantity,
                low_stock_limit   = $low_stock_limit,
                supplier_id       = $supplier_sql,
                updated_at        = NOW()
              WHERE book_id = $id";

    if (mysqli_query($conn, $query)) {
        $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-check-circle" style="font-size:20px;"></i>
                        Book updated successfully!
                    </div>';
    } else {
        $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                        <strong>Error updating book:</strong> ' . mysqli_error($conn) . '
                    </div>';
    }
}

// Pagination (5 per page)
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$total_query = "SELECT COUNT(*) as total FROM books";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_query))['total'] ?? 0;
$pages = ceil($total / $limit);

// Fetch books with pagination
$query = "SELECT b.*, s.supplier_name 
          FROM books b 
          LEFT JOIN suppliers s ON b.supplier_id = s.id 
          ORDER BY b.book_name ASC 
          LIMIT $offset, $limit";
$books = mysqli_fetch_all(mysqli_query($conn, $query), MYSQLI_ASSOC);

// Fetch suppliers for add & edit modal (books only or both)
$suppliers = mysqli_fetch_all(mysqli_query($conn, "
    SELECT id, supplier_name 
    FROM suppliers 
    WHERE status = 'active' 
      AND supplier_type IN ('books', 'both') 
    ORDER BY supplier_name ASC
"), MYSQLI_ASSOC);

// Fetch sales history (latest 20)
$history = mysqli_fetch_all(mysqli_query($conn, "
    SELECT h.*, b.book_name 
    FROM book_sales_history h 
    JOIN books b ON h.book_id = b.book_id 
    ORDER BY h.sold_at DESC LIMIT 20
"), MYSQLI_ASSOC);

// Total amount sold (quantity × price_at_sale)
$total_amount_query = "SELECT SUM(quantity_sold * price_at_sale) as total_amount FROM book_sales_history";
$total_amount = mysqli_fetch_assoc(mysqli_query($conn, $total_amount_query))['total_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Books</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">

    <!-- Same CSS as uniform.php -->
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
            <h1><i class="fas fa-book"></i> Books Management</h1>
            <button class="btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
                <i class="fas fa-plus"></i> Add Book
            </button>
        </div>

        <?= $message ?>

        <!-- Filter -->
        <div class="filter-bar">
            <a href="?filter=all" class="filter-btn <?= $filter==='all'?'active':'' ?>">All</a>
            <a href="?filter=elementary" class="filter-btn <?= $filter==='elementary'?'active':'' ?>">Elementary</a>
            <a href="?filter=highschool" class="filter-btn <?= $filter==='highschool'?'active':'' ?>">High School</a>
            <a href="?filter=others" class="filter-btn <?= $filter==='others'?'active':'' ?>">Others</a>
        </div>

        <!-- Books Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book Name</th>
                        <th>Grade Level</th>
                        <th>Subject</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr><td colspan="7" style="text-align:center; padding:60px;">No books yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($books as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['book_name']) ?></td>
                                <td><?= htmlspecialchars($b['grade_level']) ?></td>
                                <td><?= htmlspecialchars($b['subject']) ?></td>
                                <td>₱<?= number_format($b['price'], 2) ?></td>
                                <td><?= number_format($b['quantity']) ?> pcs</td>
                                <td><?= htmlspecialchars($b['supplier_name'] ?? '—') ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="openEditModal(
                                        <?= $b['book_id'] ?>,
                                        '<?= addslashes($b['book_name']) ?>',
                                        '<?= $b['grade_level'] ?>',
                                        '<?= addslashes($b['subject']) ?>',
                                        <?= $b['price'] ?>,
                                        <?= $b['quantity'] ?>,
                                        <?= $b['low_stock_limit'] ?>,
                                        <?= $b['supplier_id'] ?? 'null' ?>
                                    )">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="sold-btn" onclick="if(confirm('Sold 1 book?')) location.href='?action=sold&id=<?= $b['book_id'] ?>'">
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
            Total Amount from Book Sales: <span style="color:#2e7d5e;">₱<?= number_format($total_amount, 2) ?></span>
        </div>

        <!-- Sales History -->
        <div class="history-section">
            <h3 style="margin-bottom:16px; color:#1a4d2e;">Recent Book Sales History</h3>
            <?php if (empty($history)): ?>
                <p style="color:#4a6b57;">No sales recorded yet.</p>
            <?php else: ?>
                <ul style="list-style:none; padding:0;">
                    <?php foreach ($history as $h): ?>
                        <li style="padding:12px 0; border-bottom:1px solid #e2f0e6;">
                            <strong><?= htmlspecialchars($h['book_name']) ?></strong> —
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

<!-- Add Book Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Book</h2>
            <button onclick="document.getElementById('addModal').classList.remove('active')" style="background:none;border:none;font-size:28px;cursor:pointer;color:#4a6b57;">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_book">

                <div class="form-group">
                    <label>Book Name</label>
                    <input type="text" name="book_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Grade Level</label>
                        <select name="grade_level" class="form-control" required>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min="0" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" min="1" value="10" class="form-control">
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
                </div>

                <div class="form-actions">
                    <button type="button" onclick="document.getElementById('addModal').classList.remove('active')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Add Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Book</h2>
            <button onclick="document.getElementById('editModal').classList.remove('active')" style="background:none;border:none;font-size:28px;cursor:pointer;color:#4a6b57;">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_book">
                <input type="hidden" name="book_id" id="edit_book_id">

                <div class="form-group">
                    <label>Book Name</label>
                    <input type="text" name="book_name" id="edit_book_name" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Grade Level</label>
                        <select name="grade_level" id="edit_grade_level" class="form-control" required>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" id="edit_subject" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="edit_quantity" min="0" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" id="edit_low_stock_limit" min="1" class="form-control">
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
function openEditModal(id, name, grade, subj, price, qty, limit, sup) {
    document.getElementById('edit_book_id').value = id;
    document.getElementById('edit_book_name').value = name;
    document.getElementById('edit_grade_level').value = grade;
    document.getElementById('edit_subject').value = subj;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_quantity').value = qty;
    document.getElementById('edit_low_stock_limit').value = limit;
    document.getElementById('edit_supplier_id').value = sup || '';
    document.getElementById('editModal').classList.add('active');
}
</script>

</body>
</html>