<?php
// sidebar.php

// Koneksyon sa database (kailangan para makuha ang low stock count)
require_once '../connection/dbconnection.php';
$conn = $GLOBALS['conn'] ?? null;

$low_books_count    = 0;
$low_uniforms_count = 0;

if ($conn) {
    // Low stock books
    $low_books_result = mysqli_query($conn, "
        SELECT COUNT(*) as cnt 
        FROM books 
        WHERE quantity <= low_stock_limit 
          AND quantity > 0
    ");
    $low_books_count = $low_books_result ? (int) mysqli_fetch_assoc($low_books_result)['cnt'] : 0;

    // Low stock uniforms
    $low_uniforms_result = mysqli_query($conn, "
        SELECT COUNT(*) as cnt 
        FROM uniform 
        WHERE quantity <= low_stock_limit 
          AND quantity > 0
    ");
    $low_uniforms_count = $low_uniforms_result ? (int) mysqli_fetch_assoc($low_uniforms_result)['cnt'] : 0;
}
?>

<style>
    /* Remove all sidebar collapse related styles */
    .main-content-expanded {
        margin-left: 280px;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .main-content-expanded.sidebar-closed {
        margin-left: 0;
    }
    
    /* Modern sidebar styling - clean and minimal */
    #sidebar {
        background: #ffffff;
        border-right: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        width: 280px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 30;
        overflow-y: auto;
    }
    
    /* Header with minimal styling */
    #sidebar .flex.items-center.justify-between {
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        padding: 24px 0 16px 0;
    }
    
    /* Logo container - clean and centered */
    .logo-container {
        padding: 8px 24px 16px 24px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }
    
    /* Logo image styling - clean and professional */
    .logo-container img {
        width: 160px;
        height: 160px;
        object-fit: contain;
        border-radius: 50%;
        padding: 4px;
        background: #f8faf8;
        transition: transform 0.2s ease;
    }
    
    .logo-container img:hover {
        transform: scale(1.02);
    }
    
    /* Modern text styling */
    .logo-text {
        display: flex;
        flex-direction: column;
        gap: 4px;
        text-align: center;
    }
    
    .logo-text span:first-child {
        color: #1a4d2e;
        font-weight: 600;
        font-size: 1.1rem;
        line-height: 1.3;
        letter-spacing: 0.2px;
    }
    
    .logo-text span:last-child {
        color: #4a6b57;
        font-size: 0.85rem;
        font-weight: 400;
        letter-spacing: 0.2px;
    }
    
    /* Modern navigation - clean and minimal */
    #sidebar nav {
        padding: 8px 16px;
    }
    
    #sidebar .nav-item {
        background: transparent;
        margin-bottom: 4px;
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.2s ease;
        color: #4a5568;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-weight: 500;
        font-size: 0.95rem;
        border: none;
        position: relative;
    }
    
    #sidebar .nav-item:hover {
        background: #f0f7f2;
        color: #1a4d2e;
    }
    
    /* Active link styling - subtle and modern */
    #sidebar .nav-item.active {
        background: #e8f3ec;
        color: #1a4d2e;
        font-weight: 600;
    }
    
    /* Navigation icon styling */
    .nav-icon {
        color: #6b8c7c;
        font-size: 1.2rem;
        width: 28px;
        text-align: center;
        margin-right: 12px;
        transition: color 0.2s ease;
    }
    
    #sidebar .nav-item:hover .nav-icon {
        color: #1a4d2e;
    }
    
    #sidebar .nav-item.active .nav-icon {
        color: #1a4d2e;
    }
    
    /* Badge styling para sa low stock */
    .sidebar-badge {
        background: #ef4444;
        color: white;
        font-size: 11px;
        font-weight: bold;
        padding: 2px 7px;
        border-radius: 999px;
        margin-left: auto;
        min-width: 18px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    
    /* Open sidebar button - minimal */
    #openSidebarBtn {
        background: #ffffff;
        color: #1a4d2e;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 40;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        cursor: pointer;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    #openSidebarBtn:hover {
        background: #f0f7f2;
        border-color: rgba(26, 77, 46, 0.1);
        transform: translateY(-1px);
    }
    
    /* Scrollbar styling - subtle */
    #sidebar::-webkit-scrollbar {
        width: 4px;
    }
    
    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #sidebar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
    
    #sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.15);
    }
    
    /* Bottom right logout button styling - modern and clean */
    .logout-container {
        margin-top: auto;
        padding: 20px 16px;
        border-top: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    .logout-btn {
        color: #4a5568;
        background: transparent;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        width: 100%;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
    }
    
    .logout-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .logout-btn i {
        font-size: 1.2rem;
        margin-right: 12px;
        width: 28px;
        text-align: center;
        color: #9ca3af;
        transition: color 0.2s ease;
    }
    
    .logout-btn:hover i {
        color: #dc2626;
    }
    
    .logout-text {
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Navigation text styling */
    .nav-text {
        flex: 1;
    }

    /* Sidebar closed state */
    #sidebar.close-sidebar {
        transform: translateX(-100%);
    }

    /* Main content adjustment */
    body {
        margin: 0;
        padding: 0;
        background: #f0f7f2;
        font-family: 'Inter', sans-serif;
    }

    /* Hide button when sidebar is open */
    #openSidebarBtn.hidden {
        display: none;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        #sidebar {
            position: fixed;
            z-index: 50;
            height: 100vh;
            width: 280px;
        }
        
        .main-content-expanded {
            margin-left: 0;
        }
        
        #openSidebarBtn {
            display: flex;
        }
    }
