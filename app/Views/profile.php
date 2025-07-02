<?= $this->extend('templates/layout') ?> 

<?= $this->section('content') ?> 
<section class="py-5">
    <div class="container">
        <h2 class="text-center text-primary mb-4"><i class="fas fa-user me-2"></i>Profil Pengguna</h2>

        <?php // Flash messages (success/error/info/warning) akan ditampilkan oleh layout template ?>
        <?php // Validation errors (daftar) juga akan ditampilkan oleh layout template jika ada redirect dari form edit ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                 <div class="card p-4"> 
                     <h5>Informasi Akun</h5>
                     <hr>
                     
                     <p><strong>Nama Lengkap:</strong> <?= esc($user['full_name'] ?? 'N/A') ?></p>
                     <p><strong>Nama Pengguna:</strong> <?= esc($user['username'] ?? 'N/A') ?></p>
                     <p><strong>Email:</strong> <?= esc($user['email'] ?? 'N/A') ?></p>
                      <p><strong>Role:</strong> <?= esc(ucwords($user['role'] ?? 'N/A')) ?></p> 
                      <p><strong>Status Akun:</strong> <?= ($user['is_active'] ?? 0) ? '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Aktif</span>' : '<span class="badge bg-danger"><i class="fas fa-times me-1"></i> Tidak Aktif</span>' ?></p> 

                     <h5 class="mt-4">Detail Kontak dan Alamat</h5> 
                     <hr>
                     
                     <p><strong>Nomor Telepon:</strong> <?= esc($user['phone'] ?? 'Belum Diisi') ?></p>
                     <p><strong>Alamat Lengkap:</strong> <?= nl2br(esc($user['address'] ?? 'Belum Diisi')) ?></p> 

                     
                     <div class="mt-4 text-center"> 
                         <a href="<?= site_url('/profile/edit') ?>" class="btn btn-primary"><i class="fas fa-edit me-2"></i> Edit Profil</a> 
                     </div>
                 </div> 
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 
<?php // Catatan: Untuk membuat tombol "Edit Profil" berfungsi, Anda perlu mengimplementasikan
      // method editProfile() dan updateProfile() di App\Controllers\User dan membuat view edit_profile.php
?>