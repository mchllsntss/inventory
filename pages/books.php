<?php
// Set current page for sidebar active state
$current_page = 'books_inventory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Book Purchases</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --green-500: #10b981;
            --green-600: #059669;
            --green-700: #047857;
            --green-100: #d1fae5;
            --green-50: #ecfdf5;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--bg);
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
        }

        .main-content {
            padding: 32px;
            transition: margin-left 0.3s ease;
        }

        .dashboard {
            max-width: 1480px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .title-section h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--green-700);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .title-section h1 i {
            background: var(--green-100);
            color: var(--green-600);
            padding: 14px;
            border-radius: var(--radius-md);
            font-size: 1.8rem;
        }

        .badge-academy {
            background: var(--green-50);
            color: var(--green-700);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 500;
            border: 1px solid var(--green-100);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .date-chip {
            background: white;
            border: 1px solid var(--border);
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 0.95rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--green-600);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(16,185,129,0.2);
        }

        .btn-primary:hover {
            background: var(--green-700);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .search-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            max-width: 420px;
            box-shadow: var(--shadow-sm);
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 18px;
            border-radius: 999px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-tab.active,
        .filter-tab:hover {
            background: var(--green-600);
            color: white;
            border-color: var(--green-600);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        .stat-card h3 {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--green-700);
        }

        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--green-50);
            color: var(--green-700);
            font-weight: 600;
            text-align: left;
            padding: 16px 20px;
            font-size: 0.9rem;
            border-bottom: 2px solid var(--green-100);
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text);
            font-size: 0.95rem;
        }

        tr:nth-child(even) { background: #f8fafc; }
        tr:hover { background: var(--green-50); }

        .quantity-cell { font-weight: 600; color: var(--green-700); }
        .total-cell    { font-weight: 700; color: var(--green-600); font-size: 1.05rem; }

        .status-badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.pending  { background: #fef3c7; color: #92400e; }
        .status-badge.received { background: var(--green-100); color: var(--green-700); }
        .status-badge.returned { background: #fee2e2; color: #b91c1c; }

        .action-btn {
            background: none;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 0.85rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn.edit:hover  { border-color: var(--green-500); color: var(--green-700); background: var(--green-50); }
        .action-btn.delete:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

        /* Modal styles improved */
        .modal-overlay.active {
            display: flex;
            background: rgba(15,23,42,0.6);
        }

        .modal-container {
            background: white;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 640px;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }

        .modal-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            color: var(--green-700);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Your sidebar include + open button here (unchanged) -->
    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard">

            <div class="header">
                <div class="title-section">
                    <h1><i class="fas fa-book"></i> Book Purchases</h1>
                    <div class="badge-academy">
                        <i class="fas fa-shopping-cart"></i> Received & Pending Orders
                    </div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt"></i> 
                        <?= date('F d, Y') ?>
                    </div>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Record Purchase
                    </button>
                </div>
            </div>

            <!-- Quick stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Purchases</h3>
                    <div class="stat-value" id="totalPurchases">0</div>
                </div>
                <div class="stat-card">
                    <h3>Total Books Received</h3>
                    <div class="stat-value" id="totalQty">0</div>
                </div>
                <div class="stat-card">
                    <h3>Total Spent</h3>
                    <div class="stat-value" id="totalSpent">₱0.00</div>
                </div>
            </div>

            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search" style="color:var(--text-muted)"></i>
                    <input type="text" id="searchInput" placeholder="Search title, ISBN, supplier...">
                </div>
                <div class="filter-tabs">
                    <span class="filter-tab active" data-filter="all">All</span>
                    <span class="filter-tab" data-filter="received">Received</span>
                    <span class="filter-tab" data-filter="pending">Pending</span>
                    <span class="filter-tab" data-filter="returned">Returned</span>
                </div>
            </div>

            <div class="table-container">
                <table id="purchasesTable">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>ISBN</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="purchasesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal and rest of HTML + JavaScript remain almost the same -->
    <!-- ... paste your existing modal HTML here ... -->

    <script>
        // Your existing variables + sample data here
        // Add these lines after renderPurchases() definition:

        function updateStats() {
            const received = purchases.filter(p => p.status === 'received');
            const totalQty = received.reduce((sum, p) => sum + p.quantity, 0);
            const totalSpent = received.reduce((sum, p) => sum + (p.quantity * p.unitPrice), 0);

            document.getElementById('totalPurchases').textContent = received.length;
            document.getElementById('totalQty').textContent = totalQty;
            document.getElementById('totalSpent').textContent = 
                '₱' + totalSpent.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
        }

        // Call updateStats() at the end of renderPurchases()
        function renderPurchases() {
            // ... your existing render code ...
            updateStats();
        }

        // Rest of your JS (openAddModal, savePurchase, etc.) remains the same
    </script>
</body>
</html>