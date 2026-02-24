<?php
// Set current page for sidebar active state
$current_page = 'suppliers';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Trinidad Academy · Supplier Management</title>
    <!-- Font Awesome 5 (free) -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@500;600&display=swap" rel="stylesheet">
    <style>
        /* All your existing CSS remains exactly the same */
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
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: #f0f7f2;
        }

        .main-content.sidebar-closed {
            margin-left: 0;
        }

        .dashboard {
            max-width: 1440px;
            margin: 0 auto;
        }

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

        .btn-primary {
            background: #2e7d5e;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            font-weight: 600;
            color: white;
            transition: 0.2s;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #1a4d2e;
        }

        .btn-primary:hover {
            background: #1a4d2e;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(26, 77, 46, 0.2);
        }

        .btn-primary i {
            font-size: 14px;
        }

        .btn-secondary {
            background: white;
            border: 1px solid #b8d9c4;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
            color: #1a4d2e;
            transition: 0.2s;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary:hover {
            background: #f0f7f2;
            border-color: #2e7d5e;
        }

        .btn-save {
            background: #2e7d5e;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            font-weight: 600;
            color: white;
            transition: 0.2s;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #1a4d2e;
        }

        .btn-save:hover {
            background: #1a4d2e;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(26, 77, 46, 0.2);
        }

        .btn-danger {
            background: #fff3f0;
            border: 1px solid #ffcdc0;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
            color: #b34a4a;
            transition: 0.2s;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger:hover {
            background: #ffe8e0;
            border-color: #b34a4a;
        }

        .filter-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .search-box {
            background: white;
            border: 1px solid #d4e8da;
            border-radius: 40px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            max-width: 400px;
        }

        .search-box i {
            color: #4a6b57;
            font-size: 16px;
        }

        .search-box input {
            border: none;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            width: 100%;
            background: transparent;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 20px;
            border-radius: 40px;
            background: white;
            border: 1px solid #d4e8da;
            font-size: 14px;
            font-weight: 500;
            color: #4a6b57;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-tab.active {
            background: #2e7d5e;
            border-color: #1a4d2e;
            color: white;
        }

        .filter-tab:hover {
            border-color: #2e7d5e;
        }

        .table-container {
            background: white;
            border-radius: 32px;
            padding: 20px;
            border: 1px solid #d4e8da;
            box-shadow: 0 10px 22px -8px rgba(26, 77, 46, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 16px 12px;
            font-weight: 600;
            font-size: 14px;
            color: #1a4d2e;
            border-bottom: 2px solid #d4e8da;
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2f0e6;
            color: #1e3c2c;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f5fbf7;
        }

        .supplier-badge {
            background: #e2f0e6;
            border-radius: 40px;
            padding: 4px 12px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge {
            border-radius: 40px;
            padding: 4px 12px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-active {
            background: #e2f0e6;
            color: #1d784d;
        }

        .status-inactive {
            background: #ffeee6;
            color: #b4521c;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            background: none;
            border: 1px solid #d4e8da;
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            color: #1a4d2e;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn:hover {
            background: #f0f7f2;
            border-color: #2e7d5e;
        }

        .action-btn.edit:hover {
            background: #e2f0e6;
            border-color: #2e7d5e;
        }

        .action-btn.delete:hover {
            background: #fff3f0;
            border-color: #b34a4a;
            color: #b34a4a;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-container {
            background: white;
            border-radius: 32px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid #d4e8da;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px;
            border-bottom: 1px solid #e2f0e6;
        }

        .modal-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            color: #1a4d2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header h2 i {
            color: #2e7d5e;
            background: rgba(46, 125, 94, 0.12);
            padding: 8px;
            border-radius: 12px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #4a6b57;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #fff3f0;
            color: #b34a4a;
        }

        .modal-body {
            padding: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #1a4d2e;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group label i {
            color: #2e7d5e;
            font-size: 16px;
        }

        .form-group label span {
            color: #b34a4a;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d4e8da;
            border-radius: 16px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #fafdfb;
        }

        .form-control:focus {
            outline: none;
            border-color: #2e7d5e;
            background: white;
            box-shadow: 0 0 0 3px rgba(46, 125, 94, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #e2f0e6;
        }

        .delete-modal .modal-container {
            max-width: 400px;
            text-align: center;
        }

        .delete-icon {
            font-size: 48px;
            color: #b34a4a;
            margin-bottom: 16px;
        }

        .delete-modal h3 {
            font-size: 20px;
            color: #1a4d2e;
            margin-bottom: 8px;
        }

        .delete-modal p {
            color: #4a6b57;
            margin-bottom: 24px;
        }

        .delete-actions {
            display: flex;
            justify-content: center;
            gap: 16px;
        }

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

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: 100%;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-header {
                padding: 20px 24px;
            }

            .modal-body {
                padding: 24px;
            }
        }
        
        @media (max-width: 550px) {
            .header { 
                flex-direction: column; 
                align-items: start; 
                margin-top: 60px;
            }
            
            .top-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <button id="openSidebarBtn" class="hidden">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </button>

    <?php include '../components/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="dashboard">
            <div class="header">
                <div class="title-section">
                    <h1>
                        <i class="fas fa-truck"></i> 
                        Supplier Management
                    </h1>
                    <div class="badge-academy">
                        <i class="fas fa-boxes"></i> book & uniform suppliers · vendor directory
                    </div>
                </div>
                <div class="top-actions">
                    <div class="date-chip">
                        <i class="far fa-calendar-alt" style="margin-right: 8px; color:#2e7d5e;"></i> 
                        February 16, 2026
                    </div>
                    <!-- CHANGED: Using onclick instead of id for reliability -->
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus-circle"></i> Add Supplier
                    </button>
                </div>
            </div>

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
                        <!-- Dynamic content inserted here -->
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
                    <i class="fas" id="modalIcon"></i>
                    <span id="modalTitle">Add New Supplier</span>
                </h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierForm">
                    <input type="hidden" id="supplierId">
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-building"></i>
                            Supplier Name <span>*</span>
                        </label>
                        <input type="text" class="form-control" id="supplierName" required placeholder="Enter supplier name">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-user"></i>
                            Contact Person
                        </label>
                        <input type="text" class="form-control" id="contactPerson" placeholder="Enter contact person name">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" class="form-control" id="email" placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label>
                                <i class="fas fa-phone"></i>
                                Phone
                            </label>
                            <input type="text" class="form-control" id="phone" placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Address
                        </label>
                        <textarea class="form-control" id="address" placeholder="Enter complete address"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-tag"></i>
                                Supplier Type
                            </label>
                            <select class="form-control" id="supplierType">
                                <option value="books">Books Only</option>
                                <option value="uniforms">Uniforms Only</option>
                                <option value="both">Both Books and Uniforms</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                <i class="fas fa-credit-card"></i>
                                Payment Terms
                            </label>
                            <select class="form-control" id="paymentTerms">
                                <option value="Net 15">Net 15</option>
                                <option value="Net 30" selected>Net 30</option>
                                <option value="Net 45">Net 45</option>
                                <option value="Net 60">Net 60</option>
                                <option value="COD">Cash on Delivery</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-info-circle"></i>
                            Status
                        </label>
                        <select class="form-control" id="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn-save" id="saveBtn">
                            <i class="fas fa-save"></i>
                            <span>Save Supplier</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay delete-modal" id="deleteModal">
        <div class="modal-container">
            <div class="delete-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Delete Supplier</h3>
            <p id="deleteSupplierName">Are you sure you want to delete this supplier? This action cannot be undone.</p>
            <div class="delete-actions">
                <button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        // Make functions globally available
        window.openAddModal = function() {
            console.log('openAddModal called'); // For debugging
            const modal = document.getElementById('supplierModal');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const supplierForm = document.getElementById('supplierForm');
            
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }
            
            modalIcon.className = 'fas fa-plus-circle';
            modalTitle.textContent = 'Add New Supplier';
            if (supplierForm) supplierForm.reset();
            
            const supplierId = document.getElementById('supplierId');
            const status = document.getElementById('status');
            
            if (supplierId) supplierId.value = '';
            if (status) status.value = 'active';
            
            modal.classList.add('active');
        };

        window.closeModal = function() {
            const modal = document.getElementById('supplierModal');
            if (modal) modal.classList.remove('active');
        };

        window.closeDeleteModal = function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) deleteModal.classList.remove('active');
            deleteId = null;
        };

        window.editSupplier = function(id) {
            const supplier = suppliers.find(s => s.id === id);
            if (!supplier) return;

            const modal = document.getElementById('supplierModal');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            
            modalIcon.className = 'fas fa-edit';
            modalTitle.textContent = 'Edit Supplier';
            
            document.getElementById('supplierId').value = supplier.id;
            document.getElementById('supplierName').value = supplier.supplierName;
            document.getElementById('contactPerson').value = supplier.contactPerson || '';
            document.getElementById('email').value = supplier.email || '';
            document.getElementById('phone').value = supplier.phone || '';
            document.getElementById('address').value = supplier.address || '';
            document.getElementById('supplierType').value = supplier.supplierType;
            document.getElementById('paymentTerms').value = supplier.paymentTerms || 'Net 30';
            document.getElementById('status').value = supplier.status;

            modal.classList.add('active');
        };

        window.showDeleteModal = function(id, supplierName) {
            deleteId = id;
            document.getElementById('deleteSupplierName').textContent = 
                `Are you sure you want to delete "${supplierName}"? This action cannot be undone.`;
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) deleteModal.classList.add('active');
        };

        window.confirmDelete = function() {
            if (deleteId) {
                suppliers = suppliers.filter(s => s.id !== deleteId);
                closeDeleteModal();
                renderSuppliers();
            }
        };

        // Sample supplier data
        let suppliers = [
            {
                id: 1,
                supplierName: 'Academic Books Inc.',
                contactPerson: 'Maria Santos',
                email: 'maria@academicbooks.com',
                phone: '(02) 8123 4567',
                address: '123 Education Ave, Quezon City',
                supplierType: 'books',
                paymentTerms: 'Net 30',
                status: 'active',
                lastOrder: '2026-02-10'
            },
            {
                id: 2,
                supplierName: 'Uniform Solutions Co.',
                contactPerson: 'John Reyes',
                email: 'john@uniformsolutions.com',
                phone: '(02) 8765 4321',
                address: '456 Garment St, Manila',
                supplierType: 'uniforms',
                paymentTerms: 'Net 30',
                status: 'active',
                lastOrder: '2026-02-08'
            },
            {
                id: 3,
                supplierName: 'Global School Supply',
                contactPerson: 'Anna Lim',
                email: 'anna@globalschool.com',
                phone: '(02) 8456 7890',
                address: '789 Merchant St, Pasig',
                supplierType: 'both',
                paymentTerms: 'Net 45',
                status: 'active',
                lastOrder: '2026-02-05'
            },
            {
                id: 4,
                supplierName: 'Children\'s Book House',
                contactPerson: 'Pedro Cruz',
                email: 'pedro@childrensbook.com',
                phone: '(02) 8234 5678',
                address: '321 Reader St, Makati',
                supplierType: 'books',
                paymentTerms: 'Net 15',
                status: 'inactive',
                lastOrder: '2026-01-15'
            },
            {
                id: 5,
                supplierName: 'Elite Uniforms',
                contactPerson: 'Sofia Garcia',
                email: 'sofia@eliteuniforms.com',
                phone: '(02) 8987 6543',
                address: '654 Fashion Ave, Mandaluyong',
                supplierType: 'uniforms',
                paymentTerms: 'Net 30',
                status: 'active',
                lastOrder: '2026-02-12'
            },
            {
                id: 6,
                supplierName: 'Educational Resources Ltd.',
                contactPerson: 'Miguel Tan',
                email: 'miguel@edresources.com',
                phone: '(02) 8901 2345',
                address: '147 Learning St, Taguig',
                supplierType: 'both',
                paymentTerms: 'Net 60',
                status: 'active',
                lastOrder: '2026-02-01'
            }
        ];

        let currentFilter = 'all';
        let searchTerm = '';
        let deleteId = null;

        // DOM elements
        const searchInput = document.getElementById('searchInput');
        const filterTabs = document.querySelectorAll('.filter-tab');
        const supplierForm = document.getElementById('supplierForm');

        // Save supplier function
        function saveSupplier(event) {
            event.preventDefault();

            const formData = {
                id: document.getElementById('supplierId').value ? parseInt(document.getElementById('supplierId').value) : Date.now(),
                supplierName: document.getElementById('supplierName').value.trim(),
                contactPerson: document.getElementById('contactPerson').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                address: document.getElementById('address').value.trim(),
                supplierType: document.getElementById('supplierType').value,
                paymentTerms: document.getElementById('paymentTerms').value,
                status: document.getElementById('status').value,
                lastOrder: new Date().toISOString().split('T')[0]
            };

            if (document.getElementById('supplierId').value) {
                const index = suppliers.findIndex(s => s.id === parseInt(document.getElementById('supplierId').value));
                if (index !== -1) {
                    suppliers[index] = { ...suppliers[index], ...formData };
                }
            } else {
                suppliers.push(formData);
            }

            closeModal();
            renderSuppliers();
        }

        // Render suppliers table
        function renderSuppliers() {
            const tbody = document.getElementById('suppliersTableBody');
            if (!tbody) return;

            let filteredSuppliers = suppliers.filter(supplier => {
                if (currentFilter !== 'all') {
                    if (currentFilter === 'active' && supplier.status !== 'active') return false;
                    if (currentFilter === 'books' && supplier.supplierType !== 'books') return false;
                    if (currentFilter === 'uniforms' && supplier.supplierType !== 'uniforms') return false;
                    if (currentFilter === 'both' && supplier.supplierType !== 'both') return false;
                }
                
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    return supplier.supplierName.toLowerCase().includes(searchLower) ||
                           (supplier.contactPerson && supplier.contactPerson.toLowerCase().includes(searchLower)) ||
                           (supplier.email && supplier.email.toLowerCase().includes(searchLower)) ||
                           (supplier.phone && supplier.phone.includes(searchTerm));
                }
                
                return true;
            });

            if (filteredSuppliers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #4a6b57;">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                            No suppliers found matching your criteria
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filteredSuppliers.map(supplier => {
                const categoryDisplay = {
                    'books': '<span class="supplier-badge"><i class="fas fa-book"></i> Books</span>',
                    'uniforms': '<span class="supplier-badge"><i class="fas fa-tshirt"></i> Uniforms</span>',
                    'both': '<span class="supplier-badge"><i class="fas fa-boxes"></i> Both</span>'
                }[supplier.supplierType];

                const statusDisplay = supplier.status === 'active' 
                    ? '<span class="status-badge status-active"><i class="fas fa-check-circle"></i> Active</span>'
                    : '<span class="status-badge status-inactive"><i class="fas fa-clock"></i> Inactive</span>';

                const lastOrderDate = new Date(supplier.lastOrder);
                const formattedDate = lastOrderDate.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });

                return `
                    <tr data-id="${supplier.id}">
                        <td>
                            <strong style="color: #1a4d2e;">${supplier.supplierName}</strong>
                            <div style="font-size: 12px; color: #4a6b57;">${supplier.paymentTerms || ''}</div>
                        </td>
                        <td>${supplier.contactPerson || '—'}</td>
                        <td>
                            <div>${supplier.email || '—'}</div>
                            <div style="font-size: 12px; color: #4a6b57;">${supplier.phone || '—'}</div>
                        </td>
                        <td>${categoryDisplay}</td>
                        <td>${statusDisplay}</td>
                        <td>${formattedDate}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn edit" onclick="editSupplier(${supplier.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn delete" onclick="showDeleteModal(${supplier.id}, '${supplier.supplierName.replace(/'/g, "\\'")}')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Set filter
        function setFilter(filter) {
            currentFilter = filter;
            filterTabs.forEach(tab => {
                const tabFilter = tab.getAttribute('data-filter');
                if (tabFilter === filter) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });
            renderSuppliers();
        }

        // Event Listeners
        if (supplierForm) {
            supplierForm.addEventListener('submit', saveSupplier);
        }

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('supplierModal');
            const deleteModal = document.getElementById('deleteModal');
            if (e.target === modal) {
                closeModal();
            }
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });

        // Search input
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchTerm = e.target.value;
                renderSuppliers();
            });
        }

        // Filter tabs
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.getAttribute('data-filter');
                setFilter(filter);
            });
        });

        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('openSidebarBtn');
        const mainContent = document.querySelector('.main-content');

        window.closeSidebar = function() {
            if (sidebar) sidebar.classList.add('close-sidebar');
            if (openSidebarBtn) openSidebarBtn.classList.remove('hidden');
            if (mainContent) mainContent.classList.add('sidebar-closed');
            localStorage.setItem('sidebarClosed', 'true');
        };

        window.openSidebar = function() {
            if (sidebar) sidebar.classList.remove('close-sidebar');
            if (openSidebarBtn) openSidebarBtn.classList.add('hidden');
            if (mainContent) mainContent.classList.remove('sidebar-closed');
            localStorage.setItem('sidebarClosed', 'false');
        };

        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', openSidebar);
        }

        // Load saved sidebar state
        function loadSidebarState() {
            const savedState = localStorage.getItem('sidebarClosed');
            if (savedState === 'true') {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            } else if (localStorage.getItem('sidebarClosed') !== 'true') {
                openSidebar();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadSidebarState();
            renderSuppliers();
            console.log('Page loaded, Add Supplier button should work now');
        });

        window.addEventListener('resize', handleResize);
    </script>
</body>
</html>