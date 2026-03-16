<?php
// uniforms.php - Uniforms Management (simple stock + return & damaged notes)
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];
$current_page = 'uniforms';
$message = '';

// ────────────────────────────────────────────────
// Handle Sold Action
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sold_uniform') {
    $id = (int)($_POST['uniform_id'] ?? 0);
    $qty_sold = (int)($_POST['quantity_sold'] ?? 0);
    if ($id <= 0 || $qty_sold <= 0) {
        $message = '<div class="alert error">Invalid quantity or uniform ID.</div>';
    } else {
        $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, quantity FROM uniform WHERE uniform_id = $id"));
        if (!$get) {
            $message = '<div class="alert error">Uniform not found.</div>';
        } elseif ($get['quantity'] < $qty_sold) {
            $message = '<div class="alert error">Not enough stock. Only ' . $get['quantity'] . ' available.</div>';
        } else {
            $price_at_sale = $get['price'];
            $note = date('Y-m-d h:i A') . ": Sold $qty_sold pcs";
            $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT activity_log FROM uniform WHERE uniform_id = $id"));
            $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

            mysqli_query($conn, "UPDATE uniform SET 
                quantity = quantity - $qty_sold,
                activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
                WHERE uniform_id = $id");
            mysqli_query($conn, "INSERT INTO uniform_sales_history (uniform_id, quantity_sold, price_at_sale) VALUES ($id, $qty_sold, $price_at_sale)");
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Successfully sold ' . $qty_sold . ' uniform(s)!</div>';
        }
    }
}

