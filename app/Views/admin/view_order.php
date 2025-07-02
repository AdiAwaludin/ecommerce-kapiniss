<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center text-primary mb-4"><i class="fas fa-receipt me-2"></i>Detail Pesanan #<?= esc($order['order_number']) ?></h2>

        <?php // Notifikasi dan Error Validasi ditangani di layout.php ?>
        <?php // View ini hanya akan menampilkan konten di dalamnya ?>

        <div class="row fade-in">
            <!-- Informasi Pesanan Panel -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card p-4">
                    <h5>Informasi Pesanan</h5>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Nomor Pesanan:</strong> <?= esc($order['order_number']) ?></p>
                            <p><strong>Tanggal Pesanan:</strong> <?= date('d M Y H:i', strtotime(esc($order['created_at']))) ?></p>
                            <p><strong>Status:</strong>
                                 <?php
                                    $statusClass = [
                                        'pending' => 'badge-status-pending',
                                        'pending_review' => 'badge-status-pending_review',
                                        'confirmed' => 'badge-status-confirmed',
                                        'processing' => 'badge-status-processing',
                                        'shipped' => 'badge-status-shipped',
                                        'delivered' => 'badge-status-delivered',
                                        'cancelled' => 'badge-status-cancelled'
                                    ];
                                    $statusText = [
                                        'pending' => 'Pending',
                                        'pending_review' => 'Menunggu Konfirmasi Bayar',
                                        'confirmed' => 'Pembayaran Dikonfirmasi',
                                        'processing' => 'Pesanan Diproses',
                                        'shipped' => 'Pesanan Dikirim',
                                        'delivered' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $statusIcon = [
                                         'pending' => 'fas fa-hourglass-half',
                                         'pending_review' => 'fas fa-clock',
                                         'confirmed' => 'fas fa-check-circle',
                                         'processing' => 'fas fa-cogs',
                                         'shipped' => 'fas fa-truck',
                                         'delivered' => 'fas fa-box-open',
                                         'cancelled' => 'fas fa-times-circle'
                                     ];
                                 ?>
                                 <span class="badge badge-status <?= esc($statusClass[$order['status']] ?? 'badge-secondary') ?>">
                                      <i class="<?= esc($statusIcon[$order['status']] ?? 'fas fa-info-circle') ?> me-1"></i><?= esc($statusText[$order['status']] ?? $order['status']) ?>
                                 </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                             <p><strong>Metode Pembayaran:</strong> <?= esc(ucwords(str_replace('_', ' ', $order['payment_method']))) ?></p>
                             <p><strong>Total Pembayaran:</strong> Rp <?= number_format(esc($order['total_amount']), 0, ',', '.') ?></p>
                        </div>
                    </div>

                    <h5>Alamat Pengiriman</h5>
                    <hr>
                    <p><?= nl2br(esc($order['shipping_address'])) ?></p>
                    <p><strong>Nomor Telepon Pengiriman:</strong> <?= esc($order['phone']) ?></p>

                    <?php if (!empty($order['notes'])): ?>
                        <h5>Catatan</h5>
                        <hr>
                        <p><?= nl2br(esc($order['notes'])) ?></p>
                    <?php endif; ?>

                     <!-- Bagian Upload Bukti Pembayaran -->
                     <?php if ($order['status'] === 'pending'): ?>
                         <div class="alert alert-warning mt-4 text-center">
                             <i class="fas fa-info-circle me-2"></i> Pesanan menunggu pembayaran. Silakan lakukan pembayaran dan upload bukti transfer.
                              <br>
                              <!-- Tombol Upload Bukti Pembayaran, memicu modal -->
                              <!-- Menggunakan data attributes untuk ID dan Nomor Pesanan -->
                              <button class="btn btn-sm btn-success mt-2" data-bs-toggle="modal" data-bs-target="#uploadPaymentModal" data-order-id="<?= esc($order['id']) ?>" data-order-number="<?= esc($order['order_number']) ?>" title="Upload Bukti Bayar">
                                 <i class="fas fa-upload me-1"></i> Upload Bukti Pembayaran
                             </button>
                         </div>
                      <?php elseif ($order['status'] === 'pending_review'): ?>
                           <!-- Pesan jika bukti bayar sudah diupload dan menunggu review -->
                          <div class="alert alert-info mt-4 text-center">
                              <i class="fas fa-clock me-2"></i> Bukti pembayaran sudah diupload. Menunggu konfirmasi dari admin.
                              <?php if ($order['payment_proof']): ?>
                                   <!-- Link untuk melihat bukti pembayaran yang diupload -->
                                  <br><a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank" class="alert-link mt-2 d-inline-block"><i class="fas fa-eye me-1"></i> Lihat Bukti Pembayaran</a>
                              <?php endif; ?>
                          </div>
                     <?php elseif ($order['payment_proof']): ?>
                          <!-- Jika status confirmed/lain tapi bukti ada (opsional ditampilkan di sini juga) -->
                         <h5>Bukti Pembayaran</h5>
                         <hr>
                          <a href="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" target="_blank">
                             <?php
                                $fileExtension = pathinfo($order['payment_proof'], PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                             ?>
                             <?php if ($isImage): ?>
                                 <img src="<?= base_url('uploads/payments/' . esc($order['payment_proof'])) ?>" alt="Bukti Pembayaran" class="img-fluid border p-2 rounded" style="max-height: 300px;">
                             <?php else: ?>
                                   <i class="fas fa-file-alt me-1"></i> <?= esc($order['payment_proof']) ?>
                             <?php endif; ?>
                         </a>
                      <?php endif; ?>

                </div>
            </div>

            <!-- Item Pesanan Panel -->
            <div class="col-lg-4">
                 <div class="card p-4 sticky-top" style="top: 80px;">
                     <h5>Item Pesanan</h5>
                     <hr>
                     <ul class="list-unstyled">
                         <?php if(empty($orderItems)): ?>
                              <li><p class="text-muted">Tidak ada item dalam pesanan ini.</p></li>
                         <?php else: ?>
                              <?php foreach ($orderItems as $item): ?>
                                  <li class="mb-3 d-flex align-items-center border-bottom pb-3">
                                      <img src="<?= base_url('/assets/images/products/' . esc($item['image'] ?: 'default.jpg')) ?>"
                                           alt="<?= esc($item['name']) ?>" class="table-img-sm me-3">
                                      <div class="flex-grow-1">
                                          <p class="mb-0"><strong><?= esc($item['name'] ?: 'Produk Tidak Ditemukan') ?></strong></p>
                                          <p class="text-muted mb-0"><?= esc($item['quantity']) ?> x Rp <?= number_format(esc($item['price']), 0, ',', '.') ?></p>
                                      </div>
                                       <span class="ms-auto text-muted">Rp <?= number_format(esc($item['quantity'] * $item['price']), 0, ',', '.') ?></span>
                                  </li>
                              <?php endforeach; ?>
                         <?php endif; ?>
                     </ul>
                     <hr>
                      <?php
                        $item_subtotal = 0;
                        foreach($orderItems as $item) {
                            $item_subtotal += (float)$item['price'] * (int)$item['quantity'];
                        }
                      ?>
                     <div class="d-flex justify-content-between mb-2">
                         <span>Subtotal Item:</span>
                         <span>Rp <?= number_format($item_subtotal, 0, ',', '.') ?></span>
                     </div>
                      <div class="d-flex justify-content-between mb-2 text-muted">
                          <span>Pengiriman:</span>
                          <span>Belum dihitung</span>
                      </div>
                      <hr>
                      <div class="d-flex justify-content-between mb-3">
                          <strong>Total Pesanan:</strong>
                          <strong>Rp <?= number_format(esc($order['total_amount']), 0, ',', '.') ?></strong>
                      </div>
                 </div>
            </div>
        </div>

         <!-- Tombol "Kembali ke Daftar Pesanan" -->
         <!-- Ini yang seharusnya tampil di bawah konten detail pesanan -->
         <div class="text-center mt-4">
             <a href="<?= site_url('/orders') ?>" class="btn btn-outline-primary">
                 <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pesanan
             </a>
         </div>

    </div>
</section>

<!-- Modal Upload Bukti Pembayaran -->
<div class="modal fade" id="uploadPaymentModal" tabindex="-1" aria-labelledby="uploadPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPaymentModalLabel">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Form, action diisi oleh JS -->
            <form id="uploadPaymentForm" action="" method="post" enctype="multipart/form-data">
                 <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Silakan upload bukti pembayaran Anda untuk pesanan <strong id="modal-order-number"></strong>.</p>
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Pilih File Bukti Pembayaran (JPG, JPEG, PNG, PDF)</label>
                        <!-- File input -->
                        <input class="form-control <?= session('errors.payment_proof') ? 'is-invalid' : '' ?>" type="file" id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                         <small class="text-muted">Ukuran file maksimal 2MB.</small>
                         <!-- Error validasi khusus field file -->
                         <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['payment_proof']) && session()->getFlashdata('open_upload_modal')): ?>
                              <div class="invalid-feedback d-block"><?= esc(session()->getFlashdata('errors')['payment_proof']) ?></div>
                         <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->renderSection('scripts') ?>
