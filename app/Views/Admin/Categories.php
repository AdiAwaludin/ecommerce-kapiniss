<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout admin -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Judul Halaman -->
<h2 class="text-primary mb-4"><i class="fas fa-tags me-2"></i> Kelola Kategori</h2>

<?php // Flash messages (success/error) akan ditampilkan oleh layout template ?>

<!-- Tombol Tambah Kategori Baru -->
<div class="mb-3">
    <a href="<?= site_url('/admin/categories/create') ?>" class="btn btn-success">
        <i class="fas fa-plus me-2"></i> Tambah Kategori Baru
    </a>
</div>

<!-- Tabel Daftar Kategori -->
<div class="card p-3"> <!-- Wrap table dalam card -->
    <div class="table-responsive"> <!-- Buat tabel responsif -->
        <table class="table table-hover align-middle"> <!-- Tabel dengan hover dan rata tengah vertikal -->
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aktif</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <!-- Pesan jika tidak ada kategori -->
                    <tr>
                        <td colspan="7" class="text-center">Belum ada kategori.</td>
                    </tr>
                <?php else: ?>
                    <!-- Loop melalui daftar kategori -->
                    <?php foreach ($categories as $index => $cat): ?>
                        <tr>
                            <td><?= esc($index + 1) ?></td>
                            <td>
                                <img src="<?= base_url('/assets/images/categories/' . esc($cat['image'] ?: 'default.jpg')) ?>"
                                     alt="<?= esc($cat['name']) ?>" class="table-img-sm">
                            </td>
                            <td><?= esc($cat['name']) ?></td>
                            <td><?= esc(character_limiter($cat['description'], 50, '...')) ?></td>
                            <td>
                                 <?= $cat['is_active'] ? '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Ya</span>' : '<span class="badge bg-danger"><i class="fas fa-times me-1"></i> Tidak</span>' ?>
                            </td>
                             <td><?= date('d/m/Y', strtotime(esc($cat['created_at']))) ?></td>
                            <td>
                                 <!-- Tombol Aksi (Edit dan Hapus) -->
                                <a href="<?= site_url('/admin/categories/edit/' . esc($cat['id'])) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Kategori"><i class="fas fa-edit"></i></a>
                                 <form action="<?= site_url('/admin/categories/delete/' . esc($cat['id'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.')">
                                      <?= csrf_field() ?>
                                      <input type="hidden" name="_method" value="DELETE">
                                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kategori"><i class="fas fa-trash"></i></button>
                                 </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div> <!-- End table-responsive -->
    <?php // Tambahkan link pagination di sini jika daftar kategori sangat panjang ?>
</div> <!-- End card wrap -->

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?php // Tambahkan script spesifik jika diperlukan (misal: AJAX delete confirmation) ?>