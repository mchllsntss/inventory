<?php

// dashboard.php - Main Dashboard with Recent Movements as Modal

$current_page = 'dashboard';

require_once '../connection/dbconnection.php';

$conn = $GLOBALS['conn'];




// ────────────────────────────────────────────────
// METRICS (UPDATED: total quantity, not count of titles)
// ────────────────────────────────────────────────
// Changed from COUNT(*) to SUM(quantity) to show total items in stock
$total_books_qty = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM books"))['total'] ?? 0;
$total_uniforms_qty = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM uniform"))['total'] ?? 0;

// Low stock count (number of items below limit) — unchanged logic
$low_stock_query = "
    SELECT COUNT(*) as cnt FROM (
        SELECT uniform_id FROM uniform WHERE quantity <= low_stock_limit
        UNION ALL
        SELECT book_id FROM books WHERE quantity <= low_stock_limit
    ) AS low
";
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, $low_stock_query))['cnt'] ?? 0;

// Issued this week (number of sales transactions) — unchanged
$current_week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
$issued_this_week_query = "
    SELECT COUNT(*) as cnt FROM (
        SELECT sold_at FROM uniform_sales_history WHERE sold_at >= '$current_week_start'
        UNION ALL
        SELECT sold_at FROM book_sales_history WHERE sold_at >= '$current_week_start'
    ) AS issued
";
$issued_this_week = mysqli_fetch_assoc(mysqli_query($conn, $issued_this_week_query))['cnt'] ?? 0;




// ────────────────────────────────────────────────
// PAGINATION SETTINGS (unchanged)
// ────────────────────────────────────────────────
$per_page_books = 5;
$page_books     = isset($_GET['books_page']) ? max(1, (int)$_GET['books_page']) : 1;
$offset_books   = ($page_books - 1) * $per_page_books;

$per_page_uniforms = 7;
$page_uniforms     = isset($_GET['uniforms_page']) ? max(1, (int)$_GET['uniforms_page']) : 1;
$offset_uniforms   = ($page_uniforms - 1) * $per_page_uniforms;




// ────────────────────────────────────────────────
// BOOKS - paginated (unchanged)
// ────────────────────────────────────────────────
$books_query = "
    SELECT book_name, grade_level, subject, quantity, low_stock_limit
    FROM books
    ORDER BY book_name ASC
    LIMIT $offset_books, $per_page_books
";
$books_result = mysqli_query($conn, $books_query);
$books_data = [];
while ($row = mysqli_fetch_assoc($books_result)) {
    $percent = $row['low_stock_limit'] > 0
        ? min(100, ($row['quantity'] / ($row['low_stock_limit'] * 10)) * 100)
        : 0;
    $books_data[] = [
        'grade'    => $row['grade_level'],
        'name'     => $row['book_name'],
        'edition'  => $row['subject'],
        'stock'    => $row['quantity'],
        'percent'  => round($percent)
    ];
}
// For pagination we still need total number of book titles (rows)
$total_books_titles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM books"))['cnt'] ?? 0;
$total_books_pages = ceil($total_books_titles / $per_page_books);




// ────────────────────────────────────────────────
// UNIFORMS - paginated (unchanged)
// ────────────────────────────────────────────────
$uniforms_query = "
    SELECT uniform_name, category, size, quantity, low_stock_limit
    FROM uniform
    ORDER BY uniform_name ASC
    LIMIT $offset_uniforms, $per_page_uniforms
";
$uniforms_result = mysqli_query($conn, $uniforms_query);
$uniforms_data = [];
while ($row = mysqli_fetch_assoc($uniforms_result)) {
    $percent = $row['low_stock_limit'] > 0
        ? min(100, ($row['quantity'] / ($row['low_stock_limit'] * 10)) * 100)
        : 0;
    $uniforms_data[] = [
        'category' => $row['category'] . ' (' . $row['size'] . ')',
        'name'     => $row['uniform_name'],
        'size'     => $row['size'],
        'stock'    => $row['quantity'],
        'percent'  => round($percent)
    ];
}
$total_uniforms_titles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM uniform"))['cnt'] ?? 0;
$total_uniforms_pages = ceil($total_uniforms_titles / $per_page_uniforms);




