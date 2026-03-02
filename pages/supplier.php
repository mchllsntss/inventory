<?php
// suppliers.php
$current_page = 'suppliers';
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $supplier_name     = mysqli_real_escape_string($conn, trim($_POST['supplier_name'] ?? ''));
    $contact_person    = mysqli_real_escape_string($conn, trim($_POST['contact_person'] ?? ''));
    $email             = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone             = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address           = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $supplier_type     = $_POST['supplier_type'] ?? 'both';
    $payment_terms     = mysqli_real_escape_string($conn, trim($_POST['payment_terms'] ?? ''));
    $status            = $_POST['status'] ?? 'active';
    $last_order        = $_POST['last_order'] ?? NULL;  // pwede NULL

    if ($action === 'add') {
        $last_order_sql = $last_order ? "'$last_order'" : 'NULL';
        $query = "INSERT INTO suppliers 
                  (supplier_name, contact_person, email, phone, address, 
                   supplier_type, payment_terms, status, last_order, created_at)
                  VALUES 
                  ('$supplier_name', '$contact_person', '$email', '$phone', '$address',
                   '$supplier_type', '$payment_terms', '$status', $last_order_sql, NOW())";

        if (mysqli_query($conn, $query)) {
            $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:500; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-check-circle" style="font-size:20px;"></i>
                            Supplier added successfully!
                        </div>';
        } else {
            $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                            <strong>Error adding supplier:</strong> ' . mysqli_error($conn) . '
                        </div>';
        }
    } elseif ($action === 'edit' && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $last_order_sql = $last_order ? "'$last_order'" : 'NULL';
        $query = "UPDATE suppliers SET
                    supplier_name     = '$supplier_name',
                    contact_person    = '$contact_person',
                    email             = '$email',
                    phone             = '$phone',
                    address           = '$address',
                    supplier_type     = '$supplier_type',
                    payment_terms     = '$payment_terms',
                    status            = '$status',
                    last_order        = $last_order_sql,
                    updated_at        = NOW()
                  WHERE id = $id";

        if (mysqli_query($conn, $query)) {
            $message = '<div style="background:#e2f0e6; color:#1a4d2e; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:500; display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-check-circle" style="font-size:20px;"></i>
                            Supplier updated successfully!
                        </div>';
        } else {
            $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                            <strong>Error updating supplier:</strong> ' . mysqli_error($conn) . '
                        </div>';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $query = "DELETE FROM suppliers WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: suppliers.php?msg=deleted");
        exit;
    } else {
        $message = '<div style="background:#ffebee; color:#c62828; padding:14px 20px; border-radius:12px; margin-bottom:24px;">
                        <strong>Error deleting supplier:</strong> ' . mysqli_error($conn) . '
                    </div>';
    }
}

