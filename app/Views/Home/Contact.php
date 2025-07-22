<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center text-primary mb-4">Kontak Kami</h2>
        <p class="lead text-center text-muted">Jika Anda memiliki pertanyaan, saran, atau membutuhkan informasi lebih lanjut, jangan ragu untuk menghubungi kami.</p>

        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card p-4">
                     <h5>Informasi Kontak</h5>
                     <hr>
                     <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Desa Jambar Kec.Nusaherang</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+62 812-3456-7890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>Kapinis@gmail.com</li>
                        <li class="mb-2"><i class="fas fa-clock me-2"></i>Senin - Sabtu: 08:00 - 17:00 WIB</li>
                    </ul>

                     <h5 class="mt-4">Kirim Pesan Kepada Kami</h5>
                     <hr>
                    
                     <!-- UBAH ACTION FORM DI SINI -->
                     <form action="<?= site_url('/contact/send') ?>" method="post">
                         <?= csrf_field() ?> 

                         <div class="mb-3">
                             <label for="name" class="form-label">Nama Lengkap</label>
                             <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                              <?php if (session('errors.name')): ?>
                                   <small class="text-danger"><?= esc(session('errors.name')) ?></small>
                              <?php endif; ?>
                         </div>
                          <div class="mb-3">
                              <label for="email" class="form-label">Email</label>
                              <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                               <?php if (session('errors.email')): ?>
                                    <small class="text-danger"><?= esc(session('errors.email')) ?></small>
                               <?php endif; ?>
                          </div>
                           <div class="mb-3">
                               <label for="subject" class="form-label">Subjek (Opsional)</label>
                               <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>">
                                <?php if (session('errors.subject')): ?>
                                     <small class="text-danger"><?= esc(session('errors.subject')) ?></small>
                                <?php endif; ?>
                           </div>
                         <div class="mb-3">
                             <label for="message" class="form-label">Pesan Anda</label>
                             <textarea class="form-control" id="message" name="message" rows="4" required><?= old('message') ?></textarea>
                              <?php if (session('errors.message')): ?>
                                   <small class="text-danger"><?= esc(session('errors.message')) ?></small>
                              <?php endif; ?>
                         </div>
                          <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim Pesan</button>
                     </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>