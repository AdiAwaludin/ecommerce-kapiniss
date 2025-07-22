<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Judul Halaman Admin -->
<h2 class="text-primary mb-4"><i class="fas fa-receipt me-2"></i>Kelola Pesanan</h2>

<!-- Tabel Daftar Pesanan -->
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No Pesanan</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Metode Bayar</th>
                    <th>Bukti Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9" class="text-center">Belum ada pesanan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= esc($order['id']) ?></td>
                            <td><?= esc($order['order_number']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime(esc($order['created_at']))) ?></td>
                            <td>
                                <?= esc($order['full_name'] ?: 'Pengguna Dihapus') ?>
                                <?php if ($order['email']): ?>
                                    <br><small class="text-muted"><?= esc($order['email']) ?></small>
                                <?php endif; ?>
                            </td>
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
                                        'confirmed' => 'Dikonfirmasi',
                                        'processing' => 'Diproses',
                                        'shipped' => 'Dikirim',
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
                                <span class="badge badge-status <?= esc($statusClass[$order['status']] ?? 'badge-secondary') ?>">
                                    <i class="<?= esc($statusIcon[$order['status']] ?? 'fas fa-info-circle') ?> me-1"></i>
                                    <?= esc($statusText[$order['status']] ?? $order['status']) ?>
                                </span>
                            </td>
                            <td><?= esc(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></td>
                            <td>
                                <?php if ($order['payment_proof']): ?>
                                    <a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    Belum Ada
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('/admin/order/view/' . esc($order['id'])) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Detail Pesanan">
                                    <i class="fas fa-info-circle"></i> Detail
                                </a>

                                <?php if (in_array($order['status'], ['pending', 'pending_review'])): ?>
                                    <form action="<?= site_url('/admin/order/confirm-payment/' . esc($order['id'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin mengkonfirmasi pembayaran pesanan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi Pembayaran">
                                            <i class="fas fa-check"></i> Konfirmasi
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Ubah Status">
                                        Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><h6 class="dropdown-header">Ubah Status Ke:</h6></li>
                                        <?php foreach ($statusText as $key => $label): ?>
                                            <li>
                                                <a class="dropdown-item <?= $key === 'cancelled' ? 'text-danger' : '' ?>" href="#" onclick="updateOrderStatus(<?= esc($order['id']) ?>, '<?= $key ?>')">
                                                    <?= $label ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateOrderStatus(orderId, status) {
        const statusTextMap = {
            'pending': 'Pending',
            'pending_review': 'Menunggu Konfirmasi Bayar',
            'confirmed': 'Dikonfirmasi',
            'processing': 'Diproses',
            'shipped': 'Dikirim',
            'delivered': 'Selesai',
            'cancelled': 'Dibatalkan'
        };

        const formattedStatus = statusTextMap[status] || status;

        if (confirm(`Yakin ingin mengubah status pesanan #${orderId} menjadi "${formattedStatus}"?`)) {
            $.ajax({
                url: '<?= site_url('/admin/order/update-status/') ?>' + orderId,
                method: 'POST',
                data: {
                    status: status,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function(xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    let errorMessage = 'Terjadi kesalahan saat mengupdate status.';
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {}
                    showNotification('error', errorMessage);
                }
            });
        }
    }
</script>
<?= $this->endSection() ?>