// ────────────────────────────────────────────────
// ALL MOVEMENTS FOR MODAL (unchanged)
// ────────────────────────────────────────────────
$movements_query = "
    (SELECT 'book' AS type, h.book_id AS item_id, b.book_name AS name,
            h.quantity_sold, h.price_at_sale, h.sold_at
     FROM book_sales_history h
     JOIN books b ON h.book_id = b.book_id)
    UNION ALL
    (SELECT 'uniform' AS type, h.uniform_id AS item_id, u.uniform_name AS name,
            h.quantity_sold, h.price_at_sale, h.sold_at
     FROM uniform_sales_history h
     JOIN uniform u ON h.uniform_id = u.uniform_id)
    ORDER BY sold_at DESC
";
$movements_result = mysqli_query($conn, $movements_query);
$all_movements = [];
while ($row = mysqli_fetch_assoc($movements_result)) {
    $all_movements[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Inventory Dashboard</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">
    <style>
        /* (All existing CSS remains exactly the same — no changes) */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            background: #f0f7f2; 
            font-family: 'Inter', sans-serif; 
            color: #1e3c2c; 
            min-height: 100vh; 
        }
        
        .main-content { 
            padding: 24px 32px; 
            transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); 
            min-height: 100vh; 
            background: #f0f7f2; 
        }
        
        .main-content.sidebar-closed { 
            margin-left: 0; 
        }


        /* Button Styles */
        .btn-pill {
            background: #2e7d5e;
            color: white;
            border: none;
            border-radius: 999px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 10;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(46, 125, 94, 0.2);
        }
        
        .btn-pill:hover { 
            background: #1a4d2e; 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 77, 46, 0.3);
        }
        
        .btn-pill:active {
            transform: translateY(0);
        }


        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .title-section h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1e3c2c;
            margin-bottom: 4px;
        }

        .title-section h1 i {
            color: #2e7d5e;
            margin-right: 10px;
        }

        .badge-academy {
            background: #e0f2e7;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            color: #1a4d2e;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .top-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .date-chip {
            background: white;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
        }

        .btn-outline {
            background: white;
            border: 1px solid #c0d9cf;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-outline:hover {
            background: #e0f2e7;
            border-color: #2e7d5e;
        }

        /* Metric Cards */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: white;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .metric-icon.blue { background: #e1f0fa; color: #1e5f8e; }
        .metric-icon.green { background: #e0f2e7; color: #2e7d5e; }
        .metric-icon.orange { background: #fff2e0; color: #b45b0f; }
        .metric-icon.purple { background: #ede7f6; color: #5e3c8e; }

        .metric-details h3 {
            font-size: 14px;
            font-weight: 500;
            color: #5e7a6b;
            margin-bottom: 4px;
        }

        .metric-number {
            font-size: 32px;
            font-weight: 700;
            color: #1e3c2c;
        }

        /* Section Headers */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1e3c2c;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-header h2 i {
            color: #2e7d5e;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 8px;
            color: #5e7a6b;
            font-weight: 500;
            font-size: 14px;
            border-bottom: 2px solid #e5f0ea;
        }

        td {
            padding: 12px 8px;
            border-bottom: 1px solid #eef7f2;
            color: #1e3c2c;
        }

        .stock-bar {
            width: 120px;
            height: 8px;
            background: #e5f0ea;
            border-radius: 999px;
            overflow: hidden;
        }

        .stock-fill {
            height: 100%;
            background: #2e7d5e;
            border-radius: 999px;
            transition: width 0.3s ease;
        }

        .stock-fill.warning { background: #f59e0b; }
        .stock-fill.critical { background: #ef4444; }

        .stock-number {
            font-weight: 600;
            color: #1e3c2c;
        }

        .stock-number.low { color: #f59e0b; }
        .stock-number.critical { color: #ef4444; }

        /* Pagination */
        .pagination-controls {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
        }

        .pagination-btn {
            padding: 6px 12px;
            border: 1px solid #c0d9cf;
            background: white;
            color: #1e3c2c;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #e0f2e7;
            border-color: #2e7d5e;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #2e7d5e;
            color: white;
            border-color: #2e7d5e;
        }

        .pagination-ellipsis {
            padding: 6px 12px;
            color: #5e7a6b;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-header {
            padding: 24px 28px;
            border-bottom: 2px solid #e5f0ea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .modal-header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #1e3c2c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header h2 i {
            color: #2e7d5e;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #1a4d2e;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f0f7f2;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 24px 28px;
            overflow-y: auto;
            max-height: calc(90vh - 140px);
        }

        .history-controls {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .history-search {
            flex: 1;
            min-width: 240px;
            padding: 12px 16px;
            border: 1px solid #c0d9cf;
            border-radius: 999px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f8fbf9;
        }

        .history-search:focus {
            outline: none;
            border-color: #2e7d5e;
            box-shadow: 0 0 0 3px rgba(46, 125, 94, 0.1);
            background: white;
        }

        .history-date {
            padding: 12px 16px;
            border: 1px solid #c0d9cf;
            border-radius: 999px;
            font-size: 14px;
            background: #f8fbf9;
            transition: all 0.2s ease;
        }

        .history-date:focus {
            outline: none;
            border-color: #2e7d5e;
            box-shadow: 0 0 0 3px rgba(46, 125, 94, 0.1);
            background: white;
        }

        .history-item {
            background: #f8fbf9;
            border: 1px solid #e5f0ea;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .history-item:hover {
            background: white;
            border-color: #2e7d5e;
            box-shadow: 0 4px 12px rgba(46, 125, 94, 0.1);
            transform: translateY(-2px);
        }

        .history-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #c0d9cf;
        }

        .history-item-header span:first-child {
            font-weight: 600;
            color: #1e3c2c;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .history-item-header span:first-child i {
            color: #2e7d5e;
        }

        .history-date {
            font-size: 13px;
            color: #5e7a6b;
        }

        .tx-badge {
            background: #e0f2e7;
            color: #1a4d2e;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .tx-qty {
            font-size: 18px;
            font-weight: 700;
            color: #2e7d5e;
        }

        .history-pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .history-pagination button {
            min-width: 40px;
            height: 40px;
            border: 1px solid #c0d9cf;
            background: white;
            color: #1e3c2c;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .history-pagination button:hover:not(:disabled) {
            background: #e0f2e7;
            border-color: #2e7d5e;
        }

        .history-pagination button.active {
            background: #2e7d5e;
            color: white;
            border-color: #2e7d5e;
        }

        .history-pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* No movements message */
        .no-movements {
            text-align: center;
            color: #5e7a6b;
            padding: 60px 20px;
            font-size: 16px;
        }

        .no-movements i {
            font-size: 48px;
            color: #c0d9cf;
            margin-bottom: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 16px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .top-actions {
                width: 100%;
            }
            
            .btn-pill {
                width: 100%;
                justify-content: center;
            }
            
            .modal-container {
                width: 95%;
            }
            
            .modal-header,
            .modal-body {
                padding: 20px;
            }
        }

        /* Print styles for modal */
        @media print {
            body * {
                visibility: hidden;
            }
            .modal-container, .modal-container * {
                visibility: visible;
            }
            .modal-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: visible;
                background: white;
                box-shadow: none;
                border-radius: 0;
            }
            .modal-header .modal-close,
            .history-controls,
            .history-pagination,
            #printMovementsBtn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard">
            <!-- Header -->
            <div class="header">
                <div class="title-section">
                    <h1><i class="fas fa-archway"></i> La Trinidad Academy</h1>
                    <div class="badge-academy">
                        <i class="fas fa-user-graduate"></i> Books & Uniforms · Inventory
                    </div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt" style="margin-right:8px; color:#2e7d5e;"></i>
                        <?= date('d M Y') ?>
                    </div>
                    <div class="btn-outline"><i class="fas fa-sync-alt"></i> Update Stock</div>
                    <button class="btn-pill" id="openHistoryModalBtn">
                        <i class="fas fa-history"></i> Recent Movements
                    </button>
                </div>
            </div>

            <!-- Metrics (UPDATED: now showing total quantity of items) -->
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-icon blue">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="metric-details">
                        <h3>Total Books (Qty)</h3>
                        <div class="metric-number"><?= number_format($total_books_qty) ?></div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon green">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="metric-details">
                        <h3>Total Uniforms (Qty)</h3>
                        <div class="metric-number"><?= number_format($total_uniforms_qty) ?></div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon orange">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="metric-details">
                        <h3>Low Stock Items</h3>
                        <div class="metric-number"><?= number_format($low_stock) ?></div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon purple">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="metric-details">
                        <h3>Issued This Week</h3>
                        <div class="metric-number"><?= number_format($issued_this_week) ?></div>
                    </div>
                </div>
            </div>

            <!-- Books Section -->
            <div class="section-header">
                <h2><i class="fas fa-book"></i> Books Inventory</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Grade Level</th>
                            <th>Book Name</th>
                            <th>Edition/Subject</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books_data as $book): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['grade']) ?></td>
                            <td><strong><?= htmlspecialchars($book['name']) ?></strong></td>
                            <td><?= htmlspecialchars($book['edition']) ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="stock-bar">
                                        <div class="stock-fill <?= $book['percent'] <= 20 ? 'critical' : ($book['percent'] <= 50 ? 'warning' : '') ?>" style="width: <?= $book['percent'] ?>%;"></div>
                                    </div>
                                    <span class="stock-number <?= $book['percent'] <= 20 ? 'critical' : ($book['percent'] <= 50 ? 'warning' : '') ?>">
                                        <?= $book['stock'] ?> pcs
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($total_books_pages > 1): ?>
                <div class="pagination-controls">
                    <a href="?books_page=<?= max(1, $page_books-1) ?>&uniforms_page=<?= $page_uniforms ?>" class="pagination-btn" <?= $page_books <= 1 ? 'disabled' : '' ?>>Previous</a>
                    <?php for ($i = 1; $i <= $total_books_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_books_pages || abs($i - $page_books) <= 2): ?>
                            <a href="?books_page=<?= $i ?>&uniforms_page=<?= $page_uniforms ?>" class="pagination-btn <?= $i == $page_books ? 'active' : '' ?>"><?= $i ?></a>
                        <?php elseif (abs($i - $page_books) == 3): ?>
                            <span class="pagination-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <a href="?books_page=<?= min($total_books_pages, $page_books+1) ?>&uniforms_page=<?= $page_uniforms ?>" class="pagination-btn" <?= $page_books >= $total_books_pages ? 'disabled' : '' ?>>Next</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Uniforms Section -->
            <div class="section-header">
                <h2><i class="fas fa-tshirt"></i> Uniforms Inventory</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category (Size)</th>
                            <th>Uniform Name</th>
                            <th>Size</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uniforms_data as $uniform): ?>
                        <tr>
                            <td><?= htmlspecialchars($uniform['category']) ?></td>
                            <td><strong><?= htmlspecialchars($uniform['name']) ?></strong></td>
                            <td><?= htmlspecialchars($uniform['size']) ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="stock-bar">
                                        <div class="stock-fill <?= $uniform['percent'] <= 20 ? 'critical' : ($uniform['percent'] <= 50 ? 'warning' : '') ?>" style="width: <?= $uniform['percent'] ?>%;"></div>
                                    </div>
                                    <span class="stock-number <?= $uniform['percent'] <= 20 ? 'critical' : ($uniform['percent'] <= 50 ? 'warning' : '') ?>">
                                        <?= $uniform['stock'] ?> pcs
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($total_uniforms_pages > 1): ?>
                <div class="pagination-controls">
                    <a href="?uniforms_page=<?= max(1, $page_uniforms-1) ?>&books_page=<?= $page_books ?>" class="pagination-btn" <?= $page_uniforms <= 1 ? 'disabled' : '' ?>>Previous</a>
                    <?php for ($i = 1; $i <= $total_uniforms_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_uniforms_pages || abs($i - $page_uniforms) <= 2): ?>
                            <a href="?uniforms_page=<?= $i ?>&books_page=<?= $page_books ?>" class="pagination-btn <?= $i == $page_uniforms ? 'active' : '' ?>"><?= $i ?></a>
                        <?php elseif (abs($i - $page_uniforms) == 3): ?>
                            <span class="pagination-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <a href="?uniforms_page=<?= min($total_uniforms_pages, $page_uniforms+1) ?>&books_page=<?= $page_books ?>" class="pagination-btn" <?= $page_uniforms >= $total_uniforms_pages ? 'disabled' : '' ?>>Next</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Movements Modal (with Print Button) -->
    <div class="modal-overlay" id="historyModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-history"></i> Recent Inventory Movements</h2>
                <div style="display: flex; gap: 8px;">
                    <button class="btn-outline" id="printMovementsBtn" title="Print movements"><i class="fas fa-print"></i> Print</button>
                    <button class="modal-close" id="closeHistoryModal">×</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="history-controls">
                    <input type="text" class="history-search" id="historySearch" placeholder="Search item name...">
                    <input type="date" class="history-date" id="historyDateFilter" title="Filter by date">
                </div>
                <div id="historyList"></div>
                <div class="history-pagination" id="historyPagination"></div>
            </div>
        </div>
    </div>

    <script>
        // All Movements Data
        const allMovements = <?= json_encode($all_movements) ?>;
        let historyPage = 1;
        const itemsPerPage = 10;

        // Render History Function
        function renderHistory() {
            const term = (document.getElementById('historySearch')?.value || '').toLowerCase().trim();
            const dateFilter = document.getElementById('historyDateFilter')?.value || '';

            const filtered = allMovements.filter(m => {
                const nameMatch = m.name.toLowerCase().includes(term);
                let dateMatch = true;
                if (dateFilter) {
                    const soldDate = new Date(m.sold_at).toISOString().split('T')[0];
                    dateMatch = soldDate === dateFilter;
                }
                return nameMatch && dateMatch;
            });

            const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
            
            // Adjust current page if it exceeds total pages
            if (historyPage > totalPages) {
                historyPage = totalPages;
            }

            const start = (historyPage - 1) * itemsPerPage;
            const pageItems = filtered.slice(start, start + itemsPerPage);

            const container = document.getElementById('historyList');
            if (!container) return;

            if (pageItems.length === 0) {
                container.innerHTML = `
                    <div class="no-movements">
                        <i class="fas fa-box-open"></i>
                        <p>No movements found.</p>
                    </div>
                `;
            } else {
                container.innerHTML = '';
                pageItems.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'history-item';
                    const typeIcon = m.type === 'book' ? 'book' : 'tshirt';
                    const typeText = m.type === 'book' ? 'Book' : 'Uniform';
                    const total = (m.quantity_sold * m.price_at_sale).toFixed(2);
                    const soldDate = new Date(m.sold_at);
                    const formattedDate = soldDate.toLocaleDateString('en-PH', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    div.innerHTML = `
                        <div class="history-item-header">
                            <span><i class="fas fa-${typeIcon}"></i> ${m.name}</span>
                            <span class="history-date">${formattedDate}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="tx-badge">${typeText} Sold</span><br>
                                <span style="font-weight: 500;">Qty: ${m.quantity_sold} pcs</span>
                            </div>
                            <div style="text-align: right;">
                                <div class="tx-qty">₱${total}</div>
                                <small style="color: #5e7a6b;">₱${Number(m.price_at_sale).toFixed(2)} each</small>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                });
            }

            // Render Pagination
            const pag = document.getElementById('historyPagination');
            if (pag) {
                pag.innerHTML = '';
                if (totalPages > 1) {
                    // Previous button
                    const prevBtn = document.createElement('button');
                    prevBtn.textContent = 'Previous';
                    prevBtn.disabled = historyPage === 1;
                    prevBtn.onclick = () => {
                        if (historyPage > 1) {
                            historyPage--;
                            renderHistory();
                        }
                    };
                    pag.appendChild(prevBtn);

                    // Page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        if (i === 1 || i === totalPages || Math.abs(i - historyPage) <= 2) {
                            const btn = document.createElement('button');
                            btn.textContent = i;
                            btn.className = i === historyPage ? 'active' : '';
                            btn.onclick = () => {
                                historyPage = i;
                                renderHistory();
                            };
                            pag.appendChild(btn);
                        } else if (Math.abs(i - historyPage) === 3) {
                            const ellipsis = document.createElement('button');
                            ellipsis.textContent = '...';
                            ellipsis.disabled = true;
                            pag.appendChild(ellipsis);
                        }
                    }

                    // Next button
                    const nextBtn = document.createElement('button');
                    nextBtn.textContent = 'Next';
                    nextBtn.disabled = historyPage === totalPages;
                    nextBtn.onclick = () => {
                        if (historyPage < totalPages) {
                            historyPage++;
                            renderHistory();
                        }
                    };
                    pag.appendChild(nextBtn);
                }
            }
        }

        // Reset and open modal
        function resetAndOpenModal() {
            historyPage = 1;
            const search = document.getElementById('historySearch');
            const date = document.getElementById('historyDateFilter');
            if (search) search.value = '';
            if (date) date.value = '';
            renderHistory();
            
            const modal = document.getElementById('historyModal');
            if (modal) {
                modal.classList.add('active');
            }
        }

        // Initialize modal when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('openHistoryModalBtn');
            const modal = document.getElementById('historyModal');
            const closeBtn = document.getElementById('closeHistoryModal');
            const printBtn = document.getElementById('printMovementsBtn');

            if (openBtn) {
                openBtn.addEventListener('click', resetAndOpenModal);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            }

            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            }

            const searchInput = document.getElementById('historySearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    historyPage = 1;
                    renderHistory();
                });
            }

            const dateFilter = document.getElementById('historyDateFilter');
            if (dateFilter) {
                dateFilter.addEventListener('change', function() {
                    historyPage = 1;
                    renderHistory();
                });
            }
        });

        // Add keyboard support (ESC to close)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('historyModal');
                if (modal && modal.classList.contains('active')) {
                    modal.classList.remove('active');
                }
            }
        });
    </script>

</body>
</html>