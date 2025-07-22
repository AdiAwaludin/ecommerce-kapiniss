<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<section class="py-5">
    <div class="container text-center">
        <h2 class="text-primary mb-4">Tentang Kami</h2>
        <p class="lead">Keripik Pisang Kapinis adalah usaha yang berdedikasi untuk menyajikan keripik pisang berkualitas tinggi dengan rasa otentik dari kekayaan alam Indonesia.</p>
        <p>Kami menggunakan pisang pilihan terbaik dan diproses secara higienis dengan resep tradisional yang diwariskan turun-temurun, menghasilkan keripik yang renyah, gurih, dan kaya rasa. Kami berkomitmen untuk menjaga kualitas dan kepuasan pelanggan dalam setiap gigitan.</p>
         <a href="<?= site_url('/contact') ?>" class="btn btn-outline-primary mt-3"><i class="fas fa-phone me-2"></i> Hubungi Kami</a>
    </div>
</section>
<?= $this->endSection() ?>