$result = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY supplier_name ASC");
$suppliers = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Supplier Management</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">

    <!-- YOUR ORIGINAL CSS - WALANG BINAGO -->
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
        .btn-primary, .btn-save { background:#2e7d5e; border:none; border-radius:40px; padding:10px 24px; font-weight:600; color:white; transition:0.2s; cursor:pointer; font-size:14px; display:flex; align-items:center; gap:8px; border:1px solid #1a4d2e; }
        .btn-primary:hover, .btn-save:hover { background:#1a4d2e; transform:translateY(-2px); box-shadow:0 6px 12px rgba(26,77,46,0.2); }
        .btn-secondary { background:white; border:1px solid #b8d9c4; border-radius:40px; padding:8px 20px; font-weight:500; color:#1a4d2e; transition:0.2s; cursor:pointer; font-size:14px; display:inline-flex; align-items:center; gap:6px; }
        .btn-secondary:hover { background:#f0f7f2; border-color:#2e7d5e; }
        .btn-danger { background:#fff3f0; border:1px solid #ffcdc0; border-radius:40px; padding:8px 20px; font-weight:500; color:#b34a4a; transition:0.2s; cursor:pointer; font-size:14px; display:inline-flex; align-items:center; gap:6px; }
        .btn-danger:hover { background:#ffe8e0; border-color:#b34a4a; }
        .filter-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
        .search-box { background:white; border:1px solid #d4e8da; border-radius:40px; padding:10px 20px; display:flex; align-items:center; gap:12px; flex:1; max-width:400px; }
        .search-box i { color:#4a6b57; font-size:16px; }
        .search-box input { border:none; outline:none; font-family:'Inter',sans-serif; font-size:14px; width:100%; background:transparent; }
        .filter-tabs { display:flex; gap:10px; flex-wrap:wrap; }
        .filter-tab { padding:8px 20px; border-radius:40px; background:white; border:1px solid #d4e8da; font-size:14px; font-weight:500; color:#4a6b57; cursor:pointer; transition:all 0.2s ease; }
        .filter-tab.active { background:#2e7d5e; border-color:#1a4d2e; color:white; }
        .filter-tab:hover { border-color:#2e7d5e; }
        .table-container { background:white; border-radius:32px; padding:20px; border:1px solid #d4e8da; box-shadow:0 10px 22px -8px rgba(26,77,46,0.08); overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:16px 12px; font-weight:600; font-size:14px; color:#1a4d2e; border-bottom:2px solid #d4e8da; }
        td { padding:16px 12px; border-bottom:1px solid #e2f0e6; color:#1e3c2c; font-size:14px; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f5fbf7; }
        .supplier-badge { background:#e2f0e6; border-radius:40px; padding:4px 12px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
        .status-badge { border-radius:40px; padding:4px 12px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; }
        .status-active { background:#e2f0e6; color:#1d784d; }
        .status-inactive { background:#ffeee6; color:#b4521c; }
        .action-buttons { display:flex; gap:8px; flex-wrap:wrap; }
        .action-btn { background:none; border:1px solid #d4e8da; border-radius:30px; padding:6px 14px; font-size:13px; font-weight:500; color:#1a4d2e; cursor:pointer; transition:all 0.2s ease; display:inline-flex; align-items:center; gap:6px; }
        .action-btn:hover { background:#f0f7f2; border-color:#2e7d5e; }
        .action-btn.edit:hover { background:#e2f0e6; border-color:#2e7d5e; }
        .action-btn.delete:hover { background:#fff3f0; border-color:#b34a4a; color:#b34a4a; }
        .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center; }
        .modal-overlay.active { display:flex; }
        .modal-container { background:white; border-radius:32px; width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.2); border:1px solid #d4e8da; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; padding:24px 32px; border-bottom:1px solid #e2f0e6; }
        .modal-header h2 { font-family:'Outfit',sans-serif; font-size:24px; color:#1a4d2e; display:flex; align-items:center; gap:12px; }
        .modal-header h2 i { color:#2e7d5e; background:rgba(46,125,94,0.12); padding:8px; border-radius:12px; }
        .modal-close { background:none; border:none; font-size:24px; color:#4a6b57; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s ease; }
        .modal-close:hover { background:#fff3f0; color:#b34a4a; }
        .modal-body { padding:32px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:flex; align-items:center; gap:8px; font-weight:500; color:#1a4d2e; margin-bottom:8px; font-size:14px; }
        .form-group label i { color:#2e7d5e; font-size:16px; }
        .form-group label span { color:#b34a4a; margin-left:4px; }
        .form-control { width:100%; padding:12px 16px; border:1px solid #d4e8da; border-radius:16px; font-family:'Inter',sans-serif; font-size:14px; transition:all 0.2s ease; background:#fafdfb; }
        .form-control:focus { outline:none; border-color:#2e7d5e; background:white; box-shadow:0 0 0 3px rgba(46,125,94,0.1); }
        textarea.form-control { resize:vertical; min-height:80px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-actions { display:flex; justify-content:flex-end; gap:12px; margin-top:32px; padding-top:20px; border-top:1px solid #e2f0e6; }
        .delete-modal .modal-container { max-width:400px; text-align:center; }
        .delete-icon { font-size:48px; color:#b34a4a; margin-bottom:16px; }
        .delete-modal h3 { font-size:20px; color:#1a4d2e; margin-bottom:8px; }
        .delete-modal p { color:#4a6b57; margin-bottom:24px; }
        .delete-actions { display:flex; justify-content:center; gap:16px; }
    </style>
</head>
<body>

    <?php include '../components/sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard">
            <div class="header">
                <div class="title-section">
                    <h1><i class="fas fa-truck"></i> Supplier Management</h1>
                    <div class="badge-academy"><i class="fas fa-boxes"></i> book & uniform suppliers · vendor directory</div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt" style="margin-right:8px; color:#2e7d5e;"></i>
                        <?= date('F d, Y') ?>
                    </div>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus-circle"></i> Add Supplier
                    </button>
                </div>
            </div>

            <?= $message ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                <div style="background:#fff3f0; color:#b34a4a; padding:14px 20px; border-radius:12px; margin-bottom:24px; font-weight:500; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-trash-alt" style="font-size:20px;"></i>
                    Supplier successfully deleted.
                </div>
            <?php endif; ?>

            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search suppliers by name, contact, or category...">
                </div>
                <div class="filter-tabs">
                    <span class="filter-tab active" data-filter="all">All Suppliers</span>
                    <span class="filter-tab" data-filter="books">Books</span>
                    <span class="filter-tab" data-filter="uniforms">Uniforms</span>
                    <span class="filter-tab" data-filter="both">Both</span>
                    <span class="filter-tab" data-filter="active">Active</span>
                </div>
            </div>

            <div class="table-container">
                <table id="suppliersTable">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Contact Person</th>
                            <th>Email / Phone</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody">
                        <?php if (empty($suppliers)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color:#4a6b57;">
                                    <i class="fas fa-box-open" style="font-size:32px; margin-bottom:12px; display:block;"></i>
                                    No suppliers found in the database yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($suppliers as $s): ?>
                                <tr data-id="<?= $s['id'] ?>"
                                    data-address="<?= htmlspecialchars($s['address'] ?? '') ?>"
                                    data-lastorder="<?= htmlspecialchars($s['last_order'] ?? '') ?>">
                                    <td>
                                        <strong style="color:#1a4d2e;"><?= htmlspecialchars($s['supplier_name']) ?></strong>
                                        <div style="font-size:12px; color:#4a6b57;"><?= htmlspecialchars($s['payment_terms'] ?? '') ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($s['contact_person'] ?: '—') ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($s['email'] ?: '—') ?></div>
                                        <div style="font-size:12px; color:#4a6b57;"><?= htmlspecialchars($s['phone'] ?: '—') ?></div>
                                    </td>
                                    <td>
                                        <?php
                                        $type = $s['supplier_type'] ?? 'both';
                                        $badge = match($type) {
                                            'books' => '<span class="supplier-badge"><i class="fas fa-book"></i> Books</span>',
                                            'uniforms' => '<span class="supplier-badge"><i class="fas fa-tshirt"></i> Uniforms</span>',
                                            default => '<span class="supplier-badge"><i class="fas fa-boxes"></i> Both</span>'
                                        };
                                        echo $badge;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($s['status'] === 'active'): ?>
                                            <span class="status-badge status-active"><i class="fas fa-check-circle"></i> Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive"><i class="fas fa-clock"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $s['last_order'] ? date('M d, Y', strtotime($s['last_order'])) : '—' ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" onclick="editSupplier(<?= $s['id'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-btn delete" onclick="showDeleteModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['supplier_name'])) ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Supplier Modal -->
    <div class="modal-overlay" id="supplierModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-plus-circle" id="modalIcon"></i>
                    <span id="modalTitle">Add New Supplier</span>
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="supplierId">

                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Supplier Name <span>*</span></label>
                        <input type="text" name="supplier_name" id="supplierName" class="form-control" required placeholder="Enter supplier name">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Contact Person</label>
                        <input type="text" name="contact_person" id="contactPerson" class="form-control" placeholder="Enter contact person name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Enter complete address"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Supplier Type</label>
                            <select name="supplier_type" id="supplierType" class="form-control">
                                <option value="books">Books Only</option>
                                <option value="uniforms">Uniforms Only</option>
                                <option value="both" selected>Both Books and Uniforms</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-credit-card"></i> Payment Terms</label>
                            <input type="text" name="payment_terms" id="paymentTerms" class="form-control" placeholder="e.g. Net 30, COD" value="Net 30">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Last Order Date</label>
                            <input type="date" name="last_order" id="lastOrder" class="form-control">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay delete-modal" id="deleteModal">
        <div class="modal-container">
            <div class="delete-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3>Delete Supplier</h3>
            <p id="deleteSupplierName">Are you sure you want to delete this supplier? This action cannot be undone.</p>
            <div class="delete-actions">
                <button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-danger" onclick="document.location.href='?delete='+deleteId">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;

        function openAddModal() {
            document.getElementById('modalIcon').className = 'fas fa-plus-circle';
            document.getElementById('modalTitle').textContent = 'Add New Supplier';
            document.getElementById('formAction').value = 'add';
            document.getElementById('supplierId').value = '';
            document.getElementById('supplierForm').reset();
            document.getElementById('lastOrder').value = ''; // o pwede default today: new Date().toISOString().split('T')[0]
            document.getElementById('supplierModal').classList.add('active');
        }

        function editSupplier(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;

            document.getElementById('modalIcon').className = 'fas fa-edit';
            document.getElementById('modalTitle').textContent = 'Edit Supplier';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('supplierId').value = id;

            document.getElementById('supplierName').value   = row.cells[0].querySelector('strong').textContent.trim();
            document.getElementById('contactPerson').value = row.cells[1].textContent.trim();
            document.getElementById('email').value         = row.cells[2].querySelector('div')?.textContent.trim() || '';
            document.getElementById('phone').value         = row.cells[2].querySelector('div[style]')?.textContent.trim() || '';
            document.getElementById('address').value       = row.dataset.address || '';
            document.getElementById('lastOrder').value     = row.dataset.lastorder || '';

            const typeText = row.cells[3].textContent.trim().toLowerCase();
            document.getElementById('supplierType').value = typeText.includes('books') ? 'books' :
                                                            typeText.includes('uniforms') ? 'uniforms' : 'both';

            document.getElementById('paymentTerms').value = row.cells[0].querySelector('div')?.textContent.trim() || 'Net 30';

            const statusText = row.cells[4].textContent.trim().toLowerCase();
            document.getElementById('status').value = statusText.includes('active') ? 'active' : 'inactive';

            document.getElementById('supplierModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('supplierModal').classList.remove('active');
        }

        function showDeleteModal(id, name) {
            deleteId = id;
            document.getElementById('deleteSupplierName').textContent = 
                `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            deleteId = null;
        }

        // Client-side search & filter (your original)
        const searchInput = document.getElementById('searchInput');
        const filterTabs = document.querySelectorAll('.filter-tab');
        searchInput?.addEventListener('input', filterTable);
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                filterTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                filterTable();
            });
        });

        function filterTable() {
            const term = searchInput.value.toLowerCase().trim();
            const activeFilter = document.querySelector('.filter-tab.active')?.getAttribute('data-filter') || 'all';
            document.querySelectorAll('#suppliersTableBody tr').forEach(row => {
                if (row.cells.length < 7) return;
                const texts = Array.from(row.cells).slice(0,6).map(cell => cell.textContent.toLowerCase()).join(' ');
                const matchesSearch = texts.includes(term);
                let matchesFilter = true;
                if (activeFilter !== 'all') {
                    if (activeFilter === 'active') matchesFilter = texts.includes('active');
                    else matchesFilter = texts.includes(activeFilter);
                }
                row.style.display = matchesSearch && matchesFilter ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            filterTable();
        });
    </script>
</body>
</html>