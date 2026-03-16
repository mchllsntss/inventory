<?php
// books.php - Books Management (with return & damaged + activity log)
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];
$current_page = 'books';
$message = '';

// ────────────────────────────────────────────────
// Handle Sold Action
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sold_book') {
    $id = (int)($_POST['book_id'] ?? 0);
    $qty_sold = (int)($_POST['quantity_sold'] ?? 0);
    if ($id <= 0 || $qty_sold <= 0) {
        $message = '<div class="alert error">Invalid quantity or book ID.</div>';
    } else {
        $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, quantity FROM books WHERE book_id = $id"));
        if (!$get) {
            $message = '<div class="alert error">Book not found.</div>';
        } elseif ($get['quantity'] < $qty_sold) {
            $message = '<div class="alert error">Not enough stock. Only ' . $get['quantity'] . ' available.</div>';
        } else {
            $price_at_sale = $get['price'];
            $note = date('Y-m-d h:i A') . ": Sold $qty_sold pcs";
            $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT activity_log FROM books WHERE book_id = $id"));
            $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

            mysqli_query($conn, "UPDATE books SET 
                quantity = quantity - $qty_sold,
                activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
                WHERE book_id = $id");
            mysqli_query($conn, "INSERT INTO book_sales_history (book_id, quantity_sold, price_at_sale) VALUES ($id, $qty_sold, $price_at_sale)");
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Successfully sold ' . $qty_sold . ' book(s)!</div>';
        }
    }
}

