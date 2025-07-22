<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Bagian utama halaman Checkout -->
<section class="py-5" style="background: linear-gradient(to right, #c8e6c9, #a5d6a7);">
    <div class="container">
        <!-- Judul Halaman -->
        <h2 class="text-center text-primary mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h2>

        <?php // Flash messages dan validation errors akan ditampilkan oleh layout template ?>
        <?php // Layout template akan menampilkan pesan dan daftar error gabungan ?>
        <?php // Error spesifik per field ditampilkan manual di bawah setiap input ?>

        <!-- Tata letak kolom untuk form dan ringkasan pesanan -->
        <div class="row fade-in">
            <!-- Kolom untuk Form Informasi Pengiriman & Pembayaran -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card p-4">
                    <h5 class="mb-3">Informasi Pengiriman & Pembayaran</h5>

                    <!-- Form POST ke URL /order/process -->
                    <form action="<?= site_url('/order/process') ?>" method="post">
                        <?= csrf_field() ?> <!-- Proteksi CSRF -->

                        <!-- Input Alamat Pengiriman -->
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Alamat Pengiriman Lengkap</label>
                            <!-- Gunakan old() helper untuk mengisi kembali field setelah validation error -->
                            <!-- Tambahkan kelas is-invalid jika ada error untuk field ini -->
                            <textarea class="form-control <?= session('errors.shipping_address') ? 'is-invalid' : '' ?>"
                                      id="shipping_address" name="shipping_address" rows="4" required
                                      placeholder="Masukkan alamat lengkap Anda, termasuk nama jalan, nomor rumah, RT/RW, Kelurahan/Desa, Kecamatan, Kota/Kabupaten, Provinsi, Kode Pos"><?= old('shipping_address', $user['address'] ?? '') ?></textarea>
                            <?php if (session('errors.shipping_address')): ?>
                                <div class="invalid-feedback d-block"><?= esc(session('errors.shipping_address')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Input Nomor Telepon Penerima -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon Penerima</label>
                            <!-- Gunakan old() helper dan tambahkan kelas is-invalid -->
                            <input type="text" class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>"
                                   id="phone" name="phone" required placeholder="Masukkan nomor telepon aktif penerima"
                                   value="<?= old('phone', $user['phone'] ?? '') ?>">
                            <?php if (session('errors.phone')): ?>
                                <div class="invalid-feedback d-block"><?= esc(session('errors.phone')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Pilih Metode Pembayaran -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                            <!-- Gunakan old() helper dan tambahkan kelas is-invalid -->
                            <select class="form-select <?= session('errors.payment_method') ? 'is-invalid' : '' ?>"
                                    id="payment_method" name="payment_method" required>
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <!-- Opsi Transfer Bank -->
                                <option value="bank_transfer" <?= old('payment_method') == 'bank_transfer' ? 'selected' : '' ?>>
                                    Transfer Bank (Manual Konfirmasi Admin)
                                </option>
                                <!-- Tambahkan opsi metode pembayaran lain di sini jika diimplementasikan -->
                                <!-- Contoh: <option value="cod">Bayar di Tempat (COD)</option> -->
                            </select>
                            <?php if (session('errors.payment_method')): ?>
                                <div class="invalid-feedback d-block"><?= esc(session('errors.payment_method')) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Input Catatan Pesanan (Opsional) -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan Pesanan (Opsional)</label>
                            <!-- Gunakan old() helper dan tambahkan kelas is-invalid -->
                            <textarea class="form-control <?= session('errors.notes') ? 'is-invalid' : '' ?>"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Contoh: Jangan pedas, bungkus terpisah, atau instruksi khusus lainnya"><?= old('notes') ?></textarea>
                            <?php if (session('errors.notes')): ?>
                                <small class="text-danger"><?= esc(session('errors.notes')) ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Tombol "Buat Pesanan" -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-custom btn-lg">
                                <i class="fas fa-check-circle me-2"></i> Buat Pesanan
                            </button>
                        </div>
                    </form>
                </div> 
            </div> 

            <!-- Kolom untuk Ringkasan Pesanan -->
            <div class="col-lg-5">
                <div class="card p-4 bg-light sticky-top" style="top: 80px;"> 
                    <h5>Detail Pesanan Anda</h5>
                    <hr>
                    <ul class="list-unstyled">
                        <?php if (empty($cartItems)): ?>
                            
                            <li><p class="text-muted">Keranjang kosong.</p></li>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <li class="mb-3 d-flex align-items-center border-bottom pb-3"> 
                                   
                                    <img src="<?= base_url('/assets/images/products/' . esc($item['image'] ?: 'default.jpg')) ?>"
                                         alt="<?= esc($item['name']) ?>" class="table-img-sm me-3">
                                    
                                    <div class="flex-grow-1"> 
                                        <p class="mb-0"><strong><?= esc($item['name'] ?: 'Produk Tidak Ditemukan') ?></strong></p> 
                                        <p class="text-muted mb-0"><?= esc($item['quantity']) ?> x Rp <?= number_format(esc($item['price']), 0, ',', '.') ?></p> 
                                    </div>
                                
                                    <span class="ms-auto text-muted">Rp <?= number_format(esc($item['quantity'] * $item['price']), 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
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
                </div> 
            </div> 
        </div> 

        <!-- Tombol Kembali ke Keranjang -->
        <div class="text-center mt-4">
            <a href="<?= site_url('/cart') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
            </a>
        </div>
    </div> 
</section> 

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?= $this->section('scripts') ?> <!-- Section scripts (opsional) -->
<!-- Tidak ada script spesifik tambahan untuk halaman checkout, kecuali yang di layout utama -->
<?= $this->endSection() ?>