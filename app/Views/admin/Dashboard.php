<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<h2 class="text-primary mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h2>

<div class="row mb-4">
    <!-- Total Orders -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <div class="stats-number"><?= esc($totalOrders) ?></div>
                <div class="stats-title"><i class="fas fa-receipt me-1"></i> Total Pesanan</div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <div class="stats-number"><?= esc($totalProducts) ?></div>
                <div class="stats-title"><i class="fas fa-boxes me-1"></i> Total Produk</div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card stats-card bg-info text-white">
            <div class="card-body">
                <div class="stats-number"><?= esc($totalUsers) ?></div>
                <div class="stats-title"><i class="fas fa-users me-1"></i> Total Pelanggan</div>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card stats-card bg-warning text-dark">
            <div class="card-body">
                <div class="stats-number"><?= esc($pendingOrders) ?></div>
                <div class="stats-title"><i class="fas fa-clock me-1"></i> Pending / Review</div>
            </div>
        </div>
    </div>
</div>

<h5>Pesanan Terbaru</h5>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>No Pesanan</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Bukti Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada pesanan terbaru.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?= esc($order['order_number']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime(esc($order['created_at']))) ?></td>
                            <td>
                                <?= esc($order['full_name'] ?: 'Pengguna Dihapus') ?><br>
                                <small class="text-muted"><?= esc($order['email'] ?? '-') ?></small>
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
                                <a href="<?= site_url('/admin/order/view/' . esc($order['id'])) ?>" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="fas fa-info-circle"></i>
                                </a>

                                <?php if (in_array($order['status'], ['pending', 'pending_review'])): ?>
                                    <form action="<?= site_url('/admin/order/confirm-payment/' . esc($order['id'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin mengkonfirmasi pembayaran pesanan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>

                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><h6 class="dropdown-header">Ubah Status Ke:</h6></li>
                                        <?php foreach ($statusText as $key => $label): ?>
                                            <li><a class="dropdown-item <?= $key === 'cancelled' ? 'text-danger' : '' ?>" href="#" onclick="updateOrderStatus(<?= esc($order['id']) ?>, '<?= $key ?>')"><?= $label ?></a></li>
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

    <div class="text-end mt-3">
        <a href="<?= site_url('/admin/orders') ?>" class="btn btn-outline-primary">
            Lihat Semua Pesanan <i class="fas fa-arrow-right ms-1"></i>
        </a>
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
    const formattedStatus = statusTextMap[status] || status.toUpperCase();

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
                    alert(response.message);
                    setTimeout(() => { location.reload(); }, 500);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error, xhr.responseText);
                alert("Terjadi kesalahan saat mengupdate status.");
            }
        });
    }
}
</script>
<?= $this->endSection() ?>