// ────────────────────────────────────────────────
// Handle Return Action
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return_book') {
    $id = (int)$_POST['book_id'];
    $qty_returned = (int)$_POST['quantity_returned'];
    $notes = mysqli_real_escape_string($conn, trim($_POST['return_notes'] ?? ''));

    if ($id > 0 && $qty_returned > 0) {
        $note = date('Y-m-d h:i A') . ": Returned $qty_returned pcs" . ($notes ? " - $notes" : "");
        $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, price, activity_log FROM books WHERE book_id = $id"));
        $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

        mysqli_query($conn, "UPDATE books SET 
            quantity = quantity + $qty_returned,
            activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
            WHERE book_id = $id");

        $price_at_sale = $current['price'];
        mysqli_query($conn, "INSERT INTO book_sales_history 
            (book_id, quantity_sold, price_at_sale, sold_at) 
            VALUES ($id, -$qty_returned, $price_at_sale, NOW())");

        $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Return recorded! Stock and total sales adjusted.</div>';
    } else {
        $message = '<div class="alert error">Invalid input.</div>';
    }
}

// ────────────────────────────────────────────────
// Handle Damaged Action
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'damaged_book') {
    $id = (int)$_POST['book_id'];
    $qty_damaged = (int)$_POST['quantity_damaged'];
    $reason = mysqli_real_escape_string($conn, trim($_POST['damaged_reason'] ?? ''));

    if ($id > 0 && $qty_damaged > 0) {
        $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, price, activity_log FROM books WHERE book_id = $id"));
        if ($current['quantity'] < $qty_damaged) {
            $message = '<div class="alert error">Not enough stock to mark as damaged.</div>';
        } else {
            $note = date('Y-m-d h:i A') . ": Damaged $qty_damaged pcs" . ($reason ? " - $reason" : "");
            $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

            mysqli_query($conn, "UPDATE books SET 
                quantity = quantity - $qty_damaged,
                activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
                WHERE book_id = $id");

            $price_at_sale = $current['price'];
            mysqli_query($conn, "INSERT INTO book_sales_history 
                (book_id, quantity_sold, price_at_sale, sold_at) 
                VALUES ($id, -$qty_damaged, $price_at_sale, NOW())");

            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Damaged recorded. Stock and total sales adjusted.</div>';
        }
    } else {
        $message = '<div class="alert error">Invalid input.</div>';
    }
}

// ────────────────────────────────────────────────
// Handle Add Book
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_book') {
    $book_name = mysqli_real_escape_string($conn, trim($_POST['book_name'] ?? ''));
    $grade_level = $_POST['grade_level'] ?? '';
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject'] ?? ''));
    $price = floatval($_POST['price'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $low_stock_limit = (int)($_POST['low_stock_limit'] ?? 10);
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    if (empty($book_name) || empty($grade_level) || empty($subject) || $price <= 0 || $quantity < 0) {
        $message = '<div class="alert error">Please fill all required fields correctly.</div>';
    } else {
        $supplier_sql = $supplier_id ? $supplier_id : 'NULL';
        $query = "INSERT INTO books (book_name, grade_level, subject, price, quantity, low_stock_limit, supplier_id, date_added)
                  VALUES ('$book_name', '$grade_level', '$subject', $price, $quantity, $low_stock_limit, $supplier_sql, NOW())";
        if (mysqli_query($conn, $query)) {
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Book added successfully!</div>';
        } else {
            $message = '<div class="alert error">Error adding book: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// ────────────────────────────────────────────────
// Handle Edit Book
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_book') {
    $id = (int)$_POST['book_id'];
    $book_name = mysqli_real_escape_string($conn, trim($_POST['book_name']));
    $grade_level = $_POST['grade_level'];
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $price = floatval($_POST['price']);
    $quantity = (int)$_POST['quantity'];
    $low_stock_limit = (int)$_POST['low_stock_limit'];
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;
    $supplier_sql = $supplier_id ? $supplier_id : 'NULL';

    $query = "UPDATE books SET
                book_name = '$book_name',
                grade_level = '$grade_level',
                subject = '$subject',
                price = $price,
                quantity = $quantity,
                low_stock_limit = $low_stock_limit,
                supplier_id = $supplier_sql,
                updated_at = NOW()
              WHERE book_id = $id";
    if (mysqli_query($conn, $query)) {
        $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Book updated successfully!</div>';
    } else {
        $message = '<div class="alert error">Error updating book: ' . mysqli_error($conn) . '</div>';
    }
}

// ────────────────────────────────────────────────
// Load data
// ────────────────────────────────────────────────
$all_books = mysqli_fetch_all(mysqli_query($conn, "
    SELECT b.*, s.supplier_name
    FROM books b
    LEFT JOIN suppliers s ON b.supplier_id = s.id
    ORDER BY b.book_name ASC
"), MYSQLI_ASSOC);

$suppliers = mysqli_fetch_all(mysqli_query($conn, "
    SELECT id, supplier_name FROM suppliers
    WHERE status = 'active' AND supplier_type IN ('books', 'both')
    ORDER BY supplier_name ASC
"), MYSQLI_ASSOC);

$history = mysqli_fetch_all(mysqli_query($conn, "
    SELECT h.*, b.book_name
    FROM book_sales_history h
    JOIN books b ON h.book_id = b.book_id
    ORDER BY h.sold_at DESC
"), MYSQLI_ASSOC);

$total_amount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(quantity_sold * price_at_sale) as total 
    FROM book_sales_history
"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Books</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #2e7d5e;
            --green-dark: #1a4d2e;
            --red: #d32f2f;
            --red-dark: #b71c1c;
            --light: #e8f5e9;
            --bg: #f0f7f2;
            --blue: #0288d1;
            --blue-dark: #0277bd;
            --orange: #f57c00;
            --orange-dark: #ef6c00;
        }
        body { background: var(--bg); font-family: 'Inter', sans-serif; color: #1e3c2c; margin: 0; }
        .main-content { padding: 24px 32px; }
        .dashboard { max-width: 1440px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        h1 { font-family: 'Outfit', sans-serif; color: #1a4d2e; display: flex; align-items: center; gap: 12px; margin: 0; }
        .header-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .btn-pill { background: var(--green); color: white; border: none; border-radius: 999px; padding: 10px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-pill:hover { background: var(--green-dark); }
        .btn-outline { background: transparent; color: var(--green); border: 2px solid var(--green); }
        .btn-outline:hover { background: var(--green); color: white; }
        .top-controls { display: flex; align-items: center; gap: 40px; margin-bottom: 24px; flex-wrap: wrap; }
        .search-container { flex: 1; min-width: 260px; }
        .search-input { width: 100%; padding: 12px 16px; border: 1px solid #d4e8da; border-radius: 999px; font-size: 16px; box-sizing: border-box; }
        .search-input:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(46,125,94,0.15); }
        .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-btn { padding: 9px 16px; border-radius: 999px; background: white; border: 1px solid #d4e8da; cursor: pointer; font-weight: 500; color: #1a4d2e; white-space: nowrap; }
        .filter-btn.active { background: var(--green); color: white; border-color: var(--green-dark); }
        .table-container { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #d4e8da; box-shadow: 0 6px 16px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 14px 10px; text-align: left; vertical-align: middle; font-size: 14px; box-sizing: border-box; }
        th { background: var(--light); font-weight: 600; color: #1a4d2e; white-space: nowrap; }
        tr:hover { background: #f8fdfa; }
        th:nth-child(1), td:nth-child(1) { width: 22%; }
        th:nth-child(2), td:nth-child(2) { width: 11%; }
        th:nth-child(3), td:nth-child(3) { width: 18%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; text-align: right; }
        th:nth-child(5), td:nth-child(5) { width: 10%; text-align: right; }
        th:nth-child(6), td:nth-child(6) { width: 12%; }
        th:nth-child(7), td:nth-child(7) { width: 17%; min-width: 160px; }
        td { overflow: hidden; text-overflow: ellipsis; }
        td:nth-child(1), td:nth-child(3) { white-space: normal; word-break: break-word; }
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .action-btn {
            padding: 8px 12px;
            font-size: 13px;
            border: none;
            border-radius: 999px;
            cursor: pointer !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .action-btn i { font-size: 1.15em; }
        .action-btn.view    { background: var(--orange); color: white; }
        .action-btn.edit    { background: var(--blue);   color: white; }
        .action-btn.sold    { background: var(--red);    color: white; }
        .action-btn.return  { background: #4caf50;      color: white; }
        .action-btn.damaged { background: #e91e63;      color: white; }
        .action-btn:hover.view    { background: var(--orange-dark); transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .action-btn:hover.edit    { background: var(--blue-dark);   transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .action-btn:hover.sold    { background: var(--red-dark);    transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .action-btn:hover.return  { background: #388e3c;            transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .action-btn:hover.damaged { background: #c2185b;            transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .badge.low-stock { background: #ffcdd2; color: #b71c1c; }
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999 !important;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-container {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 820px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }
        .modal-header {
            padding: 20px 28px;
            border-bottom: 1px solid #e2f0e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .modal-body { padding: 24px 28px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-grid .full-width { grid-column: 1/-1; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #1a4d2e; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d4e8da; border-radius: 12px; font-size: 15px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 3px rgba(46,125,94,0.1); }
        .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; grid-column: 1/-1; }
        .form-actions button { padding: 12px 28px; font-size: 16px; font-weight: 600; border: none; cursor: pointer; border-radius: 999px; min-width: 140px; }
        .save-btn, .confirm-btn { background: var(--green); color: white; }
        .save-btn:hover, .confirm-btn:hover { background: var(--green-dark); }
        .btn-cancel { background: var(--red); color: white; }
        .client-pagination { margin-top: 20px; text-align: center; }
        .client-pagination button { margin: 0 5px; padding: 8px 14px; border-radius: 999px; background: #e8f5e9; border: none; cursor: pointer; font-size: 14px; color: #1a4d2e; }
        .client-pagination button.active { background: var(--green); color: white; }
        .client-pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
        .total-amount { font-size: 20px; font-weight: 600; color: #1a4d2e; text-align: center; margin: 32px 0; }
        .alert { padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
        .alert.success { background: #e2f0e6; color: #1a4d2e; }
        .alert.error { background: #ffebee; color: #c62828; }
        .history-controls { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .history-controls .search-input { flex: 1; min-width: 220px; }
        .history-controls input[type="date"] { padding: 10px 14px; border: 1px solid var(--green-dark); border-radius: 999px; background: var(--green); color: white; font-size: 15px; min-width: 170px; cursor: pointer; }
        .history-controls input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        .print-btn { background: #444; color: white; border: none; padding: 9px 18px; border-radius: 999px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .print-btn:hover { background: #222; }
        .history-item {
            padding: 16px;
            background: #f8fdfa;
            border-radius: 12px;
            margin-bottom: 12px;
            border-left: 4px solid var(--green);
        }
        .history-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .history-date { color: #555; font-size: 13px; }
        .print-area { display: none; }
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        /* Grade level badges - katulad ng low-stock style mo */
        .badge.grade {
            display: inline-block;
            padding: 4px 10px;              /* pareho sa low-stock */
            border-radius: 999px;
            font-size: 13px;                /* maliit pero readable */
            font-weight: 600;
            white-space: nowrap;
            border: 1px solid transparent;  /* optional border para mas pop */
        }

        /* Specific colors para sa bawat grade (madaling baguhin) */
        .grade-kinder {
            background: #e9d5ff;            /* light purple */
            color: #6b21a8;                 /* dark purple text */
            border-color: #c084fc;
        }

        .grade-elementary {
            background: #dbeafe;            /* light blue */
            color: #1d4ed8;                 /* blue text */
            border-color: #93c5fd;
        }

        .grade-high-school {
            background: #fee2e2;            /* light red/orange */
            color: #b91c1c;                 /* red text */
            border-color: #fca5a5;
        }

        /* Optional: gawing mas maliwanag kapag hover sa row */
        tr:hover .badge.grade {
            opacity: 0.9;
            transform: scale(1.05);
            transition: all 0.15s ease;
        }
    </style>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard">
        <div class="header">
            <h1><i class="fas fa-book"></i> Books Management</h1>
            <div class="header-right">
                <button class="btn-pill btn-outline" onclick="document.getElementById('historyModal').classList.add('active'); resetHistoryView();">
                    <i class="fas fa-history"></i> Sales History
                </button>
                <button class="btn-pill" onclick="document.getElementById('addModal').classList.add('active')">
                    <i class="fas fa-plus"></i> Add Book
                </button>
            </div>
        </div>

        <?= $message ?>

        <div class="top-controls">
            <div class="search-container">
                <input type="text" id="bookSearch" class="search-input" placeholder="Search by name, subject, grade...">
            </div>
            <div class="filter-bar">
                <button class="filter-btn active" data-grade="all">All</button>
                <button class="filter-btn" data-grade="Kinder">Kinder</button>
                <button class="filter-btn" data-grade="Elementary">Elementary</button>
                <button class="filter-btn" data-grade="High School">High School</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book Name</th>
                        <th>Grade Level</th>
                        <th>Subject</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="booksTableBody"></tbody>
            </table>
        </div>

        <div class="client-pagination" id="booksPagination"></div>

        <div class="total-amount">
            Total from Book Sales: <span style="color:var(--green);">₱<?= number_format($total_amount, 2) ?></span>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Book</h2>
            <button onclick="document.getElementById('addModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_book">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Book Name *</label>
                        <input type="text" name="book_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Grade Level *</label>
                        <select name="grade_level" class="form-control" required>
                            <option value="Kinder">Kinder</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱) *</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" min="1" value="10" class="form-control">
                    </div>
                    <div class="form-group full-width">
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
                    <button type="button" class="btn-cancel" onclick="document.getElementById('addModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="save-btn">Add Book</button>
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
            <button onclick="document.getElementById('editModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_book">
                <input type="hidden" name="book_id" id="edit_book_id">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Book Name *</label>
                        <input type="text" name="book_name" id="edit_book_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Grade Level *</label>
                        <select name="grade_level" id="edit_grade_level" class="form-control" required>
                            <option value="Kinder">Kinder</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" id="edit_subject" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱) *</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" id="edit_quantity" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Low Stock Limit</label>
                        <input type="number" name="low_stock_limit" id="edit_low_stock_limit" min="1" class="form-control">
                    </div>
                    <div class="form-group full-width">
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
                    <button type="button" class="btn-cancel" onclick="document.getElementById('editModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sold Modal -->
<div class="modal-overlay" id="soldModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-shopping-cart"></i> Record Sale</h2>
            <button onclick="document.getElementById('soldModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="sold_book">
                <input type="hidden" name="book_id" id="sold_book_id">
                <div class="item-details">
                    <div>
                        <span>Book:</span>
                        <span id="sold_book_title"></span>
                    </div>
                    <div>
                        <span>Price:</span>
                        <span id="sold_book_price"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Quantity to Sell *</label>
                    <input type="number" name="quantity_sold" id="sold_quantity" min="1" value="1" class="form-control" required>
                    <small id="stock_info" style="color:#666; margin-top:6px; display:block;"></small>
                </div>
                <div class="form-group">
                    <label>Total Amount:</label>
                    <div style="font-size: 24px; font-weight: 700; color: var(--green);" id="sold_total">₱0.00</div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('soldModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal-overlay" id="returnModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-undo"></i> Record Return</h2>
            <button onclick="document.getElementById('returnModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="return_book">
                <input type="hidden" name="book_id" id="return_book_id">
                <div><strong>Book:</strong> <span id="return_book_name"></span></div>
                <div><strong>Current Stock:</strong> <span id="return_current_stock"></span></div>
                <div class="form-group">
                    <label>Quantity Returned *</label>
                    <input type="number" name="quantity_returned" min="1" value="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="return_notes" rows="2" class="form-control" placeholder="e.g. Returned by student Juan"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('returnModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="save-btn">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Damaged Modal -->
<div class="modal-overlay" id="damagedModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-times-circle"></i> Mark as Damaged</h2>
            <button onclick="document.getElementById('damagedModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="damaged_book">
                <input type="hidden" name="book_id" id="damaged_book_id">
                <div><strong>Book:</strong> <span id="damaged_book_name"></span></div>
                <div><strong>Current Stock:</strong> <span id="damaged_current_stock"></span></div>
                <div class="form-group">
                    <label>Quantity Damaged *</label>
                    <input type="number" name="quantity_damaged" min="1" value="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Reason (optional)</label>
                    <textarea name="damaged_reason" rows="2" class="form-control" placeholder="e.g. Torn pages, water damaged"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('damagedModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm Damaged</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal (Activity Log only) -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-history"></i> Activity Log</h2>
            <button onclick="document.getElementById('viewModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <div id="view_details"></div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal-overlay" id="historyModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-history"></i> Book Sales History</h2>
            <button onclick="document.getElementById('historyModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <div class="history-controls">
                <input type="text" id="historySearch" class="search-input" placeholder="Search book name...">
                <input type="date" id="historyDateFilter" title="Filter by exact date">
                <button class="print-btn" onclick="printHistory()"><i class="fas fa-print"></i> Print</button>
            </div>
            <div id="historyList"></div>
            <div class="client-pagination" id="historyPagination"></div>
        </div>
    </div>
</div>

<!-- Print Area -->
<div class="print-area" id="printContent">
    <h2 style="text-align:center; margin-bottom:8px;">La Trinidad Academy</h2>
    <h3 style="text-align:center; margin-top:0;">Book Sales History</h3>
    <p style="text-align:center; color:#555;" id="printDateRange"></p>
    <hr style="border:1px solid #ccc; margin:16px 0;">
    <table style="width:100%; border-collapse:collapse; font-family:Arial,sans-serif;">
        <thead>
            <tr style="background:#f0f7f2;">
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Date Sold</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Book</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:center;">Qty</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:right;">Price</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody id="printBody"></tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#e8f5e9;">
                <td colspan="4" style="padding:12px; text-align:right; border:1px solid #ddd;">Grand Total:</td>
                <td style="padding:12px; text-align:right; border:1px solid #ddd;" id="printGrandTotal">₱0.00</td>
            </tr>
        </tfoot>
    </table>
    <div style="margin-top:40px; text-align:center; color:#777; font-size:13px;">
        Generated on <?= date('M d, Y h:i A') ?> • For internal use only
    </div>
</div>

<script>
// DATA
const allBooks = <?= json_encode($all_books) ?>;
const allHistory = <?= json_encode($history) ?>;

// ── Modal Functions ───────────────────────────────────────────────
function openViewModal(id) {
    const b = allBooks.find(x => Number(x.book_id) === Number(id));
    if (!b) return;

    document.getElementById('view_details').innerHTML = `
        <h3 style="margin-bottom:12px; color:#1a4d2e;">Activity Log</h3>
        <pre style="background:#f8f9fa; padding:16px; border-radius:8px; white-space:pre-wrap; font-size:14px; max-height:400px; overflow-y:auto; line-height:1.5;">
${b.activity_log || 'Walang activity na naitala pa para sa librong ito.'}
        </pre>
    `;
    document.getElementById('viewModal').classList.add('active');
}

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

function openSoldModal(id, title, stock) {
    document.getElementById('sold_book_id').value = id;
    document.getElementById('sold_book_title').textContent = title;
    const qtyInput = document.getElementById('sold_quantity');
    qtyInput.value = 1;
    qtyInput.max = stock;
    document.getElementById('stock_info').textContent = `Available: ${stock} pcs`;
    document.getElementById('soldModal').classList.add('active');
}

function openReturnModal(id) {
    const b = allBooks.find(x => Number(x.book_id) === Number(id));
    if (!b) return;
    document.getElementById('return_book_id').value = id;
    document.getElementById('return_book_name').textContent = b.book_name;
    document.getElementById('return_current_stock').textContent = b.quantity + ' pcs';
    document.getElementById('returnModal').classList.add('active');
}

function openDamagedModal(id) {
    const b = allBooks.find(x => Number(x.book_id) === Number(id));
    if (!b) return;
    document.getElementById('damaged_book_id').value = id;
    document.getElementById('damaged_book_name').textContent = b.book_name;
    document.getElementById('damaged_current_stock').textContent = b.quantity + ' pcs';
    document.getElementById('damagedModal').classList.add('active');
}

// ── Render Books Table ───────────────────────────────────────────────
let currentPage = 1;
const itemsPerPage = 10;

function renderBooks() {
    const term = document.getElementById('bookSearch').value.toLowerCase().trim();
    const activeGrade = document.querySelector('.filter-bar .filter-btn.active')?.dataset.grade || 'all';

    const filtered = allBooks.filter(b => {
        const gradeMatch = (activeGrade === 'all' || b.grade_level === activeGrade);
        const searchMatch = 
            b.book_name.toLowerCase().includes(term) ||
            b.subject.toLowerCase().includes(term) ||
            b.grade_level.toLowerCase().includes(term);
        return gradeMatch && searchMatch;
    });

    const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
    currentPage = Math.min(currentPage, totalPages);
    const start = (currentPage - 1) * itemsPerPage;
    const pageItems = filtered.slice(start, start + itemsPerPage);

    const tbody = document.getElementById('booksTableBody');
    tbody.innerHTML = pageItems.length === 0
        ? '<tr><td colspan="7" style="text-align:center; padding:60px;">No books found.</td></tr>'
        : '';

    pageItems.forEach(b => {
        const tr = document.createElement('tr');
        const lowStock = Number(b.quantity) <= Number(b.low_stock_limit) ? 'badge low-stock' : '';
        tr.innerHTML = `
            <td title="${b.book_name}">${b.book_name}</td>
            <td>
                <span class="badge grade-${b.grade_level.toLowerCase().replace(' ', '-')}">
                    ${b.grade_level}
                </span>
            </td>
            <td>${b.subject}</td>
            <td>₱${Number(b.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td><span class="${lowStock}">${Number(b.quantity).toLocaleString()} pcs</span></td>
            <td>${b.supplier_name || '—'}</td>
            <td class="action-buttons">
                <button type="button" class="action-btn view" onclick="openViewModal(${b.book_id})"><i class="fas fa-eye"></i></button>
                <button type="button" class="action-btn edit" onclick="openEditModal(${b.book_id}, '${b.book_name.replace(/'/g, "\\'")}', '${b.grade_level.replace(/'/g, "\\'")}', '${b.subject.replace(/'/g, "\\'")}', ${b.price}, ${b.quantity}, ${b.low_stock_limit}, ${b.supplier_id || 'null'})"><i class="fas fa-edit"></i></button>
                <button type="button" class="action-btn return" onclick="openReturnModal(${b.book_id})"><i class="fas fa-undo"></i></button>
                <button type="button" class="action-btn damaged" onclick="openDamagedModal(${b.book_id})"><i class="fas fa-times-circle"></i></button>
                <button type="button" class="action-btn sold" onclick="openSoldModal(${b.book_id}, '${b.book_name.replace(/'/g, "\\'")}', ${b.quantity})"><i class="fas fa-shopping-cart"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    const pag = document.getElementById('booksPagination');
    pag.innerHTML = '';
    if (totalPages > 1) {
        let html = `<button ${currentPage===1?'disabled':''} onclick="if(currentPage>1){currentPage--;renderBooks()}">Previous</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
                html += `<button class="${i===currentPage?'active':''}" onclick="currentPage=${i};renderBooks()">${i}</button>`;
            } else if (Math.abs(i - currentPage) === 3) {
                html += '<button disabled>...</button>';
            }
        }
        html += `<button ${currentPage===totalPages?'disabled':''} onclick="if(currentPage<${totalPages}){currentPage++;renderBooks()}">Next</button>`;
        pag.innerHTML = html;
    }
}

// ── Event Listeners ────────────────────────────────────────────────────
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1;
        renderBooks();
    });
});

document.getElementById('bookSearch').addEventListener('input', () => {
    currentPage = 1;
    renderBooks();
});

// ── History Rendering ─────────────────────────────────────────────────
let historyPage = 1;
const historyPerPage = 10;

function renderHistory() {
    const term = document.getElementById('historySearch').value.toLowerCase().trim();
    const dateFilter = document.getElementById('historyDateFilter').value;

    const filtered = allHistory.filter(h => {
        const nameMatch = (h.book_name || '').toLowerCase().includes(term);
        let dateMatch = true;
        if (dateFilter) {
            const soldDate = new Date(h.sold_at).toISOString().split('T')[0];
            dateMatch = soldDate === dateFilter;
        }
        return nameMatch && dateMatch;
    });

    const totalPages = Math.ceil(filtered.length / historyPerPage) || 1;
    historyPage = Math.min(historyPage, totalPages);

    const start = (historyPage - 1) * historyPerPage;
    const pageItems = filtered.slice(start, start + historyPerPage);

    const container = document.getElementById('historyList');
    container.innerHTML = pageItems.length === 0
        ? '<div style="text-align:center; padding:40px 0; color:#777;">No sales records found.</div>'
        : '';

    pageItems.forEach(h => {
        const div = document.createElement('div');
        div.className = 'history-item';
        div.innerHTML = `
            <div class="history-item-header">
                <span>${h.book_name}</span>
                <span class="history-date">${new Date(h.sold_at).toLocaleString('en-PH', {
                    year: 'numeric', month: 'short', day: 'numeric',
                    hour: 'numeric', minute: '2-digit', hour12: true
                })}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:14px;">
                <div>
                    <strong>Qty Sold:</strong> ${h.quantity_sold} pcs<br>
                    <strong>Price at sale:</strong> ₱${Number(h.price_at_sale).toFixed(2)}
                </div>
                <div style="text-align:right; font-weight:600; font-size:16px; color:var(--green);">
                    ₱${(h.quantity_sold * h.price_at_sale).toFixed(2)}
                </div>
            </div>
        `;
        container.appendChild(div);
    });

    const pag = document.getElementById('historyPagination');
    pag.innerHTML = '';
    if (totalPages > 1) {
        let html = `<button ${historyPage===1?'disabled':''} onclick="historyPage > 1 && (historyPage--, renderHistory())">Previous</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - historyPage) <= 2) {
                html += `<button class="${i===historyPage?'active':''}" onclick="historyPage=${i}; renderHistory()">${i}</button>`;
            } else if (Math.abs(i - historyPage) === 3) {
                html += '<button disabled>...</button>';
            }
        }
        html += `<button ${historyPage===totalPages?'disabled':''} onclick="historyPage < ${totalPages} && (historyPage++, renderHistory())">Next</button>`;
        pag.innerHTML = html;
    }
}

function resetHistoryView() {
    historyPage = 1;
    document.getElementById('historySearch').value = '';
    document.getElementById('historyDateFilter').value = '';
    renderHistory();
}

// ── Print History ─────────────────────────────────────────────────────
function printHistory() {
    const term = document.getElementById('historySearch').value.toLowerCase().trim();
    const dateFilter = document.getElementById('historyDateFilter').value;

    const filtered = allHistory.filter(h => {
        const nameMatch = (h.book_name || '').toLowerCase().includes(term);
        let dateMatch = true;
        if (dateFilter) {
            const soldDate = new Date(h.sold_at).toISOString().split('T')[0];
            dateMatch = soldDate === dateFilter;
        }
        return nameMatch && dateMatch;
    });

    let total = 0;
    let tbody = '';
    filtered.forEach(h => {
        const amt = h.quantity_sold * h.price_at_sale;
        total += amt;
        tbody += `
            <tr>
                <td style="padding:10px; border:1px solid #ddd;">${new Date(h.sold_at).toLocaleString('en-PH', {
                    month: 'short', day: 'numeric', year: 'numeric',
                    hour: 'numeric', minute: '2-digit', hour12: true
                })}</td>
                <td style="padding:10px; border:1px solid #ddd;">${h.book_name}</td>
                <td style="padding:10px; border:1px solid #ddd; text-align:center;">${h.quantity_sold}</td>
                <td style="padding:10px; border:1px solid #ddd; text-align:right;">₱${Number(h.price_at_sale).toFixed(2)}</td>
                <td style="padding:10px; border:1px solid #ddd; text-align:right;">₱${amt.toFixed(2)}</td>
            </tr>
        `;
    });

    document.getElementById('printBody').innerHTML = tbody || '<tr><td colspan="5" style="text-align:center;padding:20px;">No records</td></tr>';
    document.getElementById('printGrandTotal').textContent = '₱' + total.toFixed(2);
    document.getElementById('printDateRange').textContent = dateFilter
        ? `Sales on ${new Date(dateFilter).toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' })}`
        : 'All recorded sales';

    const printWin = window.open('', '_blank');
    printWin.document.write(`
        <html>
        <head><title>Book Sales History - La Trinidad Academy</title></head>
        <body style="font-family:Arial,sans-serif; margin:40px; color:#333;">
            ${document.getElementById('printContent').innerHTML}
        </body>
        </html>
    `);
    printWin.document.close();
    setTimeout(() => printWin.print(), 600);
}

// ── Event Listeners ────────────────────────────────────────────────────
document.getElementById('historySearch')?.addEventListener('input', () => { historyPage = 1; renderHistory(); });
document.getElementById('historyDateFilter')?.addEventListener('change', () => { historyPage = 1; renderHistory(); });

document.addEventListener('DOMContentLoaded', () => {
    renderBooks();
});
</script>
</body>
</html>