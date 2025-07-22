<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card">
                    <!-- Improved product image display with fallback -->
                    <img src="<?= base_url('assets/images/products/' . esc($product['image'] ?? 'default.jpg')) ?>"
                         class="card-img-top img-fluid" 
                         alt="<?= esc($product['name']) ?>"
                         style="max-height: 400px; object-fit: contain;"
                         onerror="this.src='<?= base_url('assets/images/default.jpg') ?>'">
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="mb-3"><?= esc($product['name']) ?></h1>
                <h3 class="product-price mb-4">Rp <?= number_format($product['price'], 0, ',', '.') ?></h3>

                <!-- Stock information with better visual hierarchy -->
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?> me-2">
                        <?= $product['stock'] > 0 ? 'Tersedia' : 'Habis' ?>
                    </span>
                    <span class="text-muted">
                        <i class="fas fa-box me-1"></i>Stok: <?= esc($product['stock']) ?>
                    </span>
                </div>

                <!-- Product description with improved formatting -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Deskripsi Produk</h5>
                    <p class="mt-3"><?= nl2br(esc($product['description'])) ?></p>
                </div>

                <!-- Additional product details -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Detail Produk</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-2"><i class="fas fa-weight me-2"></i> Berat: <?= esc($product['weight']) ?> gram</li>
                        <?php if(isset($product['category_name'])): ?>
                        <li class="mb-2"><i class="fas fa-tag me-2"></i> Kategori: <?= esc($product['category_name']) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Add to Cart section - improved with better validation -->
                <?php if ($product['stock'] > 0): ?>
                <div class="card p-3 mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Kuantitas:</label>
                        </div>
                        <div class="col-4 col-md-3">
                            <input type="number" id="quantity" name="quantity" 
                                   class="form-control text-center" 
                                   value="1" min="1" max="<?= esc($product['stock']) ?>"
                                   aria-describedby="quantityHelp">
                        </div>
                        <div class="col-auto">
                            <span id="quantityHelp" class="form-text">
                                Maks: <?= esc($product['stock']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-3">
                        <button class="btn btn-primary btn-lg add-to-cart-btn" 
                                data-product-id="<?= esc($product['id']) ?>"
                                data-stock="<?= esc($product['stock']) ?>">
                            <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i> Produk ini sedang tidak tersedia
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Quantity input validation
    $('#quantity').on('change blur', function() {
        const maxStock = parseInt($(this).attr('max'));
        let quantity = parseInt($(this).val()) || 1;
        
        if (quantity < 1) {
            quantity = 1;
            $(this).val(1);
        } else if (quantity > maxStock) {
            quantity = maxStock;
            $(this).val(maxStock);
            showNotification('warning', 'Kuantitas melebihi stok. Diset ke maksimal ' + maxStock);
        }
    });

    // Add to cart button handler - specific to this page
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($('#quantity').val()) || 1;
        const stock = parseInt($(this).data('stock'));
        
        if (quantity > stock) {
            showNotification('error', 'Kuantitas melebihi stok tersedia');
            return;
        }
        
        // Show loading state
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...');
        
        // Call the addToCart function from the layout
        addToCart(productId, quantity, function() {
            // Re-enable button after completion
            btn.prop('disabled', false).html(originalHtml);
        });
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>