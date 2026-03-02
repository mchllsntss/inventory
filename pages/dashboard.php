<?php
// dashboard.php - Main Dashboard with REAL DB data
$current_page = 'dashboard';

require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];

// ────────────────────────────────────────────────
// METRICS (real from DB)
// ────────────────────────────────────────────────

// Total Books
$total_books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM books"))['cnt'] ?? 0;

// Total Uniform Items
$total_uniforms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM uniform"))['cnt'] ?? 0;

// Low Stock Alerts (both tables)
$low_stock_query = "
    SELECT COUNT(*) as cnt FROM (
        SELECT uniform_id FROM uniform WHERE quantity <= low_stock_limit
        UNION ALL
        SELECT book_id FROM books WHERE quantity <= low_stock_limit
    ) AS low
";
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, $low_stock_query))['cnt'] ?? 0;

// Issued This Week - count ng recent sales within current week
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
// BOOKS SECTION - real data (limit 5 for display)
// ────────────────────────────────────────────────
$books_result = mysqli_query($conn, "
    SELECT book_name, grade_level, subject, quantity, low_stock_limit 
    FROM books 
    ORDER BY book_name ASC 
    LIMIT 5
");
$books_data = [];
while ($row = mysqli_fetch_assoc($books_result)) {
    $percent = $row['low_stock_limit'] > 0 ? min(100, ($row['quantity'] / ($row['low_stock_limit'] * 10)) * 100) : 0;
    $books_data[] = [
        'grade'    => $row['grade_level'],
        'name'     => $row['book_name'],
        'edition'  => $row['subject'],
        'stock'    => $row['quantity'],
        'percent'  => round($percent)
    ];
}

// ────────────────────────────────────────────────
// UNIFORMS SECTION - real data (limit 5 for display)
// ────────────────────────────────────────────────
$uniforms_result = mysqli_query($conn, "
    SELECT uniform_name, category, size, quantity, low_stock_limit 
    FROM uniform 
    ORDER BY uniform_name ASC 
    LIMIT 5
");
$uniforms_data = [];
while ($row = mysqli_fetch_assoc($uniforms_result)) {
    $percent = $row['low_stock_limit'] > 0 ? min(100, ($row['quantity'] / ($row['low_stock_limit'] * 10)) * 100) : 0;
    $uniforms_data[] = [
        'category' => $row['category'] . ' (' . $row['size'] . ')',
        'name'     => $row['uniform_name'],
        'size'     => $row['size'],
        'stock'    => $row['quantity'],
        'percent'  => round($percent)
    ];
}

// ────────────────────────────────────────────────
// RECENT MOVEMENTS - combined history (5 per page)
// ────────────────────────────────────────────────
$per_page = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

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
    LIMIT $offset, $per_page
";
$movements_result = mysqli_query($conn, $movements_query);
$paginated_transactions = [];
while ($row = mysqli_fetch_assoc($movements_result)) {
    $paginated_transactions[] = $row;
}

// Total pages for pagination
$total_movements_query = "
    SELECT COUNT(*) as total 
    FROM (
        SELECT book_id FROM book_sales_history
        UNION ALL
        SELECT uniform_id FROM uniform_sales_history
    ) AS all_mov
";
$total_movements = mysqli_fetch_assoc(mysqli_query($conn, $total_movements_query))['total'] ?? 0;
$pages = ceil($total_movements / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Inventory Dashboard</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">

    <!-- YOUR ORIGINAL CSS - hindi binago -->
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#f0f7f2; font-family:'Inter',sans-serif; color:#1e3c2c; min-height:100vh; }
        .main-content { padding:24px 32px; transition:margin-left 0.3s cubic-bezier(0.4,0,0.2,1); min-height:100vh; background:#f0f7f2; }
        .main-content.sidebar-closed { margin-left:0; }
        .dashboard { max-width:1440px; margin:0 auto; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom:32px; flex-wrap:wrap; gap:16px; }
        .title-section h1 { font-family:'Outfit',sans-serif; font-weight:600; font-size:28px; color:#1a4d2e; display:flex; align-items:center; gap:12px; }
        .title-section h1 i { color:#2e7d5e; background:rgba(46,125,94,0.12); padding:10px; border-radius:16px; font-size:28px; }
        .badge-academy { background:#e2f0e6; padding:8px 18px; border-radius:40px; font-weight:500; font-size:15px; color:#1a4d2e; display:inline-flex; align-items:center; gap:8px; letter-spacing:0.2px; border:1px solid #b8d9c4; }
        .badge-academy i { color:#2e7d5e; }
        .top-actions { display:flex; gap:16px; align-items:center; }
        .date-chip { background:white; border-radius:40px; padding:8px 20px; font-weight:500; box-shadow:0 2px 6px rgba(0,40,20,0.04); border:1px solid #d4e8da; font-size:14px; }
        .btn-outline { background:white; border:1px solid #b8d9c4; border-radius:40px; padding:8px 20px; font-weight:500; color:#1a4d2e; transition:0.2s; cursor:default; font-size:14px; }
        .btn-outline i { color:#2e7d5e; margin-right:6px; }
        .metric-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:22px; margin-bottom:32px; }
        .metric-card { background:white; border-radius:28px; padding:22px 20px; box-shadow:0 6px 18px rgba(0,40,20,0.04); border:1px solid #d4e8da; transition:transform 0.1s ease; display:flex; align-items:center; gap:16px; }
        .metric-card:hover { transform:translateY(-2px); box-shadow:0 12px 24px rgba(0,40,20,0.08); }
        .metric-icon { width:58px; height:58px; background:#e2f0e6; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:28px; color:#1a4d2e; }
        .metric-content h3 { font-size:15px; font-weight:500; color:#4a6b57; margin-bottom:6px; letter-spacing:0.02em; }
        .metric-content .value { font-size:34px; font-weight:700; color:#1a4d2e; line-height:1.1; }
        .metric-content .sub { font-size:14px; color:#4a6b57; margin-top:6px; display:flex; align-items:center; gap:4px; }
        .sub i { font-size:12px; color:#2e7d5e; }
        .inventory-sections { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:32px; }
        .section-card { background:white; border-radius:32px; padding:20px 20px 24px; border:1px solid #d4e8da; box-shadow:0 10px 22px -8px rgba(26,77,46,0.08); transition:transform 0.2s ease,box-shadow 0.2s ease; }
        .section-card:hover { box-shadow:0 16px 28px -8px rgba(26,77,46,0.12); }
        .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding:0 8px; }
        .section-header h2 { font-family:'Outfit',sans-serif; font-weight:600; font-size:20px; color:#1a4d2e; display:flex; align-items:center; gap:8px; }
        .section-header h2 i { color:#2e7d5e; background:#e2f0e6; padding:8px; border-radius:14px; font-size:18px; }
        .stock-pill { background:#e2f0e6; border-radius:30px; padding:6px 16px; font-size:14px; font-weight:600; color:#1a4d2e; }
        .stock-pill i { color:#2e7d5e; margin-right:4px; font-size:12px; }
        .list-item { display:flex; align-items:center; justify-content:space-between; padding:14px 12px; border-radius:20px; background:#f5fbf7; margin-bottom:8px; transition:all 0.2s ease; border:1px solid transparent; }
        .list-item:hover { border-color:#b8d9c4; background:white; transform:translateX(4px); }
        .item-info { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
        .item-category { min-width:110px; }
        .item-category small { font-weight:500; color:#4a6b57; }
        .item-name { font-weight:600; color:#1a4d2e; font-size:16px; display:flex; align-items:center; gap:8px; }
        .item-name small { font-weight:400; font-size:13px; color:#4a6b57; background:#e2f0e6; padding:3px 10px; border-radius:40px; }
        .stock-indicator { display:flex; align-items:center; gap:16px; }
        .stock-bar-bg { width:100px; height:8px; background:#d4e8da; border-radius:20px; overflow:hidden; }
        .stock-bar-fill { height:100%; border-radius:20px; background:#2e7d5e; transition:width 0.3s ease; }
        .fill-low { background:#c24f4a; }
        .fill-medium { background:#e68a3e; }
        .item-qty { font-weight:700; font-size:16px; color:#1a4d2e; min-width:50px; text-align:right; }
        .item-qty span { font-weight:400; font-size:13px; color:#4a6b57; margin-left:2px; }
        .lower-panel { background:white; border-radius:30px; padding:22px 24px; border:1px solid #d4e8da; box-shadow:0 8px 20px -10px rgba(26,77,46,0.06); margin-top:16px; }
        .panel-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
        .panel-header h3 { font-weight:600; font-size:18px; color:#1a4d2e; display:flex; align-items:center; gap:8px; }
        .transaction-list { display:flex; flex-direction:column; gap:12px; }
        .transaction-row { display:flex; align-items:center; justify-content:space-between; padding:12px 8px; border-bottom:1px solid #d4e8da; transition:background 0.2s ease; }
        .transaction-row:hover { background:#f5fbf7; border-radius:12px; padding:12px 16px; margin:0 -8px; }
        .transaction-row:last-child { border-bottom:none; }
        .tx-type { display:flex; align-items:center; gap:15px; width:40%; }
        .tx-badge { background:#e2f0e6; border-radius:40px; padding:4px 14px; font-size:13px; font-weight:600; color:#1a4d2e; white-space:nowrap; }
        .tx-badge.warning { background:#fff3e6; color:#b4521c; }
        .tx-badge i { margin-right:4px; font-size:11px; color:#2e7d5e; }
        .tx-desc { font-weight:500; color:#1a4d2e; }
        .tx-meta { display:flex; gap:30px; align-items:center; color:#4a6b57; font-size:14px; }
        .tx-qty { font-weight:600; color:#1a4d2e; }
        .tx-time i { margin-right:5px; font-size:12px; opacity:0.8; }
        .chip-warning { background:#fff3e6; color:#b4521c; border-radius:30px; padding:4px 14px; font-size:13px; font-weight:500; }
        .chip-success { background:#e2f0e6; color:#1d784d; }
        .pagination { margin-top:20px; text-align:center; }
        .pagination a { margin:0 6px; padding:8px 14px; border-radius:30px; background:#e8f5e9; text-decoration:none; color:#1a4d2e; font-weight:500; }
        .pagination a:hover { background:#d4e8da; }
        .pagination a.active { background:#2e7d5e; color:white; }
        #openSidebarBtn {
            position:fixed; top:24px; left:24px; z-index:45;
            background:white; border:1px solid #d4e8da; border-radius:12px;
            padding:12px 16px; color:#1a4d2e; font-size:14px; font-weight:500;
            cursor:pointer; display:flex; align-items:center; gap:8px;
            box-shadow:0 4px 12px rgba(0,0,0,0.05); transition:all 0.2s ease;
        }
        #openSidebarBtn:hover { background:#f0f7f2; border-color:#b8d9c4; transform:translateY(-1px); box-shadow:0 6px 16px rgba(0,0,0,0.08); }
        #openSidebarBtn.hidden { display:none; }
        @media (max-width:1200px) { .metric-grid { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:1000px) { .inventory-sections { grid-template-columns:1fr; } }
        @media (max-width:768px) { .main-content { margin-left:0; padding:16px; } }
        @media (max-width:550px) {
            .metric-grid { grid-template-columns:1fr; }
            .header { flex-direction:column; align-items:start; margin-top:60px; }
            .top-actions { width:100%; justify-content:space-between; }
            .transaction-row { flex-direction:column; align-items:flex-start; gap:8px; }
            .tx-type { width:100%; }
            .tx-meta { width:100%; justify-content:space-between; }
        }
    </style>
</head>
<body>

    <!-- Menu button -->
    <button id="openSidebarBtn" class="hidden">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </button>

    <!-- Sidebar -->
    <?php include '../components/sidebar.php'; ?>

    <!-- Main content -->
    <div class="main-content">
        <div class="dashboard">

            <!-- Header -->
            <div class="header">
                <div class="title-section">
                    <h1>
                        <i class="fas fa-archway"></i>
                        La Trinidad Academy
                    </h1>
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
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-book-open"></i></div>
                    <div class="metric-content">
                        <h3>Total Books</h3>
                        <div class="value"><?= number_format($total_books) ?></div>
                        <div class="sub"><i class="fas fa-arrow-up"></i> Updated Today</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-tshirt"></i></div>
                    <div class="metric-content">
                        <h3>Uniform Items</h3>
                        <div class="value"><?= number_format($total_uniforms) ?></div>
                        <div class="sub"><i class="fas fa-arrow-down" style="color:#b34a4a;"></i> <?= $low_stock ?> Low</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="metric-content">
                        <h3>Low Stock Alerts</h3>
                        <div class="value"><?= $low_stock ?></div>
                        <div class="sub"><i class="fas fa-clock"></i> Check Now</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="metric-content">
                        <h3>Issued This Week</h3>
                        <div class="value"><?= $issued_this_week ?></div>
                        <div class="sub"><i class="fas fa-undo-alt"></i> Recent Sales</div>
                    </div>
                </div>
            </div>

            <!-- Books & Uniforms Sections -->
            <div class="inventory-sections">
                <!-- BOOKS SECTION -->
                <div class="section-card">
                    <div class="section-header">
                        <h2><i class="fas fa-book"></i> Academic Books</h2>
                        <div class="stock-pill"><i class="fas fa-layer-group"></i> <?= $total_books ?> Titles</div>
                    </div>

                    <?php if (empty($books_data)): ?>
                        <p style="text-align:center; color:#4a6b57; padding:30px;">No books in inventory yet.</p>
                    <?php else: ?>
                        <?php foreach ($books_data as $item): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <span class="item-category"><small><?= htmlspecialchars($item['grade']) ?></small></span>
                                <span class="item-name"><?= htmlspecialchars($item['name']) ?> <small><?= htmlspecialchars($item['edition']) ?></small></span>
                            </div>
                            <div class="stock-indicator">
                                <div class="stock-bar-bg">
                                    <div class="stock-bar-fill <?= $item['percent'] <= 20 ? 'fill-low' : ($item['percent'] <= 50 ? 'fill-medium' : '') ?>" style="width:<?= $item['percent'] ?>%;"></div>
                                </div>
                                <span class="item-qty"><?= number_format($item['stock']) ?> <span>Left</span></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- UNIFORMS SECTION -->
                <div class="section-card">
                    <div class="section-header">
                        <h2><i class="fas fa-vest"></i> School Uniforms</h2>
                        <div class="stock-pill"><i class="fas fa-ruler-combined"></i> <?= $total_uniforms ?> Variants</div>
                    </div>

                    <?php if (empty($uniforms_data)): ?>
                        <p style="text-align:center; color:#4a6b57; padding:30px;">No uniforms in inventory yet.</p>
                    <?php else: ?>
                        <?php foreach ($uniforms_data as $item): ?>
                        <div class="list-item">
                            <div class="item-info">
                                <span class="item-category"><small><?= htmlspecialchars($item['category']) ?></small></span>
                                <span class="item-name"><?= htmlspecialchars($item['name']) ?> <small><?= htmlspecialchars($item['size']) ?></small></span>
                            </div>
                            <div class="stock-indicator">
                                <div class="stock-bar-bg">
                                    <div class="stock-bar-fill <?= $item['percent'] <= 20 ? 'fill-low' : ($item['percent'] <= 50 ? 'fill-medium' : '') ?>" style="width:<?= $item['percent'] ?>%;"></div>
                                </div>
                                <span class="item-qty"><?= number_format($item['stock']) ?> <span>pcs</span></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Inventory Movements with Pagination -->
            <div class="lower-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-history" style="color:#2e7d5e;"></i> Recent Inventory Movements</h3>
                    <span class="chip-success" style="background:#e2f0e6; padding:6px 18px;">
                        <i class="fas fa-check-circle"></i> <?= $total_movements ?> Transactions
                    </span>
                </div>

                <div class="transaction-list">
                    <?php if (empty($paginated_transactions)): ?>
                        <p style="text-align:center; color:#4a6b57; padding:40px 20px;">
                            No recent movements recorded yet.
                        </p>
                    <?php else: ?>
                        <?php foreach ($paginated_transactions as $tx): ?>
                        <div class="transaction-row">
                            <div class="tx-type">
                                <span class="tx-badge">
                                    <i class="fas fa-<?= $tx['type'] === 'book' ? 'book' : 'tshirt' ?>"></i> 
                                    <?= ucfirst($tx['type']) ?> Sold
                                </span>
                                <span class="tx-desc">
                                    <?= htmlspecialchars($tx['name']) ?> · 
                                    <?= $tx['quantity_sold'] ?> pcs
                                </span>
                            </div>
                            <div class="tx-meta">
                                <span class="tx-qty">₱<?= number_format($tx['price_at_sale'] * $tx['quantity_sold'], 2) ?></span>
                                <span class="tx-time">
                                    <i class="far fa-clock"></i> 
                                    <?= date('M d, Y g:i A', strtotime($tx['sold_at'])) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            </div>

        </div>
    </div>

    <!-- Sidebar toggle script (your original) -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('openSidebarBtn');
        const mainContent = document.querySelector('.main-content');
        let isClosed = false;

        function closeSidebar() {
            sidebar.classList.add('close-sidebar');
            openSidebarBtn.classList.remove('hidden');
            mainContent.classList.add('sidebar-closed');
            isClosed = true;
            saveSidebarState();
        }

        function openSidebar() {
            sidebar.classList.remove('close-sidebar');
            openSidebarBtn.classList.add('hidden');
            mainContent.classList.remove('sidebar-closed');
            isClosed = false;
            saveSidebarState();
        }

        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', openSidebar);
        }

        function saveSidebarState() {
            localStorage.setItem('sidebarClosed', isClosed);
        }

        function loadSidebarState() {
            const savedState = localStorage.getItem('sidebarClosed');
            if (savedState !== null) {
                isClosed = savedState === 'true';
                if (isClosed) {
                    sidebar.classList.add('close-sidebar');
                    openSidebarBtn.classList.remove('hidden');
                    mainContent.classList.add('sidebar-closed');
                } else {
                    sidebar.classList.remove('close-sidebar');
                    openSidebarBtn.classList.add('hidden');
                    mainContent.classList.remove('sidebar-closed');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', loadSidebarState);

        function handleResize() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            } else {
                const savedState = localStorage.getItem('sidebarClosed');
                if (savedState === 'false' || savedState === null) {
                    openSidebar();
                }
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>
</html>