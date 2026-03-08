<?php
// books.php - Books Management (client-side pagination & filtering)
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
            mysqli_query($conn, "UPDATE books SET quantity = quantity - $qty_sold WHERE book_id = $id");
            mysqli_query($conn, "INSERT INTO book_sales_history (book_id, quantity_sold, price_at_sale) VALUES ($id, $qty_sold, $price_at_sale)");
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Successfully sold ' . $qty_sold . ' book(s)!</div>';
        }
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
    SELECT SUM(quantity_sold * price_at_sale) as total FROM book_sales_history
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
        }
        body {
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: #1e3c2c;
            margin: 0;
        }
        .main-content { padding: 24px 32px; }
        .dashboard { max-width: 1440px; margin: 0 auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            color: #1a4d2e;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }
        .header-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .btn-pill {
            background: var(--green);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-pill:hover { background: var(--green-dark); }
        .btn-outline { background: transparent; color: var(--green); border: 2px solid var(--green); }
        .btn-outline:hover { background: var(--green); color: white; }
        .top-controls {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .search-container { flex: 1; min-width: 260px; }
        .search-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d4e8da;
            border-radius: 999px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .search-input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(46,125,94,0.15);
        }
        .filter-bar { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-btn {
            padding: 10px 18px;
            border-radius: 999px;
            background: white;
            border: 1px solid #d4e8da;
            cursor: pointer;
            font-weight: 500;
            color: #1a4d2e;
        }
        .filter-btn.active {
            background: var(--green);
            color: white;
            border-color: var(--green-dark);
        }
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #d4e8da;
            box-shadow: 0 6px 16px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            padding: 14px 10px;
            text-align: left;
            vertical-align: middle;
            font-size: 14px;
            box-sizing: border-box;
        }
        th {
            background: var(--light);
            font-weight: 600;
            color: #1a4d2e;
            white-space: nowrap;
        }
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

        .action-buttons {
            display: flex;
            flex-direction: row;
            gap: 8px;
            align-items: flex-start;
        }
        .action-btn {
            padding: 8px 4px;
            font-size: 13.5px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
            white-space: nowrap;
            width: 100%;
            justify-content: center;
        }
        .action-btn i { font-size: 1.15em; }
        .action-btn.edit  { background: var(--blue);  color: white; }
        .action-btn.sold  { background: var(--red);   color: white; }
        .action-btn:hover.edit { background: var(--blue-dark); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(2,136,209,0.3); }
        .action-btn:hover.sold { background: var(--red-dark);  transform: translateY(-2px); box-shadow: 0 4px 10px rgba(211,47,47,0.3); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-container {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 720px;
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
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d4e8da;
            border-radius: 12px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
            grid-column: 1/-1;
        }
        .form-actions button {
            padding: 12px 28px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            border-radius: 999px;
            min-width: 140px;
        }
        .save-btn, .confirm-btn { background: var(--green); color: white; }
        .save-btn:hover, .confirm-btn:hover { background: var(--green-dark); }
        .btn-cancel { background: var(--red); color: white; }

        .client-pagination { margin-top: 20px; text-align: center; }
        .client-pagination button {
            margin: 0 5px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e8f5e9;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #1a4d2e;
        }
        .client-pagination button.active { background: var(--green); color: white; }
        .client-pagination button:disabled { opacity: 0.5; cursor: not-allowed; }

        .total-amount {
            font-size: 20px;
            font-weight: 600;
            color: #1a4d2e;
            text-align: center;
            margin: 32px 0;
        }
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert.success { background: #e2f0e6; color: #1a4d2e; }
        .alert.error { background: #ffebee; color: #c62828; }

        .history-controls {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .history-controls .search-input { flex: 1; min-width: 220px; }
        .history-controls input[type="date"] {
            padding: 10px 14px;
            border: 1px solid var(--green-dark);
            border-radius: 999px;
            background: var(--green);
            color: white;
            font-size: 16px;
            min-width: 170px;
            cursor: pointer;
        }
        .print-btn {
            background: #444;
            color: white;
            border: none;
            padding: 9px 18px;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover { background: #222; }

        .history-list { list-style: none; padding: 0; margin: 0 0 16px 0; }
        .history-list li { padding: 14px 0; border-bottom: 1px solid #e2f0e6; }

        @media print {
            body * { visibility: hidden; }
            #printArea, #printArea * { visibility: visible; }
            #printArea { position: absolute; left: 0; top: 0; width: 100%; }
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
                <button class="filter-btn" data-grade="kinder">Kinder</button>
                <button class="filter-btn" data-grade="elementary">Elementary</button>
                <button class="filter-btn" data-grade="high school">High School</button>
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
                        <th>Quantity</th>
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
                    <div class="form-group"><label>Book Name</label><input type="text" name="book_name" class="form-control" required></div>
                    <div class="form-group"><label>Grade Level</label>
                        <select name="grade_level" class="form-control" required>
                            <option value="Kinder">Kinder</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Subject</label><input type="text" name="subject" class="form-control" required></div>
                    <div class="form-group"><label>Price (₱)</label><input type="number" name="price" step="0.01" min="0" class="form-control" required></div>
                    <div class="form-group"><label>Quantity</label><input type="number" name="quantity" min="0" class="form-control" required></div>
                    <div class="form-group"><label>Low Stock Limit</label><input type="number" name="low_stock_limit" min="1" value="10" class="form-control"></div>
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
                    <button type="submit" class="btn-pill save-btn">Add Book</button>
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
                    <div class="form-group"><label>Book Name</label><input type="text" name="book_name" id="edit_book_name" class="form-control" required></div>
                    <div class="form-group"><label>Grade Level</label>
                        <select name="grade_level" id="edit_grade_level" class="form-control" required>
                            <option value="Kinder">Kinder</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Subject</label><input type="text" name="subject" id="edit_subject" class="form-control" required></div>
                    <div class="form-group"><label>Price (₱)</label><input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control" required></div>
                    <div class="form-group"><label>Quantity</label><input type="number" name="quantity" id="edit_quantity" min="0" class="form-control" required></div>
                    <div class="form-group"><label>Low Stock Limit</label><input type="number" name="low_stock_limit" id="edit_low_stock_limit" min="1" class="form-control"></div>
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
                    <button type="submit" class="btn-pill save-btn">Save Changes</button>
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
                <div style="margin:0 0 20px; font-size:1.1rem; color:#1a4d2e;">
                    <strong id="sold_book_title"></strong>
                </div>
                <div class="form-group">
                    <label>Quantity to Sell</label>
                    <input type="number" name="quantity_sold" id="sold_quantity" min="1" value="1" class="form-control" required>
                    <small id="stock_info" style="color:#555; margin-top:6px; display:block;"></small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('soldModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="btn-pill confirm-btn">Confirm Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal-overlay" id="historyModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-history"></i> Recent Sales History</h2>
            <button onclick="document.getElementById('historyModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <div class="history-controls">
                <input type="text" id="historySearch" class="search-input" placeholder="Search book name...">
                <input type="date" id="historyDateFilter" title="Filter by exact date (leave blank for all)">
                <button class="print-btn" onclick="openPrintPage()"><i class="fas fa-print"></i> Print</button>
            </div>
            <ul class="history-list" id="historyList"></ul>
            <div class="history-pagination" id="historyPagination"></div>
        </div>
    </div>
</div>

<script>
// ────────────────────────────────────────────────
// BOOKS TABLE - Client-side rendering
// ────────────────────────────────────────────────
const allBooks = <?= json_encode($all_books) ?>;
let currentPage = 1;
const itemsPerPage = 10;
const booksTableBody = document.getElementById('booksTableBody');
const booksPagination = document.getElementById('booksPagination');
const bookSearchInput = document.getElementById('bookSearch');

function renderBooks() {
    const searchTerm = bookSearchInput.value.toLowerCase().trim();
    const activeGrade = document.querySelector('.filter-btn.active')?.dataset.grade?.toLowerCase() || 'all';

    const filteredBooks = allBooks.filter(book => {
        const gradeMatch = (activeGrade === 'all' || book.grade_level.toLowerCase() === activeGrade);
        const searchMatch =
            book.book_name.toLowerCase().includes(searchTerm) ||
            book.subject.toLowerCase().includes(searchTerm) ||
            book.grade_level.toLowerCase().includes(searchTerm);
        return gradeMatch && searchMatch;
    });

    const totalFiltered = filteredBooks.length;
    const totalPages = Math.ceil(totalFiltered / itemsPerPage);
    currentPage = Math.min(currentPage, totalPages || 1);

    const start = (currentPage - 1) * itemsPerPage;
    const pageItems = filteredBooks.slice(start, start + itemsPerPage);

    booksTableBody.innerHTML = '';
    if (pageItems.length === 0) {
        booksTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:60px;">No books found.</td></tr>';
        booksPagination.innerHTML = '';
        return;
    }

    pageItems.forEach(book => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td title="${book.book_name}">${book.book_name}</td>
            <td>${book.grade_level}</td>
            <td title="${book.subject}">${book.subject}</td>
            <td>₱${Number(book.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td>${Number(book.quantity).toLocaleString()} pcs</td>
            <td>${book.supplier_name || '—'}</td>
            <td class="action-buttons">
                <button class="action-btn edit"
                        onclick="openEditModal(
                            ${book.book_id},
                            '${book.book_name.replace(/'/g, "\\'")}',
                            '${book.grade_level.replace(/'/g, "\\'")}',
                            '${book.subject.replace(/'/g, "\\'")}',
                            ${book.price},
                            ${book.quantity},
                            ${book.low_stock_limit},
                            ${book.supplier_id ?? 'null'}
                        )">
                    <i class="fas fa-edit"></i> <span>Edit</span>
                </button>
                <button class="action-btn sold"
                        onclick="openSoldModal(${book.book_id}, '${book.book_name.replace(/'/g, "\\'")}', ${book.quantity})">
                    <i class="fas fa-shopping-cart"></i> <span>Sold</span>
                </button>
            </td>
        `;
        booksTableBody.appendChild(tr);
    });

    booksPagination.innerHTML = '';
    if (totalPages > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderBooks(); } };
        booksPagination.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = (i === currentPage) ? 'active' : '';
            btn.onclick = () => { currentPage = i; renderBooks(); };
            booksPagination.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderBooks(); } };
        booksPagination.appendChild(nextBtn);
    }
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1;
        renderBooks();
    });
});

bookSearchInput.addEventListener('input', () => { currentPage = 1; renderBooks(); });
renderBooks();

// ────────────────────────────────────────────────
// Modal open functions
// ────────────────────────────────────────────────
function openSoldModal(id, title, stock) {
    document.getElementById('sold_book_id').value = id;
    document.getElementById('sold_book_title').textContent = title;
    const qtyInput = document.getElementById('sold_quantity');
    qtyInput.value = 1;
    qtyInput.max = stock;
    document.getElementById('stock_info').textContent = `Available: ${stock} pcs`;
    document.getElementById('soldModal').classList.add('active');
}

document.getElementById('sold_quantity')?.addEventListener('input', function() {
    let val = parseInt(this.value) || 1;
    const max = parseInt(this.max) || 999999;
    if (val > max) this.value = max;
    if (val < 1) this.value = 1;
});

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

// ────────────────────────────────────────────────
// HISTORY RENDERING
// ────────────────────────────────────────────────
const allHistory = <?= json_encode($history) ?>;
let historyPage = 1;
const historyPerPage = 10;
const historySearch = document.getElementById('historySearch');
const historyDate = document.getElementById('historyDateFilter');
const historyList = document.getElementById('historyList');
const historyPagination = document.getElementById('historyPagination');

function renderHistory() {
    const term = historySearch.value.toLowerCase().trim();
    const selectedDate = historyDate.value;

    const filtered = allHistory.filter(item => {
        const nameMatch = item.book_name.toLowerCase().includes(term);
        let dateMatch = true;
        if (selectedDate) {
            const itemDate = item.sold_at.split(' ')[0];
            dateMatch = itemDate === selectedDate;
        }
        return nameMatch && dateMatch;
    });

    const total = filtered.length;
    const pages = Math.ceil(total / historyPerPage);
    historyPage = Math.min(historyPage, pages || 1);

    const start = (historyPage - 1) * historyPerPage;
    const items = filtered.slice(start, start + historyPerPage);

    historyList.innerHTML = items.length === 0
        ? '<p style="text-align:center; padding:40px 0; color:#555;">No sales found.</p>'
        : '';

    items.forEach(h => {
        const li = document.createElement('li');
        li.innerHTML = `
            <strong>${h.book_name}</strong><br>
            <span style="color:var(--red); font-weight:500;">Sold ${h.quantity_sold} pcs</span>
            <span style="color:#555; margin-left:10px;">₱${(h.price_at_sale * h.quantity_sold).toFixed(2)}</span>
            <div style="color:#6b8e5f; font-size:0.9rem; margin-top:4px;">
                ${new Date(h.sold_at).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit', hour12:true })}
            </div>
        `;
        historyList.appendChild(li);
    });

    historyPagination.innerHTML = '';
    if (pages > 1) {
        const prev = document.createElement('button');
        prev.textContent = 'Previous';
        prev.disabled = historyPage === 1;
        prev.onclick = () => { if (historyPage > 1) { historyPage--; renderHistory(); } };
        historyPagination.appendChild(prev);

        for (let i = 1; i <= pages; i++) {
            const b = document.createElement('button');
            b.textContent = i;
            b.className = i === historyPage ? 'active' : '';
            b.onclick = () => { historyPage = i; renderHistory(); };
            historyPagination.appendChild(b);
        }

        const next = document.createElement('button');
        next.textContent = 'Next';
        next.disabled = historyPage === pages;
        next.onclick = () => { if (historyPage < pages) { historyPage++; renderHistory(); } };
        historyPagination.appendChild(next);
    }
}

function resetHistoryView() {
    historyPage = 1;
    historySearch.value = '';
    historyDate.value = '';
    renderHistory();
}

historySearch.addEventListener('input', () => { historyPage = 1; renderHistory(); });
historyDate.addEventListener('change', () => { historyPage = 1; renderHistory(); });

document.getElementById('historyModal').addEventListener('transitionend', e => {
    if (e.target.classList.contains('active')) resetHistoryView();
});

// ────────────────────────────────────────────────
// PRINT PAGE FUNCTION
// ────────────────────────────────────────────────
function openPrintPage() {
    const term = historySearch.value.trim();
    const selectedDate = historyDate.value;

    let filtered = allHistory;
    if (term) {
        filtered = filtered.filter(h => h.book_name.toLowerCase().includes(term.toLowerCase()));
    }
    if (selectedDate) {
        filtered = filtered.filter(h => h.sold_at.startsWith(selectedDate));
    }

    if (filtered.length === 0) {
        alert("Walang makukuhang data para i-print.");
        return;
    }

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Sales History - Print</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 30px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2e7d5e;
            margin-bottom: 10px;
        }
        .info {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #e8f5e9;
            color: #1a4d2e;
        }
        .total-row {
            font-weight: bold;
            background: #f0f7f2;
        }
        .print-button {
            display: block;
            margin: 0 auto 30px;
            padding: 12px 30px;
            font-size: 18px;
            background: #2e7d5e;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .print-button:hover {
            background: #1a4d2e;
        }
        @media print {
            .print-button { display: none; }
            body { margin: 15mm; }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Print</button>

    <h1>Book Sales History</h1>
    <div class="info">
        ${selectedDate ? `Date: ${new Date(selectedDate).toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' })}<br>` : ''}
        ${term ? `Search: "${term}"<br>` : ''}
        Generated: ${new Date().toLocaleString()}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date Sold</th>
                <th>Book Name</th>
                <th>Qty Sold</th>
                <th>Price per Unit</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
    `);

    let grandTotal = 0;
    filtered.forEach(h => {
        const total = h.quantity_sold * h.price_at_sale;
        grandTotal += total;
        printWindow.document.write(`
            <tr>
                <td>${new Date(h.sold_at).toLocaleString('en-US')}</td>
                <td>${h.book_name}</td>
                <td>${h.quantity_sold}</td>
                <td>₱${Number(h.price_at_sale).toFixed(2)}</td>
                <td>₱${Number(total).toFixed(2)}</td>
            </tr>
        `);
    });

    printWindow.document.write(`
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">Grand Total</td>
                <td>₱${Number(grandTotal).toFixed(2)}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
    `);

    printWindow.document.close();
}
</script>
</body>
</html>