<script>
    $(document).ready(function() {
         // Handle saat modal upload pembayaran ditampilkan
        $('#uploadPaymentModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Tombol yang memicu modal (Upload Bayar)
            const orderId = button.data('order-id'); // Ambil order ID dari data attribute
            const orderNumber = button.data('order-number'); // Ambil order number dari data attribute

            const modal = $(this);
            // Tampilkan nomor pesanan di dalam modal
            modal.find('#modal-order-number').text(orderNumber);
            // Atur action URL form di dalam modal agar mengarah ke endpoint uploadPayment controller
            modal.find('#uploadPaymentForm').attr('action', '<?= site_url('/order/upload-payment/') ?>' + orderId);

             // Bersihkan tampilan error validasi sebelumnya di dalam modal setiap kali modal dibuka
             modal.find('.invalid-feedback, .alert-danger').remove();
             // Reset nilai input file
             modal.find('#payment_proof').val('');
             // Pastikan input file tidak lagi ditandai is-invalid dari redirect sebelumnya
             modal.find('#payment_proof').removeClass('is-invalid');
        });

         // Otomatis buka modal jika di-redirect kembali dengan error validasi upload file
         // Ini terjadi jika uploadPayment gagal validasi dan controller me-redirect back()
         // ke halaman view ini dengan flashdata 'errors' dan flag 'open_upload_modal'.
         <?php if (session()->getFlashdata('errors') && session()->getFlashdata('open_upload_modal')): ?>
             // Gunakan window.on('load') atau pastikan script ini dieksekusi SETELAH elemen modal ada
             $(window).on('load', function() {
                  // Dapatkan order ID dari URL saat ini
                  // Asumsikan format URL adalah /order/view/{orderId}
                  const pathArray = window.location.pathname.split('/');
                  const orderIdFromUrl = pathArray[pathArray.length - 1];

                  // Cari tombol yang memicu modal untuk order ID ini di halaman
                   // Karena di halaman view detail hanya ada satu tombol Upload untuk pesanan ini, kita bisa target langsung.
                   // Namun, data-order-id perlu ada di tombol tersebut di file order/view.php
                  const triggerButton = $(`button[data-order-id="${orderIdFromUrl}"]`);

                   // Jika tombol ditemukan dan ada di halaman, picu klik untuk membuka modal
                  if (triggerButton.length) {
                       triggerButton.trigger('click');
                  } else {
                       // Jika tombol tidak ditemukan (mungkin di halaman ini memang tidak ada tombol upload krn status bukan 'pending'?)
                       // Atau URL tidak sesuai harapan.
                       console.warn("Modal trigger button not found for auto-opening after validation errors.");
                  }
             });
         <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>