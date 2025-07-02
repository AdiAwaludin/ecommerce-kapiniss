<?= $this->extend('templates/layout') ?>
<?= $this->section('content') ?>

<h2 class="text-primary mb-4"><i class="fas fa-edit me-2"></i>Edit Kategori</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= site_url('admin/categories/update/' . $category['id']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Kategori</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $category['name']) ?>" required>
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save me-1"></i> Simpan Perubahan
    </button>
    <a href="<?= site_url('/admin/categories') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</form>

<?= $this->endSection() ?>
