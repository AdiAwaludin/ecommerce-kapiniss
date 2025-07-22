<?= $this->extend('templates/layout') ?> <!-- Menggunakan template layout sebagai kerangka utama -->

<?= $this->section('content') ?> <!-- Memulai section bernama 'content' -->

<!-- Bagian utama halaman toko -->
<section class="py-5">
    <div class="container">
        <!-- Judul Bagian -->
        <div class="section-title">
            <h2>Toko Online Kami</h2>
            <p class="text-muted">Jelajahi berbagai varian keripik pisang dan makanan ringan lainnya</p>
        </div>

        <div class="row mb-4">
            <!-- Sidebar untuk Filter Kategori -->
            <div class="col-md-3 mb-3"> <!-- Kolom untuk sidebar, margin bottom untuk tampilan mobile -->
                <div class="card p-3"> <!-- Wrap filter dalam card -->
                    <h5>Filter Kategori</h5>
                    <div class="list-group list-group-flush"> <!-- List grup Bootstrap tanpa border luar -->
                        <!-- Link "Semua Kategori" -->
                        <!-- URL ini akan mengarahkan kembali ke halaman shop tanpa parameter category -->
                        <!-- Jika ada parameter search yang aktif, parameter tersebut tetap disertakan -->
                        <a href="<?= site_url('/shop' . ( !empty($search) ? '?search=' . urlencode($search) : '') ) ?>"
                           class="list-group-item list-group-item-action <?= empty($selectedCategory) ? 'active' : '' ?>"> <!-- Tandai link ini aktif jika tidak ada kategori terpilih -->
                             Semua Kategori
                        </a>
                        <!-- Daftar Kategori Aktif dari database -->
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <!-- Link untuk setiap kategori -->
                                <!-- URL ini menyertakan ID kategori yang dipilih dan tetap menyertakan parameter search jika ada -->
                                <a href="<?= site_url('/shop?category=' . esc($category['id']) . ( !empty($search) ? '&search=' . urlencode($search) : '') ) ?>"
                                   class="list-group-item list-group-item-action <?= $selectedCategory == $category['id'] ? 'active' : '' ?>"> <!-- Tandai link ini aktif jika kategori ini yang dipilih -->
                                    <?= esc($category['name']) ?> <!-- Tampilkan nama kategori -->
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div> <!-- End list-group -->
                </div> <!-- End card -->
            </div> <!-- End col -->

            <!-- Area untuk Daftar Produk -->
            <div class="col-md-9">
                <!-- Form Pencarian -->
                 <div class="card p-3 mb-4"> <!-- Wrap search form dalam card -->
                     <h5>Cari Produk</h5>
                     <!-- Form GET ke URL shop -->
                     <form action="<?= site_url('/shop') ?>" method="get">
                         <div class="input-group">
                             <!-- Input field untuk kata kunci pencarian -->
                             <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau deskripsi..." value="<?= esc($search ?? '') ?>">
                             <!-- Hidden input untuk menyimpan ID kategori yang sedang dipilih -->
                             <?php if (!empty($selectedCategory)): ?>
                                  <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                             <?php endif; ?>
                             <!-- Tombol Cari -->
                             <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                             <!-- Tombol Reset Filter -->
                             <?php if (!empty($search) || !empty($selectedCategory)): ?>
                                  <a href="<?= site_url('/shop') ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Reset Filter</a>
                             <?php endif; ?>
                         </div> <!-- End input-group -->
                     </form> <!-- End form -->
                 </div> <!-- End card -->

                <!-- Daftar Produk -->
                <div class="row">
                    <!-- Check if there are products found after filtering/searching -->
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <!-- Pesan jika tidak ada produk -->
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>Produk tidak ditemukan sesuai filter.
                                <!-- Tampilkan link reset jika filter/search aktif -->
                                 <?php if (!empty($search) || !empty($selectedCategory)): ?>
                                      <br><a href="<?= site_url('/shop') ?>" class="alert-link">Tampilkan semua produk.</a>
                                 <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4"> <!-- Mengatur lebar kolom untuk grid produk -->
                            <!-- Product Card -->
                            <div class="card product-card fade-in h-100">
                                <!-- Gambar Produk -->
                                <img src="<?= base_url('/assets/images/placeholder.jpg') ?>"
                                     data-src="<?= base_url('/assets/images/products/' . esc($product['image'] ?: 'default.jpg')) ?>"
                                     class="card-img-top" alt="<?= esc($product['name']) ?>">

                                <!-- Product Overlay on Hover -->
                                <div class="product-overlay">
                                     <?php if ($product['stock'] > 0): ?>
                                         <button class="btn btn-add-cart" onclick="addToCart(<?= esc($product['id']) ?>)">
                                             <i class="fas fa-cart-plus me-2"></i>Tambah ke Keranjang
                                         </button>
                                     <?php else: ?>
                                         <span class="badge bg-danger text-white p-2"><i class="fas fa-box-open me-1"></i>Stok Habis</span>
                                     <?php endif; ?>
                                </div> <!-- End product-overlay -->

                                <!-- Product Card Body -->
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= esc($product['name']) ?></h5>
                                    <p class="card-text text-muted flex-grow-1"><?= character_limiter($product['description'] ?? '', 100) ?>
 ?></p>

                                    <!-- Harga dan Stok -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="product-price">Rp <?= number_format(esc($product['price']), 0, ',', '.') ?></span>
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>Stok: <?= esc($product['stock']) ?>
                                        </small>
                                    </div>

                                    <!-- Tombol Aksi -->
                                    <div class="mt-3">
                                        <a href="<?= site_url('/product/' . esc($product['id'])) ?>"
                                           class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        <?php if ($product['stock'] > 0): ?>
                                             <button class="btn btn-success btn-sm float-end"
                                                     onclick="addToCart(<?= esc($product['id']) ?>)">
                                                 <i class="fas fa-cart-plus"></i> 
                                             </button>
                                         <?php endif; ?>
                                    </div>
                                </div> <!-- End card-body -->
                            </div> <!-- End product-card -->
                        </div> <!-- End col -->
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div> <!-- End row (for products) -->

                <!-- Bagian Pagination (jika aktifkan nanti) -->
                <!--
                <div class="mt-4">
                    <?php // echo $pager->links(); ?>
                </div>
                -->

            </div> <!-- End col-md-9 (for products area) -->
        </div> <!-- End row -->
    </div> <!-- End container -->
</section> <!-- End section utama -->

<?= $this->endSection() ?> <!-- Mengakhiri section 'content' -->

<!-- Section scripts (opsional) -->
<?= $this->section('scripts') ?>
<!-- Script untuk addToCart dan lazyload ada di layout -->
<?= $this->endSection() ?> <!-- Mengakhiri section 'scripts' -->