// Handle Return Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return_uniform') {
    $id = (int)$_POST['uniform_id'];
    $qty_returned = (int)$_POST['quantity_returned'];
    $notes = mysqli_real_escape_string($conn, trim($_POST['return_notes'] ?? ''));

    if ($id > 0 && $qty_returned > 0) {
        $note = date('Y-m-d h:i A') . ": Returned $qty_returned pcs" . ($notes ? " - $notes" : "");
        $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, price, activity_log FROM uniform WHERE uniform_id = $id"));
        $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

        // Idagdag ulit sa stock
        mysqli_query($conn, "UPDATE uniform SET 
            quantity = quantity + $qty_returned,
            activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
            WHERE uniform_id = $id");

        // Magdagdag ng NEGATIVE sales record para mabawas sa total sales
        $price_at_sale = $current['price'];
        mysqli_query($conn, "INSERT INTO uniform_sales_history 
            (uniform_id, quantity_sold, price_at_sale, sold_at) 
            VALUES ($id, -$qty_returned, $price_at_sale, NOW())");

        $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Return recorded! Stock and total sales adjusted.</div>';
    } else {
        $message = '<div class="alert error">Invalid input.</div>';
    }
}

// Handle Damaged Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'damaged_uniform') {
    $id = (int)$_POST['uniform_id'];
    $qty_damaged = (int)$_POST['quantity_damaged'];
    $reason = mysqli_real_escape_string($conn, trim($_POST['damaged_reason'] ?? ''));

    if ($id > 0 && $qty_damaged > 0) {
        $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity, price, activity_log FROM uniform WHERE uniform_id = $id"));
        if ($current['quantity'] < $qty_damaged) {
            $message = '<div class="alert error">Not enough stock to mark as damaged.</div>';
        } else {
            $note = date('Y-m-d h:i A') . ": Damaged $qty_damaged pcs" . ($reason ? " - $reason" : "");
            $new_log = $current['activity_log'] ? $current['activity_log'] . "\n" . $note : $note;

            // Bawasan ang stock
            mysqli_query($conn, "UPDATE uniform SET 
                quantity = quantity - $qty_damaged,
                activity_log = '" . mysqli_real_escape_string($conn, $new_log) . "'
                WHERE uniform_id = $id");

            // Magdagdag ng NEGATIVE sales record para mabawas sa total sales
            $price_at_sale = $current['price'];
            mysqli_query($conn, "INSERT INTO uniform_sales_history 
                (uniform_id, quantity_sold, price_at_sale, sold_at) 
                VALUES ($id, -$qty_damaged, $price_at_sale, NOW())");

            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Damaged recorded. Stock and total sales adjusted.</div>';
        }
    } else {
        $message = '<div class="alert error">Invalid input.</div>';
    }
}

// ────────────────────────────────────────────────
// Handle Add Uniform
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_uniform') {
    $uniform_name    = mysqli_real_escape_string($conn, trim($_POST['uniform_name'] ?? ''));
    $category        = $_POST['category'] ?? '';
    $gender          = $_POST['gender'] ?? $category;
    $school_level    = mysqli_real_escape_string($conn, trim($_POST['school_level'] ?? ''));
    $item_type       = mysqli_real_escape_string($conn, trim($_POST['item_type'] ?? ''));
    $size            = mysqli_real_escape_string($conn, trim($_POST['size'] ?? ''));
    $color           = mysqli_real_escape_string($conn, trim($_POST['color'] ?? ''));
    $price           = floatval($_POST['price'] ?? 0);
    $quantity        = (int)($_POST['quantity'] ?? 0);
    $low_stock_limit = (int)($_POST['low_stock_limit'] ?? 10);
    $supplier_id     = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;

    if (empty($uniform_name) || empty($category) || empty($size) || $price <= 0 || $quantity < 0) {
        $message = '<div class="alert error">Please fill all required fields correctly.</div>';
    } else {
        $supplier_sql = $supplier_id ? $supplier_id : 'NULL';
        $query = "INSERT INTO uniform (uniform_name, category, gender, school_level, item_type, size, color, price, quantity, low_stock_limit, supplier_id, date_added)
                  VALUES ('$uniform_name', '$category', '$gender', '$school_level', '$item_type', '$size', '$color', $price, $quantity, $low_stock_limit, $supplier_sql, NOW())";
        if (mysqli_query($conn, $query)) {
            $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Uniform added successfully!</div>';
        } else {
            $message = '<div class="alert error">Error adding uniform: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// ────────────────────────────────────────────────
// Handle Edit Uniform
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_uniform') {
    $id              = (int)$_POST['uniform_id'];
    $uniform_name    = mysqli_real_escape_string($conn, trim($_POST['uniform_name']));
    $category        = $_POST['category'];
    $gender          = $_POST['gender'] ?? $category;
    $school_level    = mysqli_real_escape_string($conn, trim($_POST['school_level']));
    $item_type       = mysqli_real_escape_string($conn, trim($_POST['item_type']));
    $size            = mysqli_real_escape_string($conn, trim($_POST['size']));
    $color           = mysqli_real_escape_string($conn, trim($_POST['color']));
    $price           = floatval($_POST['price']);
    $quantity        = (int)$_POST['quantity'];
    $low_stock_limit = (int)$_POST['low_stock_limit'];
    $supplier_id     = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL;
    $supplier_sql    = $supplier_id ? $supplier_id : 'NULL';

    $query = "UPDATE uniform SET
                uniform_name    = '$uniform_name',
                category        = '$category',
                gender          = '$gender',
                school_level    = '$school_level',
                item_type       = '$item_type',
                size            = '$size',
                color           = '$color',
                price           = $price,
                quantity        = $quantity,
                low_stock_limit = $low_stock_limit,
                supplier_id     = $supplier_sql
              WHERE uniform_id = $id";

    if (mysqli_query($conn, $query)) {
        $message = '<div class="alert success"><i class="fas fa-check-circle"></i> Uniform updated successfully!</div>';
    } else {
        $message = '<div class="alert error">Error updating uniform: ' . mysqli_error($conn) . '</div>';
    }
}

// ────────────────────────────────────────────────
// Load data
// ────────────────────────────────────────────────
$all_uniforms = mysqli_fetch_all(mysqli_query($conn, "
    SELECT u.*, s.supplier_name
    FROM uniform u
    LEFT JOIN suppliers s ON u.supplier_id = s.id
    ORDER BY u.uniform_name ASC
"), MYSQLI_ASSOC);

$suppliers = mysqli_fetch_all(mysqli_query($conn, "
    SELECT id, supplier_name FROM suppliers
    WHERE status = 'active' AND supplier_type IN ('uniforms', 'both')
    ORDER BY supplier_name ASC
"), MYSQLI_ASSOC);

$history = mysqli_fetch_all(mysqli_query($conn, "
    SELECT h.*, u.uniform_name, u.size, u.color
    FROM uniform_sales_history h
    JOIN uniform u ON h.uniform_id = u.uniform_id
    ORDER BY h.sold_at DESC
"), MYSQLI_ASSOC);

$total_amount = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(quantity_sold * price_at_sale) as total FROM uniform_sales_history
"))['total'] ?? 0;
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

    body {
        background: var(--bg);
        font-family: 'Inter', sans-serif;
        color: #1e3c2c;
        margin: 0;
    }

    .main-content {
        padding: 24px 32px;
    }

    .dashboard {
        max-width: 1440px;
        margin: 0 auto;
    }

    /* ────────────────────────────────────────
    Header & Buttons
    ───────────────────────────────────────── */

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

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

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

    .btn-pill:hover {
        background: var(--green-dark);
    }

    .btn-outline {
        background: transparent;
        color: var(--green);
        border: 2px solid var(--green);
    }

    .btn-outline:hover {
        background: var(--green);
        color: white;
    }

    /* ────────────────────────────────────────
    Search & Filter
    ───────────────────────────────────────── */

    .top-controls {
        display: flex;
        align-items: center;
        gap: 40px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .search-container {
        flex: 1;
        min-width: 260px;
    }

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
        box-shadow: 0 0 0 3px rgba(46, 125, 94, 0.15);
    }

    .filter-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        max-width: 100%;
    }

    .filter-btn {
        padding: 9px 16px;
        border-radius: 999px;
        background: white;
        border: 1px solid #d4e8da;
        cursor: pointer;
        font-weight: 500;
        color: #1a4d2e;
        white-space: nowrap;
    }

    .filter-btn.active {
        background: var(--green);
        color: white;
        border-color: var(--green-dark);
    }

    /* ────────────────────────────────────────
    Table
    ───────────────────────────────────────── */

    .table-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #d4e8da;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    th,
    td {
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

    tr:hover {
        background: #f8fdfa;
    }

    /* Column widths */
    th:nth-child(1), td:nth-child(1) { width: 19%; }
    th:nth-child(2), td:nth-child(2) { width: 8%; }
    th:nth-child(3), td:nth-child(3) { width: 11%; }
    th:nth-child(4), td:nth-child(4) { width: 10%; }
    th:nth-child(5), td:nth-child(5) { width: 8%; }
    th:nth-child(6), td:nth-child(6) { width: 9%; text-align: right; }
    th:nth-child(7), td:nth-child(7) { width: 9%; text-align: right; }
    th:nth-child(8), td:nth-child(8) { width: 8%; }
    th:nth-child(9), td:nth-child(9) { width: 18%; min-width: 190px; }

    td {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    td:nth-child(1) {
        white-space: normal;
        word-break: break-word;
    }

    /* ────────────────────────────────────────
    Action Buttons
    ───────────────────────────────────────── */

    .action-btn {
        padding: 8px 10px;
        font-size: 13px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
        background: #f0f0f0;
        color: #333;
    }

    .action-btn i {
        font-size: 1.1em;
    }

    .action-btn.view {
        background: var(--orange);
        color: white;
    }
    .action-btn.view:hover {
        background: var(--orange-dark);
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(245, 124, 0, 0.4);
    }

    .action-btn.edit {
        background: var(--blue);
        color: white;
    }
    .action-btn.edit:hover {
        background: var(--blue-dark);
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.4);
    }

    .action-btn.sold {
        background: var(--red);
        color: white;
    }
    .action-btn.sold:hover {
        background: var(--red-dark);
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(211, 47, 47, 0.4);
    }

    .action-btn.return {
        background: #4caf50;
        color: white;
    }
    .action-btn.return:hover {
        background: #388e3c;
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    }

    .action-btn.damaged {
        background: #e91e63;
        color: white;
    }
    .action-btn.damaged:hover {
        background: #c2185b;
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.4);
    }

    /* ────────────────────────────────────────
    Badges, Modals, Forms, etc.
    ───────────────────────────────────────── */

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge.male { background: #bbdefb; color: #0d47a1; }
    .badge.female { background: #f8bbd0; color: #880e4f; }
    .badge.low-stock { background: #ffcdd2; color: #b71c1c; }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999 !important;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-container {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .modal-body {
        padding: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-grid .full-width {
        grid-column: 1/-1;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .form-control,
    textarea.form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        grid-column: 1/-1;
    }

    .save-btn,
    .confirm-btn {
        background: var(--green);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 30px;
        font-size: 1.125rem;
        font-weight: 500;
        cursor: pointer;
        transition: box-shadow 0.25s ease, transform 0.15s ease;
    }

    .save-btn:hover,
    .confirm-btn:hover {
        box-shadow: 2px 2px 8px rgba(0, 100, 0, 0.5),
                    -2px -2px 8px rgba(120, 255, 120, 0.45);
        transform: translateY(-1px);
    }

    .save-btn:active,
    .confirm-btn:active {
        transform: translateY(1px);
        box-shadow: 1px 1px 6px rgba(0, 100, 0, 0.55),
                    -1px -1px 6px rgba(100, 255, 100, 0.4);
    }

    .btn-cancel {
        background: var(--red);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 30px;
        font-size: 1.125rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.25s ease;
        letter-spacing: 0.3px;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 0, 0, 0.28),
                    inset 0 2px 6px rgba(255, 255, 255, 0.35);
        background: linear-gradient(to bottom, var(--red), #e60000);
    }

    .btn-cancel:active {
        transform: translateY(1px);
        box-shadow: 0 3px 10px rgba(255, 0, 0, 0.22),
                    inset 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Pagination, Alerts, History, Print */

    /* Pagination container - importante 'to para ma-center */
    .client-pagination {
        display: flex;
        justify-content: center;       /* ← nakasentro horizontally */
        align-items: center;           /* patayo na align */
        gap: 8px;                      /* mas magandang spacing sa pagitan ng buttons */
        margin: 32px 0 48px 0;         /* breathing room sa taas at baba */
        flex-wrap: wrap;               /* kung maraming page, bababa sa susunod na linya */
    }

    /* Individual buttons */
    .client-pagination button {
        margin: 0;                     /* tanggalin na yung dating margin: 0 5px; */
        min-width: 40px;               /* pantay-pantay ang lapad */
        padding: 8px 12px;
        border-radius: 999px;
        background: #e8f5e9;
        border: none;
        cursor: pointer;
        font-size: 14px;
        color: #1a4d2e;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    /* Hover effect */
    .client-pagination button:hover:not(:disabled) {
        background: var(--green);
        color: white;
        transform: scale(1.05);
    }

    /* Active/Current page */
    .client-pagination button.active {
        background: var(--green);
        color: white;
        box-shadow: 0 2px 8px rgba(46, 125, 94, 0.3);
    }

    /* Disabled buttons (Previous/Next kapag wala na) */
    .client-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
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

    .alert.success {
        background: #e2f0e6;
        color: #1a4d2e;
    }

    .alert.error {
        background: #ffebee;
        color: #c62828;
    }

    .history-controls {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .history-controls .search-input {
        flex: 1;
        min-width: 220px;
    }

    .history-controls input[type="date"] {
        padding: 10px 14px;
        border: 1px solid var(--green-dark);
        border-radius: 999px;
        background: var(--green);
        color: white;
        font-size: 15px;
        min-width: 170px;
        cursor: pointer;
    }

    .history-controls input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
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

    .print-btn:hover {
        background: #222;
    }

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

    .history-date {
        color: #555;
        font-size: 13px;
    }

    .print-area {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .print-area,
        .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
    </style>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard">
        <div class="header">
            <h1><i class="fas fa-tshirt"></i> Uniform Management</h1>
            <div class="header-right">
                <button class="btn-pill btn-outline" onclick="document.getElementById('historyModal').classList.add('active'); resetHistoryView();">
                    <i class="fas fa-history"></i> Sales History
                </button>
                <button class="btn-pill" onclick="document.getElementById('addModal').classList.add('active')">
                    <i class="fas fa-plus"></i> Add Uniform
                </button>
            </div>
        </div>

        <?= $message ?>

        <div class="top-controls">
            <div class="search-container">
                <input type="text" id="uniformSearch" class="search-input" placeholder="Search name, size, color, type...">
            </div>
            <div class="filter-bar" id="filterBar">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="male">Male</button>
                <button class="filter-btn" data-filter="female">Female</button>
                <button class="filter-btn" data-filter="Pre-school">Pre-school</button>
                <button class="filter-btn" data-filter="Elementary">Elementary</button>
                <button class="filter-btn" data-filter="Grade I-III">Grade I-III</button>
                <button class="filter-btn" data-filter="Intermediate">Intermediate</button>
                <button class="filter-btn" data-filter="High School">High School</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Uniform Name</th>
                        <th>Gender</th>
                        <th>Level</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Color</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="uniformsTableBody"></tbody>
            </table>
        </div>

        <div class="client-pagination" id="uniformsPagination"></div>

        <div class="total-amount">
            Total from Uniform Sales: <span style="color:var(--green);">₱<?= number_format($total_amount, 2) ?></span>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Uniform</h2>
            <button onclick="document.getElementById('addModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_uniform">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Uniform Name *</label>
                        <input type="text" name="uniform_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" class="form-control" required onchange="this.form.gender.value = this.value">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>School Level</label>
                        <select name="school_level" class="form-control">
                            <option value="">Select Level</option>
                            <option value="Pre-school">Pre-school</option>
                            <option value="Elementary">Elementary</option>
                            <option value="Grade I-III">Grade I-III</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="High School">High School</option>
                            <option value="All Levels">All Levels</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Item Type</label>
                        <select name="item_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="Skirt">Skirt</option>
                            <option value="Blouse">Blouse</option>
                            <option value="Dress">Dress</option>
                            <option value="Polo">Polo</option>
                            <option value="Shorts">Shorts</option>
                            <option value="Jogging Pants">Jogging Pants</option>
                            <option value="Shirt">Shirt</option>
                            <option value="PE Uniform">PE Uniform</option>
                            <option value="Accessory">Accessory</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size *</label>
                        <input type="text" name="size" class="form-control" placeholder="e.g. Small, XS, Waist 20 L18" required>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control" value="Default">
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
                    <button type="submit" class="save-btn">Add Uniform</button>
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
            <button onclick="document.getElementById('editModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit_uniform">
                <input type="hidden" name="uniform_id" id="edit_uniform_id">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Uniform Name *</label>
                        <input type="text" name="uniform_name" id="edit_uniform_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" id="edit_category" class="form-control" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" id="edit_gender" class="form-control">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>School Level</label>
                        <select name="school_level" id="edit_school_level" class="form-control">
                            <option value="">Select Level</option>
                            <option value="Pre-school">Pre-school</option>
                            <option value="Elementary">Elementary</option>
                            <option value="Grade I-III">Grade I-III</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="High School">High School</option>
                            <option value="All Levels">All Levels</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Item Type</label>
                        <select name="item_type" id="edit_item_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="Skirt">Skirt</option>
                            <option value="Blouse">Blouse</option>
                            <option value="Dress">Dress</option>
                            <option value="Polo">Polo</option>
                            <option value="Shorts">Shorts</option>
                            <option value="Jogging Pants">Jogging Pants</option>
                            <option value="Shirt">Shirt</option>
                            <option value="PE Uniform">PE Uniform</option>
                            <option value="Accessory">Accessory</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size *</label>
                        <input type="text" name="size" id="edit_size" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" id="edit_color" class="form-control">
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
                <input type="hidden" name="action" value="sold_uniform">
                <input type="hidden" name="uniform_id" id="sold_uniform_id">
                <div class="item-details">
                    <div>
                        <span>Item:</span>
                        <span id="sold_uniform_title"></span>
                    </div>
                    <div>
                        <span>Price:</span>
                        <span id="sold_uniform_price"></span>
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
                <input type="hidden" name="action" value="return_uniform">
                <input type="hidden" name="uniform_id" id="return_uniform_id">
                <div><strong>Item:</strong> <span id="return_item_name"></span></div>
                <div><strong>Current stock:</strong> <span id="return_current_stock"></span></div>
                <div class="form-group">
                    <label>Quantity to return</label>
                    <input type="number" name="quantity_returned" min="1" value="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="return_notes" rows="2" class="form-control"></textarea>
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
                <input type="hidden" name="action" value="damaged_uniform">
                <input type="hidden" name="uniform_id" id="damaged_uniform_id">
                <div><strong>Item:</strong> <span id="damaged_item_name"></span></div>
                <div><strong>Current stock:</strong> <span id="damaged_current_stock"></span></div>
                <div class="form-group">
                    <label>Quantity damaged</label>
                    <input type="number" name="quantity_damaged" min="1" value="1" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Reason / Notes (optional)</label>
                    <textarea name="damaged_reason" rows="2" class="form-control"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('damagedModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm Damaged</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-eye"></i> ACTIVITY LOGS</h2>
            <button onclick="document.getElementById('viewModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <div class="item-details" id="view_details"></div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal-overlay" id="historyModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-history"></i> Uniform Sales History</h2>
            <button onclick="document.getElementById('historyModal').classList.remove('active')">×</button>
        </div>
        <div class="modal-body">
            <div class="history-controls">
                <input type="text" id="historySearch" class="search-input" placeholder="Search uniform name...">
                <input type="date" id="historyDateFilter" title="Filter by exact date">
                <button class="print-btn" onclick="printHistory()"><i class="fas fa-print"></i> Print</button>
            </div>
            <div id="historyList"></div>
            <div class="client-pagination" id="historyPagination"></div>
        </div>
    </div>
</div>

<!-- Hidden print-friendly content -->
<div class="print-area" id="printContent">
    <h2 style="text-align:center; margin-bottom:8px;">La Trinidad Academy</h2>
    <h3 style="text-align:center; margin-top:0;">Uniform Sales History</h3>
    <p style="text-align:center; color:#555;" id="printDateRange"></p>
    <hr style="border:1px solid #ccc; margin:16px 0;">
    <table style="width:100%; border-collapse:collapse; font-family:Arial,sans-serif;">
        <thead>
            <tr style="background:#f0f7f2;">
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Date Sold</th>
                <th style="padding:10px; border:1px solid #ddd; text-align:left;">Item</th>
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
const allUniforms = <?= json_encode($all_uniforms) ?>;
const allHistory  = <?= json_encode($history) ?>;

// ── Modal open functions ───────────────────────────────────────────────
function openViewModal(id) {
    const u = allUniforms.find(x => Number(x.uniform_id) === Number(id));
    if (!u) return;

    document.getElementById('view_details').innerHTML = `
        <div style="margin-top:16px;">
            <h3 style="margin-bottom:12px; color:#1a4d2e;">Monitoring returnes and damages</h3>
            <pre style="background:#f8f9fa; padding:16px; border-radius:8px; white-space:pre-wrap; font-size:14px; max-height:350px; overflow-y:auto; line-height:1.5;">
${u.activity_log || 'No Activities.'}
            </pre>
        </div>
    `;

    document.getElementById('viewModal').classList.add('active');
}

function openEditModal(id) {
    const u = allUniforms.find(x => Number(x.uniform_id) === Number(id));
    if (!u) return;
    document.getElementById('edit_uniform_id').value = u.uniform_id;
    document.getElementById('edit_uniform_name').value = u.uniform_name;
    document.getElementById('edit_category').value = u.category || 'male';
    document.getElementById('edit_gender').value = u.gender || u.category || 'male';
    document.getElementById('edit_school_level').value = u.school_level || '';
    document.getElementById('edit_item_type').value = u.item_type || '';
    document.getElementById('edit_size').value = u.size || '';
    document.getElementById('edit_color').value = u.color || 'Default';
    document.getElementById('edit_price').value = u.price;
    document.getElementById('edit_quantity').value = u.quantity;
    document.getElementById('edit_low_stock_limit').value = u.low_stock_limit;
    document.getElementById('edit_supplier_id').value = u.supplier_id || '';
    document.getElementById('editModal').classList.add('active');
}

function openSoldModal(id) {
    const u = allUniforms.find(x => Number(x.uniform_id) === Number(id));
    if (!u) return;
    document.getElementById('sold_uniform_id').value = u.uniform_id;
    document.getElementById('sold_uniform_title').textContent = `${u.uniform_name} (${u.size || '—'})`;
    document.getElementById('sold_uniform_price').textContent = `₱${Number(u.price).toFixed(2)}`;
    const qtyInput = document.getElementById('sold_quantity');
    qtyInput.value = 1;
    qtyInput.max = u.quantity;
    document.getElementById('stock_info').textContent = `Available stock: ${u.quantity} pcs`;
    updateSoldTotal();
    document.getElementById('soldModal').classList.add('active');
}

function openReturnModal(id) {
    const u = allUniforms.find(x => Number(x.uniform_id) === Number(id));
    if (!u) return;
    document.getElementById('return_uniform_id').value = id;
    document.getElementById('return_item_name').textContent = u.uniform_name + ' (' + (u.size || '—') + ')';
    document.getElementById('return_current_stock').textContent = u.quantity + ' pcs';
    document.getElementById('returnModal').classList.add('active');
}

function openDamagedModal(id) {
    const u = allUniforms.find(x => Number(x.uniform_id) === Number(id));
    if (!u) return;
    document.getElementById('damaged_uniform_id').value = id;
    document.getElementById('damaged_item_name').textContent = u.uniform_name + ' (' + (u.size || '—') + ')';
    document.getElementById('damaged_current_stock').textContent = u.quantity + ' pcs';
    document.getElementById('damagedModal').classList.add('active');
}

function updateSoldTotal() {
    const qty = parseInt(document.getElementById('sold_quantity').value) || 0;
    const price = parseFloat(document.getElementById('sold_uniform_price').textContent.replace('₱','')) || 0;
    document.getElementById('sold_total').textContent = `₱${(qty * price).toFixed(2)}`;
}

// ── Uniform Table + Pagination ────────────────────────────────────────
let currentPage = 1;
const itemsPerPage = 10;

function renderUniforms() {
    const term = document.getElementById('uniformSearch').value.toLowerCase().trim();
    const activeFilter = document.querySelector('#filterBar .filter-btn.active')?.dataset.filter || 'all';

    const filtered = allUniforms.filter(u => {
        const searchMatch =
            (u.uniform_name   || '').toLowerCase().includes(term) ||
            (u.item_type      || '').toLowerCase().includes(term) ||
            (u.size           || '').toLowerCase().includes(term) ||
            (u.color          || '').toLowerCase().includes(term);

        let filterMatch = true;
        if (activeFilter !== 'all') {
            if (activeFilter === 'male' || activeFilter === 'female') {
                filterMatch = (u.gender || u.category || '').toLowerCase() === activeFilter;
            } else {
                filterMatch = (u.school_level || '').toLowerCase() === activeFilter.toLowerCase();
            }
        }
        return searchMatch && filterMatch;
    });

    const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
    currentPage = Math.min(currentPage, totalPages);
    const start = (currentPage - 1) * itemsPerPage;
    const pageItems = filtered.slice(start, start + itemsPerPage);

    const tbody = document.getElementById('uniformsTableBody');
    tbody.innerHTML = pageItems.length === 0
        ? '<tr><td colspan="9" style="text-align:center; padding:60px;">No uniforms found.</td></tr>'
        : '';

    pageItems.forEach(u => {
        const tr = document.createElement('tr');
        const lowStock = Number(u.quantity) <= Number(u.low_stock_limit) ? 'badge low-stock' : '';
        tr.innerHTML = `
            <td title="${u.uniform_name}">${u.uniform_name}</td>
            <td><span class="badge ${(u.gender||u.category||'').toLowerCase()}">${u.gender||u.category||'—'}</span></td>
            <td>${u.school_level || '—'}</td>
            <td>${u.item_type || '—'}</td>
            <td>${u.size || '—'}</td>
            <td>₱${Number(u.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td><span class="${lowStock}">${Number(u.quantity).toLocaleString()} pcs</span></td>
            <td>${u.color || 'Default'}</td>
            <td class="action-buttons">
                <button type="button" class="action-btn view"    onclick="openViewModal(${u.uniform_id})"    title="View"><i class="fas fa-eye"></i></button>
                <button type="button" class="action-btn edit"    onclick="openEditModal(${u.uniform_id})"    title="Edit"><i class="fas fa-edit"></i></button>
                <button type="button" class="action-btn return"  onclick="openReturnModal(${u.uniform_id})"  title="Return"><i class="fas fa-undo"></i></button>
                <button type="button" class="action-btn damaged" onclick="openDamagedModal(${u.uniform_id})" title="Damaged"><i class="fas fa-times-circle"></i></button>
                <button type="button" class="action-btn sold"    onclick="openSoldModal(${u.uniform_id})"    title="Sell"><i class="fas fa-shopping-cart"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    const pag = document.getElementById('uniformsPagination');
    pag.innerHTML = '';
    if (totalPages > 1) {
        let html = `<button ${currentPage===1?'disabled':''} onclick="if(currentPage>1){currentPage--;renderUniforms()}">Previous</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
                html += `<button class="${i===currentPage?'active':''}" onclick="currentPage=${i};renderUniforms()">${i}</button>`;
            } else if (Math.abs(i - currentPage) === 3) {
                html += '<button disabled>...</button>';
            }
        }
        html += `<button ${currentPage===totalPages?'disabled':''} onclick="if(currentPage<${totalPages}){currentPage++;renderUniforms()}">Next</button>`;
        pag.innerHTML = html;
    }
}

// ── History Rendering & Print ─────────────────────────────────────────
let historyPage = 1;
const historyPerPage = 10;

function renderHistory() {
    const searchTerm = document.getElementById('historySearch').value.toLowerCase().trim();
    const dateFilter = document.getElementById('historyDateFilter').value;

    const filtered = allHistory.filter(h => {
        const nameMatch = (h.uniform_name || '').toLowerCase().includes(searchTerm);
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
                <span>${h.uniform_name} (${h.size || '—'})</span>
                <span class="history-date">${new Date(h.sold_at).toLocaleString('en-PH', {
                    year: 'numeric', month: 'short', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
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

function printHistory() {
    const searchTerm = document.getElementById('historySearch').value.toLowerCase().trim();
    const dateFilter = document.getElementById('historyDateFilter').value;

    const filtered = allHistory.filter(h => {
        const nameMatch = (h.uniform_name || '').toLowerCase().includes(searchTerm);
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
                <td style="padding:10px; border:1px solid #ddd;">${new Date(h.sold_at).toLocaleDateString('en-PH')}</td>
                <td style="padding:10px; border:1px solid #ddd;">${h.uniform_name} (${h.size || '—'})</td>
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
        <head><title>Uniform Sales History - La Trinidad Academy</title></head>
        <body style="font-family:Arial,sans-serif; margin:40px; color:#333;">
            ${document.getElementById('printContent').innerHTML}
        </body>
        </html>
    `);
    printWin.document.close();
    setTimeout(() => printWin.print(), 600);
}

// ── Event Listeners ────────────────────────────────────────────────────
document.querySelectorAll('#filterBar .filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#filterBar .filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPage = 1;
        renderUniforms();
    });
});

document.getElementById('uniformSearch').addEventListener('input', () => {
    currentPage = 1;
    renderUniforms();
});

document.getElementById('sold_quantity')?.addEventListener('input', updateSoldTotal);

document.getElementById('historySearch')?.addEventListener('input', () => { historyPage = 1; renderHistory(); });
document.getElementById('historyDateFilter')?.addEventListener('change', () => { historyPage = 1; renderHistory(); });

// ── INIT ───────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    renderUniforms();
    console.log("Uniforms page fully loaded");
});
</script>
</body>
</html>