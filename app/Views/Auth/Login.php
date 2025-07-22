<?= $this->extend('templates/layout') ?> 

<?= $this->section('content') ?> 
<section class="py-5" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(to right, #e0f2f7, #b2ebf2);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="text-center mb-4">
                        <h2 class="text-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </h2>
                        <p class="text-muted">Masuk ke akun Anda</p>
                    </div>

                 

               
                    <form action="<?= site_url('/login') ?>" method="post"> 
                        <?= csrf_field() ?> 

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                               
                                <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email" name="email"
                                       placeholder="Masukkan email Anda" value="<?= old('email') ?>" required>
                            </div>
                             
                             <?php if (session('errors.email')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.email')) ?></div> 
                             <?php endif; ?>
                        </div>

                        <div class="mb-4"> 
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                               
                                <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" id="password" name="password"
                                       placeholder="Masukkan password Anda" required>
                            </div>
                            
                            <?php if (session('errors.password')): ?>
                                 <div class="invalid-feedback d-block"><?= esc(session('errors.password')) ?></div> 
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    
                    <div class="text-center mt-4">
                        Belum punya akun? <a href="<?= site_url('/register') ?>" class="text-primary text-decoration-none">Daftar di sini</a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="text-muted text-decoration-none"><small>Lupa Password?</small></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?> 