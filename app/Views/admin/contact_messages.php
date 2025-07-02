<?= $this->extend('admin/layout/default') ?> // Asumsikan Anda memiliki layout admin

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($title) ?></h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Subjek</th>
                <th>Dikirim Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($messages) && is_array($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= esc($message['id']) ?></td>
                        <td><?= esc($message['name']) ?></td>
                        <td><?= esc($message['email']) ?></td>
                        <td><?= esc($message['subject']) ?></td>
                        <td><?= esc($message['created_at']) ?></td>
                        <td>
                            <a href="#" class="btn btn-info btn-sm">Lihat</a> // Tambahkan link ke view detail jika ada
                            <a href="#" class="btn btn-danger btn-sm">Hapus</a> // Tambahkan form/link untuk delete
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Tidak ada pesan kontak.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>