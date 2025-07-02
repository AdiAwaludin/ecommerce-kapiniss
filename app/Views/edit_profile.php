<?= $this->extend('templates/layout') ?> 

<?= $this->section('content') ?> 
<section class="py-5">
    <div class="container">
        <h2 class="text-center text-primary mb-4"><i class="fas fa-user-edit me-2"></i>Edit Profil Pengguna</h2>

        <?php // Flash messages (success/error/info/warning) akan ditampilkan oleh layout template ?>
        <?php // Validation errors (daftar) akan ditampilkan oleh layout template jika ada redirect dari form ini ?>
        
    

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                 <div class="card p-4"> 

       
                     <form action="<?= site_url('/profile/update') ?>" method="post">
                         <?= csrf_field() ?>
                        
                         <?php // <input type="hidden" name="_method" value="PUT"> ?>

                         <div class="mb-3">
                             <label for="full_name" class="form-label">Nama Lengkap</label>
                           
                             <input type="text" class="form-control <?= session('errors.full_name') ? 'is-invalid' : '' ?>" id="full_name" name="full_name"
                                    value="<?= old('full_name', $user['full_name'] ?? '') ?>" required>
                              <?php if (session('errors.full_name')): ?>
                                  <div class="invalid-feedback d-block"><?= esc(session('errors.full_name')) ?></div> 
                              <?php endif; ?>
                         </div>

                   
                          <div class="mb-3">
                              <label class="form-label">Email</label>
                      
                              <input type="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" readonly disabled>
                           
                          </div>
                           <div class="mb-3">
                               <label class="form-label">Nama Pengguna</label>
                          
                               <input type="text" class="form-control" value="<?= esc($user['username'] ?? '') ?>" readonly disabled>
                               
                           </div>


                         <div class="mb-3">
                             <label for="phone" class="form-label">Nomor Telepon (Opsional)</label>
                          
                             <input type="text" class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" id="phone" name="phone"
                                    value="<?= old('phone', $user['phone'] ?? '') ?>">
                              <?php if (session('errors.phone')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.phone')) ?></div> 
                              <?php endif; ?>
                         </div>

                         <div class="mb-4">
                             <label for="address" class="form-label">Alamat Lengkap (Opsional)</label>
                         
                             <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" id="address" name="address" rows="3"><?= old('address', $user['address'] ?? '') ?></textarea>
                              <?php if (session('errors.address')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.address')) ?></div> 
                              <?php endif; ?>
                         </div>


                         <div class="d-grid gap-2 mt-4">
                             <button type="submit" class="btn btn-primary btn-lg">
                                 <i class="fas fa-save me-2"></i> Simpan Perubahan
                             </button>
                         </div>
                     </form> 

                   
                     <div class="text-center mt-3">
                         <a href="<?= site_url('/profile') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali ke Profil</a>
                     </div>

                 </div> 
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 