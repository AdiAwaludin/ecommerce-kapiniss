<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?> 
<section class="py-5" style="background: linear-gradient(to right, #e0f2f7, #b2ebf2);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="register-card">
                    <div class="text-center mb-4">
                        <h2 class="text-primary">
                            <i class="fas fa-user-plus me-2"></i>Daftar Akun Baru
                        </h2>
                        <p class="text-muted">Silakan isi formulir di bawah untuk mendaftar</p>
                    </div>

                    <form action="<?= site_url('/register') ?>" method="post"> 

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                             
                                <input type="text" class="form-control <?= session('errors.full_name') ? 'is-invalid' : '' ?>" id="full_name" name="full_name"
                                       placeholder="Masukkan nama lengkap" value="<?= old('full_name') ?>" required>
                            </div>
                             <?php if (session('errors.full_name')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.full_name')) ?></div>
                             <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Pengguna</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                              
                                <input type="text" class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" id="username" name="username"
                                       placeholder="Buat nama pengguna unik" value="<?= old('username') ?>" required>
                            </div>
                            <?php if (session('errors.username')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.username')) ?></div>
                             <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                
                                <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email"
                                       placeholder="Masukkan email aktif" value="<?= old('email') ?>" required>
                            </div>
                            <?php if (session('errors.email')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.email')) ?></div>
                             <?php endif; ?>
                        </div>

                         <div class="mb-3">
                             <label for="phone" class="form-label">Nomor Telepon (Opsional)</label>
                             <div class="input-group">
                                 <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                
                                 <input type="text" class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" id="phone" name="phone"
                                        placeholder="Contoh: 081234567890" value="<?= old('phone') ?>">
                             </div>
                              <?php if (session('errors.phone')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.phone')) ?></div>
                             <?php endif; ?>
                         </div>

                         <div class="mb-3">
                             <label for="address" class="form-label">Alamat Lengkap (Opsional)</label>
                             <div class="input-group">
                                 <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            
                                 <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" id="address" name="address" rows="3"
                                           placeholder="Masukkan alamat lengkap Anda"><?= old('address') ?></textarea>
                             </div>
                              <?php if (session('errors.address')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.address')) ?></div>
                             <?php endif; ?>
                         </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                               
                                <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password"
                                       placeholder="Minimal 6 karakter" required>
                            </div>
                            <?php if (session('errors.password')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.password')) ?></div>
                             <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                               
                                <input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password"
                                       placeholder="Ulangi password" required>
                            </div>
                             <?php if (session('errors.confirm_password')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.confirm_password')) ?></div>
                             <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Daftar
                            </button>
                        </div>
                    </form>

                    
                    <div class="text-center mt-4">
                        Sudah punya akun? <a href="<?= site_url('/login') ?>" class="text-primary text-decoration-none">Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 