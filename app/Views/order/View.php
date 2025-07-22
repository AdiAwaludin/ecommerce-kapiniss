<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Bagian utama halaman Detail Pesanan Pengguna -->
<section class="py-5">
    <div class="container">
        <!-- Judul Halaman Detail Pesanan -->
        <h2 class="text-center text-primary mb-4">
            <i class="fas fa-receipt me-2"></i>Detail Pesanan #<?= esc($order['order_number']) ?>
        </h2> <!-- Menampilkan nomor pesanan di judul -->

        <!-- Flash messages dan validation errors akan ditampilkan oleh layout template -->
        <!-- Error spesifik per field (misal upload bukti bayar) ditampilkan di dalam modal -->

        <!-- Tata letak kolom untuk informasi pesanan dan daftar item -->
        <div class="row fade-in">
            <!-- Kolom untuk Informasi Pesanan -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card p-4">
                    <h5>Informasi Pesanan</h5>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Nomor Pesanan:</strong> <?= esc($order['order_number']) ?></p>
                            <p><strong>Tanggal Pesanan:</strong> <?= date('d M Y H:i', strtotime(esc($order['created_at']))) ?></p>
                            <p><strong>Status:</strong>
                                <?php
                                    $statusClass = [
                                        'pending' => 'badge-status-pending',
                                        'pending_review' => 'badge-status-pending_review',
                                        'confirmed' => 'badge-status-confirmed',
                                        'processing' => 'badge-status-processing',
                                        'shipped' => 'badge-status-shipped',
                                        'delivered' => 'badge-status-delivered',
                                        'cancelled' => 'badge-status-cancelled'
                                    ];
                                    $statusText = [
                                        'pending' => 'Pending',
                                        'pending_review' => 'Menunggu Konfirmasi Bayar',
                                        'confirmed' => 'Pembayaran Dikonfirmasi',
                                        'processing' => 'Pesanan Diproses',
                                        'shipped' => 'Pesanan Dikirim',
                                        'delivered' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $statusIcon = [
                                        'pending' => 'fas fa-hourglass-half',
                                        'pending_review' => 'fas fa-clock',
                                        'confirmed' => 'fas fa-check-circle',
                                        'processing' => 'fas fa-cogs',
                                        'shipped' => 'fas fa-truck',
                                        'delivered' => 'fas fa-box-open',
                                        'cancelled' => 'fas fa-times-circle'
                                    ];
                                ?>
                                <!-- Tampilkan status badge -->
                                <span class="badge badge-status <?= esc($statusClass[$order['status']] ?? 'badge-secondary') ?>">
                                    <i class="<?= esc($statusIcon[$order['status']] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($statusText[$order['status']] ?? $order['status']) ?>
                                </span>
                            </p>
                            <!-- (Opsional) Tombol Batalkan jika status masih pending -->
                        </div>
                        <div class="col-md-6">
                            <p><strong>Metode Pembayaran:</strong> <?= esc(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></p>
                            <p><strong>Total Pembayaran:</strong> Rp <?= number_format(esc($order['total_amount']), 0, ',', '.') ?></p>
                        </div>
                    </div>

                    <h5>Alamat Pengiriman</h5>
                    <hr>
                    <p><?= nl2br(esc($order['shipping_address'])) ?></p>
                    <p><strong>Nomor Telepon Pengiriman:</strong> <?= esc($order['phone']) ?></p>

                    <?php if (!empty($order['notes'])): ?>
                        <h5>Catatan</h5>
                        <hr>
                        <p><?= nl2br(esc($order['notes'])) ?></p>
                    <?php endif; ?>

                    <!-- Bagian Pembayaran -->
                    <?php if ($order['status'] === 'pending'): ?>
                        <div class="alert alert-warning mt-4 text-center">
                            <i class="fas fa-info-circle me-2"></i> Pesanan menunggu pembayaran. Silakan lakukan pembayaran dan upload bukti transfer.
                            <br>
                            <button class="btn btn-sm btn-success mt-2" data-bs-toggle="modal" data-bs-target="#uploadPaymentModal"
                                    data-order-id="<?= esc($order['id']) ?>" data-order-number="<?= esc($order['order_number']) ?>">
                                <i class="fas fa-upload me-1"></i> Upload Bukti Pembayaran
                            </button>
                        </div>
                    <?php elseif ($order['status'] === 'pending_review'): ?>
                        <div class="alert alert-info mt-4 text-center">
                            <i class="fas fa-clock me-2"></i> Bukti pembayaran sudah diupload. Menunggu konfirmasi dari admin.
                            <?php if ($order['payment_proof']): ?>
                                <br>
                                <a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank" class="alert-link mt-2 d-inline-block">
                                    <i class="fas fa-eye me-1"></i> Lihat Bukti Pembayaran
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($order['payment_proof']): ?>
                        <h5>Bukti Pembayaran</h5>
                        <hr>
                        <a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank">
                            <?php
                                $fileExtension = pathinfo($order['payment_proof'], PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            ?>
                            <?php if ($isImage): ?>
                                <img src="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" alt="Bukti Pembayaran"
                                     class="img-fluid border p-2 rounded" style="max-height: 300px;">
                            <?php else: ?>
                                <i class="fas fa-file-alt me-1"></i> <?= esc($order['payment_proof']) ?>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kolom Item -->
            <div class="col-lg-4">
                <div class="card p-4 sticky-top" style="top: 80px;">
                    <h5>Item Pesanan</h5>
                    <hr>
                    <ul class="list-unstyled">
                        <?php if (empty($orderItems)): ?>
                            <li><p class="text-muted">Tidak ada item dalam pesanan ini.</p></li>
                        <?php else: ?>
                            <?php foreach ($orderItems as $item): ?>
                                <li class="mb-3 d-flex align-items-center border-bottom pb-3">
                                    <img src="<?= base_url('/assets/images/products/' . esc($item['image'] ?: 'default.jpg')) ?>"
                                         alt="<?= esc($item['name'] ?: 'Produk Dihapus') ?>" class="table-img-sm me-3">
                                    <div class="flex-grow-1">
                                        <p class="mb-0"><strong><?= esc($item['name'] ?: 'Produk Dihapus') ?></strong></p>
                                        <p class="text-muted mb-0"><?= esc($item['quantity']) ?> x Rp <?= number_format(esc($item['price']), 0, ',', '.') ?></p>
                                    </div>
                                    <span class="ms-auto text-muted">Rp <?= number_format(esc($item['quantity'] * $item['price']), 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <hr>
                    <?php
                        $item_subtotal = 0;
                        foreach($orderItems as $item) {
                            $item_subtotal += (float)$item['price'] * (int)$item['quantity'];
                        }
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal Item:</span>
                        <span>Rp <?= number_format($item_subtotal, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Pengiriman:</span>
                        <span>Belum dihitung</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total Pesanan:</strong>
                        <strong>Rp <?= number_format(esc($order['total_amount']), 0, ',', '.') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-4">
            <a href="<?= site_url('/orders') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pesanan
            </a>
        </div>

        <!-- Modal Upload Pembayaran -->
        <div class="modal fade" id="uploadPaymentModal" tabindex="-1" aria-labelledby="uploadPaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="uploadPaymentForm" action="" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <p>Silakan upload bukti pembayaran Anda untuk pesanan <strong id="modal-order-number"></strong>.</p>
                            <div class="mb-3">
                                <label for="payment_proof" class="form-label">Pilih File Bukti Pembayaran</label>
                                <input class="form-control <?= session('errors.payment_proof') ? 'is-invalid' : '' ?>" type="file"
                                       id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="text-muted">Ukuran maksimal 2MB.</small>
                            </div>
                            <?php if (session()->getFlashdata('errors')['payment_proof'] ?? false && session()->getFlashdata('open_upload_modal')): ?>
                                <div class="invalid-feedback d-block mt-2"><?= esc(session()->getFlashdata('errors')['payment_proof']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?= $this->section('scripts') ?> <!-- Script untuk modal upload bukti -->
<script>
    $(document).ready(function() {
        $('#uploadPaymentModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const orderId = button.data('order-id');
            const orderNumber = button.data('order-number');
            const modal = $(this);
            modal.find('#modal-order-number').text(orderNumber);
            modal.find('#uploadPaymentForm').attr('action', '<?= site_url('/order/upload-payment/') ?>' + orderId);
            modal.find('.invalid-feedback, .alert-danger').remove();
            modal.find('#payment_proof').val('').removeClass('is-invalid');
        });

        <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_upload_modal')): ?>
            $(window).on('load', function() {
                const currentOrderId = <?= esc($order['id']) ?>;
                const triggerButton = $(`button[data-order-id="${currentOrderId}"]`);
                if (triggerButton.length) {
                    triggerButton.trigger('click');
                }
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?> <!-- Mengakhiri section 'scripts' -->
