<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Judul Halaman -->
<h2 class="text-primary mb-4"><i class="fas fa-plus me-2"></i>Tambah Produk Baru</h2>

<!-- Tampilkan Error Validasi -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Form Tambah Produk -->
<form action="<?= site_url('admin/products/store') ?>" method="post">


    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" name="price" id="price" class="form-control" value="<?= old('price') ?>" required>
    </div>

    <div class="mb-3">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" name="stock" id="stock" class="form-control" value="<?= old('stock') ?>" required>
    </div>

    <div class="mb-3">
        <label for="weight" class="form-label">Berat (gram)</label>
        <input type="number" name="weight" id="weight" class="form-control" value="<?= old('weight') ?>" required>
    </div>

    <div class="mb-3">
        <label for="category_id" class="form-label">Kategori</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                    <?= esc($cat['name']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" <?= old('is_active') ? 'checked' : '' ?>>
        <label for="is_active" class="form-check-label">Aktifkan Produk</label>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> Simpan
    </button>
    <a href="<?= site_url('/admin/products') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</form>

<?= $this->endSection() ?>
