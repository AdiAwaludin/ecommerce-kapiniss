<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Keripik Kapinis') ?></title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #81C784;
            --accent-color: #FFC107;
            --text-dark: #2C3E50;
            --text-light: #6C757D;
            --bg-light: #F8F9FA;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 0.3rem;
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 0.5rem 1rem !important;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
         .navbar-nav .nav-link.active {
             background: rgba(255,255,255,0.3);
             color: white !important;
         }

         .main-content-area, .admin-wrapper {
             margin-top: 65px;
             flex-grow: 1;
         }
         .main-content-area > div:last-child {
             min-height: calc(100vh - 65px - 200px);
         }
         .admin-content {
             min-height: calc(100vh - 65px);
         }


        .hero-section {
            background: linear-gradient(135deg, rgba(46,125,50,0.9), rgba(129,199,132,0.8)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="2" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            color: white;
            padding: 3rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:white;stop-opacity:0.1" /><stop offset="50%" style="stop-color:white;stop-opacity:0.05" /><stop offset="100%" style="stop-color:white;stop-opacity:0.1" /></linearGradient></defs><rect width="100" height="20" fill="url(%23grad)"/></svg>');
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--accent-color), #FFD54F);
            color: var(--text-dark);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255,193,7,0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255,193,7,0.4);
            color: var(--text-dark);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .product-price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .product-card {
            position: relative;
            overflow: hidden;
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(46,125,50,0.9), rgba(129,199,132,0.8));
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            pointer-events: none;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .product-overlay .btn-add-cart {
             pointer-events: auto;

            background: var(--accent-color);
            color: var(--text-dark);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            transform: translateY(20px);
            opacity: 0;
        }
        .product-card:hover .product-overlay .btn-add-cart {
             transform: translateY(0);
             opacity: 1;
        }


        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--accent-color), #FFD54F);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .footer {
            background: linear-gradient(135deg, var(--text-dark), #34495E);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: auto;
            flex-shrink: 0;
        }

        .footer h5 {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-link {
            color: #BDC3C7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--accent-color);
        }

        .footer hr { /* Target hr elements specifically within the footer */
            border-color: rgba(255, 255, 255, 0.2) !important; /* Subtly transparent white */
            border-top-width: 1px; /* Ensure it's 1px thin */
         }

        .notification {
             position: fixed;
             top: 80px;
             right: 20px;
             z-index: 9999;
             max-width: 350px;
             animation: slideInRight 0.5s ease-out;
             box-shadow: 0 5px 15px rgba(0,0,0,0.2);
         }

         @keyframes slideInRight {
             from { right: -370px; opacity: 0; }
             to { right: 20px; opacity: 1; }
         }
         .alert {
             border: none;
             border-radius: 10px;
             padding: 1rem 1.5rem;
             margin-bottom: 0;
         }

         .alert-success { background: linear-gradient(135deg, #4CAF50, #81C784); color: white; }
         .alert-danger { background: linear-gradient(135deg, #F44336, #EF5350); color: white; }
         .alert-info { background: linear-gradient(135deg, #2196F3, #64B5F6); color: white; }
         .alert-warning { background: linear-gradient(135deg, #FF9800, #FF7043); color: white; }
         .alert .btn-close {
            color: white;
            opacity: 0.8;
            filter: invert(1);
            box-shadow: none;
         }
         .alert .btn-close:hover { opacity: 1; }


        .form-control {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46,125,50,0.25);
        }

        .input-group-text {
            border: 1px solid #ced4da;
            border-right: none;
            background-color: #e9ecef;
            color: #495057;
            border-radius: 0.25rem 0 0 0.25rem;
        }
        .input-group > .form-control:focus + .input-group-text {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46,125,50,0.25);
             z-index: 3;
        }
         .input-group-text:has(+ .form-control:focus) {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(46,125,50,0.25);
            z-index: 3;
         }


        .login-card, .register-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .admin-body {
            background: #e9ecef;
         }
         .admin-wrapper {
             display: flex;
             min-height: 100vh;
             margin-top: 65px;
         }
         .admin-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding-top: 20px;
            position: fixed;
            top: 65px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1020;
         }
         .admin-content {
             flex-grow: 1;
             padding: 20px;
             margin-left: 250px;
         }

         .admin-sidebar .nav-link {
             color: rgba(255, 255, 255, 0.8);
             padding: 0.75rem 1.5rem;
             margin: 0.25rem 0.5rem;
             border-radius: 8px;
             transition: all 0.2s ease-in-out;
         }
          .admin-sidebar .nav-link:hover {
             background: rgba(255,255,255,0.2);
             color: white;
             transform: translateX(5px);
         }
         .admin-sidebar .nav-link.active {
             background: var(--accent-color);
             color: var(--text-dark) !important;
             font-weight: 600;
             box-shadow: 0 2px 5px rgba(0,0,0,0.2);
         }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
         .stats-card.bg-primary { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
         .stats-card.bg-success { background: linear-gradient(135deg, #FFC107, #FF9800); }
         .stats-card.bg-info { background: linear-gradient(135deg, #2196F3, #1976D2); }
         .stats-card.bg-warning { background: linear-gradient(135deg, #FF5722, #E64A19); }


        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(45deg);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
         .stats-title {
             font-size: 1rem;
             opacity: 0.9;
         }


        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 1.2rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
        }
         .d-flex .form-control {
             display: inline-block;
             width: auto;
         }


         .badge-status {
             padding: 0.4em 0.8em;
             border-radius: 0.8rem;
             font-weight: 600;
             font-size: 0.75em;
             display: inline-flex;
             align-items: center;
         }
         .badge-status i {
             margin-right: 0.3em;
         }
         .badge-status-pending { background-color: #ffda6a; color: #a77700; }
         .badge-status-pending_review { background-color: #4dd0e1; color: #006064; }
         .badge-status-confirmed { background-color: #a5d6a7; color: #1b5e20; }
         .badge-status-processing { background-color: #ffab91; color: #bf360c; }
         .badge-status-shipped { background-color: #90caf9; color: #0d47a1; }
         .badge-status-delivered { background-color: #c5e1a5; color: #33691e; }
         .badge-status-cancelled { background-color: #ef9a9a; color: #b71c1c; }


        @media (max-width: 991.98px) {
             .admin-sidebar {
                 position: static;
                 width: 100%;
                 height: auto;
                 overflow-y: visible;
                 padding-bottom: 20px;
                 margin-top: 0;
             }
              .admin-content {
                 margin-left: 0;
             }
             .admin-wrapper {
                 flex-direction: column;
                 margin-top: 65px;
             }
             .stats-card {
                 margin-bottom: 1rem;
             }
             .hero-section {
                 padding: 6rem 0 4rem;
             }
             .hero-title {
                 font-size: 2.5rem;
             }
             .hero-subtitle {
                 font-size: 1.1rem;
             }
             .section-title h2 {
                 font-size: 2rem;
             }
         }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-top: 2px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
         .btn-outline-primary .loading, .btn-secondary .loading, .btn-outline-secondary .loading {
             border-top-color: var(--primary-color);
         }
          .btn-danger .loading, .btn-outline-danger .loading {
              border-top-color: white;
              border-left-color: white;
               border-bottom-color: rgba(255,255,255,0.3);
              border-right-color: rgba(255,255,255,0.3);
         }
         .btn-custom .loading {
             border-top-color: var(--text-dark);
              border-left-color: var(--text-dark);
              border-bottom-color: rgba(0,0,0,0.3);
              border-right-color: rgba(0,0,0,0.3);
         }


        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

         .table-img-sm {
             width: 50px;
             height: 50px;
             object-fit: cover;
             border-radius: 5px;
         }
         .sticky-top {
             top: 80px;
         }

         .is-invalid {
             border-color: #e3342f !important;
         }
         .invalid-feedback {
             display: block !important;
         }


    </style>
</head>
<body class="<?= (session()->get('logged_in') && session()->get('role') === 'admin') ? 'admin-body' : '' ?>">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="fas fa-leaf me-2"></i>KAPINIS 
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                         
                        <a class="nav-link <?= (uri_string(true) == '' || uri_string(true) == '/') ? 'active' : '' ?>" href="<?= base_url('/') ?>"><i class="fas fa-home me-1"></i>Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(true), 'shop') === 0 ? 'active' : '' ?>" href="<?= base_url('/shop') ?>"><i class="fas fa-store me-1"></i>Toko</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string(true) == 'about' ? 'active' : '' ?>" href="<?= base_url('/about') ?>"><i class="fas fa-info-circle me-1"></i>Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string(true) == 'contact' ? 'active' : '' ?>" href="<?= base_url('/contact') ?>"><i class="fas fa-phone me-1"></i>Kontak</a>
                    </li>
                </ul>

                
                <ul class="navbar-nav">
                    <?php if (session()->get('logged_in')): ?>
                        <?php if (session()->get('role') !== 'admin'): ?>
                        <li class="nav-item">
                            
                            <a class="nav-link <?= strpos(uri_string(true), 'cart') === 0 ? 'active' : '' ?>" href="<?= base_url('/cart') ?>">
                                <i class="fas fa-shopping-cart me-1"></i>Keranjang
                                <span class="badge bg-warning text-dark" id="cart-count">0</span> 
                            </a>
                        </li>
                         <?php endif; ?>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?= esc(session()->get('full_name')) ?> 
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (session()->get('role') === 'admin'): ?>
                                    
                                    <li><a class="dropdown-item" href="<?= base_url('/admin') ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= base_url('/admin/products') ?>"><i class="fas fa-boxes me-2"></i>Kelola Produk</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-receipt me-2"></i>Kelola Pesanan</a></li>
                                     
                                     <li><a class="dropdown-item" href="<?= base_url('/admin/users') ?>"><i class="fas fa-users me-2"></i>Kelola Pengguna</a></li>
                                     <li><a class="dropdown-item" href="<?= base_url('/admin/categories') ?>"><i class="fas fa-tags me-2"></i>Kelola Kategori</a></li>
                                      
                                       <li><a class="dropdown-item" href="<?= base_url('/admin/contacts') ?>"><i class="fas fa-envelope-open-text me-2"></i>Pesan Kontak</a></li> 
                                <?php else: ?>
                                    
                                    <li><a class="dropdown-item <?= strpos(uri_string(true), 'order/') === 0 || uri_string(true) == 'orders' ? 'active' : '' ?>" href="<?= base_url('/orders') ?>"><i class="fas fa-box me-2"></i>Pesanan Saya</a></li>
                                    <li><a class="dropdown-item <?= uri_string(true) == 'profile' ? 'active' : '' ?>" href="<?= base_url('/profile') ?>"><i class="fas fa-user-edit me-2"></i>Profile</a></li> 
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                               
                                <li><a class="dropdown-item" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                       
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string(true) == 'login' ? 'active' : '' ?>" href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string(true) == 'register' ? 'active' : '' ?>" href="<?= base_url('/register') ?>"><i class="fas fa-user-plus me-1"></i>Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    
    <?php if (session()->get('logged_in') && session()->get('role') === 'admin'): ?>
      
        <div class="admin-wrapper">

            <div class="admin-sidebar">
                <ul class="nav flex-column">
                   
                     <li class="nav-item">
                         
                         <a class="nav-link <?= (uri_string(true) == 'admin' || uri_string(true) == 'admin/') ? 'active' : '' ?>" href="<?= base_url('/admin') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                         </a>
                     </li>
                    
                    <li class="nav-item">
                        
                        <a class="nav-link <?= strpos(uri_string(true), 'admin/products') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/products') ?>">
                            <i class="fas fa-boxes me-2"></i> Kelola Produk
                        </a>
                    </li>

                    <li class="nav-item">
                       
                        <a class="nav-link <?= strpos(uri_string(true), 'admin/orders') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/orders') ?>">
                            <i class="fas fa-receipt me-2"></i> Kelola Pesanan
                        </a>
                    </li>
                
                     <li class="nav-item">
                         <a class="nav-link <?= strpos(uri_string(true), 'admin/users') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/users') ?>">
                             <i class="fas fa-users me-2"></i> Kelola Pengguna
                         </a>
                     </li>
                      <li class="nav-item">
                         <a class="nav-link <?= strpos(uri_string(true), 'admin/categories') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/categories') ?>">
                             <i class="fas fa-tags me-2"></i> Kelola Kategori
                         </a>
                      </li>
                       
                       <li class="nav-item">
                          <a class="nav-link <?= strpos(uri_string(true), 'admin/contacts') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/contacts') ?>">
                              <i class="fas fa-envelope-open-text me-2"></i> Pesan Kontak
                          </a>
                       </li>
                </ul>
            </div>
  
            <div class="admin-content">
                
                 
                 <div class="container-fluid mt-3"> 
                      <?php // Tampilkan flash messages ?>
                      <?php if (session()->getFlashdata('success')): ?>
                         <div class="alert alert-success notification">
                             <i class="fas fa-check-circle me-2"></i>
                             <?= session()->getFlashdata('success') ?>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                     <?php endif; ?>
                     <?php if (session()->getFlashdata('error')): ?>
                         <div class="alert alert-danger notification">
                             <i class="fas fa-exclamation-triangle me-2"></i>
                             <?= session()->getFlashdata('error') ?>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                     <?php endif; ?>
                     <?php if (session()->getFlashdata('info')): ?>
                          <div class="alert alert-info notification">
                             <i class="fas fa-info-circle me-2"></i>
                             <?= session()->getFlashdata('info') ?>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                     <?php endif; ?>
                     <?php if (session()->getFlashdata('warning')): ?>
                          <div class="alert alert-warning notification">
                             <i class="fas fa-exclamation-circle me-2"></i>
                             <?= session()->getFlashdata('warning') ?>
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>
                     <?php endif; ?>
        
                      <?php if (session()->getFlashdata('errors')): ?>
                  
                       <div class="alert alert-danger notification">
                            <p><i class="fas fa-exclamation-triangle me-2"></i> Validasi Gagal:</p>
                           <ul class="mb-0">
                               <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                   <li><?= esc($error) ?></li>
                               <?php endforeach; ?>
                           </ul>
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>
                   <?php endif; ?>
                 </div>
                 
                 <?= $this->renderSection('content') ?>
            </div>
        </div>
    <?php else: ?>
        
        <div class="main-content-area"> 

       
             
             <div class="container mt-3">
                  <?php // Tampilkan flash messages ?>
                  <?php if (session()->getFlashdata('success')): ?>
                     <div class="alert alert-success notification">
                         <i class="fas fa-check-circle me-2"></i>
                         <?= session()->getFlashdata('success') ?>
                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 <?php endif; ?>
                 <?php if (session()->getFlashdata('error')): ?>
                     <div class="alert alert-danger notification">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         <?= session()->getFlashdata('error') ?>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 <?php endif; ?>
                 <?php if (session()->getFlashdata('info')): ?>
                      <div class="alert alert-info notification">
                         <i class="fas fa-info-circle me-2"></i>
                         <?= session()->getFlashdata('info') ?>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 <?php endif; ?>
                 <?php if (session()->getFlashdata('warning')): ?>
                      <div class="alert alert-warning notification">
                         <i class="fas fa-exclamation-circle me-2"></i>
                         <?= session()->getFlashdata('warning') ?>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 <?php endif; ?>
        
                  <?php if (session()->getFlashdata('errors')): ?>
                       <div class="alert alert-danger notification">
                            <p><i class="fas fa-exclamation-triangle me-2"></i> Validasi Gagal:</p>
                           <ul class="mb-0">
                               <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                   <li><?= esc($error) ?></li>
                               <?php endforeach; ?>
                           </ul>
                           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                       </div>
                   <?php endif; ?>
             </div>
           
             <div style="min-height: calc(100vh - 65px - 200px);"> 
                 <?= $this->renderSection('content') ?>
             </div>
        </div>
    <?php endif; ?>


    
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i>Kapiniss Shop</h5> 
                    <p class="text-muted">Menyediakan keripik pisang berkualitas tinggi dengan cita rasa autentik Indonesia. Dibuat dari pisang pilihan dan bumbu tradisional.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="footer-link me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-link me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-link me-3"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="footer-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/') ?>" class="footer-link">Beranda</a></li>
                        <li><a href="<?= base_url('/shop') ?>" class="footer-link">Toko</a></li>
                        <li><a href="<?= base_url('/about') ?>" class="footer-link">Tentang</a></li>
                        <li><a href="<?= base_url('/contact') ?>" class="footer-link">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Produk</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/shop?category=1') ?>" class="footer-link">Keripik Pisang Original</a></li> 
                        <li><a href="<?= base_url('/shop?category=1') ?>" class="footer-link">Keripik Pisang Balado</a></li>
                        <li><a href="<?= base_url('/shop?category=1') ?>" class="footer-link">Keripik Pisang Coklat</a></li>
                        <li><a href="<?= base_url('/shop') ?>" class="footer-link">Semua Produk</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Desa Jambar Kec.Nusaherang</li> 
                        <li><i class="fas fa-phone me-2"></i>+62 812-3456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i>Kapinis@gmail.com</li> 
                        <li><i class="fas fa-clock me-2"></i>Senin - Sabtu: 08:00 - 17:00</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary"> 
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">© <?= date('Y') ?> KAPINIS. All rights reserved.</p> 
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        <a href="#" class="footer-link me-3">Privacy Policy</a>
                        <a href="#" class="footer-link">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script> 

    
    <script>
        // Auto-hide notifications (alerts) that are present on the page when loaded
        $(document).ready(function() {
             // Add fade-out after 5 seconds for notifications present on page load
             $('.notification').each(function() {
                 const $this = $(this);
                 setTimeout(() => {
                     $this.fadeOut('slow', function() {
                         $(this).remove();
                     });
                 }, 5000); 
             });
        });


        // === Fungsi AJAX addToCart ===
        // Membungkus logic addToCart dalam fungsi bernama, bisa dipanggil dari berbagai tempat
        function addToCart(productId, quantity = 1, $buttonElement = null) { 

            // Cek apakah user sudah login (validasi frontend, backend tetap wajib)
            <?php if (!session()->get('logged_in')): ?>
                showNotification('info', 'Anda harus login untuk menambah ke keranjang.');
                // Opsional: Redirect ke halaman login setelah beberapa saat
                // setTimeout(function() { window.location.href = '<?= base_url('/login') ?>'; }, 2000);
                return; // Hentikan eksekusi
            <?php endif; ?>
             // Jika user adalah admin, cegah penambahan ke keranjang
             <?php if (session()->get('role') === 'admin'): ?>
                  showNotification('warning', 'Admin tidak dapat menambah produk ke keranjang.');
                  return; // Hentikan eksekusi
             <?php endif; ?>


            // Tampilkan indikator loading pada tombol, jika elemen tombol diberikan
            if ($buttonElement && !$buttonElement.hasClass('is-loading')) {
                 $buttonElement.addClass('is-loading').prop('disabled', true); // Tambahkan kelas loading dan disable tombol
                 
                 // Tambahkan spinner dan simpan HTML asli tombol
                 if ($buttonElement.text().trim() !== '' && !$buttonElement.find('.loading').length) { // Jika ada teks dan belum ada spinner
                     $buttonElement.data('original-html', $buttonElement.html()); // Simpan HTML asli
                     const spinner = '<span class="loading me-2"></span>'; // Elemen spinner
                     $buttonElement.html(spinner + $buttonElement.text()); // Tambahkan spinner di depan teks
                 } else if ($buttonElement.text().trim() === '' && !$buttonElement.find('.loading').length) { // Jika tidak ada teks
                      $buttonElement.data('original-html', $buttonElement.html());
                      $buttonElement.html('<span class="loading"></span>'); // Tampilkan hanya spinner
                 } else { // Jika ada teks tapi sudah ada spinner (untuk beberapa kasus)
                       const spinner = '<span class="loading me-2"></span>';
                       if (!$buttonElement.find('.loading').length) {
                           $buttonElement.html(spinner + $buttonElement.text());
                       }
                 }
            }


            // Lakukan permintaan AJAX ke endpoint penambahan keranjang
            $.ajax({
                 url: '<?= site_url('cart/add') ?>', // URL endpoint
                 method: 'POST', // Metode HTTP
                 data: { // Data yang akan dikirim
                     product_id: productId,
                     quantity: quantity,
                     <?= csrf_token() ?>: '<?= csrf_hash() ?>' // CSRF token untuk keamanan
                 },
                 dataType: 'json', // Harapkan respons dalam format JSON
                 
                 success: function(response) {
                     if (response.success) {
                         // Tampilkan notifikasi sukses
                         showNotification('success', response.message);
                         // Perbarui jumlah item di keranjang di navbar
                         if (response.cart_count !== undefined) {
                             updateCartCountDisplay(response.cart_count);
                         }
                     } else {
                         // Tampilkan notifikasi error
                         showNotification('error', response.message);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.error("AJAX error (cart/add): ", status, error, xhr.responseText);
                     // Tangani error berdasarkan status HTTP
                      if (xhr.status === 302 || xhr.status === 401) {
                           // Jika redirect ke login atau tidak terautentikasi
                           showNotification('info', 'Anda harus login untuk menambah ke keranjang.');
                           // Opsional: Langsung redirect setelah beberapa saat
                           // setTimeout(function() { window.location.href = '<?= base_url('/login') ?>'; }, 2000);
                      } else {
                          // Untuk error server lainnya, coba parse pesan error dari respons JSON
                          let errorMessage = 'Terjadi kesalahan saat menambahkan produk ke keranjang.';
                          try {
                               const errorResponse = JSON.parse(xhr.responseText);
                               if (errorResponse.message) {
                                    errorMessage = errorResponse.message;
                               }
                          } catch (e) {
                               // abaikan error parsing, gunakan pesan default
                          }
                          showNotification('error', errorMessage);
                      }
                 },
                 complete: function() { // Fungsi yang dijalankan setelah permintaan AJAX selesai (sukses atau error)
                     // Kembalikan tombol ke kondisi semula (non-loading, aktif)
                      if ($buttonElement && $buttonElement.hasClass('is-loading')) {
                          // Pastikan HTML asli dikembalikan jika ada
                           if ($buttonElement.data('original-html') !== undefined) {
                               $buttonElement.html($buttonElement.data('original-html'));
                           }
                           $buttonElement.prop('disabled', false).removeClass('is-loading'); // Aktifkan kembali dan hapus kelas loading
                      }
                 }
            });
        }
        
        // Memperbarui tampilan jumlah item di keranjang di navbar
        function updateCartCountDisplay(count) {
             // Pastikan elemen #cart-count ada sebelum diubah
             const cartCountSpan = $('#cart-count');
             if (cartCountSpan.length) {
                 cartCountSpan.text(count);
                 // Sembunyikan badge jika jumlahnya 0, tampilkan jika lebih dari 0
                 if (parseInt(count) > 0) {
                     cartCountSpan.show();
                 } else {
                     cartCountSpan.hide();
                 }
             }
        }

        // === Inisialisasi Umum Saat Dokumen Siap ===
        $(document).ready(function() {
            // Muat jumlah item keranjang di navbar saat halaman dimuat (jika user login dan bukan admin)
            <?php if (session()->get('logged_in') && session()->get('role') !== 'admin'): ?>
                 // Lakukan permintaan AJAX untuk mendapatkan jumlah item keranjang
                $.ajax({
                     url: '<?= site_url('cart/count') ?>', // URL endpoint untuk mendapatkan jumlah item
                     method: 'GET', // Metode HTTP
                     dataType: 'json', // Harapkan respons JSON
                     success: function(response) {
                          // Perbarui tampilan jumlah item keranjang berdasarkan respons
                         let count = 0;
                         if (typeof response === 'number') { // Jika respons langsung angka
                             count = response;
                         } else if (response && response.cart_count !== undefined) { // Jika respons adalah objek JSON dengan properti cart_count
                             count = response.cart_count;
                         }
                         updateCartCountDisplay(count); // Perbarui tampilan
                     },
                     error: function(xhr, status, error) {
                          // Jika terjadi error saat mengambil jumlah item
                         console.error("Error fetching cart count:", status, error, xhr.responseText);
                         updateCartCountDisplay(0); // Set ke 0 jika gagal
                          // Opsional: Tampilkan notifikasi error jika diperlukan
                          // showNotification('error', 'Gagal memuat jumlah item keranjang.');
                     }
                });
            <?php else: ?>
                 // Jika user tidak login atau adalah admin, sembunyikan badge keranjang
                 $('#cart-count').hide();
            <?php endif; ?>

            // === Penanganan Klik Tombol 'Add to Cart' ===
            // Delegasikan event click untuk tombol "Tambah ke Keranjang" yang mungkin ditambahkan secara dinamis
            $('body').on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault(); // Cegah perilaku default tombol

                const $button = $(this); // Tombol yang diklik
                const productId = $button.data('product-id'); // Ambil product ID dari data-attribute
                let quantity = 1; // Default quantity

                // Jika ada input kuantitas terkait (misalnya di halaman detail produk)
                const $quantityInput = $button.siblings('.quantity-input'); // Cari input kuantitas saudara
                if ($quantityInput.length) {
                    quantity = parseInt($quantityInput.val()) || 1; // Ambil nilai kuantitas dari input
                     // Validasi kuantitas terhadap stok maksimum (juga ada di backend)
                     const maxStock = parseInt($quantityInput.attr('max'));
                     if (quantity > maxStock) {
                          showNotification('warning', 'Kuantitas yang diminta (' + quantity + ') melebihi stok tersedia (' + maxStock + ').');
                          return; // Hentikan proses jika kuantitas melebihi stok
                     }
                } else {
                    // Jika tidak ada input kuantitas, ambil dari data-attribute tombol (misalnya di list produk)
                    quantity = $button.data('quantity') || 1;
                }

                 // Pastikan kuantitas adalah angka positif
                 if (isNaN(quantity) || quantity < 1) {
                      quantity = 1; // Default ke 1 jika tidak valid
                 }

                // Panggil fungsi addToCart dengan product ID, kuantitas, dan elemen tombol untuk indikator loading
                addToCart(productId, quantity, $button); // Kirim elemen tombol untuk menangani loading state
            });
            

            // === Penanganan Indikator Loading Generik untuk Form Submission ===
            // Menambahkan indikator loading pada tombol submit saat form di-submit
            $('form').on('submit', function(e) {
                const $form = $(this); // Form yang di-submit
                 // Lewati form yang memiliki kelas 'no-generic-loading' (untuk form dengan penanganan loading kustom)
                 if ($form.hasClass('no-generic-loading')) { 
                     return; // Jangan terapkan loading generik
                 }

                 // Temukan tombol submit dalam form
                const $submitButton = $form.find('button[type="submit"]:not(.btn-close):not(.no-loading)');


                if ($submitButton.length) {
                    // Terapkan loading state pada setiap tombol submit yang ditemukan
                     $submitButton.each(function() {
                          const $btn = $(this); // Tombol submit saat ini
                           // Hanya terapkan loading jika tombol belum di-disable
                          if (!$btn.prop('disabled')) {
                              $btn.addClass('is-loading').prop('disabled', true); // Tambahkan kelas loading dan disable tombol
                              $btn.data('original-html', $btn.html()); // Simpan HTML asli tombol
                               
                               // Tambahkan spinner di depan teks tombol
                                const spinner = '<span class="loading me-2"></span>';
                                const btnText = $btn.text().trim();
                                if (btnText !== '' && !$btn.find('.loading').length) { // Jika ada teks dan belum ada spinner
                                      $btn.html(spinner + btnText); // Tampilkan spinner + teks
                                 } else if (btnText === '' && !$btn.find('.loading').length) { // Jika tidak ada teks
                                      $btn.html('<span class="loading"></span>'); // Tampilkan hanya spinner
                                 }
                          }
                     });

                     // Catatan: Untuk form yang reload halaman, loading state akan hilang saat halaman baru dimuat.
                     // Untuk form AJAX, Anda perlu secara manual mengembalikan loading state di fungsi `complete` AJAX.
                } else {
                     // Log peringatan jika tidak ada tombol submit yang ditemukan
                     console.warn("Form submitted, but no standard submit button found matching selector for loading state:", $form[0]);
                     // Lanjutkan proses form submission
                }
            });


            // === Lazy Loading Gambar ===
            // Menggunakan Intersection Observer API untuk memuat gambar saat masuk ke viewport
            const imageObserver = new IntersectionObserver((entries, observer) => {
                // Iterasi setiap entri yang diamati
                entries.forEach(entry => {
                    if (entry.isIntersecting) { // Jika elemen masuk ke dalam viewport
                        const img = entry.target; // Elemen gambar
                        const src = img.getAttribute('data-src'); // Ambil URL gambar dari data-src

                        if (src) { // Jika data-src ada
                            img.src = src; // Set src gambar ke URL asli
                            img.onload = () => { img.removeAttribute('data-src'); }; // Hapus data-src setelah gambar berhasil dimuat
                             // Tangani error pemuatan gambar, ganti dengan gambar default
                             img.onerror = () => {
                                 img.src = '<?= base_url('assets/images/default.jpg') ?>'; // Gambar default jika gagal
                                  img.removeAttribute('data-src'); // Hapus data-src
                                  console.warn("Failed to load image:", src); // Log error
                              };
                        }

                        observer.unobserve(img); // Berhenti mengamati gambar setelah dimuat
                    }
                });
            }, { threshold: 0.1 }); // Deteksi saat 10% gambar terlihat

            // Amati semua gambar yang memiliki atribut data-src
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img); // Mulai mengamati setiap gambar
            });
        });

        // === Fungsi showNotification ===
        // Menampilkan notifikasi pop-up kustom (sukses, error, info, warning)
        function showNotification(type, message) {
             // Opsional: Hapus notifikasi yang ada sebelumnya jika hanya ingin satu notifikasi tampil
            // $('.notification').remove();

            // Tentukan kelas CSS alert berdasarkan tipe notifikasi
            const alertClass = {
                 'success': 'alert-success',
                 'error': 'alert-danger',
                 'info': 'alert-info',
                 'warning': 'alert-warning'
            }[type] || 'alert-info'; // Default ke info jika tipe tidak dikenal

            // Tentukan ikon Font Awesome berdasarkan tipe notifikasi
            const iconClass = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle',
                'warning': 'fas fa-exclamation-circle'
            }[type] || 'fas fa-info-circle';

            // Buat elemen HTML notifikasi
            const notification = `
                <div class="alert ${alertClass} alert-dismissible fade show notification">
                    <i class="${iconClass} me-2"></i> 
                    ${message} 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
                </div>
            `;
            $('body').append(notification); // Tambahkan notifikasi ke body dokumen

            // Atur agar notifikasi menghilang secara otomatis setelah 5 detik
             setTimeout(function() {
                 // Target notifikasi terakhir yang ditambahkan
                 $('.notification:last').fadeOut('slow', function() {
                     $(this).remove(); // Hapus elemen dari DOM setelah fade out
                 });
             }, 5000); // Durasi sebelum fade out dimulai (5 detik)
        }

         // === Fungsi formatRupiah ===
         // Memformat angka menjadi format mata uang Rupiah
         function formatRupiah(angka) {
            // Konversi input ke float, kembalikan jika bukan angka
            const num = parseFloat(angka);
            if (isNaN(num)) return angka; // Jika bukan angka, kembalikan nilai aslinya

            // Gunakan Intl.NumberFormat untuk pemformatan mata uang yang kuat
            const formatter = new Intl.NumberFormat('id-ID', {
                 style: 'currency',
                 currency: 'IDR',
                 minimumFractionDigits: 0, // Tidak ada desimal
                 maximumFractionDigits: 0, // Tidak ada desimal
            });

            // Format angka dan hapus spasi tambahan yang mungkin ditambahkan oleh formatter
            return formatter.format(num).replace(/\s/g, '');
         }

    </script>

    
    <?= $this->renderSection('scripts') ?> 
</body>
</html>