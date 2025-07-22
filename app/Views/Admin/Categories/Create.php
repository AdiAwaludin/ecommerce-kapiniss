<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout admin -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Judul Halaman -->
<h2 class="text-primary mb-4"><i class="fas fa-tags me-2"></i> Tambah Kategori Baru</h2>

<?php // Flash messages dan validation errors akan ditampilkan oleh layout template ?>
<?php // Error spesifik per field ditampilkan di bawah input ?>

<!-- Form Tambah Kategori -->
<div class="card p-4"> <!-- Wrap form dalam card -->
    <h5>Form Kategori Baru</h5>
    <hr>
    <!-- Form HTML -->
    <!-- Method POST, action mengarah ke rute storeCategory, enctype untuk upload file -->
    <form action="<?= site_url('/admin/categories/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?> <!-- CSRF Protection -->

        <!-- Field Nama Kategori -->
        <div class="mb-3">
            <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
             <!-- Gunakan old() untuk mempertahankan nilai input jika validasi gagal -->
             <!-- Tambahkan class is-invalid jika ada error validasi untuk field ini -->
            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                   id="name" name="name" value="<?= old('name') ?>" required maxlength="100">
             <!-- Tampilkan pesan error validasi spesifik -->
            <?php if (session('errors.name')): ?>
                <div class="invalid-feedback d-block"><?= esc(session('errors.name')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Field Deskripsi Kategori -->
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
             <!-- Gunakan old() untuk mempertahankan nilai input jika validasi gagal -->
             <!-- Tambahkan class is-invalid jika ada error validasi untuk field ini -->
            <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>"
                      id="description" name="description" rows="3"><?= old('description') ?></textarea>
             <?php if (session('errors.description')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.description')) ?></div>
             <?php endif; ?>
        </div>

        <!-- Field Gambar Kategori -->
        <div class="mb-3">
            <label for="image" class="form-label">Gambar Kategori (Opsional)</label>
             <!-- Input file. Tambahkan class is-invalid jika ada error validasi -->
             <!-- Validasi max_size dan ext_in ada di controller -->
            <input type="file" class="form-control <?= session('errors.image') ? 'is-invalid' : '' ?>"
                   id="image" name="image" accept=".png,.jpg,.jpeg,.webp">
            <small class="form-text text-muted">Ukuran maksimal 1MB. Format: PNG, JPG, JPEG, WEBP.</small>
             <?php if (session('errors.image')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.image')) ?></div>
             <?php endif; ?>
             <?php // Note: old('image') tidak berfungsi untuk input file karena alasan keamanan ?>
        </div>

         <!-- Field Status Aktif -->
        <div class="mb-3 form-check">
            <!-- Checkbox untuk status aktif. Default checked -->
            <!-- Gunakan old() untuk mempertahankan status checked -->
            <input type="checkbox" class="form-check-input <?= session('errors.is_active') ? 'is-invalid' : '' ?>"
                   id="is_active" name="is_active" value="1" <?= old('is_active', 1) == 1 ? 'checked' : '' ?>> {/* Default to checked(1) */}
            <label class="form-check-label" for="is_active">Aktif</label>
             <?php if (session('errors.is_active')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.is_active')) ?></div>
             <?php endif; ?>
        </div>


        <!-- Tombol Submit dan Batal -->
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-2"></i> Simpan Kategori
            </button>
             <!-- Tombol Batal/Kembali ke daftar kategori -->
            <a href="<?= site_url('/admin/categories') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Batal
            </a>
        </div>
    </form>

</div> <!-- End card wrap -->

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<?php // Tidak ada script spesifik yang dibutuhkan untuk form sederhana ini ?>