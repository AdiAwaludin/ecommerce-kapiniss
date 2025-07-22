<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Bagian utama halaman Keranjang Belanja -->
<section class="py-5">
    <div class="container">
        <!-- Judul Halaman -->
        <h2 class="text-center text-primary mb-4">
            <i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja
        </h2>

        <!-- Flash messages dan validation errors akan ditampilkan oleh layout template -->

        <!-- Cek apakah keranjang kosong -->
        <?php if (empty($cartItems)): ?>
            <!-- Pesan jika keranjang kosong -->
            <div class="alert alert-info text-center fade-in">
                <i class="fas fa-info-circle me-2"></i>Keranjang belanja Anda kosong.
                <br><a href="<?= site_url('/shop') ?>" class="alert-link">Mulai belanja sekarang!</a>
            </div>
        <?php else: ?>
            <!-- Tampilkan daftar item keranjang dan ringkasan -->
            <div class="row fade-in">
                <!-- Kolom Daftar Item -->
                <div class="col-lg-8">
                    <div class="card p-3">
                        <?php $itemCount = count($cartItems); $i = 0; ?>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item d-flex align-items-center py-3 <?= (++$i < $itemCount) ? 'border-bottom' : '' ?>">
                                <!-- Gambar Produk -->
                                <img src="<?= base_url('/assets/images/products/' . esc($item['image'] ?: 'default.jpg')) ?>"
                                     alt="<?= esc($item['name']) ?>" class="table-img-sm me-4">

                                <!-- Informasi Produk -->
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                                    <p class="text-muted mb-1">Harga: Rp <?= number_format(esc($item['price']), 0, ',', '.') ?></p>
                                    <p class="text-muted mb-0"><small>Stok Tersisa: <?= esc($item['stock']) ?></small></p>
                                </div>

                                <!-- Update Kuantitas & Hapus -->
                                <div class="d-flex align-items-center">
                                    <form action="<?= site_url('/cart/update/' . esc($item['id'])) ?>" method="post"
                                          class="d-flex align-items-center update-cart-form me-2">
                                        <?= csrf_field() ?>
                                        <label for="quantity_<?= esc($item['id']) ?>" class="visually-hidden">Jumlah:</label>
                                        <input type="number" id="quantity_<?= esc($item['id']) ?>" name="quantity"
                                               class="form-control quantity-input form-control-sm"
                                               value="<?= esc($item['quantity']) ?>" min="1" max="<?= esc($item['stock']) ?>" required>
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2" title="Update Kuantitas">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>

                                    <a href="<?= site_url('/cart/remove/' . esc($item['id'])) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       title="Hapus Item"
                                       onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Kolom Ringkasan Belanja -->
                <div class="col-lg-4">
                    <div class="card p-4 bg-light sticky-top" style="top: 80px;">
                        <h5>Ringkasan Belanja</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal Item:</span>
                            <span>Rp <?= number_format(esc($total), 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Pengiriman:</span>
                            <span>Belum dihitung</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong>Rp <?= number_format(esc($total), 0, ',', '.') ?></strong>
                        </div>
                        <a href="<?= site_url('/order/checkout') ?>" class="btn btn-custom btn-lg d-block">
                            <i class="fas fa-credit-card me-2"></i> Lanjut ke Checkout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tombol Lanjut Belanja -->
            <div class="text-center mt-4">
                <a href="<?= site_url('/shop') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Lanjutkan Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<!-- Section Scripts -->
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function () {
        $('.quantity-input').on('change input', function () {
            const maxStock = parseInt($(this).attr('max'));
            let currentVal = parseInt($(this).val());

            if (isNaN(currentVal) || currentVal < 1) {
                $(this).val(1);
                currentVal = 1;
            }

            if (currentVal > maxStock) {
                $(this).val(maxStock);
                showNotification('warning', 'Kuantitas melebihi stok tersedia (' + maxStock + ').');
            }
        });

        $('.update-cart-form').on('submit', function (event) {
            const quantityInput = $(this).find('.quantity-input');
            const quantity = parseInt(quantityInput.val());
            const maxStock = parseInt(quantityInput.attr('max'));

            if (isNaN(quantity) || quantity < 1) {
                event.preventDefault();
                showNotification('warning', 'Kuantitas harus minimal 1 untuk item ini.');
                return false;
            }

            if (quantity > maxStock) {
                event.preventDefault();
                showNotification('warning', 'Kuantitas melebihi stok tersedia (' + maxStock + ').');
                return false;
            }

            return true;
        });
    });
</script>
<?= $this->endSection() ?> <!-- Mengakhiri section 'scripts' -->
