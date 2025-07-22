<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Judul Halaman Admin -->
<h2 class="text-primary mb-4"><i class="fas fa-boxes me-2"></i>Kelola Produk</h2>

<!-- Tombol Tambah Produk Baru -->
<div class="mb-3">
    <a href="<?= site_url('/admin/products/create') ?>" class="btn btn-success">
        <i class="fas fa-plus me-2"></i> Tambah Produk Baru
    </a> 
</div>

<!-- Tabel Daftar Produk -->
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Berat (gr)</th>
                    <th>Aktif</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="10" class="text-center">Belum ada produk.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= esc($product['id']) ?></td>
                            <td>
                                <img src="<?= base_url('/assets/images/products/' . esc($product['image'] ?: 'default.jpg')) ?>"
                                     alt="<?= esc($product['name']) ?>" class="table-img-sm">
                            </td>
                            <td><?= esc($product['name']) ?></td>
                            <td><?= esc($product['category_name'] ?: 'N/A') ?></td>
                            <td>Rp <?= number_format(esc($product['price']), 0, ',', '.') ?></td>
                            <td><?= esc($product['stock']) ?></td>
                            <td><?= esc($product['weight']) ?></td>
                            <td>
                                <?= $product['is_active'] 
                                    ? '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Ya</span>' 
                                    : '<span class="badge bg-danger"><i class="fas fa-times me-1"></i> Tidak</span>' ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime(esc($product['created_at']))) ?></td>
                            <td>
                                <a href="<?= site_url('/admin/products/edit/' . esc($product['id'])) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Produk">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="<?= site_url('/admin/products/delete/' . esc($product['id'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Produk">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
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
<!-- Tambahkan script JS di sini jika diperlukan -->
<?= $this->endSection() ?>
