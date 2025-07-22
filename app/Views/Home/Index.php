<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout sebagai kerangka utama -->

<?= $this->section('content') ?> <!-- Memulai section 'content' yang akan dimasukkan ke dalam layout -->

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <!-- Reorder columns for small screens: text first on small, image first on large -->
            <div class="col-lg-6 order-lg-1 order-2">
                <h1 class="hero-title">Keripik Pisang Terbaik </h1>
                <p class="hero-subtitle">Nikmati kelezatan keripik pisang renyah dengan berbagai varian rasa yang menggugah selera. Dibuat dari pisang pilihan berkualitas tinggi.</p>
                <!-- Link to the shop page -->
                <a href="<?= site_url('/shop') ?>" class="btn btn-custom btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                </a>
            </div>

            <!-- Hero Image -->
            <div class="col-lg-6 order-lg-2 order-1 mb-4 mb-lg-0">
                <div class="hero-image text-center">
                    <!-- Pastikan Anda memiliki file assets/images/hero-keripik.png -->
                    <img src="<?= base_url('/assets/images/hero-keripik.png') ?>" alt="Keripik Pisang" class="img-fluid" style="max-width: 400px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Feature 1 -->
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-seedling fa-3x" style="color: var(--primary-color);"></i>
                    </div>
                    <h4>Bahan Pilihan</h4>
                    <p class="text-muted">Dibuat dari pisang segar pilihan tanpa pengawet buatan</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shipping-fast fa-3x" style="color: var(--accent-color);"></i>
                    </div>
                    <h4>Pengiriman Cepat</h4>
                    <p class="text-muted">Kami kirim pesanan Anda secepat mungkin</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-heart fa-3x" style="color: var(--success-color);"></i>
                    </div>
                    <h4>Cita Rasa Autentik</h4>
                    <p class="text-muted">Resep tradisional warisan Nusantara</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <!-- Section Title -->
        <div class="section-title">
            <h2>Produk Unggulan</h2>
            <p class="text-muted">Pilihan terbaik keripik pisang dengan berbagai varian rasa</p>
        </div>

        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i> Belum ada produk unggulan yang tersedia saat ini.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card fade-in h-100">
                            <img src="<?= base_url('/assets/images/placeholder.jpg') ?>"
                                 data-src="<?= base_url('/assets/images/products/' . esc($product['image'] ?: 'default.jpg')) ?>"
                                 class="card-img-top" alt="<?= esc($product['name']) ?>">

                            <div class="product-overlay">
                                <?php if ($product['stock'] > 0): ?>
                                    <button class="btn btn-add-cart" onclick="addToCart(<?= esc($product['id']) ?>)">
                                        <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white p-2">
                                        <i class="fas fa-box-open me-1"></i>Stok Habis
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= esc($product['name']) ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= character_limiter($product['description'] ?? '', 100) ?>0 ?></p>

                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="product-price">Rp <?= number_format(esc($product['price']), 0, ',', '.') ?></span>
                                    <small class="text-muted">
                                        <i class="fas fa-box me-1"></i>Stok: <?= esc($product['stock']) ?>
                                    </small>
                                </div>

                                <div class="mt-3">
                                    <a href="<?= site_url('/product/' . esc($product['id'])) ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                    <?php if ($product['stock'] > 0): ?>
                                        <button class="btn btn-success btn-sm float-end" onclick="addToCart(<?= esc($product['id']) ?>)">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Lihat Semua Produk -->
        <div class="text-center mt-4">
            <a href="<?= site_url('/shop') ?>" class="btn btn-custom">
                <i class="fas fa-store me-2"></i>Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Testimoni Pelanggan</h2>
            <p class="text-muted">Apa kata mereka tentang produk kami</p>
        </div>

        <div class="row">
            <!-- Testimoni 1 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-primary"></i>
                        </div>
                        <p class="card-text">"Keripik pisangnya enak banget! Renyah dan tidak terlalu berminyak. Anak-anak suka sekali."</p>
                        <div class="mt-3">
                            <div class="mb-2">
                                <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                            </div>
                            <strong>Siti Nurhaliza</strong>
                            <small class="text-muted d-block">Jakarta</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 2 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-primary"></i>
                        </div>
                        <p class="card-text">"Pengiriman cepat dan packaging rapi. Produknya fresh dan rasanya autentik."</p>
                        <div class="mt-3">
                            <div class="mb-2">
                                <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star-half-alt text-warning"></i>
                            </div>
                            <strong>Ahmad Fauzi</strong>
                            <small class="text-muted d-block">Bandung</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 3 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-primary"></i>
                        </div>
                        <p class="card-text">"Varian rasa banyak dan semuanya enak. Harga juga terjangkau. Recommended!"</p>
                        <div class="mt-3">
                            <div class="mb-2">
                                <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                            </div>
                            <strong>Maya Sari</strong>
                            <small class="text-muted d-block">Surabaya</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?= $this->section('scripts') ?> <!-- Jika ada script tambahan -->
<!-- Script addToCart harus sudah ada di layout -->
<?= $this->endSection() ?>
