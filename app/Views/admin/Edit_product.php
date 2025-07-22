<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h2 class="text-primary mb-4"><i class="fas fa-edit me-2"></i>Edit Produk</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (isset($errors)): ?>
        <div class="alert alert-warning">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('admin/products/update/' . $product['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $product['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="number" name="price" id="price" class="form-control" value="<?= old('price', $product['price']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stok</label>
            <input type="number" name="stock" id="stock" class="form-control" value="<?= old('stock', $product['stock']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Kategori</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"
                        <?= old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
            <a href="<?= site_url('admin/products') ?>" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left me-1"></i> Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
                    