<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout admin -->

<?= $this->section('content') ?> <!-- Memulai section 'content' -->

<!-- Judul Halaman -->
<h2 class="text-primary mb-4"><i class="fas fa-tags me-2"></i> Edit Kategori</h2>

<?php // Flash messages dan validation errors akan ditampilkan oleh layout template ?>
<?php // Error spesifik per field ditampilkan di bawah input ?>

<!-- Form Edit Kategori -->
<div class="card p-4"> <!-- Wrap form dalam card -->
    <h5>Form Edit Kategori</h5>
    <hr>
    <!-- Form HTML -->
    <!-- Method POST, action mengarah ke rute updateCategory, enctype untuk upload file -->
    <!-- URL action menyertakan ID kategori yang sedang diedit -->
    <form action="<?= site_url('/admin/categories/update/' . esc($category['id'])) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?> <!-- CSRF Protection -->
        <!-- Input hidden untuk method spoofing karena form HTML hanya mendukung GET/POST, sementara kita pakai rute POST/PUT untuk update -->
        <!-- Controller Admin::updateCategory menerima POST, jadi _method PUT atau POST sama-sama bisa diatur di Routes.php -->
        <!-- Jika Routes.php menggunakan $routes->post('categories/update/(:num)', 'Admin::updateCategory/$1'); maka ini tidak wajib tapi good practice -->
        <!-- Jika Routes.php menggunakan $routes->put('categories/update/(:num)', 'Admin::updateCategory/$1'); maka ini wajib -->
        <input type="hidden" name="_method" value="POST"> 


        <!-- Field Nama Kategori -->
        <div class="mb-3">
            <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
             <!-- Gunakan old() untuk mempertahankan nilai input jika validasi gagal -->
             <!-- Gunakan $category['name'] sebagai nilai default jika tidak ada old input -->
             <!-- Tambahkan class is-invalid jika ada error validasi untuk field ini -->
            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                   id="name" name="name" value="<?= old('name', $category['name']) ?>" required maxlength="100">
             <!-- Tampilkan pesan error validasi spesifik -->
            <?php if (session('errors.name')): ?>
                <div class="invalid-feedback d-block"><?= esc(session('errors.name')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Field Deskripsi Kategori -->
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
             <!-- Gunakan old() untuk mempertahankan nilai input jika validasi gagal -->
             <!-- Gunakan $category['description'] sebagai nilai default jika tidak ada old input -->
             <!-- Tambahkan class is-invalid jika ada error validasi untuk field ini -->
            <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>"
                      id="description" name="description" rows="3"><?= old('description', $category['description']) ?></textarea>
             <?php if (session('errors.description')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.description')) ?></div>
             <?php endif; ?>
        </div>

        <!-- Field Gambar Kategori -->
        <div class="mb-3">
            <label for="image" class="form-label">Gambar Kategori (Opsional)</label>
             <!-- Input file untuk gambar. Jika ada file lama, tampilkan preview atau namanya -->
            <input type="file" class="form-control <?= session('errors.image') ? 'is-invalid' : '' ?>"
                   id="image" name="image" accept=".png,.jpg,.jpeg,.webp">
            <small class="form-text text-muted">Ukuran maksimal 1MB. Format: PNG, JPG, JPEG, WEBP.</small>
             <?php if (session('errors.image')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.image')) ?></div>
             <?php endif; ?>

             <!-- Tampilkan gambar lama jika ada -->
             <?php if ($category['image']): ?>
                 <div class="mt-2">
                      <p>Gambar saat ini:</p>
                      <!-- Pastikan path ke gambar kategori benar -->
                      <img src="<?= base_url('/assets/images/categories/' . esc($category['image'])) ?>"
                           alt="Gambar Kategori Saat Ini" class="img-thumbnail" style="max-width: 150px;">
                 </div>
             <?php endif; ?>
        </div>

         <!-- Field Status Aktif -->
        <div class="mb-3 form-check">
            <!-- Checkbox untuk status aktif. Gunakan old() atau nilai dari $category -->
            <input type="checkbox" class="form-check-input <?= session('errors.is_active') ? 'is-invalid' : '' ?>"
                   id="is_active" name="is_active" value="1" <?= old('is_active', $category['is_active']) == 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
             <?php if (session('errors.is_active')): ?>
                 <div class="invalid-feedback d-block"><?= esc(session('errors.is_active')) ?></div>
             <?php endif; ?>
        </div>


        <!-- Tombol Submit dan Batal -->
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Simpan Perubahan
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