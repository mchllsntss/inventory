<?php
// Set current page for sidebar active state
$current_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · inventory dashboard</title>
    <!-- Font Awesome 5 (free) -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">
    <style>
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

        /* Main content wrapper - positioned to the right of fixed sidebar */
        .main-content {
            padding: 24px 32px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: #f0f7f2;
        }

        /* When sidebar is closed */
        .main-content.sidebar-closed {
            margin-left: 0;
        }

        .dashboard {
            max-width: 1440px;
            margin: 0 auto;
        }

        /* header with academy identity */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .title-section h1 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 28px;
            color: #1a4d2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .title-section h1 i {
            color: #2e7d5e;
            background: rgba(46, 125, 94, 0.12);
            padding: 10px;
            border-radius: 16px;
            font-size: 28px;
        }

        .badge-academy {
            background: #e2f0e6;
            padding: 8px 18px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 15px;
            color: #1a4d2e;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.2px;
            border: 1px solid #b8d9c4;
        }

        .badge-academy i {
            color: #2e7d5e;
        }

        /* date / quick actions */
        .top-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .date-chip {
            background: white;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0,40,20,0.04);
            border: 1px solid #d4e8da;
            font-size: 14px;
        }

        .btn-outline {
            background: white;
            border: 1px solid #b8d9c4;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
            color: #1a4d2e;
            transition: 0.2s;
            cursor: default;
            font-size: 14px;
        }

        .btn-outline i {
            color: #2e7d5e;
            margin-right: 6px;
        }

        /* metrics cards */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: white;
            border-radius: 28px;
            padding: 22px 20px;
            box-shadow: 0 6px 18px rgba(0, 40, 20, 0.04);
            border: 1px solid #d4e8da;
            transition: transform 0.1s ease;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 40, 20, 0.08);
        }

        .metric-icon {
            width: 58px;
            height: 58px;
            background: #e2f0e6;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #1a4d2e;
        }

        .metric-content h3 {
            font-size: 15px;
            font-weight: 500;
            color: #4a6b57;
            margin-bottom: 6px;
            letter-spacing: 0.02em;
        }

        .metric-content .value {
            font-size: 34px;
            font-weight: 700;
            color: #1a4d2e;
            line-height: 1.1;
        }

        .metric-content .sub {
            font-size: 14px;
            color: #4a6b57;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sub i {
            font-size: 12px;
            color: #2e7d5e;
        }

        /* two main sections (books & uniforms) */
        .inventory-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .section-card {
            background: white;
            border-radius: 32px;
            padding: 20px 20px 24px 20px;
            border: 1px solid #d4e8da;
            box-shadow: 0 10px 22px -8px rgba(26, 77, 46, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .section-card:hover {
            box-shadow: 0 16px 28px -8px rgba(26, 77, 46, 0.12);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding: 0 8px;
        }

        .section-header h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 20px;
            color: #1a4d2e;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-header h2 i {
            color: #2e7d5e;
            background: #e2f0e6;
            padding: 8px;
            border-radius: 14px;
            font-size: 18px;
        }

        .stock-pill {
            background: #e2f0e6;
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 14px;
            font-weight: 600;
            color: #1a4d2e;
        }

        .stock-pill i {
            color: #2e7d5e;
            margin-right: 4px;
            font-size: 12px;
        }

        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 12px;
            border-radius: 20px;
            background: #f5fbf7;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .list-item:hover {
            border-color: #b8d9c4;
            background: white;
            transform: translateX(4px);
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .item-category {
            min-width: 110px;
        }

        .item-category small {
            font-weight: 500;
            color: #4a6b57;
        }

        .item-name {
            font-weight: 600;
            color: #1a4d2e;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-name small {
            font-weight: 400;
            font-size: 13px;
            color: #4a6b57;
            background: #e2f0e6;
            padding: 3px 10px;
            border-radius: 40px;
        }

        .stock-indicator {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stock-bar-bg {
            width: 100px;
            height: 8px;
            background: #d4e8da;
            border-radius: 20px;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: #2e7d5e;
            transition: width 0.3s ease;
        }

        .fill-low {
            background: #c24f4a;
        }

        .fill-medium {
            background: #e68a3e;
        }

        .item-qty {
            font-weight: 700;
            font-size: 16px;
            color: #1a4d2e;
            min-width: 50px;
            text-align: right;
        }

        .item-qty span {
            font-weight: 400;
            font-size: 13px;
            color: #4a6b57;
            margin-left: 2px;
        }

        /* recent activity / transactions combined */
        .lower-panel {
            background: white;
            border-radius: 30px;
            padding: 22px 24px;
            border: 1px solid #d4e8da;
            box-shadow: 0 8px 20px -10px rgba(26, 77, 46, 0.06);
            margin-top: 16px;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .panel-header h3 {
            font-weight: 600;
            font-size: 18px;
            color: #1a4d2e;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .transaction-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .transaction-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 8px;
            border-bottom: 1px solid #d4e8da;
            transition: background 0.2s ease;
        }

        .transaction-row:hover {
            background: #f5fbf7;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 0 -8px;
        }

        .transaction-row:last-child {
            border-bottom: none;
        }

        .tx-type {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 40%;
        }

        .tx-badge {
            background: #e2f0e6;
            border-radius: 40px;
            padding: 4px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #1a4d2e;
            white-space: nowrap;
        }

        .tx-badge i {
            margin-right: 4px;
            font-size: 11px;
            color: #2e7d5e;
        }

        .tx-desc {
            font-weight: 500;
            color: #1a4d2e;
        }

        .tx-meta {
            display: flex;
            gap: 30px;
            align-items: center;
            color: #4a6b57;
            font-size: 14px;
        }

        .tx-qty {
            font-weight: 600;
            color: #1a4d2e;
        }

        .tx-time i {
            margin-right: 5px;
            font-size: 12px;
            opacity: 0.8;
        }

        .chip-warning {
            background: #fff3e6;
            color: #b4521c;
            border-radius: 30px;
            padding: 4px 14px;
            font-size: 13px;
            font-weight: 500;
        }

        .chip-success {
            background: #e2f0e6;
            color: #1d784d;
        }

        /* Menu button styling to match sidebar */
        #openSidebarBtn {
            position: fixed;
            top: 24px;
            left: 24px;
            z-index: 45;
            background: white;
            border: 1px solid #d4e8da;
            border-radius: 12px;
            padding: 12px 16px;
            color: #1a4d2e;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        #openSidebarBtn:hover {
            background: #f0f7f2;
            border-color: #b8d9c4;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        #openSidebarBtn.hidden {
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .metric-grid { 
                grid-template-columns: repeat(2,1fr); 
            }
        }
        
        @media (max-width: 1000px) {
            .inventory-sections { 
                grid-template-columns: 1fr; 
            }
        }
        
        @media (max-width: 768px) {
            .main-content { 
                margin-left: 0;
                padding: 16px;
            }
            
            .main-content.sidebar-closed {
                margin-left: 0;
            }
            
            #openSidebarBtn {
                top: 16px;
                left: 16px;
                padding: 10px 14px;
            }
        }
        
        @media (max-width: 550px) {
            .metric-grid { 
                grid-template-columns: 1fr; 
            }
            
            .header { 
                flex-direction: column; 
                align-items: start; 
                margin-top: 60px;
            }
            
            .top-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .transaction-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .tx-type {
                width: 100%;
            }
            
            .tx-meta {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <!-- Menu button (visible when sidebar is closed) -->
    <button id="openSidebarBtn" class="hidden">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </button>

    <!-- Include sidebar -->
    <?php include '../components/sidebar.php'; ?>
    
    <!-- Main content wrapper - shifted right -->
    <div class="main-content">
        <div class="dashboard">
            <!-- header -->
            <div class="header">
                <div class="title-section">
                    <h1>
                        <i class="fas fa-archway"></i> 
                        La Trinidad Academy
                    </h1>
                    <div class="badge-academy">
                        <i class="fas fa-user-graduate"></i> books & uniforms · inventory
                    </div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt" style="margin-right: 8px; color:#2e7d5e;"></i> 13 Feb 2026
                    </div>
                    <div class="btn-outline"><i class="fas fa-sync-alt"></i> update stock</div>
                </div>
            </div>

            <!-- metric cards (4) -->
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-book-open"></i></div>
                    <div class="metric-content">
                        <h3>total books</h3>
                        <div class="value">1,426</div>
                        <div class="sub"><i class="fas fa-arrow-up"></i> +23 this month</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-tshirt"></i></div>
                    <div class="metric-content">
                        <h3>uniform items</h3>
                        <div class="value">843</div>
                        <div class="sub"><i class="fas fa-arrow-down" style="color:#b34a4a;"></i> -8 low stock</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="metric-content">
                        <h3>low stock alerts</h3>
                        <div class="value">7</div>
                        <div class="sub"><i class="fas fa-clock"></i> 3 uniforms, 4 books</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="metric-content">
                        <h3>issued this week</h3>
                        <div class="value">94</div>
                        <div class="sub"><i class="fas fa-undo-alt"></i> 28 returns pending</div>
                    </div>
                </div>
            </div>

            <!-- books & uniforms detailed sections -->
            <div class="inventory-sections">
                <!-- BOOKS SECTION -->
                <div class="section-card">
                    <div class="section-header">
                        <h2><i class="fas fa-book"></i> Academic books</h2>
                        <div class="stock-pill"><i class="fas fa-layer-group"></i> 12 titles</div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>grade 7</small></span>
                            <span class="item-name">English literature <small>F. 2026</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill" style="width: 85%"></div></div>
                            <span class="item-qty">187 <span>left</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>grade 8</small></span>
                            <span class="item-name">Mathematics 8 <small>rev.</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill" style="width: 42%"></div></div>
                            <span class="item-qty">94 <span>left</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>grade 9</small></span>
                            <span class="item-name">Science & Tech <small>lab</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill fill-medium" style="width: 28%"></div></div>
                            <span class="item-qty">62 <span>left</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>grade 10</small></span>
                            <span class="item-name">Filipino panitikan <small>gamit</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill fill-low" style="width: 15%"></div></div>
                            <span class="item-qty">33 <span>left</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>grade 11</small></span>
                            <span class="item-name">General Physics <small>STEM</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill" style="width: 67%"></div></div>
                            <span class="item-qty">148 <span>left</span></span>
                        </div>
                    </div>
                </div>

                <!-- UNIFORMS SECTION -->
                <div class="section-card">
                    <div class="section-header">
                        <h2><i class="fas fa-vest"></i> School uniforms</h2>
                        <div class="stock-pill"><i class="fas fa-ruler-combined"></i> 8 variants</div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>blouse (girls)</small></span>
                            <span class="item-name">white short sleeve <small>7-10</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill" style="width: 73%"></div></div>
                            <span class="item-qty">206 <span>pcs</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>polo (boys)</small></span>
                            <span class="item-name">khaki button-down <small>gr.7-10</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill fill-low" style="width: 11%"></div></div>
                            <span class="item-qty">27 <span>pcs</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>skirt (girls)</small></span>
                            <span class="item-name">pleated navy <small>gr.7-12</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill fill-medium" style="width: 35%"></div></div>
                            <span class="item-qty">84 <span>pcs</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>trousers</small></span>
                            <span class="item-name">navy slim fit <small>boys</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill fill-low" style="width: 19%"></div></div>
                            <span class="item-qty">42 <span>pcs</span></span>
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="item-info">
                            <span class="item-category"><small>P.E. uniform</small></span>
                            <span class="item-name">dri-fit shirt <small>all</small></span>
                        </div>
                        <div class="stock-indicator">
                            <div class="stock-bar-bg"><div class="stock-bar-fill" style="width: 62%"></div></div>
                            <span class="item-qty">153 <span>pcs</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- lower panel: recent activity / transaction log -->
            <div class="lower-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-history" style="color: #2e7d5e;"></i> recent inventory movements</h3>
                    <span class="chip-success" style="background:#e2f0e6; padding:6px 18px;"><i class="fas fa-check-circle"></i> 9 transactions today</span>
                </div>
                <div class="transaction-list">
                    <div class="transaction-row">
                        <div class="tx-type">
                            <span class="tx-badge"><i class="fas fa-sign-out-alt"></i> issued</span>
                            <span class="tx-desc">Filipino 10 (book) · 28 copies</span>
                        </div>
                        <div class="tx-meta">
                            <span class="tx-qty">to Gr.10</span>
                            <span class="tx-time"><i class="far fa-clock"></i> 09:45 AM</span>
                        </div>
                    </div>
                    <div class="transaction-row">
                        <div class="tx-type">
                            <span class="tx-badge"><i class="fas fa-sign-in-alt"></i> returned</span>
                            <span class="tx-desc">Khaki polo (boys) · 12 pcs</span>
                        </div>
                        <div class="tx-meta">
                            <span class="tx-qty">good cond.</span>
                            <span class="tx-time"><i class="far fa-clock"></i> 10:12 AM</span>
                        </div>
                    </div>
                    <div class="transaction-row">
                        <div class="tx-type">
                            <span class="tx-badge"><i class="fas fa-plus-circle"></i> restock</span>
                            <span class="tx-desc">English lit G7 · 45 new books</span>
                        </div>
                        <div class="tx-meta">
                            <span class="tx-qty">supplier: ABM</span>
                            <span class="tx-time"><i class="far fa-clock"></i> 11:30 AM</span>
                        </div>
                    </div>
                    <div class="transaction-row">
                        <div class="tx-type">
                            <span class="tx-badge"><i class="fas fa-exclamation"></i> low stock</span>
                            <span class="tx-desc">Khaki trousers (boys) only 9 left</span>
                        </div>
                        <div class="tx-meta">
                            <span class="chip-warning"><i class="fas fa-exclamation-triangle"></i> reorder</span>
                            <span class="tx-time"><i class="far fa-clock"></i> 13:20 PM</span>
                        </div>
                    </div>
                    <div class="transaction-row">
                        <div class="tx-type">
                            <span class="tx-badge"><i class="fas fa-undo-alt"></i> damaged</span>
                            <span class="tx-desc">Science 9 (3 copies, withdrawn)</span>
                        </div>
                        <div class="tx-meta">
                            <span class="tx-qty">for mending</span>
                            <span class="tx-time"><i class="far fa-clock"></i> 14:05 PM</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Get DOM elements
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('openSidebarBtn');
        const mainContent = document.querySelector('.main-content');
        
        // State variable for closed state
        let isClosed = false;
        
        // Close sidebar function
        function closeSidebar() {
            sidebar.classList.add('close-sidebar');
            openSidebarBtn.classList.remove('hidden');
            mainContent.classList.add('sidebar-closed');
            isClosed = true;
            saveSidebarState();
        }
        
        // Open sidebar function
        function openSidebar() {
            sidebar.classList.remove('close-sidebar');
            openSidebarBtn.classList.add('hidden');
            mainContent.classList.remove('sidebar-closed');
            isClosed = false;
            saveSidebarState();
        }
        
        // Open sidebar when button is clicked
        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', openSidebar);
        }
        
        // Store sidebar state in localStorage for persistence
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
        
        // Load saved state on page load
        document.addEventListener('DOMContentLoaded', loadSidebarState);
        
        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth <= 768) {
                // On mobile, always close sidebar
                closeSidebar();
            } else {
                // On desktop, restore saved state
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