</style>

<!-- Toggle Button (visible when sidebar is closed) -->
<button id="openSidebarBtn" class="hidden">
    <i class="fas fa-bars"></i>
    <span>Menu</span>
</button>

<!-- Sidebar -->
<div id="sidebar" class="transition-all duration-300">
    <!-- Header with enhanced logo section -->
    <div class="flex items-center justify-center">
        <div class="flex flex-col items-center justify-center logo-container">
            <img src="../images/logo.png" alt="La Trinidad Academy Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="logo-text">
                <span>La Trinidad Academy</span>
                <span>Inventory Management System</span>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1">
        <a href="../pages/dashboard.php" class="nav-item" data-page="dashboard">
            <i class="fas fa-tachometer-alt nav-icon"></i>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="../pages/books.php" class="nav-item" data-page="books">
            <i class="fas fa-book nav-icon"></i>
            <span class="nav-text">Books</span>
            <?php if ($low_books_count > 0): ?>
                <span class="sidebar-badge"><?= min(99, $low_books_count) ?></span>
            <?php endif; ?>
        </a>
        <a href="../pages/uniform.php" class="nav-item" data-page="uniform">
            <i class="fas fa-tshirt nav-icon"></i>
            <span class="nav-text">Uniform</span>
            <?php if ($low_uniforms_count > 0): ?>
                <span class="sidebar-badge"><?= min(99, $low_uniforms_count) ?></span>
            <?php endif; ?>
        </a>
        <a href="../pages/supplier.php" class="nav-item" data-page="supplier">
            <i class="fas fa-truck nav-icon"></i>
            <span class="nav-text">Suppliers</span>
        </a>
    </nav>
    
    <!-- Logout Button Container at Bottom -->
    <div class="logout-container">
        <a href="../controller/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span class="logout-text">Logout</span>
        </a>
    </div>
</div>

<script>
    // Get DOM elements
    const sidebar = document.getElementById('sidebar');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const body = document.body;
    const navItems = document.querySelectorAll('#sidebar .nav-item');
    
    // State variable for closed state only
    let isClosed = false;
    
    // Initialize main content wrapper
    function initializeMainContent() {
        // Find main content (the dashboard content)
        const mainContent = document.querySelector('.main-content') || document.querySelector('.dashboard')?.parentElement;
        
        if (mainContent) {
            mainContent.classList.add('main-content-expanded');
        } else {
            // If no specific wrapper, add class to body
            body.classList.add('main-content-expanded');
        }
        
        return mainContent;
    }
    
    const mainContent = initializeMainContent();
    
    // Set active navigation item based on current page
    function setActiveNavItem() {
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop().replace('.php', '');
        
        navItems.forEach(item => {
            const pageAttribute = item.getAttribute('data-page');
            
            // Remove active class from all
            item.classList.remove('active');
            
            // Add active class to matching item
            if (pageAttribute === currentPage || 
                (currentPage === '' && pageAttribute === 'dashboard') ||
                (currentPath.includes(currentPage) && pageAttribute === currentPage)) {
                item.classList.add('active');
            }
        });
    }
    
    // Close sidebar function
    function closeSidebar() {
        sidebar.classList.add('close-sidebar');
        openSidebarBtn.classList.remove('hidden');
        if (mainContent) mainContent.classList.add('sidebar-closed');
        isClosed = true;
        saveSidebarState();
    }
    
    // Open sidebar function
    function openSidebar() {
        sidebar.classList.remove('close-sidebar');
        openSidebarBtn.classList.add('hidden');
        if (mainContent) mainContent.classList.remove('sidebar-closed');
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
                if (mainContent) mainContent.classList.add('sidebar-closed');
            } else {
                sidebar.classList.remove('close-sidebar');
                openSidebarBtn.classList.add('hidden');
                if (mainContent) mainContent.classList.remove('sidebar-closed');
            }
        }
    }
    
    // Load saved state on page load and set active nav item
    document.addEventListener('DOMContentLoaded', () => {
        loadSidebarState();
        setActiveNavItem();
    });
    
    // Update active nav item when clicked (immediate feedback)
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // Don't prevent default - let navigation happen
            // Update active state immediately for visual feedback
            navItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            // Save the active state to sessionStorage for immediate feedback
            const pageName = item.getAttribute('data-page');
            sessionStorage.setItem('lastActivePage', pageName);
        });
    });
    
    // Check sessionStorage for last active page on page load
    window.addEventListener('pageshow', () => {
        const lastActivePage = sessionStorage.getItem('lastActivePage');
        if (lastActivePage) {
            navItems.forEach(item => {
                if (item.getAttribute('data-page') === lastActivePage) {
                    item.classList.add('active');
                }
            });
        }
    });

    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth <= 768) {
            // On mobile, always start with sidebar closed
            if (!isClosed && !localStorage.getItem('sidebarClosed')) {
                closeSidebar();
            }
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