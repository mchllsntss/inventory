<?php
// Set current page for sidebar active state
$current_page = 'uniforms_inventory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Uniform Purchases</title>
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

        .main-content { padding: 32px; transition: margin-left 0.3s ease; }
        .dashboard { max-width: 1480px; margin: 0 auto; }

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

        .stat-card h3 { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 8px; font-weight: 500; }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: var(--green-700); }

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

        .filter-tab.active, .filter-tab:hover {
            background: var(--green-600);
            color: white;
            border-color: var(--green-600);
        }

        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        table { width: 100%; border-collapse: collapse; }

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
            font-size: 0.95rem;
        }

        tr:nth-child(even) { background: #f8fafc; }
        tr:hover { background: var(--green-50); }

        .quantity-cell { font-weight: 600; color: var(--green-700); }
        .total-cell    { font-weight: 700; color: var(--green-600); }

        .status-badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.received { background: var(--green-100); color: var(--green-700); }
        .status-badge.pending  { background: #fef3c7; color: #92400e; }
        .status-badge.low-stock { background: #fee2e2; color: #b91c1c; }

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

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal-container { background: white; border-radius: var(--radius-lg); width: 90%; max-width: 640px; max-height: 92vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(0,0,0,0.25); }
        .modal-header { padding: 24px 32px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h2 { font-family: 'Outfit', sans-serif; font-size: 1.6rem; color: var(--green-700); display: flex; align-items: center; gap: 12px; }
        .modal-close { background: none; border: none; font-size: 1.6rem; color: var(--text-muted); cursor: pointer; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: var(--green-700); font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.95rem; }
        .form-control:focus { outline: none; border-color: var(--green-500); box-shadow: 0 0 0 3px rgba(16,185,129,0.15); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 16px; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); }

        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar include (same as your other pages) -->
    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard">

            <div class="header">
                <div class="title-section">
                    <h1><i class="fas fa-tshirt"></i> Uniform Purchases</h1>
                    <div class="badge-academy">
                        <i class="fas fa-boxes"></i> Received & Stock Tracking
                    </div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt"></i> 
                        <?= date('F d, Y') ?>
                    </div>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Record Uniform Purchase
                    </button>
                </div>
            </div>

            <!-- Quick stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Uniform Items</h3>
                    <div class="stat-value" id="totalItems">0</div>
                </div>
                <div class="stat-card">
                    <h3>Total Quantity Received</h3>
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
                    <input type="text" id="searchInput" placeholder="Search type, size, gender, supplier...">
                </div>
                <div class="filter-tabs">
                    <span class="filter-tab active" data-filter="all">All</span>
                    <span class="filter-tab" data-filter="received">Received</span>
                    <span class="filter-tab" data-filter="pending">Pending</span>
                    <span class="filter-tab" data-filter="low-stock">Low Stock</span>
                </div>
            </div>

            <div class="table-container">
                <table id="uniformsTable">
                    <thead>
                        <tr>
                            <th>Uniform Type</th>
                            <th>Gender</th>
                            <th>Size</th>
                            <th>Supplier</th>
                            <th>Qty Received</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="uniformsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="uniformModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle" id="modalIcon"></i><span id="modalTitle">Record New Uniform Purchase</span></h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body" style="padding:32px;">
                <form id="uniformForm">
                    <input type="hidden" id="uniformId">

                    <div class="form-group">
                        <label>Uniform Type <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control" id="uniformType" required placeholder="e.g. Type A Polo, PE Shirt, Senior High Blazer">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender</label>
                            <select class="form-control" id="gender">
                                <option value="">Any / Unisex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Size <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control" id="size" required placeholder="e.g. S, M, L, XL, 28, 30">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Supplier <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control" id="supplier" required placeholder="e.g. Uniform Solutions Co.">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Quantity Received <span style="color:#ef4444;">*</span></label>
                            <input type="number" class="form-control" id="quantity" min="1" required value="25">
                        </div>
                        <div class="form-group">
                            <label>Unit Price (₱) <span style="color:#ef4444;">*</span></label>
                            <input type="number" class="form-control" id="unitPrice" step="0.01" required value="450.00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Purchase / Received Date <span style="color:#ef4444;">*</span></label>
                            <input type="date" class="form-control" id="receivedDate" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status">
                                <option value="received" selected>Received</option>
                                <option value="pending">Pending Delivery</option>
                                <option value="low-stock">Low Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remarks / Batch / PO #</label>
                        <textarea class="form-control" id="remarks" rows="2" placeholder="PO# 2026-034 • Batch 2026-A"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" style="padding:12px 24px;border:1px solid var(--border);border-radius:999px;" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal (simple version) -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container" style="max-width:420px;text-align:center;padding:32px;">
            <div style="font-size:3rem;color:#ef4444;margin-bottom:16px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Delete Record?</h3>
            <p id="deleteMessage" style="margin:16px 0;color:var(--text-muted);">This action cannot be undone.</p>
            <div style="display:flex;justify-content:center;gap:16px;">
                <button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-primary" style="background:#ef4444;" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let uniforms = [
            { id:1, uniformType:"Type A Polo", gender:"Male", size:"M", supplier:"Uniform Solutions Co.", quantity:60, unitPrice:420.00, receivedDate:"2026-02-10", status:"received", remarks:"PO# 2026-015" },
            { id:2, uniformType:"PE Shirt", gender:"Female", size:"L", supplier:"Elite Uniforms", quantity:45, unitPrice:280.00, receivedDate:"2026-02-12", status:"received", remarks:"Batch Feb-26" },
            { id:3, uniformType:"Senior High Blazer", gender:"Any", size:"XL", supplier:"Uniform Solutions Co.", quantity:12, unitPrice:1250.00, receivedDate:"2026-02-05", status:"low-stock", remarks:"Only 12 left" },
            { id:4, uniformType:"Type B Skirt", gender:"Female", size:"S", supplier:"Global School Supply", quantity:8, unitPrice:520.00, receivedDate:"2026-01-28", status:"pending", remarks:"Expected Feb 28" },
        ];

        let currentFilter = 'all';
        let searchTerm = '';
        let deleteId = null;

        const searchInput = document.getElementById('searchInput');
        const filterTabs = document.querySelectorAll('.filter-tab');
        const form = document.getElementById('uniformForm');

        function openAddModal() {
            document.getElementById('modalIcon').className = 'fas fa-plus-circle';
            document.getElementById('modalTitle').textContent = 'Record New Uniform Purchase';
            form.reset();
            document.getElementById('uniformId').value = '';
            document.getElementById('status').value = 'received';
            document.getElementById('receivedDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('uniformModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('uniformModal').classList.remove('active');
        }

        function editUniform(id) {
            const item = uniforms.find(u => u.id === id);
            if (!item) return;

            document.getElementById('modalIcon').className = 'fas fa-edit';
            document.getElementById('modalTitle').textContent = 'Edit Uniform Record';

            Object.keys(item).forEach(key => {
                const el = document.getElementById(key);
                if (el) el.value = item[key];
            });

            document.getElementById('uniformModal').classList.add('active');
        }

        function showDeleteModal(id, type) {
            deleteId = id;
            document.getElementById('deleteMessage').textContent = `Delete "${type}" record? This cannot be undone.`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                uniforms = uniforms.filter(u => u.id !== deleteId);
                closeDeleteModal();
                renderTable();
            }
        }

        function saveUniform(e) {
            e.preventDefault();
            const data = {
                id: document.getElementById('uniformId').value ? parseInt(document.getElementById('uniformId').value) : Date.now(),
                uniformType: document.getElementById('uniformType').value.trim(),
                gender: document.getElementById('gender').value,
                size: document.getElementById('size').value.trim(),
                supplier: document.getElementById('supplier').value.trim(),
                quantity: parseInt(document.getElementById('quantity').value),
                unitPrice: parseFloat(document.getElementById('unitPrice').value),
                receivedDate: document.getElementById('receivedDate').value,
                status: document.getElementById('status').value,
                remarks: document.getElementById('remarks').value.trim()
            };

            if (document.getElementById('uniformId').value) {
                const idx = uniforms.findIndex(u => u.id === data.id);
                if (idx !== -1) uniforms[idx] = data;
            } else {
                uniforms.push(data);
            }

            closeModal();
            renderTable();
        }

        function renderTable() {
            const tbody = document.getElementById('uniformsTableBody');
            let filtered = uniforms.filter(u => {
                if (currentFilter !== 'all' && u.status !== currentFilter) return false;
                if (searchTerm) {
                    const q = searchTerm.toLowerCase();
                    return (
                        u.uniformType.toLowerCase().includes(q) ||
                        (u.gender||'').toLowerCase().includes(q) ||
                        (u.size||'').toLowerCase().includes(q) ||
                        u.supplier.toLowerCase().includes(q)
                    );
                }
                return true;
            });

            tbody.innerHTML = filtered.length === 0 ? `
                <tr><td colspan="10" style="text-align:center;padding:60px;color:var(--text-muted);">
                    <i class="fas fa-box-open" style="font-size:3rem;margin-bottom:16px;display:block;"></i>
                    No uniform records found
                </td></tr>
            ` : filtered.map(u => {
                const total = (u.quantity * u.unitPrice).toFixed(2);
                const statusClass = `status-badge ${u.status}`;
                const statusIcon = u.status === 'received' ? 'fa-check-circle' :
                                  u.status === 'pending' ? 'fa-hourglass-half' : 'fa-exclamation-triangle';

                return `
                <tr>
                    <td><strong>${u.uniformType}</strong></td>
                    <td>${u.gender || '—'}</td>
                    <td>${u.size}</td>
                    <td>${u.supplier}</td>
                    <td class="quantity-cell">${u.quantity}</td>
                    <td>₱${u.unitPrice.toFixed(2)}</td>
                    <td class="total-cell">₱${total}</td>
                    <td>${new Date(u.receivedDate).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'})}</td>
                    <td><span class="${statusClass}"><i class="fas ${statusIcon}"></i> ${u.status.replace('-',' ').replace(/\b\w/g,c=>c.toUpperCase())}</span></td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <button class="action-btn edit" onclick="editUniform(${u.id})"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-btn delete" onclick="showDeleteModal(${u.id}, '${u.uniformType.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            // Update stats (only received items)
            const received = uniforms.filter(u => u.status === 'received');
            const totalQty = received.reduce((sum, u) => sum + u.quantity, 0);
            const totalSpent = received.reduce((sum, u) => sum + (u.quantity * u.unitPrice), 0);

            document.getElementById('totalItems').textContent = received.length;
            document.getElementById('totalQty').textContent = totalQty;
            document.getElementById('totalSpent').textContent = '₱' + totalSpent.toLocaleString('en-PH',{minimumFractionDigits:2});
        }

        function setFilter(filter) {
            currentFilter = filter;
            filterTabs.forEach(t => t.classList.toggle('active', t.dataset.filter === filter));
            renderTable();
        }

        // Event listeners
        form.addEventListener('submit', saveUniform);
        searchInput.addEventListener('input', e => { searchTerm = e.target.value; renderTable(); });
        filterTabs.forEach(tab => tab.addEventListener('click', () => setFilter(tab.dataset.filter)));

        // Init
        document.addEventListener('DOMContentLoaded', renderTable);
    </script>
</body>
</html>