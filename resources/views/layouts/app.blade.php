<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Medical Appointment System')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        /* Include the CSS from the previous artifact here */
        
        
        /* Root Variables for Consistent Theming */
        :root {
          --primary-color: #2563eb;
          --primary-hover: #1d4ed8;
          --secondary-color: #64748b;
          --success-color: #059669;
          --warning-color: #d97706;
          --danger-color: #dc2626;
          --light-bg: #f8fafc;
          --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
          --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
          --border-radius: 12px;
          --border-radius-sm: 8px;
          --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        body {
          background-color: var(--light-bg);
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          color: #1f2937;
          line-height: 1.6;
        }

        /* Navigation Enhancement */
        .navbar {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
          box-shadow: var(--card-shadow);
          border: none;
          padding: 1rem 0;
        }

        .navbar-brand {
          color: white !important;
          font-weight: 700;
          font-size: 1.5rem;
          letter-spacing: -0.025em;
        }

        .navbar-brand:hover {
          color: #f3f4f6 !important;
        }

        /* Sidebar Styling */
        .list-group {
          border-radius: var(--border-radius);
          overflow: hidden;
          box-shadow: var(--card-shadow);
          border: none;
        }

        .list-group-item {
          border: none;
          border-bottom: 1px solid #e5e7eb;
          padding: 1rem 1.25rem;
          color: #4b5563;
          font-weight: 500;
          transition: var(--transition);
          background-color: white;
        }

        .list-group-item:hover {
          background-color: #f9fafb;
          color: var(--primary-color);
          transform: translateX(4px);
        }

        .list-group-item.active {
          background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
          color: white;
          border-color: var(--primary-color);
          font-weight: 600;
          box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.25);
        }

        .list-group-item:last-child {
          border-bottom: none;
        }

        /* Card Enhancements */
        .card {
          border: none;
          border-radius: var(--border-radius);
          box-shadow: var(--card-shadow);
          transition: var(--transition);
          background: white;
          overflow: hidden;
        }

        .card:hover {
          box-shadow: var(--card-shadow-hover);
          transform: translateY(-2px);
        }

        .card-header {
          background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
          border-bottom: 1px solid #e5e7eb;
          padding: 1.25rem 1.5rem;
          font-weight: 600;
          color: #374151;
        }

        .card-body {
          padding: 1.5rem;
        }

        /* Dashboard Stats Cards */
        .stats-card {
          background: linear-gradient(135deg, white 0%, #f8fafc 100%);
          border-left: 4px solid var(--primary-color);
        }

        .stats-card:nth-child(1) {
          border-left-color: var(--primary-color);
        }

        .stats-card:nth-child(2) {
          border-left-color: var(--success-color);
        }

        .stats-card:nth-child(3) {
          border-left-color: var(--warning-color);
        }

        .stats-card:nth-child(4) {
          border-left-color: var(--danger-color);
        }

        .card-title {
          color: #6b7280;
          font-size: 0.875rem;
          font-weight: 600;
          text-transform: uppercase;
          letter-spacing: 0.05em;
          margin-bottom: 0.75rem;
        }

        .card-text {
          font-size: 2.5rem;
          font-weight: 700;
          color: #111827;
          line-height: 1;
          margin-bottom: 1rem;
        }

        .card-link {
          color: var(--primary-color);
          text-decoration: none;
          font-weight: 500;
          font-size: 0.875rem;
          transition: var(--transition);
        }

        .card-link:hover {
          color: var(--primary-hover);
          text-decoration: underline;
        }

        /* Page Headers */
        h1 {
          color: #111827;
          font-weight: 700;
          font-size: 2rem;
          letter-spacing: -0.025em;
          margin-bottom: 0;
        }

        /* Button Enhancements */
        .btn {
          border-radius: var(--border-radius-sm);
          font-weight: 500;
          padding: 0.75rem 1.5rem;
          transition: var(--transition);
          border: none;
          box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
          transform: translateY(-1px);
          box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
          background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        }

        .btn-success {
          background: linear-gradient(135deg, var(--success-color), #047857);
        }

        .btn-warning {
          background: linear-gradient(135deg, var(--warning-color), #b45309);
          color: white;
        }

        .btn-danger {
          background: linear-gradient(135deg, var(--danger-color), #b91c1c);
        }

        .btn-secondary {
          background: linear-gradient(135deg, var(--secondary-color), #475569);
        }

        .btn-sm {
          padding: 0.5rem 1rem;
          font-size: 0.875rem;
        }

        /* Table Enhancements */
        .table {
          margin-bottom: 0;
        }

        .table thead th {
          background-color: #f9fafb;
          color: #374151;
          font-weight: 600;
          font-size: 0.875rem;
          text-transform: uppercase;
          letter-spacing: 0.05em;
          border-bottom: 2px solid #e5e7eb;
          border-top: none;
          padding: 1rem 0.75rem;
        }

        .table tbody tr {
          transition: var(--transition);
        }

        .table tbody tr:hover {
          background-color: #f9fafb;
          transform: scale(1.01);
        }

        .table td {
          padding: 1rem 0.75rem;
          border-bottom: 1px solid #f3f4f6;
          vertical-align: middle;
        }

        /* Badge Enhancements */
        .badge {
          padding: 0.5rem 0.875rem;
          border-radius: 50px;
          font-weight: 500;
          font-size: 0.75rem;
          letter-spacing: 0.025em;
        }

        .bg-warning {
          background: linear-gradient(135deg, var(--warning-color), #b45309) !important;
        }

        .bg-success {
          background: linear-gradient(135deg, var(--success-color), #047857) !important;
        }

        .bg-danger {
          background: linear-gradient(135deg, var(--danger-color), #b91c1c) !important;
        }

        .bg-primary {
          background: linear-gradient(135deg, var(--primary-color), var(--primary-hover)) !important;
        }

        /* Form Enhancements */
        .form-select, .form-control {
          border: 2px solid #e5e7eb;
          border-radius: var(--border-radius-sm);
          padding: 0.75rem 1rem;
          transition: var(--transition);
          background-color: white;
        }

        .form-select:focus, .form-control:focus {
          border-color: var(--primary-color);
          box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
          outline: none;
        }

        .form-select-sm {
          padding: 0.5rem 0.75rem;
          font-size: 0.875rem;
        }

        /* Alert Enhancements */
        .alert {
          border: none;
          border-radius: var(--border-radius);
          padding: 1rem 1.25rem;
          font-weight: 500;
          box-shadow: var(--card-shadow);
        }

        .alert-success {
          background: linear-gradient(135deg, #d1fae5, #a7f3d0);
          color: #065f46;
        }

        .alert-danger {
          background: linear-gradient(135deg, #fee2e2, #fecaca);
          color: #991b1b;
        }

        /* Pagination Enhancements */
        .pagination {
          gap: 0.25rem;
        }

        .page-link {
          border: 1px solid #e5e7eb;
          border-radius: var(--border-radius-sm);
          color: var(--secondary-color);
          padding: 0.5rem 0.75rem;
          transition: var(--transition);
          margin: 0 0.125rem;
        }

        .page-link:hover {
          background-color: var(--light-bg);
          border-color: var(--primary-color);
          color: var(--primary-color);
          transform: translateY(-1px);
        }

        .page-item.active .page-link {
          background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
          border-color: var(--primary-color);
          box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        /* Input Group Enhancements */
        .input-group .form-select {
          border-top-right-radius: 0;
          border-bottom-right-radius: 0;
        }

        .input-group .btn {
          border-top-left-radius: 0;
          border-bottom-left-radius: 0;
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
          .container-fluid {
            padding: 0.5rem;
          }
          
          .col-md-2 {
            margin-bottom: 1rem;
          }
          
          .list-group-item:hover {
            transform: none;
          }
          
          .card:hover {
            transform: none;
          }
          
          .table-responsive {
            border-radius: var(--border-radius);
          }
        }

        /* Loading Animation */
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .card, .alert, .table-responsive {
          animation: fadeInUp 0.6s ease-out;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
          width: 8px;
          height: 0; /* Hide horizontal scrollbar */
        }

        ::-webkit-scrollbar:horizontal {
          display: none; /* Completely hide horizontal scrollbar */
        }

        ::-webkit-scrollbar-track {
          background: #f1f5f9;
          border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
          background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
          border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background: linear-gradient(135deg, var(--primary-hover), #1e40af);
        }

        /* Hide horizontal scrollbars completely */
        .table-responsive {
          scrollbar-width: none; /* Firefox */
          -ms-overflow-style: none; /* IE and Edge */
        }

        .table-responsive::-webkit-scrollbar:horizontal {
          display: none; /* Chrome, Safari, Opera */
          height: 0;
        }

        /* Remove blue outline/focus ring */
        *:focus {
            outline: none !important;
        }

        .btn:focus,
        .btn:active,
        .btn.active,
        .list-group-item:focus,
        .list-group-item:active,
        a:focus,
        a:active {
            outline: none !important;
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
            border-color: var(--primary-color) !important;
        }

        /* Remove webkit tap highlight */
        * {
            -webkit-tap-highlight-color: transparent !important;
        }

        /* Fix for blue border on table rows */
        .table tbody tr:focus,
        .table tbody tr:active,
        .table tbody tr:hover,
        .table tbody tr {
            outline: none !important;
            box-shadow: none !important;
        }

        .table td:focus,
        .table th:focus,
        .table td:active,
        .table th:active {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Fix dropdown in table */
        .table .form-select:focus,
        .table .form-select:active {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1) !important;
            border-color: var(--primary-color) !important;
        }

        /* Ensure clean hover without blue border */
        .table tbody tr:hover {
            background-color: #f9fafb !important;
            transform: scale(1.01) !important;
            outline: none !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <i class="fas fa-heartbeat me-2"></i>
                Medical Appointment System
            </a>
            
            <!-- Add user info or logout button here if needed -->
            <div class="navbar-nav ms-auto">
                <!-- Example user menu - customize as needed -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        Admin
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Enhanced Footer -->
    <footer class="mt-5 py-4 text-center text-muted">
        <div class="container">
            <small>&copy; {{ date('Y') }} Medical Appointment System. All rights reserved.</small>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for enhanced interactions -->
    <script>
        // Add smooth scrolling and fade-in animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.type === 'submit' || this.tagName === 'BUTTON') {
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                        this.disabled = true;
                        
                        // Re-enable after 2 seconds (adjust as needed)
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 2000);
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });

            // Add confirmation dialogs for delete actions
            const deleteButtons = document.querySelectorAll('button[onclick*="confirm"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const confirmMessage = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    
                    // Create custom modal instead of browser confirm
                    const modalHtml = `
                        <div class="modal fade" id="deleteModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title text-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">${confirmMessage}</p>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();
                    
                    document.getElementById('confirmDelete').addEventListener('click', () => {
                        this.closest('form').submit();
                        modal.hide();
                    });
                    
                    // Clean up modal after hiding
                    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
                        this.remove();
                    });
                });
            });

            // Add search functionality (if needed)
            const searchInputs = document.querySelectorAll('input[type="search"]');
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const table = this.closest('.card').querySelector('table tbody');
                    const rows = table.querySelectorAll('tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            });
        });

        // Add tooltip functionality
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    
    @stack('scripts')
</body>
</html>