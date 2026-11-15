# Project Pengeluaran — Nochi Farm Pengeluaran dan Dashboard Keuangan

> Dokumen analisis lengkap aplikasi `nochifarmpengeluaran`, ditulis untuk pemilik yang akan menjual aplikasi ini dan untuk calon pembeli atau analis.
> Dibuat dari pembacaan kode (route, controller, service, model, migration, view Blade dan JavaScript di dalamnya) pada 2026-11-11.
> **Aplikasi belum dijalankan di browser dan belum diuji.** Yang diambil langsung dari kode ditulis apa adanya (disertai file dan baris). Yang perlu dibuktikan lewat uji jalan ditandai `[verifikasi]`.

## Daftar isi

1. [Ringkasan](#1-ringkasan)
2. [Inventaris halaman dan layar](#2-inventaris-halaman-dan-layar)
3. [Data master lengkap](#3-data-master-lengkap)
4. [Kamus data (tabel dan kolom)](#4-kamus-data-tabel-dan-kolom)
5. [Rumus dan aturan hitung (dari kode)](#5-rumus-dan-aturan-hitung-dari-kode)
6. [Foto nota, lokasi, dan GPS](#6-foto-nota-lokasi-dan-gps)
7. [Daftar lengkap izin akses](#7-daftar-lengkap-izin-akses)
8. [Laporan cetak investor, CSV, dan share WhatsApp](#8-laporan-cetak-investor-csv-dan-share-whatsapp)
9. [Integrasi dengan aplikasi lain dan tabel bersama](#9-integrasi-dengan-aplikasi-lain-dan-tabel-bersama)
10. [Alur kerja per peran dan skenario demo](#10-alur-kerja-per-peran-dan-skenario-demo)
11. [Matriks fitur](#11-matriks-fitur)
12. [Penilaian kelayakan jual](#12-penilaian-kelayakan-jual)
13. [Temuan masalah dan risiko](#13-temuan-masalah-dan-risiko)
14. [Yang bisa dibanggakan dan yang harus diakui saat menjual](#14-yang-bisa-dibanggakan-dan-yang-harus-diakui-saat-menjual)
15. [Instalasi dan deploy](#15-instalasi-dan-deploy)
16. [Cakupan pengujian](#16-cakupan-pengujian)
17. [Checklist sebelum dijual](#17-checklist-sebelum-dijual)
18. [Rencana perbaikan](#18-rencana-perbaikan)
19. [Bahan analisis lanjutan](#19-bahan-analisis-lanjutan)

---

## 1. Ringkasan

| Item | Isi |
|---|---|
| Nama | Nochi Farm Pengeluaran — pencatatan pengeluaran kandang + dashboard keuangan (pemasukan vs pengeluaran) |
| Fungsi | Mencatat biaya operasional (pakan, obat, listrik, gaji, dll.) lengkap dengan foto nota. Menggabungkannya dengan **pemasukan dari aplikasi penjualan nochifram** menjadi saldo, margin, laporan investor, dan ringkasan aset gudang |
| Framework | Laravel 10, PHP 8.1, Blade, Tailwind (lewat CDN), Chart.js (berkas lokal), Lucide dan SweetAlert2 (lewat CDN) |
| Database | MySQL, DB `nochifram`, **dipakai bersama** dengan tiga aplikasi lain |
| Skala | ± 2,4 ribu baris PHP di `app/`, ± 7,4 ribu baris Blade, 2 service, 12 model, 4 migration milik sendiri |
| Pengguna | `admin` (akses penuh) dan `user` (izin per fitur) |
| Tes otomatis | **Tidak ada** (hanya `ExampleTest` bawaan Laravel) |
| Git | Ada repositori, riwayat commit pendek |
| Peran di paket 4 aplikasi | Modul keuangan: satu-satunya yang membaca data dari **ketiga** aplikasi lain |

Berkas utama:
[DashboardController](../app/Http/Controllers/DashboardController.php) (612 baris),
[ExpenseController](../app/Http/Controllers/ExpenseController.php) (585),
[PermissionController](../app/Http/Controllers/PermissionController.php) (258),
[ProductionController](../app/Http/Controllers/ProductionController.php) (152),
[MasterTemplateController](../app/Http/Controllers/MasterTemplateController.php) (118),
[ExpensePermissionService](../app/Services/ExpensePermissionService.php),
[WarehouseInventoryService](../app/Services/WarehouseInventoryService.php),
[routes/web.php](../routes/web.php).

---

## 2. Inventaris halaman dan layar

Semua layar memakai tata letak [layouts/app.blade.php](../resources/views/layouts/app.blade.php): navbar atas (desktop), drawer menu (mobile), dan tombol tambah (+). Tema merah marun dan oranye. Menu hanya tampil bila izin `menu_*` aktif. Menu Hak Akses hanya untuk admin.

### 2.1 Peta layar

| # | Layar | URL | Izin route | Berkas |
|---|---|---|---|---|
| 1 | Login | `/login` | tamu | [auth/login](../resources/views/auth/login.blade.php) |
| 2 | Dashboard keuangan | `/` | `menu_dashboard` | [dashboard](../resources/views/dashboard.blade.php) (2.301 baris) |
| 3 | Form catat pengeluaran | `/pengeluaran/tambah` | `expense_create` | [expenses/create](../resources/views/expenses/create.blade.php) |
| 4 | Riwayat pengeluaran | `/pengeluaran` | `menu_expenses_index` | [expenses/index](../resources/views/expenses/index.blade.php) |
| 5 | Master template | `/master/templates` | `menu_master_templates` | [master/templates](../resources/views/master/templates/index.blade.php) |
| 6 | Data produksi telur | `/produksi` | `menu_production` | [production/index](../resources/views/production/index.blade.php) |
| 7 | Hak akses dan pengguna | `/master/hak-akses` | admin (dicek di fungsi) | [master/permissions](../resources/views/master/permissions/index.blade.php) |
| 8 | Laporan cetak investor | `/laporan/cetak` | `dashboard_export_report` | [reports/print](../resources/views/reports/print.blade.php) |
| 9 | Halaman 403 | otomatis | — | [errors/403](../resources/views/errors/403.blade.php) |

Endpoint JSON (dipakai modal): `GET /transaksi/{type}/{id}` (detail pemasukan/pengeluaran), `GET /pengeluaran/{id}` (detail dan data edit), `PUT /pengeluaran/{id}`, `DELETE /pengeluaran/{id}`, `GET /produksi/{id}`, `POST /master/hak-akses/{userId}/toggle|bulk`.

### 2.2 Login
- Satu kolom "Username atau Email" dan satu kolom password, opsi "ingat saya".
- Penolakan: akun nonaktif, atau akun non-admin yang belum diberi izin `app_login_access` (pesan: "Akun Anda belum diizinkan oleh Administrator").
- Setelah masuk diarahkan ke halaman tujuan semula atau dashboard. Tidak ada lupa password dan tidak ada registrasi.

### 2.3 Dashboard keuangan (`/`)
Urutan blok dari atas ke bawah:

| Blok | Isi | Sumber |
|---|---|---|
| Bar filter | Label "Menampilkan transaksi periode: …" dan tombol buka kalender (izin `dashboard_filter_date`) | query `start_date`, `end_date` |
| Kartu **Saldo Kas Saat Ini** | Pemasukan − Pengeluaran | bagian 5.1 |
| Kartu **Total Pemasukan** | "Omzet Penjualan (Nochi Fram)" | `sales.total_amount` |
| Kartu **Total Pengeluaran** | "Belanja Operasional Kandang" | `expenses.amount` |
| Kartu **Total Transaksi** | jumlah penjualan + jumlah pengeluaran | hitung baris |
| **Stok Telur Gudang** | Peti + Kg (atau "Defisit"), "Harga Master Harian", rumus nilai | `WarehouseInventoryService` |
| **Pakan Layer (Petelur)** | stok karung + kg, harga jual, nilai | idem |
| **Pakan Grower / Starter** | idem | idem |
| **Total Sisa Ayam** | populasi aktif, rincian per kloter | idem |
| **Net Profit Margin** (klik untuk rumus) | persen | bagian 5.1 |
| **OPEX Ratio** (klik untuk rumus) | persen | bagian 5.1 |
| Saldo kas tunai dan saldo bank/transfer | dua angka terpisah dengan modal rumus | bagian 5.2 |
| Tombol aksi | **Share WA**, **Excel (CSV)**, **Cetak Laporan** | bagian 8 |
| Grafik **Tren Arus Kas** | batang pemasukan vs pengeluaran per bulan + tabel "Rincian Arus Kas Per Bulan" (Surplus/Defisit, Sisa Kas) | bagian 5.4 |
| Grafik **Komposisi Pengeluaran (%)** | donat per kategori, "Pengeluaran Terbesar", "Total Biaya" | bagian 5.3 |
| **Semua Data Transaksi** | tabel gabungan 10 baris per halaman, filter tipe (semua/pemasukan/pengeluaran) dan pencarian | bagian 5.5 |

Modal di halaman dashboard:

| Modal | Fungsi |
|---|---|
| **Filter tanggal gaya Traveloka** | Pintasan: Semua, Hari Ini, Kemarin, 7 Hari Terakhir, Bulan Ini, Bulan Lalu, 30 Hari Terakhir. Dua kolom tanggal ("Pergi–Pulang"), label hari, lencana durasi rentang, tombol Terapkan dan Hapus filter. Mengatur `start_date` dan `end_date` di URL. Filter hanya aktif bila **kedua** tanggal terisi |
| **Detail transaksi** | Pemasukan: invoice, tanggal, pelanggan dan telepon, metode dan status bayar, kategori, penginput (nama, username, role), kode trip, rute, kendaraan, petugas trip, catatan, daftar item (nama, jumlah, satuan, harga satuan, subtotal). Pengeluaran: kode, tanggal, kategori, subkategori, keperluan, nominal, sumber dana, penginput, catatan, foto nota. Menu aksi edit/hapus muncul bila izin `expense_edit`/`expense_delete` |
| **Edit pengeluaran** | Edit langsung dari dashboard (tanggal, kategori, subkategori, keperluan, nominal, sumber dana, catatan, ganti foto) lewat AJAX |
| **Konfirmasi hapus** | SweetAlert, lalu `DELETE` AJAX |
| **Cara hitung metrik finansial** | Penjelasan rumus Margin, OPEX, Kas Tunai, Saldo Bank dengan angka saat itu |

Catatan perilaku: sorotan angka merah/hijau (surplus/defisit) mengikuti tanda saldo. Grafik memakai Chart.js dari berkas lokal `public/js/chart.umd.min.js`, dengan cadangan CDN bila gagal dimuat.

### 2.4 Form catat pengeluaran (`/pengeluaran/tambah`)

| Bagian | Field | Aturan |
|---|---|---|
| Template cepat | Tombol "chip" dari master template aktif | Klik mengisi kategori, subkategori, keperluan, nominal, catatan |
| Tanggal | `date` | wajib, tanggal valid, bawaan hari ini |
| Kategori | `category` | wajib, teks maks 100 (dipilih dari 12 kategori) |
| Subkategori | `subcategory` | wajib, teks maks 100 (daftar bergantung kategori) |
| Keperluan | `purpose` | wajib, maks 255 |
| Nominal (Rp) | `amount` | wajib, numerik, minimal 1. Input bebas seperti `Rp 350.000` dibersihkan jadi angka |
| Sumber Dana | `payment_method` | tiga pilihan di form (bagian 3.2), bawaan "Omzet Kandang" |
| Keterangan | `notes` | opsional, maks 500 |
| Bukti foto/nota | `receipt_photo` | opsional, gambar jpeg/png/jpg/webp maks 5 MB, kompresi otomatis (bagian 6) |
| Konfirmasi | Modal ringkasan (nominal, metode bayar, keterangan, bukti nota) sebelum simpan | — |
| Sukses | Modal "Pengeluaran Berhasil Disimpan" dengan kode `EXP-…`, tombol "Tambah Pengeluaran Lagi" dan "Lihat Daftar Transaksi" | — |

Catatan: kategori/subkategori hanya dibatasi di antarmuka. Server menerima teks apa pun sampai 100 karakter (lihat temuan #9).

### 2.5 Riwayat pengeluaran (`/pengeluaran`)
- Kartu "Total Biaya Pengeluaran" mengikuti filter.
- Filter: kategori, dari/sampai tanggal, pencarian (kode transaksi, keperluan, kategori, subkategori, catatan). Paginasi 10 baris.
- Kolom: Tanggal & Kode, Kategori & Subkategori, Keperluan & Keterangan, Nominal, menu aksi (Lihat Detail, Edit, Hapus) sesuai izin.
- Modal Detail dan modal Edit memuat data lewat AJAX.

### 2.6 Master template (`/master/templates`)
- Daftar: nama template, kategori dan subkategori, keperluan bawaan, nominal bawaan, status aktif.
- Aksi: tambah, ubah, aktif/nonaktif (klik lencana status), hapus.
- Template aktif tampil sebagai tombol di form pengeluaran, diurutkan `order_num`.
- Kolom `icon` ada di tabel tetapi tidak diisi dari formulir (selalu kosong) `[verifikasi]`.

### 2.7 Data produksi telur (`/produksi`)
Tampilan baca dari tabel aplikasi input, bukan tempat input.
- Kartu: Total Telur Terkumpul (baik, pecah), Konversi Peti & Kg (total bobot, "1 Peti = 10 Kg"), Produksi Hari Ini, Kualitas Telur Utuh ("Grade Prima / Standar / Perlu Cek" dengan persentase butir utuh).
- Filter: tanggal, kandang, pencarian (nama/kode kandang, nama/username petugas, catatan).
- Tabel: Tanggal & Waktu, Kandang & Kloter, Jumlah Butir, Petugas Penginput. Detail per baris lewat modal (butir total/baik/pecah/abnormal, peti, kg, catatan, waktu input).

### 2.8 Hak akses dan pengguna (`/master/hak-akses`, admin saja)
- Pilih pengguna, lihat 25 saklar izin dalam 6 kelompok (bagian 7).
- Tombol massal: aktifkan semua, nonaktifkan semua, reset ke bawaan, aktifkan/nonaktifkan per kelompok.
- Tambah pengguna, ubah (termasuk reset password), aktif/nonaktif. Admin tidak bisa menonaktifkan dirinya sendiri.

### 2.9 Laporan cetak investor (`/laporan/cetak`)
Halaman siap cetak/PDF (browser Print). Isi di bagian 8.

---

## 3. Data master lengkap

### 3.1 Kategori dan subkategori pengeluaran (12 kategori, 87 subkategori)
Sumber: `ExpenseController::getCategoriesData()` ([ExpenseController.php:15](../app/Http/Controllers/ExpenseController.php#L15)). Data **ditulis langsung di kode**, tidak ada layar untuk mengubahnya.

| # | Kategori (`id`) | Ikon | Warna lencana | Subkategori |
|---|---|---|---|---|
| 1 | **Pakan** (`pakan`) — "Pakan Grower, Pakan Layer, Ongkos Angkut" | `package-open` | amber | Pakan Grower · Pakan Layer · Ongkos Angkut Pakan · Bongkar/Muat Pakan · Pakan Starter · Konsentrat · Jagung Giling · Dedak/Bekatul · Lainnya (9) |
| 2 | **Obat & Vitamin** (`obat_vitamin`) — "Obat, vitamin, vaksin, suplemen" | `pill` | rose | Vitamin & Suplemen · Vaksinasi Ayam · Obat-obatan Khusus · Antibiotik & Antistres · Disinfektan Kandang · Sanitasi Air Minum · Lainnya (7) |
| 3 | **Operasional Kandang** (`operasional_kandang`) — "Listrik, air, gas, kebersihan, disinfeksi" | `settings` | blue | Listrik PLN · Air Bersih / PDAM / Sumur · Gas Elpiji (Pemanas/Mess) · Kebersihan & Disinfeksi · Sekam / Litter Padi · Pest Control / Semprot Lalat · Perlengkapan Harian Kandang · Lainnya (8) |
| 4 | **Peralatan Kandang** (`peralatan_kandang`) — "Peralatan baru, penggantian, perbaikan" | `wrench` | purple | Tempat Pakan (Feeder) · Tempat Minum (Nipple / Bell) · Pemanas (Gasolek / Brooder) · Timbangan Digital / Manual · Lampu & Kelistrikan Kandang · Sekop, Cangkul & Ember · Alat Semprot (Sprayer) · Penggantian Alat Rusak · Lainnya (9) |
| 5 | **Perawatan Kandang** (`perawatan_kandang`) — "Perbaikan kandang, instalasi, renovasi" | `home` | stone | Perbaikan Atap & Seng · Perbaikan Tirai / Terpal · Bambu, Kawat & Kayu · Instalasi Pipa Air & Kabel · Bahan Semen & Material · Upah Tukang / Renovasi · Pengecatan / Kapur Dinding · Lainnya (8) |
| 6 | **Ayam** (`ayam`) — "Pembelian ayam, transport, penanganan" | `egg` | orange | Pembelian DOC / Pullet · Ongkos Angkut / Truk DOC · Penanganan & Seleksi Bibit · Biaya Afkir Ayam · Karantina & Uji Lab · Lainnya (6) |
| 7 | **Telur** (`telur`) — "Peti telur, tray, kemasan, perlengkapan" | `circle-dot` | amber (tua) | Peti Telur Kayu · Tray Telur (Egg Tray Karton/Plastik) · Tali Rafia & Lakban Pengemas · Plastik / Kemasan Khusus · Stiker / Label Nochi Farm · Timbangan Telur · Lainnya (7) |
| 8 | **Transportasi** (`transportasi`) — "BBM, parkir, tol, servis, perawatan" | `truck` | emerald | BBM (Solar / Dexlite / Pertalite) · Uang Tol & Parkir · Servis Rutin Kendaraan · Ganti Oli & Sparepart · Tambal Ban & Cuci Kendaraan · Sewa Truk / Pickup Tambahan · Lainnya (7) |
| 9 | **Tenaga Kerja** (`tenaga_kerja`) — "Gaji, uang makan, lembur, insentif" | `users` | indigo | Gaji Karyawan Kandang · Gaji Supir & Helper · Uang Makan Harian · Upah Lembur · Bonus & Insentif Produksi · Kesehatan & Pengobatan Karyawan · Tunjangan Hari Raya (THR) · Lainnya (8) |
| 10 | **Administrasi** (`administrasi`) — "ATK, print, internet, pulsa, dokumen" | `file-text` | sky | Alat Tulis Kantor (ATK) · Kertas, Print & Fotocopy Nota · Pulsa & Kuota Paket Data HP · Langganan WiFi / Internet Kantor · Materai & Legalitas Usaha · Jasa Pembukuan / Akuntansi · Lainnya (7) |
| 11 | **Logistik & Konsumsi** (`logistik_konsumsi`) — "Bahan makanan, air minum, keperluan mess" | `utensils` | teal | Beras & Bahan Makanan Mess · Air Minum Galon Karyawan · Kopi, Teh & Gula Dapur · Gas Elpiji Dapur Mess · Sabun Cuci & Perlengkapan Mess · Lainnya (6) |
| 12 | **Lain-lain** (`lain_lain`) — "Pengeluaran lain yang tidak termasuk" | `more-horizontal` | gray | Iuran Warga & Lingkungan · Keamanan & Ronda Desa · Sumbangan / Donasi Sosial · Pengeluaran Tak Terduga · Lainnya (5) |

Catatan:
- Nilai yang tersimpan di `expenses.category` adalah teks nama kategori yang dipilih di form (dikelompokkan sebagai teks di grafik). Yang tersimpan nama atau `id` perlu dipastikan lewat uji `[verifikasi]`.
- Subkategori "Stiker / Label Nochi Farm" memuat nama merek. Untuk pembeli lain harus diganti di kode.
- Kategori bercabang "Pakan" dan "Obat" tidak terhubung ke stok gudang. Pengeluaran pakan hanya biaya, bukan penambah stok.

### 3.2 Sumber dana / metode bayar pengeluaran
Kolom `expenses.payment_method`. Validasi server menerima lima nilai ([ExpenseController.php:289](../app/Http/Controllers/ExpenseController.php#L289)). Form hanya menawarkan tiga.

| Nilai | Tampil di form | Arti | Masuk ke saldo |
|---|:--:|---|---|
| **Omzet Kandang** (bawaan) | ya, "Penghasilan / Kas Kandang" | Dibayar dari uang hasil penjualan kandang | Mengurangi **Kas Tunai** |
| **Tunai Pribadi** | ya, "Modal / Talangan Tunai" | Pemilik/staf menalangi dengan uang tunai pribadi | Mengurangi **Kas Tunai** (lihat temuan #6) |
| **Transfer Pribadi** | ya, "Modal / Rekening Pribadi" | Dibayar lewat rekening pribadi | Mengurangi **Saldo Bank/Transfer** |
| **Kas Tunai** | tidak (nilai lama/legacy) | Kas tunai kandang | Mengurangi **Kas Tunai** |
| **Transfer Bank** | tidak (nilai lama/legacy) | Transfer dari rekening bank farm | Mengurangi **Saldo Bank/Transfer** |

Nilai kosong diperlakukan sebagai "Omzet Kandang" saat dihitung, dan "Kas Tunai" saat ditampilkan di tabel.

### 3.3 Metode bayar pemasukan (dari aplikasi nochifram)
Nilai di `sales.payment_method`: `Tunai`, `Transfer`, `Belum Bayar`. Status `sales.payment_status`: `Lunas`, `Belum Lunas`. Cara dashboard membaca keduanya dibahas di bagian 5.2 dan temuan #1–#2.

### 3.4 Template pengeluaran
Tabel `expense_templates`, dikelola di layar 5. **Tidak ada seeder**: instalasi baru kosong. Contoh isi nyata yang berguna untuk demo: "Listrik PLN", "Gaji Karyawan", "Solar Mobil", "Ongkos Bongkar Pakan" (ini contoh yang disarankan, bukan data bawaan).

### 3.5 Pengguna dan role
Tabel `users` (milik nochifram). Role hanya dua: `admin` dan `user`.
- `admin`: selalu lolos semua izin, tidak perlu baris di `expense_user_permissions`.
- `user`: harus punya izin `app_login_access` agar bisa masuk. Semua izin bawaan **nonaktif** (default tolak).
- Akun nonaktif (`is_active = 0`) ditolak di login dan oleh middleware pada setiap permintaan.
- Tidak ada seeder pengguna di aplikasi ini ([DatabaseSeeder](../database/seeders/DatabaseSeeder.php) kosong). Akun dibuat lewat aplikasi nochifram atau layar hak akses oleh admin.

### 3.6 Settings
Tabel `settings` (key–value, milik nochifram). Aplikasi ini hanya membaca satu kunci: `kg_per_karung` (bawaan 50). Kunci itu **tidak dibuat oleh aplikasi mana pun** di paket ini (aplikasi input memakai kunci `berat_per_karung`), sehingga praktis nilai 50 kg per karung selalu dipakai `[verifikasi]`. Tidak ada layar pengaturan di aplikasi ini (nama farm, alamat, rekening tidak bisa diubah dari antarmuka).

### 3.7 Nilai hardcode yang perlu diketahui

| Nilai | Lokasi | Dampak |
|---|---|---|
| 1 Peti = 10 Kg | gudang dan produksi | tetap |
| 1 butir rusak = 0,06 kg | [WarehouseInventoryService.php](../app/Services/WarehouseInventoryService.php) | estimasi stok telur |
| Harga cadangan telur Rp 225.000/peti (dan `harga_peti/10` per kg) | idem | dipakai bila tidak ada harga harian dan riwayat |
| Harga cadangan pakan Layer Rp 385.000, Grower Rp 395.000 per karung | idem | dipakai bila tidak ada produk pakan/riwayat |
| Harga cadangan saat error (Rp 280.000 / Rp 28.000) | idem (blok `catch`) | tampil bila perhitungan stok telur gagal |
| Kop laporan: "NOCHI FARM — Peternak Ayam Petelur & Distribusi Telur Segar — Jl. Raya Peternakan Nochi Farm • Kebumen, Jawa Tengah" | [reports/print](../resources/views/reports/print.blade.php) | wajib diganti untuk pembeli lain |
| Tanda tangan "Petugas Administrasi / Manager Operasional Farm / Owner / Investor Nochi Farm" | idem | idem |
| Judul pesan WhatsApp "LAPORAN KAS NOCHI FARM" | DashboardController | idem |

---

## 4. Kamus data (tabel dan kolom)

### 4.1 Tabel milik aplikasi ini (migration di folder [database/migrations](../database/migrations))

**`expenses`** — satu baris per pengeluaran

| Kolom | Tipe | Fungsi |
|---|---|---|
| `id` | bigint, PK | — |
| `transaction_code` | string(50) | Kode `EXP-YYYYMMDD-XXXX`. **Tidak unik di level database** |
| `date` | date | Tanggal pengeluaran (bukan tanggal input) |
| `category` | string(100) | Kategori |
| `subcategory` | string(100) | Subkategori |
| `purpose` | string(255) | Keperluan |
| `amount` | decimal(15,2) | Nominal dalam Rupiah |
| `payment_method` | string(50) | Sumber dana (migration kedua) |
| `notes` | text, nullable | Catatan |
| `receipt_photo` | string(255), nullable | Path relatif, mis. `uploads/expenses/nota_20261111_101500_ab12cd.jpg` |
| `user_id` | unsigned bigint | Pencatat. Tanpa foreign key. Bila tak ada sesi dipakai `1` |
| `created_at`, `updated_at` | timestamp | — |

**`expense_templates`**

| Kolom | Tipe | Fungsi |
|---|---|---|
| `id` | PK | — |
| `name` | string(100) | Label tombol |
| `icon` | string(50), nullable | Tidak terisi dari formulir |
| `category`, `subcategory` | string(100) | Isian bawaan |
| `purpose` | string(255) | Keperluan bawaan |
| `amount` | decimal(15,2) | Nominal bawaan (boleh 0) |
| `notes` | text, nullable | Catatan bawaan |
| `is_active` | boolean | Tampil/tidak di form |
| `order_num` | integer | Urutan tampil |
| timestamps | — | — |

**`expense_user_permissions`**

| Kolom | Tipe | Fungsi |
|---|---|---|
| `id` | PK | — |
| `user_id` | FK ke `users`, hapus berantai | Pemilik izin |
| `permission_key` | string(100) | Kunci izin (bagian 7) |
| `is_enabled` | boolean | Nyala/mati |
| timestamps | — | — |
| unik | (`user_id`, `permission_key`) | Satu baris per izin per user |

Tabel Laravel bawaan: `users` (versi dasar), `password_reset_tokens`, `failed_jobs`, `personal_access_tokens`.

### 4.2 Tabel yang dibaca dari aplikasi lain

| Tabel | Dipakai untuk | Kolom yang dibaca | Asal migration |
|---|---|---|---|
| `users` | login, penginput, izin | `id, name, username, email, password, role, phone, avatar, is_active` | nochifram (kolom tambahan: `role`, `username`, `phone`, `avatar`, `is_active`) |
| `sales` | pemasukan | `id, user_id, trip_id, stand_session_id, category, invoice_no, customer_name, customer_phone, date, total_amount, payment_method, payment_status, notes` | nochifram |
| `sale_items` | detail dan stok terjual | `sale_id, item_name, unit, quantity, unit_price, total_price` | nochifram |
| `trips` | asal penjualan | `id, user_id, trip_code, vehicle, route, date, status` | nochifram |
| `daily_prices` | harga telur harian | `date, price_peti, price_kg` | nochifram |
| `feed_products` | harga pakan | `name, price_per_karung, weight_per_karung, is_active` | nochifram |
| `settings` | `kg_per_karung` | `key, value` | nochifram |
| `egg_productions` | halaman produksi dan stok telur | `flock_id, coop_id, user_id, date, time, total_eggs, broken_eggs, abnormal_eggs, good_eggs, crates_count, weight_kg, notes` | nochifarminput |
| `coops` | populasi, filter kandang | `flock_id, name, code, capacity, active_chickens, chicken_age_weeks, is_active` | nochifarminput |
| `flocks` | populasi per kloter | `name, code, breed, initial_population, current_population, is_active` | nochifarminput |
| `farm_stocks` | mutasi stok telur/pakan | `category, type, item_name, unit, quantity, notes` | nochifarminput |
| `feed_consumptions` | konsumsi pakan | `feed_name, quantity_kg` | nochifarminput |

---

## 5. Rumus dan aturan hitung (dari kode)

### 5.1 Ringkasan keuangan utama
Sumber: [DashboardController.php:41-60](../app/Http/Controllers/DashboardController.php#L41).

```php
// DashboardController.php:41-43
$totalPemasukan  = (clone $salesQuery)->sum('total_amount');
$totalPengeluaran = (clone $expensesQuery)->sum('amount');
$saldoSaatIni    = $totalPemasukan - $totalPengeluaran;
```

| Metrik | Rumus |
|---|---|
| Total Pemasukan | jumlah `sales.total_amount` pada periode (semua penjualan, **apa pun status bayarnya**) |
| Total Pengeluaran | jumlah `expenses.amount` pada periode (apa pun sumber dananya) |
| Saldo Kas Saat Ini | Pemasukan − Pengeluaran |
| Jumlah transaksi | jumlah baris `sales` + jumlah baris `expenses` |
| Net Profit Margin (%) | `round(saldo / pemasukan × 100, 1)`. Bila pemasukan 0: −100 jika saldo negatif, selain itu 0 |
| OPEX Ratio (%) | `round(pengeluaran / pemasukan × 100, 1)`. Bila pemasukan 0: 100 jika ada pengeluaran, selain itu 0 |

Filter periode berlaku bila **dua** tanggal terisi (`whereBetween('date', …)` pada `sales.date` dan `expenses.date`). Tanpa filter, semua riwayat dihitung.

### 5.2 Kas tunai vs bank, dan bug piutang
Kode ([DashboardController.php:71-83](../app/Http/Controllers/DashboardController.php#L71)):

```php
$tunaiPemasukan    = $sales->filter(fn($s) => strtolower($s->payment_method) === 'tunai')->sum('total_amount');
$transferPemasukan = $sales->filter(fn($s) => strtolower($s->payment_method) !== 'tunai')->sum('total_amount');
$tunaiPengeluaran  = $expenses->filter(...in_array($m, ['kas tunai','tunai pribadi','omzet kandang']))->sum('amount');
$transferPengeluaran = $expenses->filter(...in_array($m, ['transfer bank','transfer pribadi']))->sum('amount');
$saldoKasTunai     = $tunaiPemasukan - $tunaiPengeluaran;
$saldoBankTransfer = $transferPemasukan - $transferPengeluaran;
```

| Sumber | Masuk ke "Kas Tunai" | Masuk ke "Saldo Bank/Transfer" |
|---|---|---|
| Penjualan `Tunai` | ya | — |
| Penjualan `Transfer` | — | ya |
| Penjualan `Belum Bayar` | — | **ya (salah, seharusnya piutang)** |
| Pengeluaran `Omzet Kandang`, `Kas Tunai`, `Tunai Pribadi`, kosong | mengurangi | — |
| Pengeluaran `Transfer Bank`, `Transfer Pribadi` | — | mengurangi |

**Bug yang dipastikan dari kode (butuh uji jalan hanya untuk melihat angkanya):**
1. **Piutang dihitung sebagai uang masuk.** `payment_status` (Lunas/Belum Lunas) tidak pernah dipakai di baris 41, 71, 72, 489. Penjualan "Belum Lunas" ikut menaikkan Pemasukan, Saldo, Margin, dan menurunkan OPEX Ratio.
2. **"Belum Bayar" menjadi "Saldo Bank".** Kondisi `!== 'tunai'` menangkap semua selain tunai, termasuk `Belum Bayar`. Saldo rekening tampak lebih besar dari kenyataan.
3. **Uang pribadi memotong saldo kas.** Pengeluaran bersumber `Tunai Pribadi`/`Transfer Pribadi` tetap mengurangi Saldo Kas (bagian 5.1) dan Kas Tunai/Bank, padahal uang farm tidak keluar. Label "Saldo Kas" sebenarnya lebih mirip **laba operasional**, bukan kas riil. Akibatnya angka kas tidak cocok dengan isi laci atau rekening.
4. Perbandingan metode memakai `strtolower` tetapi nilai berbeda ejaan (mis. "Tunai " dengan spasi) akan salah kelompok `[verifikasi]`.

Cara membuktikan cepat: di nochifram buat satu penjualan Rp 1.000.000 dengan metode "Belum Bayar" lalu muat dashboard. Pemasukan naik Rp 1.000.000 dan Saldo Bank ikut naik.

### 5.3 Ringkasan per kategori (donat)
[DashboardController.php:199-215](../app/Http/Controllers/DashboardController.php#L199).
- Kelompokkan `expenses` periode itu berdasarkan `category` (kosong menjadi "Lainnya").
- Per kelompok: `total = sum(amount)`, `count`, `percentage = round(total / totalPengeluaran × 100, 1)`.
- Urut menurun menurut total. Pesan WhatsApp mengambil 3 teratas.

### 5.4 Grafik tren bulanan
[DashboardController.php:217-310](../app/Http/Controllers/DashboardController.php#L217).
- Agregasi **per bulan** (`DATE_FORMAT(date, "%Y-%m")`) untuk penjualan (`SUM(total_amount)`) dan pengeluaran (`SUM(amount)`).
- Daftar bulan = gabungan semua bulan yang punya data + **6 bulan terakhir sampai bulan sekarang**, diurutkan naik.
- Per bulan: `net = pemasukan − pengeluaran`, flag `has_activity`. Tabel "Rincian Arus Kas Per Bulan" dibalik (terbaru di atas).
- **Grafik ini mengabaikan filter tanggal**: selalu seluruh riwayat. Kartu di atasnya mengikuti filter, grafik tidak (lihat temuan #10).
- Ada cadangan bila `DATE_FORMAT` gagal (memuat semua baris lalu mengelompokkan di PHP). Fungsi `DATE_FORMAT` khusus MySQL.
- Jenis grafik: batang (`bar`) dua seri.

### 5.5 Tabel transaksi gabungan
- `sales` dan `expenses` diubah menjadi satu daftar seragam (`type` = `pemasukan`/`pengeluaran`).
- Kode pemasukan: `invoice_no`, atau `INV-` + id 5 digit bila kosong.
- Judul pemasukan: "Penjualan - {pelanggan}" atau "Penjualan Tunai". Status pengeluaran selalu "Dibayar".
- Pencarian (PHP, huruf kecil) meliputi kode, judul, kategori, subkategori, deskripsi, penginput (nama dan username), petugas trip, kode trip.
- Urut menurun menurut `created_at` + id. Paginasi manual **10 baris per halaman**.
- **Seluruh data periode dimuat ke memori** lebih dulu (`->get()`), baru diproses di PHP.

### 5.6 Kode transaksi pengeluaran
[ExpenseController.php:315-322](../app/Http/Controllers/ExpenseController.php#L315).
- Format `EXP-{YYYYMMDD tanggal pengeluaran}-{urut 4 digit}`.
- Urutan = angka akhir kode terakhir pada tanggal yang sama + 1, mulai 0001.
- Dibuat dengan "baca kode terakhir lalu tambah 1" tanpa kunci, tanpa transaksi, dan tanpa indeks unik. Dua simpan bersamaan bisa kembar (temuan #7). Memakai `latest('id')`: bila ada pengeluaran bertanggal sama yang diubah/dihapus, nomor bisa meloncat atau berulang `[verifikasi]`.

### 5.7 Konversi peti–kg pada halaman produksi
[ProductionController.php:57-80](../app/Http/Controllers/ProductionController.php#L57).
- `intPeti = floor(sum(crates_count))`, `sisaKg = round((sum(crates_count) − intPeti) × 10, 1)`; bila `sisaKg ≥ 10` ditambahkan ke peti.
- Catatan: aplikasi input menyimpan `crates_count` sebagai peti bulat dan `weight_kg` sebagai sisa < 10 kg. Rumus di halaman ini menurunkan "sisa kg" dari pecahan `crates_count`, bukan dari `weight_kg`. Untuk data baru yang bulat, sisa kg yang tampil 0 sehingga **bobot kg sisa dari `weight_kg` tidak ikut tampil** `[verifikasi]`. Kartu "Total Bobot" memang menjumlah `weight_kg`.

### 5.8 Ringkasan gudang ([WarehouseInventoryService](../app/Services/WarehouseInventoryService.php))
Dipanggil sekali per muat dashboard.

**Telur (peti dan kg)**
1. Terjual: jumlah `sale_items.quantity` untuk `sales.category = 'telur'` per satuan `Peti` dan `Kg` (**tanpa filter tanggal**).
2. Normalisasi: tiap 10 kg dijadikan 1 peti.
3. Masuk: `sum(egg_productions.crates_count) + farm_stocks(telur, masuk, Peti)` untuk peti. `sum(egg_productions.weight_kg) + farm_stocks(telur, masuk, Kg)` untuk kg. Dinormalisasi (10 kg = 1 peti).
4. Rusak/keluar manual: `farm_stocks(telur, keluar)` + `sum(broken_eggs) × 0,06 kg`.
5. Keluar = terjual + rusak/keluar manual, dinormalisasi.
6. **Stok bersih (kg)** = `(masukPeti×10 + masukKg) − (keluarPeti×10 + keluarKg)`. Bila negatif ditandai "Defisit".
7. **Nilai stok** = `stokPeti × hargaPeti + stokKg × hargaKg`. Harga: baris `daily_prices` terbaru → harga jual terakhir di `sale_items` → Rp 225.000/peti (kg = peti/10).

**Pakan (Layer dan Grower/Starter/Pullet)**
1. `kgPerKarung` = `settings.kg_per_karung` atau 50.
2. Terjual (`sales.category = 'pakan'`): karung × kgPerKarung + kg. Jenis Grower bila nama item memuat "grower", "starter", atau "pullet"; sisanya Layer.
3. Konsumsi kandang: `sum(feed_consumptions.quantity_kg)`, pisah Grower/Layer dengan aturan nama yang sama.
4. Masuk: `farm_stocks` (pakan, masuk) dikonversi ke kg (satuan karung/sak/krg × kgPerKarung, ton × 1000, selain itu apa adanya).
5. Keluar manual: `farm_stocks` (pakan, keluar) selain yang bercatatan `[AUTO-KONSUMSI]` (agar tidak dihitung ganda dengan konsumsi).
6. **Stok (kg)** = masuk − (konsumsi + terjual + keluar manual), per jenis dan total. Karung = kg / kgPerKarung.
7. **Nilai stok** per jenis = `karungBulat × hargaKarung + sisaKg × hargaKg`, dengan hargaKg = hargaKarung / kgPerKarung. Harga: `feed_products` aktif (nama memuat grower/starter/pullet atau layer) → harga jual terakhir → Rp 385.000 (Layer) / Rp 395.000 (Grower).

**Populasi ayam**: `sum(coops.active_chickens)` dan `sum(coops.capacity)` untuk kandang aktif. Keterisian = `round(populasi / kapasitas × 100, 1)`. Per kloter: jumlah ayam di kandang aktif kloter itu, atau `flocks.current_population` bila kosong.

**Total estimasi aset gudang** = nilai stok telur + nilai stok pakan.

Catatan penting dari kode:
- Perhitungan stok **memakai seluruh riwayat** dan tidak mengikuti filter tanggal dashboard.
- Pada langkah harga telur cadangan, query memakai kolom **`sale_items.price`** padahal kolom di nochifram bernama **`unit_price`** (aplikasi input pun memakai `unit_price`). Bila tabel `daily_prices` kosong (tidak ada harga harian) query gagal dan blok `catch` mengembalikan stok telur **0** dengan harga cadangan Rp 280.000. Artinya kartu "Stok Telur Gudang" bisa tampil 0 meski ada stok `[verifikasi]` (temuan #5).
- Pengenalan Grower/Layer berdasarkan potongan nama (heuristik). Nama produk di luar kata kunci masuk Layer.

---

## 6. Foto nota, lokasi, dan GPS

### 6.1 Foto nota
| Aspek | Perilaku |
|---|---|
| Field | `receipt_photo` (opsional) |
| Tipe diterima | `image` dengan ekstensi `jpeg, png, jpg, webp`. Maks **5 MB** |
| Pengambilan | Pilih dari galeri atau kamera (`accept="image/*"`) |
| **Kompresi sisi klien** | JavaScript Canvas ([expenses/create.blade.php:549-700](../resources/views/expenses/create.blade.php#L549)): resolusi dibatasi sisi terpanjang **1400 px**, kualitas JPEG diturunkan bertahap sampai ukuran ± **100 KB**. Ada animasi progres dan pratinjau ukuran |
| **Kompresi sisi server (cadangan)** | `ensureMaxFileSize()` memakai ekstensi **GD** ([ExpenseController.php:521](../app/Http/Controllers/ExpenseController.php#L521)): bila berkas > 100 KB, resize maks 1400 px dan simpan JPEG kualitas 80 → 20 (turun 10) sampai ≤ 100 KB. **Bila GD tidak aktif, dilewati diam-diam** |
| Folder | `public/uploads/expenses/` (**folder publik**, bukan storage privat) |
| Nama berkas | `nota_{YmdHis}_{6 acak}.{ekstensi asli}` |
| URL | `asset('uploads/expenses/…')`, bisa dibuka siapa pun yang tahu alamatnya (tanpa login) |
| Ganti foto | Berkas lama dihapus (`unlink`), berkas baru disimpan |
| Hapus pengeluaran | Berkas ikut dihapus |
| Catatan teknis | Kompresi server selalu menulis format JPEG ke berkas dengan ekstensi asli (mis. `.png` berisi data JPEG). Umumnya tetap terbuka di browser `[verifikasi]` |

### 6.2 Lokasi dan GPS
**Tidak ada.** Pencarian `latitude`, `longitude`, `geolocation`, dan `gps` di seluruh `app/`, `resources/`, `routes/`, `database/` tidak menemukan apa pun. Aplikasi ini tidak merekam lokasi pada pengeluaran dan tidak memakai GPS.

Fitur lokasi/GPS ada di aplikasi **nochifram** (foto bukti penjualan, GPS awal/akhir trip). Di dashboard ini, jejak GPS tidak ditampilkan; yang ditampilkan dari trip hanya kode trip, rute, kendaraan, dan petugas.

---

## 7. Daftar lengkap izin akses

Sumber: [ExpensePermissionService::getAllPermissions()](../app/Services/ExpensePermissionService.php). **25 izin** dalam 6 kelompok (koreksi dari versi dokumen sebelumnya yang menulis 26). Semua bernilai bawaan `false` (tolak). Admin selalu lolos.

Kolom "Dijaga di kode" menunjukkan apakah izin benar-benar diperiksa (route `permission:`, `@canExpense` di view, atau service).

| # | Kelompok | Kunci | Label | Default | Dijaga di kode |
|---|---|---|---|:--:|---|
| 1 | 0. Akses sistem | `app_login_access` | Izin Login ke Sistem Pengeluaran | tolak | ya (login dan middleware) |
| 2 | 1. Navigator | `menu_dashboard` | Menu Beranda & Dashboard | tolak | ya (route dan menu) |
| 3 | 1. Navigator | `menu_expenses_create` | Menu Catat Pengeluaran Baru | tolak | **hanya tampilan menu** (route memakai `expense_create`) |
| 4 | 1. Navigator | `menu_expenses_index` | Menu Riwayat Pengeluaran | tolak | ya |
| 5 | 1. Navigator | `menu_production` | Menu Produksi Telur | tolak | ya |
| 6 | 1. Navigator | `menu_master_templates` | Menu Master Template Pengeluaran | tolak | ya |
| 7 | 2. Dashboard | `dashboard_view` | Akses Halaman Dashboard | tolak | **tidak** |
| 8 | 2. Dashboard | `dashboard_filter_date` | Filter Tanggal & Kalender (Traveloka) | tolak | ya (tombol kalender saja) |
| 9 | 2. Dashboard | `dashboard_view_balance` | Kartu Saldo, Pemasukan & Pengeluaran | tolak | **tidak** |
| 10 | 2. Dashboard | `dashboard_view_charts` | Grafik Tren Keuangan & Kategori | tolak | **tidak** |
| 11 | 2. Dashboard | `dashboard_view_transaction_detail` | Modal Detail Transaksi | tolak | ya (route) |
| 12 | 2. Dashboard | `dashboard_export_report` | Cetak Laporan Investor & Export CSV | tolak | ya (route) |
| 13 | 3. Pengeluaran | `expense_view` | Lihat Tabel Riwayat Pengeluaran | tolak | **tidak** |
| 14 | 3. Pengeluaran | `expense_view_detail` | Lihat Detail Pengeluaran & Nota | tolak | ya (route) |
| 15 | 3. Pengeluaran | `expense_create` | Catat / Tambah Pengeluaran Baru | tolak | ya (route) |
| 16 | 3. Pengeluaran | `expense_edit` | Ubah / Edit Catatan Pengeluaran | tolak | ya (route dan tombol) |
| 17 | 3. Pengeluaran | `expense_delete` | Hapus Catatan Pengeluaran | tolak | ya (route dan tombol) |
| 18 | 3. Pengeluaran | `expense_filter` | Filter Kategori & Pencarian Pengeluaran | tolak | **tidak** |
| 19 | 4. Template | `template_view` | Lihat Daftar Master Template | tolak | **tidak** |
| 20 | 4. Template | `template_create` | Tambah Template Baru | tolak | ya |
| 21 | 4. Template | `template_edit` | Ubah / Edit Template | tolak | ya |
| 22 | 4. Template | `template_toggle` | Ubah Status Aktif / Nonaktif Template | tolak | ya |
| 23 | 4. Template | `template_delete` | Hapus Template Pengeluaran | tolak | ya |
| 24 | 5. Produksi | `production_view` | Lihat Data Produksi Telur | tolak | **tidak** |
| 25 | 5. Produksi | `production_view_detail` | Lihat Detail Produksi Telur | tolak | ya (route) |

**Temuan penting:** 7 dari 25 saklar (`dashboard_view`, `dashboard_view_balance`, `dashboard_view_charts`, `expense_view`, `expense_filter`, `template_view`, `production_view`) tidak diperiksa di mana pun. Mematikan "Kartu Saldo, Pemasukan & Pengeluaran" tidak menyembunyikan saldo bagi staf yang punya `menu_dashboard`. Jika pembeli ingin staf tidak boleh melihat omzet, saat ini **tidak bisa** (temuan #4). Ini berbeda dari nochifram yang punya saklar harga global.

Mekanisme: `ExpensePermissionService::canAccess()` urutan keputusan: tidak login → tolak; akun nonaktif → tolak; admin → izinkan; baris di `expense_user_permissions` → ikuti; selain itu → nilai bawaan katalog (semua tolak). Middleware alias `permission` dan `expense.permission` ([Kernel.php:67-68](../app/Http/Kernel.php#L67)), directive Blade `@canExpense`.

---

## 8. Laporan cetak investor, CSV, dan share WhatsApp

### 8.1 Laporan cetak (`/laporan/cetak`, izin `dashboard_export_report`)
[DashboardController::printReport](../app/Http/Controllers/DashboardController.php#L463) dan [reports/print.blade.php](../resources/views/reports/print.blade.php) (488 baris). Dicetak lewat tombol "Cetak / PDF" browser.

| Bagian | Isi |
|---|---|
| Panel filter (tidak ikut tercetak) | Rentang tanggal (boleh salah satu), preset cepat, "Terapkan Filter Laporan", "Reset ke Semua Riwayat", "Kembali ke Dashboard" |
| Kop | NOCHI FARM, "Peternak Ayam Petelur & Distribusi Telur Segar", alamat (hardcode) |
| Metadata | Periode laporan, klasifikasi "Laporan Arus Kas & Laba Rugi Operasional" |
| Ringkasan | Total Pemasukan (Omzet), Total Pengeluaran (Biaya), Laba Bersih (Saldo Kas) + margin |
| Rekap biaya per kategori | Kategori, Frekuensi, Total, Porsi (%), baris TOTAL BIAYA PENGELUARAN |
| Buku kas | Tanggal, Kode/Ref, Keterangan/Keperluan, Pemasukan, Pengeluaran (seluruh mutasi urut tanggal naik) |
| Tanda tangan | Petugas Administrasi, Manager Operasional Farm, Owner/Investor (garis titik-titik) |

Perbedaan dengan dashboard: laporan ini menerima filter **satu tanggal** (hanya mulai atau hanya akhir), dashboard dan CSV memerlukan **dua** tanggal. Laporan **tidak menampilkan status pembayaran**, dan ikut mengandung bug piutang bagian 5.2 (Laba Bersih memuat penjualan belum lunas). Laporan ini jenis "laba rugi sederhana (kas campur akrual)", bukan laporan akuntansi baku.

### 8.2 Ekspor CSV (`/laporan/export-csv`, izin yang sama)
- Nama berkas `Laporan_Kas_NochiFarm_{YmdHis}.csv`, UTF-8 dengan BOM (agar Excel membaca benar).
- Memerlukan **dua** tanggal untuk filter; tanpa itu semua riwayat.
- Kolom (12):

| # | Kolom | Isi |
|---|---|---|
| 1 | No | urut |
| 2 | Tanggal | `Y-m-d` |
| 3 | Kode Transaksi | invoice atau kode `EXP-…` |
| 4 | Tipe | `PEMASUKAN` / `PENGELUARAN` |
| 5 | Kategori | "Penjualan Telur/Pakan" atau "Kategori (Subkategori)" |
| 6 | Keterangan / Keperluan | "Penjualan - pelanggan" atau keperluan |
| 7 | Petugas Penginput | username atau nama |
| 8 | Petugas Perjalanan (Trip) | username petugas trip atau kode trip |
| 9 | Metode Bayar | metode penjualan atau sumber dana |
| 10 | Pemasukan (Rp) | angka |
| 11 | Pengeluaran (Rp) | angka |
| 12 | Saldo Kumulatif (Rp) | berjalan = Σ(pemasukan − pengeluaran) |

CSV **tidak memuat status pembayaran**, jadi piutang tidak bisa dipisahkan dari data ini. Pengurutan memakai `date` saja, sehingga urutan antar transaksi pada hari yang sama bisa berbeda dari tabel dashboard.

### 8.3 Share WhatsApp
Tautan `https://wa.me/?text=…` berisi teks: "LAPORAN KAS NOCHI FARM", periode, Saldo Kas Saat Ini, Total Omzet Penjualan, Total Biaya Operasional, Profit Margin, tiga pos pengeluaran terbesar (nilai dan persen), dan catatan "Laporan real-time resmi dari Sistem Nochi Farm". Dibuka di tab baru. Tidak ada integrasi API WhatsApp, hanya tautan berbagi.

### 8.4 Filter yang tersedia (ringkas)
| Layar | Filter |
|---|---|
| Dashboard | rentang tanggal (dua tanggal), jenis (semua/pemasukan/pengeluaran), pencarian bebas, paginasi 10 |
| Riwayat pengeluaran | rentang tanggal (satu atau dua), kategori, pencarian, paginasi 10 |
| Produksi | rentang tanggal (satu atau dua), kandang, pencarian, paginasi 10 |
| Laporan cetak | rentang tanggal (satu atau dua) |
| CSV | rentang tanggal (dua) |

---

## 9. Integrasi dengan aplikasi lain dan tabel bersama

Semua aplikasi memakai **satu database `nochifram`**. Tidak ada API antar aplikasi: integrasi = membaca tabel langsung.

| Dari aplikasi | Tabel | Dipakai di aplikasi ini untuk | Risiko |
|---|---|---|---|
| **nochifram** (penjualan) | `sales`, `sale_items`, `trips` | Pemasukan, detail transaksi, trip, harga cadangan | Perubahan nama kolom di sana merusak dashboard diam-diam (lihat `price` vs `unit_price`) |
| nochifram | `users` | Login, penginput, role, status aktif | Perubahan role/aktif di aplikasi lain langsung mempengaruhi akses di sini |
| nochifram | `daily_prices`, `feed_products`, `settings` | Harga telur harian, harga pakan, `kg_per_karung` | `kg_per_karung` tidak pernah diisi |
| **nochifarminput** (kandang) | `egg_productions`, `coops`, `flocks` | Halaman produksi, populasi ayam, stok telur | Tidak ada di DB bila aplikasi input belum dipasang |
| nochifarminput | `farm_stocks`, `feed_consumptions` | Stok pakan dan telur | idem |
| **companynochifarm** | — | Tidak ada hubungan data langsung | — |

Aplikasi lain yang membaca tabel milik aplikasi ini: tidak ditemukan. `expenses` hanya dibaca oleh aplikasi ini.

Perilaku bila tabel sumber tidak ada: `WarehouseInventoryService` memeriksa `Schema::hasTable` dan membungkus dengan `try/catch` sehingga dashboard tetap terbuka dengan angka 0 atau harga cadangan. **Pemasukan** (`Sale::…`) dan halaman produksi (`EggProduction::…`) **tidak diamankan** dengan cara itu: tanpa tabel `sales` dashboard error, tanpa `egg_productions` halaman produksi error `[verifikasi]`.

---

## 10. Alur kerja per peran dan skenario demo

### 10.1 Pemilik / Admin
1. Buka `/login`, masuk dengan akun admin.
2. Dashboard: lihat Saldo, Pemasukan, Pengeluaran, Margin, OPEX, stok gudang, grafik.
3. Pilih periode: tombol kalender → pintasan "Bulan Ini" → Terapkan.
4. Telusuri transaksi: filter "Pengeluaran", cari kata kunci, klik baris untuk detail (nota dan penginput).
5. Koreksi: dari modal detail pilih Edit (ubah nominal/kategori/foto) atau Hapus.
6. Laporan: tombol Cetak Laporan → atur periode → Cetak/PDF. Atau Excel (CSV) atau Share WA.
7. Kelola staf: menu Hak Akses → Tambah Pengguna → atur saklar atau tombol massal → simpan (otomatis lewat toggle).
8. Atur template: Master → Tambah Template Baru untuk biaya rutin.

### 10.2 Staf pencatat (role `user`)
Prasyarat: admin sudah memberi `app_login_access`, `menu_expenses_create`, `expense_create` (dan idealnya `menu_expenses_index`).
1. Masuk dengan akun staf. Bila izin login belum ada, muncul pesan ditolak.
2. Tekan tombol (+) "Catat Pengeluaran".
3. Ketuk template cepat (mis. "Solar") atau isi manual: tanggal, kategori, subkategori, keperluan, nominal ("350000" otomatis menjadi "Rp 350.000").
4. Pilih sumber dana (Omzet Kandang bila dari uang farm, Tunai/Transfer Pribadi bila menalangi).
5. Foto nota. Aplikasi mengompres ke ± 100 KB.
6. Konfirmasi di modal ringkasan, lalu Simpan. Kode `EXP-YYYYMMDD-XXXX` muncul.
7. Jika salah ketik, minta admin mengedit (staf hanya bisa edit bila diberi `expense_edit`).

### 10.3 Skenario demo (10 menit, urut)
1. **Persiapan data**: di nochifram buat 3–4 penjualan (campur Tunai, Transfer, satu "Belum Bayar" bila ingin menunjukkan transparansi) dan di aplikasi input isi produksi telur. Di aplikasi ini buat 3 template.
2. Login admin → dashboard: tunjukkan kartu, grafik, stok gudang.
3. Filter "Bulan Ini" gaya Traveloka → angka berubah.
4. Catat pengeluaran dari HP: template "Solar" + foto nota → tunjukkan kompresi dan kode transaksi.
5. Kembali ke dashboard: saldo berkurang, donat bertambah, transaksi muncul di tabel, klik untuk melihat nota.
6. Cetak Laporan Investor (PDF) dan unduh CSV.
7. Hak akses: buat staf dengan izin terbatas, login di jendela lain, tunjukkan menu yang terbatas.
8. **Jangan** demokan penjualan "Belum Bayar" sebelum bug piutang diperbaiki, atau jelaskan bahwa total pemasukan adalah omzet (akrual), bukan kas masuk.

---

## 11. Matriks fitur

| Fitur | Status | Catatan |
|---|:--:|---|
| Catat pengeluaran (kategori, subkategori, nominal, tanggal) | **Ya** | 12 kategori, 87 subkategori |
| Foto nota + kompresi otomatis | **Ya** | klien dan server |
| Template pengeluaran rutin | **Ya** | tanpa isi bawaan |
| Sumber dana (kandang vs pribadi) | **Ya** | dampak ke saldo perlu diperbaiki |
| Dashboard pemasukan vs pengeluaran | **Ya** | |
| Filter kalender gaya Traveloka | **Ya** | |
| Grafik tren bulanan dan komposisi kategori | **Ya** | tren tidak ikut filter |
| Pemisahan kas tunai dan bank | **Sebagian** | salah untuk "Belum Bayar" dan dana pribadi |
| Margin laba dan rasio biaya | **Sebagian** | memuat piutang |
| Laporan cetak investor | **Ya** | kop hardcode |
| Ekspor CSV | **Ya** | tidak ada status bayar |
| Ekspor Excel (.xlsx) / PDF server | **Tidak** | CSV dan cetak browser saja |
| Share WhatsApp | **Ya** | tautan, bukan API |
| Ringkasan stok gudang dan nilai aset | **Sebagian** | bug kolom `price`, harga cadangan hardcode |
| Data produksi telur sinkron | **Ya** | baca saja |
| Hak akses granular + pengguna | **Sebagian** | 7 dari 25 izin tidak berfungsi |
| Izin login per aplikasi | **Ya** | default tolak |
| GPS / lokasi | **Tidak** | tidak ada |
| Audit log dan restore | **Tidak** | |
| Soft delete / arsip | **Tidak** | hapus permanen |
| Anggaran (budget) dan peringatan | **Tidak** | |
| Persetujuan (approval) | **Tidak** | |
| Hutang/piutang pemasok | **Tidak** | |
| Pengeluaran berulang otomatis | **Tidak** | template hanya mempercepat input |
| Multi-farm / multi-perusahaan | **Tidak** | |
| Lupa password / registrasi | **Tidak** | |
| Pengaturan farm (nama, alamat, rekening) dari UI | **Tidak** | semua hardcode |
| Tes otomatis | **Tidak** | |
| Mode offline / PWA | **Tidak** | butuh internet (juga untuk CDN) |
| Bisa dipasang mandiri tanpa aplikasi lain | **Tidak** | |

---

## 12. Penilaian kelayakan jual

Skala 1–5 (5 = siap jual).

| Aspek | Nilai | Catatan |
|---|:--:|---|
| Kelengkapan fitur | 4 | Pengeluaran + nota + template + laporan investor + dashboard. Belum ada budget dan approval |
| Nilai bisnis | 5 | Menjawab pertanyaan pemilik/investor: untung atau rugi, uang farm atau pribadi |
| Kemudahan pakai | 4 | Template satu ketuk, kategori khusus peternakan, nominal fleksibel, kompresi foto otomatis |
| Akurasi keuangan | **2** | Piutang terhitung pemasukan, "Belum Bayar" masuk bank, dana pribadi memotong saldo, stok telur bisa 0 |
| Keamanan | 3 | Izin default tolak dan cek admin di fungsi, tetapi login tanpa throttle, `APP_DEBUG=true`, nota di folder publik, 7 izin tidak berfungsi |
| Kualitas kode | 3 | Ringkas dan terbaca. Dashboard gemuk, tanpa service untuk laporan |
| Pengujian | **1** | Tidak ada. Berisiko karena aplikasi uang |
| Kemandirian | **1** | Bergantung penuh pada DB aplikasi lain |
| Dokumentasi | 1 | README bawaan Laravel (sebelum dokumen ini) |
| **Rata-rata** | **±2,7** | Konsep kuat. Akurasi angka dan kemandirian perlu dibenahi |

### Kesimpulan
- **Paling cocok sebagai modul keuangan dalam paket 4 aplikasi**, bukan produk berdiri sendiri. Nilainya muncul karena membaca penjualan dan produksi.
- **Estimasi harga mandiri** (kasar, pasar Indonesia): Rp 10–25 juta sekali putus. Rp 25–45 juta setelah angka keuangan diperbaiki, ada tes, dan dokumentasi. Dalam bundel, ini yang membuat paket terasa "lengkap sampai laporan investor".
- **Pembeda untuk pembeli:** pemisahan dana farm vs pribadi, laporan investor siap cetak, kompresi foto nota, kategori khusus peternakan, stok gudang dan nilai aset di satu layar.

---

## 13. Temuan masalah dan risiko

Semua dari pembacaan kode. `[verifikasi]` berarti perlu dibuktikan lewat uji jalan.

### Tinggi
1. **Pemasukan menghitung piutang sebagai uang masuk.** [DashboardController.php:41](../app/Http/Controllers/DashboardController.php#L41) dan [:489](../app/Http/Controllers/DashboardController.php#L489) menjumlah `total_amount` tanpa melihat `payment_status`. Saldo, Margin, OPEX, laporan investor, dan pesan WhatsApp ikut terdampak. Perbaikan: pisahkan "Pendapatan (akrual)", "Kas masuk (Lunas)", dan "Piutang".
2. **"Belum Bayar" masuk Saldo Bank.** [DashboardController.php:72](../app/Http/Controllers/DashboardController.php#L72) memakai `!== 'tunai'`. Perbaikan: hitung hanya `Tunai` dan `Transfer`, dan keluarkan "Belum Bayar".
3. **Tanpa pengujian untuk perhitungan uang.** Saldo, margin, OPEX, kas tunai/bank, kode transaksi, dan rumus stok tidak punya tes sama sekali.
4. **Tujuh saklar izin tidak berfungsi** (bagian 7). Staf yang hanya punya `menu_dashboard` tetap melihat saldo, pemasukan, dan grafik. Tidak ada cara menyembunyikan omzet dari staf.
5. **Stok telur bisa tampil 0 karena kolom salah.** [WarehouseInventoryService](../app/Services/WarehouseInventoryService.php) memakai `sale_items.price` yang tidak ada (nama sebenarnya `unit_price`). Bila `daily_prices` kosong, query gagal dan seluruh blok telur diganti nilai 0. `[verifikasi]` dengan mengosongkan harga harian.
6. **Dana pribadi memotong saldo kas.** Pengeluaran bersumber `Tunai Pribadi`/`Transfer Pribadi` mengurangi Saldo Kas dan Kas Tunai/Bank farm padahal kas farm tidak keluar. "Saldo Kas" sebenarnya laba operasional, tidak cocok dengan isi laci atau rekening. Perlu dipisah: "Biaya" vs "Kas keluar dari kas farm" vs "Talangan pemilik (utang ke pemilik)".
7. **Kode transaksi bisa kembar.** `EXP-YYYYMMDD-XXXX` dibuat dengan "ambil kode terakhir +1", tanpa kunci atau indeks unik ([ExpenseController.php:315](../app/Http/Controllers/ExpenseController.php#L315)).
8. **`APP_DEBUG=true`, `APP_NAME=Laravel`, `APP_URL` alamat IP lokal** di `.env`. Membocorkan detail internal bila dipasang di server.

### Sedang
9. **Kategori dan subkategori tidak divalidasi di server.** Hanya `string|max:100`. Permintaan langsung (bukan dari form) bisa menyimpan kategori apa pun yang memecah grafik per kategori. Validasi terhadap daftar master.
10. **Grafik tren bulanan mengabaikan filter tanggal**, sedangkan kartu di atasnya mengikuti filter. Pengguna bisa mengira keduanya sinkron.
11. **Hapus permanen tanpa jejak.** Pengeluaran yang dihapus hilang beserta fotonya. Tidak ada audit log atau soft delete. Dalam konteks keuangan ini celah kecurangan. Aplikasi input dan nochifram sudah punya audit dan restore.
12. **Foto nota di folder publik** `public/uploads/expenses/`. Nama berkas acak, tetapi tanpa login siapa pun yang tahu URL bisa melihatnya. Folder juga tidak ada di `.gitignore`, jadi nota nyata bisa ikut ter-commit.
13. **Tanpa throttle login.** Brute force tidak dibatasi.
14. **Pemeriksaan admin tersebar di dalam fungsi** ([PermissionController](../app/Http/Controllers/PermissionController.php)), bukan middleware. Rute `master/hak-akses` hanya dijaga `auth`. Aman selama tiap fungsi mengecek, mudah terlewat di fungsi baru.
15. **Password minimal 6** untuk akun baru, tanpa aturan kerumitan. Admin dapat menetapkan role admin ke akun mana pun.
16. **Beban data di dashboard.** `sales` dan `expenses` periode dimuat seluruhnya (`->get()`) lalu diproses di PHP. Tanpa filter tanggal berarti seluruh riwayat setiap kali buka dashboard. Lambat pada data bertahun-tahun.
17. **Tanpa transaksi database** pada simpan/ubah pengeluaran + unggah foto. Bila penyimpanan gagal setelah foto tersimpan, berkas yatim tertinggal.
18. **Kompresi server bergantung ekstensi GD** dan dilewati diam-diam bila tidak ada.
19. **Ketergantungan CDN.** Tailwind (`cdn.tailwindcss.com`, tidak untuk produksi), Lucide (`unpkg … @latest`, versi tidak dikunci), SweetAlert2, Google Fonts. Tanpa internet tampilan rusak, dan `@latest` bisa mengubah tampilan sewaktu-waktu. Hanya Chart.js yang lokal.
20. **`kg_per_karung` tidak pernah diisi** sehingga selalu 50.
21. **Nilai hardcode merek dan kop** (bagian 3.7) harus diganti untuk pembeli lain.
22. **Query MySQL-spesifik** (`DATE_FORMAT`, `FIELD()`), tidak jalan di SQLite/PostgreSQL.
23. **Bahasa tanggal.** `config/app.php` `locale = 'en'` sedangkan kode memakai `translatedFormat`; nama bulan kemungkinan tampil Inggris (mis. "Nov") `[verifikasi]`.

### Rendah
24. **README bawaan Laravel.**
25. **Log** `storage/logs/laravel.log` ± 445 KB di folder proyek. Jangan ikut terjual.
26. **Tidak ada seeder** pengguna dan template awal. Pemasangan baru kosong.
27. **Kolom `icon` template tidak dipakai.**
28. **Penamaan campur** (`pengeluaran`/`expenses`, `produksi`/`production`).
29. **Pencatat bawaan `user_id = 1`** bila sesi tidak ada (`auth()->id() ?? 1`). Tidak terjadi lewat rute berotentikasi, tetapi meninggalkan kebiasaan berisiko.

---

## 14. Yang bisa dibanggakan dan yang harus diakui saat menjual

### Bisa dibanggakan (benar adanya di kode)
- Pencatatan pengeluaran yang dirancang untuk peternakan: 12 kategori, 87 subkategori nyata (pakan grower/layer, vaksin, DOC, peti telur, solar, THR, dll.).
- Pembedaan **sumber dana** (uang kandang vs uang pribadi pemilik), jarang ada di aplikasi serupa.
- **Foto nota otomatis dikompres** sekitar 100 KB di HP, hemat kuota dan ruang server.
- **Template satu ketuk** untuk biaya rutin.
- Dashboard yang menyatukan **pemasukan penjualan dan pengeluaran** dalam satu tabel, dengan filter kalender gaya Traveloka.
- **Laporan investor siap cetak/PDF**, ekspor CSV dengan saldo kumulatif, dan berbagi ringkasan ke WhatsApp.
- **Ringkasan aset gudang** (telur, pakan, populasi ayam) dengan nilai estimasi di layar yang sama.
- Hak akses per fitur dengan **default tolak**, izin login per aplikasi, dan toggle massal.
- Kode ringkas (≈ 2,4 ribu baris PHP) sehingga mudah dipelajari dan dimodifikasi.

### Harus diakui kepada calon pembeli
- Angka **Saldo Kas** saat ini adalah **omzet dikurangi biaya**, termasuk penjualan yang belum dibayar dan biaya yang dibayar dengan uang pribadi. **Bukan** saldo kas riil. (Sampai bug #1, #2, #6 diperbaiki.)
- Tidak bisa berdiri sendiri: butuh database dan aplikasi penjualan (nochifram) dan idealnya aplikasi input kandang.
- Tidak ada lokasi/GPS di aplikasi ini (ada di nochifram).
- Tidak ada audit log atau pemulihan data. Hapus bersifat permanen.
- 7 saklar izin belum berfungsi, jadi omzet tidak bisa disembunyikan dari staf.
- Belum ada tes otomatis, anggaran, persetujuan, hutang pemasok, dan multi-farm.
- Tampilan bergantung internet (CDN).
- Kop laporan dan nama merek masih Nochi Farm.

---

## 15. Instalasi dan deploy

### 15.1 Prasyarat
PHP ≥ 8.1 dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `gd` (**wajib untuk kompresi cadangan**), `openssl`, `tokenizer`; MySQL/MariaDB; Composer; akses internet untuk CDN tampilan.

### 15.2 Urutan migration lintas aplikasi (DB `nochifram`)
Aplikasi ini **tidak bisa** membangun skema penuh sendiri. Urutan aman:

1. **nochifram** → `php artisan migrate` (membuat `users` dengan role/username/dll., `sales`, `sale_items`, `trips`, `daily_prices`, `feed_products`, `settings`, dst.).
2. **nochifarminput** → `php artisan migrate` (membuat `flocks`, `coops`, `egg_productions`, `farm_stocks`, `feed_consumptions`, dst.; sebagian migration defensif `if !hasTable`).
3. **nochifarmpengeluaran** → `php artisan migrate` (membuat `expenses`, `expense_templates`, `expense_user_permissions`).
4. Buat admin (lewat nochifram atau seeder buatan sendiri). Beri akun staf izin `app_login_access` lewat layar Hak Akses.

Jika urutan dibalik atau hanya satu aplikasi dipasang, dashboard atau halaman produksi error (bagian 9).

### 15.3 `.env`
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_NAME` dan `APP_URL` sesuai domain (saat ini `APP_URL` berisi IP lokal).
- `DB_*` ke database bersama yang sama dengan aplikasi lain.
- `SESSION_DRIVER=file`, `QUEUE_CONNECTION=sync` (tidak ada antrean yang dipakai). Pengaturan email tidak dipakai karena tidak ada fitur email.
- `php artisan key:generate` bila baru, lalu `php artisan config:cache`.

### 15.4 Folder dan hak tulis
- `public/uploads/expenses/` harus ada dan dapat ditulis web server. Saat ini kosong.
- `storage/` dan `bootstrap/cache/` dapat ditulis.
- `php artisan storage:link` tidak diperlukan (nota disimpan di `public/`, bukan storage).
- Pastikan server tidak mengeksekusi file di `public/uploads` (nonaktifkan PHP di folder itu).
- Cadangkan folder `public/uploads/expenses/` bersama database.

### 15.5 Hosting
Berjalan di XAMPP/Apache lokal. Untuk online: pastikan HTTPS, atur izin berkas, dan jangan ekspos `.env`. Karena aplikasi diakses di jaringan LAN (`APP_URL` IP), di jaringan publik wajib ada HTTPS dan batas percobaan login.

---

## 16. Cakupan pengujian

| Bagian | Tes | Catatan |
|---|:--:|---|
| Login dan izin | tidak ada | |
| CRUD pengeluaran | tidak ada | |
| Rumus saldo, margin, OPEX | tidak ada | prioritas tertinggi |
| Kas tunai vs bank | tidak ada | |
| Kode transaksi | tidak ada | |
| Rumus stok gudang | tidak ada | |
| Kompresi foto | tidak ada | |
| Bawaan Laravel | `ExampleTest` (Feature dan Unit) | tidak bermakna |

Tes yang disarankan pertama: (1) satu penjualan Lunas/Tunai, satu "Belum Bayar", satu Transfer, dua pengeluaran berbeda sumber dana → cek Saldo, Kas Tunai, Saldo Bank; (2) dua pengeluaran tanggal sama → kode berurutan dan unik; (3) staf tanpa `expense_delete` ditolak (403); (4) `WarehouseInventoryService` dengan `daily_prices` kosong.

---

## 17. Checklist sebelum dijual

### Wajib (akurasi dan keamanan)
- [ ] Pisahkan **Pendapatan**, **Kas masuk (Lunas)**, dan **Piutang**. Perbaiki baris 41, 71–72, 489 dan CSV/laporan
- [ ] Keluarkan "Belum Bayar" dari Saldo Bank
- [ ] Pisahkan **biaya** dari **kas keluar farm** dan **talangan pemilik**, atau ganti label "Saldo Kas" menjadi "Laba Operasional"
- [ ] Perbaiki kolom `price` → `unit_price` di `WarehouseInventoryService` dan uji dengan `daily_prices` kosong
- [ ] Tambah tes untuk saldo, margin, OPEX, kas/bank, kode transaksi
- [ ] Indeks unik `transaction_code` + pembuatan kode di dalam transaksi
- [ ] Hubungkan 7 saklar izin yang belum berfungsi (terutama `dashboard_view_balance`, `dashboard_view_charts`, `expense_view`)
- [ ] Soft delete atau audit log untuk pengeluaran
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_NAME`, `APP_URL` benar; bersihkan log
- [ ] Throttle login, perketat password (≥ 8)
- [ ] Pindahkan foto nota ke storage privat dengan route berizin, dan tambahkan folder ke `.gitignore`
- [ ] Pindahkan pengecekan admin ke middleware

### Sebaiknya (nilai jual)
- [ ] Validasi kategori/subkategori terhadap daftar master di server
- [ ] Halaman pengaturan: nama farm, alamat, rekening, kop laporan, tanda tangan, `kg_per_karung`
- [ ] Kategori dan sumber dana yang bisa diatur dari antarmuka
- [ ] Anggaran per kategori dan peringatan bila melebihi
- [ ] Persetujuan (approval) pengeluaran di atas nominal tertentu
- [ ] Hutang/piutang pemasok dan jatuh tempo
- [ ] Pengeluaran berulang otomatis dari template
- [ ] Grafik tren mengikuti filter tanggal
- [ ] Ekspor .xlsx dan PDF dari server
- [ ] Mode mandiri: pemasukan manual bila tanpa aplikasi penjualan
- [ ] Agregat dihitung di SQL (bukan di PHP) untuk data besar
- [ ] Ganti CDN dengan aset lokal (build Tailwind, kunci versi Lucide)
- [ ] Peringatan di layar bila ekstensi GD tidak aktif
- [ ] Seeder pengguna demo dan template contoh

### Dokumen serah terima
- [ ] README instalasi dan urutan migration lintas aplikasi (bagian 15)
- [ ] Manual pemilik (membaca dashboard, laporan investor) dan manual staf (mencatat pengeluaran, foto nota)
- [ ] Daftar 25 izin dan artinya (bagian 7)
- [ ] Penjelasan rumus (bagian 5) dalam bahasa awam
- [ ] Diagram hubungan data dengan nochifram dan nochifarminput (bagian 9)
- [ ] Daftar kategori/subkategori (bagian 3.1)
- [ ] Lisensi dan ketentuan penggunaan (siapa pemilik kode, boleh dijual ulang atau tidak)

---

## 18. Rencana perbaikan

1. **Hari 1 — Akurasi angka:** poin 1, 2, 6 (pendapatan vs kas vs piutang, "Belum Bayar", dana pribadi) + perbaiki `price` → `unit_price`. Tambah tes rumus keuangan. Ini yang paling menentukan kepercayaan pembeli.
2. **Hari 2 — Keamanan dan integritas:** `APP_DEBUG` mati, throttle login, middleware admin, kode transaksi unik, soft delete/audit, nota privat, hubungkan 7 saklar izin.
3. **Hari 3–4 — Nilai jual:** pengaturan farm dan kop laporan, kategori yang bisa diatur, anggaran, approval, grafik ikut filter, mode mandiri.
4. **Hari 5 — Dokumentasi dan demo:** README, manual per peran, diagram rumus, data demo, skrip seeder.

---

## 19. Bahan analisis lanjutan

- Matriks fitur 4 aplikasi dan skema DB gabungan (bagian paket bundel).
- Proposal penjualan: fitur (bagian 2), nilai bagi pemilik/investor (bagian 14), harga (bagian 12).
- Uji akurasi: bandingkan angka dashboard dengan data penjualan nyata (Lunas vs Belum Lunas) untuk satu bulan, dan dengan pembukuan Excel pemilik.
- Audit penuh: jalankan semua alur dengan data uji, cetak laporan investor, uji dengan data bertahun-tahun untuk mengukur waktu muat dashboard.

*Seluruh temuan di atas dari analisis statis. Jalankan aplikasi dan konfirmasi setiap temuan sebelum dianggap pasti. Temuan #1, #2, #5, dan #6 dapat dibuktikan cepat: buat satu penjualan "Belum Bayar" di nochifram, kosongkan `daily_prices`, lalu muat dashboard dan bandingkan angkanya dengan isi kas.*
