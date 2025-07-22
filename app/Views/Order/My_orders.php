<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Bagian utama halaman Pesanan Saya -->
<section class="py-5">
    <div class="container">
        <!-- Judul Halaman -->
        <h2 class="text-center text-primary mb-4"><i class="fas fa-box me-2"></i>Pesanan Saya</h2>

        <!-- Flash messages dan validation errors akan ditampilkan oleh layout template -->

        <!-- Cek apakah pengguna memiliki pesanan -->
        <?php if (empty($orders)): ?>
            <!-- Pesan jika tidak ada pesanan -->
            <div class="alert alert-info text-center fade-in">
                <i class="fas fa-info-circle me-2"></i>Anda belum memiliki pesanan.
                <!-- Link untuk mulai berbelanja -->
                <br><a href="<?= site_url('/shop') ?>" class="alert-link">Mulai belanja sekarang!</a>
            </div>
        <?php else: ?>
            <!-- Tampilkan tabel pesanan -->
            <div class="table-responsive fade-in">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Metode Bayar</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop melalui setiap pesanan -->
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= esc($order['order_number']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime(esc($order['created_at']))) ?></td>
                                <td>Rp <?= number_format(esc($order['total_amount']), 0, ',', '.') ?></td>
                                <td>
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
                                    <!-- Display status badge -->
                                    <span class="badge badge-status <?= esc($statusClass[$order['status']] ?? 'badge-secondary') ?>">
                                        <i class="<?= esc($statusIcon[$order['status']] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($statusText[$order['status']] ?? $order['status']) ?>
                                    </span>
                                </td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></td>
                                <td>
                                    <?php if ($order['payment_proof']): ?>
                                        <!-- Link untuk melihat bukti pembayaran -->
                                        <a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat Bukti Bayar">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        Belum Ada
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Tombol Detail Pesanan -->
                                    <a href="<?= site_url('/order/view/' . esc($order['id'])) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Detail Pesanan">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </a>
                                    <!-- Tombol Upload Bukti Bayar jika status pending -->
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="modal" data-bs-target="#uploadPaymentModal"
                                                data-order-id="<?= esc($order['id']) ?>" data-order-number="<?= esc($order['order_number']) ?>"
                                                title="Upload Bukti Bayar">
                                            <i class="fas fa-upload me-1"></i> Upload Bayar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upload Payment Modal -->
    <div class="modal fade" id="uploadPaymentModal" tabindex="-1" aria-labelledby="uploadPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPaymentModalLabel">Upload Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadPaymentForm" action="" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <p>Silakan upload bukti pembayaran Anda untuk pesanan <strong id="modal-order-number"></strong>.</p>
                        <div class="mb-3">
                            <label for="payment_proof" class="form-label">Pilih File Bukti Pembayaran (JPG, JPEG, PNG, PDF)</label>
                            <input class="form-control <?= session('errors.payment_proof') ? 'is-invalid' : '' ?>" type="file" id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">Ukuran file maksimal 2MB.</small>
                        </div>
                        <?php if (session()->getFlashdata('errors')['payment_proof'] ?? false && session()->getFlashdata('open_upload_modal_on_orders')): ?>
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
</section>

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?= $this->section('scripts') ?> <!-- Section scripts -->

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

        <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_upload_modal_on_orders')): ?>
            $(window).on('load', function() {
                const failedUploadOrderId = <?= json_encode(session()->getFlashdata('failed_upload_order_id') ?? null) ?>;
                if (failedUploadOrderId) {
                    const triggerButton = $(`button[data-order-id="${failedUploadOrderId}"]`);
                    if (triggerButton.length) {
                        triggerButton.trigger('click');
                    }
                }
            });
        <?php endif; ?>
    });
</script>

<?= $this->endSection() ?> <!-- Mengakhiri section 'scripts' -->
