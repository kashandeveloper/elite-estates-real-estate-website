<?php
require_once(__DIR__ . '/auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Elite Estates</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0A2540;
            --accent-color: #F97316;
            --sidebar-width: 260px;
            --bg-light: #F8FAFC;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--primary-color);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar-brand {
            padding: 2rem 1.5rem;
            color: white;
            text-decoration: none;
            display: block;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }
        .sidebar-nav {
            padding: 0 1rem;
        }
        .nav-link-admin {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        .nav-link-admin:hover, .nav-link-admin.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .nav-link-admin.active {
            background-color: var(--accent-color);
            color: white;
        }
        .nav-link-admin i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }
        .top-navbar {
            background-color: white;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .content-body {
            padding: 2rem;
        }
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .icon-primary { background: rgba(10, 37, 64, 0.1); color: var(--primary-color); }
        .icon-accent { background: rgba(249, 115, 22, 0.1); color: var(--accent-color); }
        .icon-success { background: rgba(16, 185, 129, 0.1); color: #10B981; }
        .icon-info { background: rgba(59, 130, 246, 0.1); color: #3B82F6; }

        @media (max-width: 991px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .main-content { margin-left: 0; }
            .sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar shadow" id="sidebar">
    <a href="index.php" class="sidebar-brand">
        ELITE<span class="text-accent">ESTATES</span>
    </a>
    
    <div class="sidebar-nav">
        <a href="index.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-grid-2"></i> Dashboard
        </a>
        <a href="properties.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'properties.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Properties
        </a>
        <a href="add-property.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'add-property.php' ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i> Add New
        </a>
        <a href="inquiries.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'inquiries.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Inquiries
        </a>
        <a href="agent-messages.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'agent-messages.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Agent Messages
        </a>
        <a href="agents.php" class="nav-link-admin <?php echo basename($_SERVER['PHP_SELF']) == 'agents.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Agents
        </a>
        <div class="mt-5 pt-4 border-top border-white-50 opacity-50">
            <a href="logout.php" class="nav-link-admin text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="top-navbar d-flex justify-content-between">
        <button class="btn d-lg-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-none d-md-block">
            <h5 class="fw-bold m-0">Admin Panel</h5>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-3 text-end d-none d-sm-block">
                <p class="m-0 fw-bold small"><?php echo $_SESSION['admin_username'] ?? 'Admin User'; ?></p>
                <p class="m-0 text-muted small">Super Admin</p>
            </div>
            <img src="https://i.pravatar.cc/150?u=admin" class="rounded-circle" width="40" height="40" alt="Admin">
        </div>
    </div>
    
    <div class="content-body">
