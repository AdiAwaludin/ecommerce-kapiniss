<?= $this->extend('templates/layout') ?>
<?= $this->section('content') ?>

<h2 class="text-primary mb-4"><i class="fas fa-users me-2"></i>Daftar Pengguna</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Email</th>
            <th>Nama Lengkap</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($users)) : ?>
            <tr><td colspan="6" class="text-center">Tidak ada pengguna.</td></tr>
        <?php else : ?>
            <?php foreach ($users as $index => $user) : ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                    <td>
                        <?= $user['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>
