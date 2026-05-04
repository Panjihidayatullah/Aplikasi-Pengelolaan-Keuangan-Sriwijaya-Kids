# Laporan Pengujian Black Box - Sistem Pengelolaan Keuangan
## Sriwijaya Kids Financial Management System (Updated)

Dokumen ini berisi detail pengujian Black Box yang telah disesuaikan dengan implementasi sistem keuangan dan aset pada aplikasi Sriwijaya Kids.

### 1. Halaman Pembayaran (Siswa)
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| FIN-01 | Mencatat pembayaran baru | Memilih Siswa, Jenis Pembayaran (SPP, dll), Jumlah (Numeric), Tanggal, Metode Bayar (Tunai, Transfer, QRIS), Keterangan | Data tersimpan dengan `kode_transaksi` format `PAY-YYYYMMDD...`, muncul notifikasi ke Admin/Bendahara | Pass |
| FIN-02 | Validasi field wajib | Mengosongkan field Siswa atau Jumlah | Sistem menampilkan error: "The siswa id field is required" atau "The jumlah field is required" | Pass |
| FIN-03 | Pencarian data pembayaran | Nama Siswa, NIS, atau Kode Transaksi | Daftar pembayaran terfilter secara real-time sesuai kata kunci | Pass |
| FIN-04 | Filter data pembayaran | Range Tanggal, Jenis Pembayaran, Metode, atau Status | Tabel menampilkan data yang hanya memenuhi kriteria filter | Pass |
| FIN-05 | Export Bukti Pembayaran PDF | Klik tombol "Cetak PDF" pada baris data | Sistem mengunduh file PDF dengan format `bukti-pembayaran-PAY-XXX.pdf` | Pass |

### 2. Halaman Pengeluaran
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| FIN-06 | Mencatat pengeluaran umum | Memilih Jenis Pengeluaran, Jumlah, Tanggal, Deskripsi, Status (Pending/Disetujui), Bukti File (JPG/PDF) | Data tersimpan dengan `kode_transaksi` format `OUT-YYYYMMDD...`, file bukti tersimpan di storage | Pass |
| FIN-07 | Update status pengeluaran | Mengubah status dari 'Pending' ke 'Disetujui' | Status diperbarui, notifikasi dikirim ke user terkait, dan saldo cashflow terupdate | Pass |
| FIN-08 | Hapus data pengeluaran | Klik tombol "Hapus" | Data terhapus secara soft-delete (masih ada di DB tapi tidak muncul di list), file bukti di storage ikut dihapus | Pass |
| FIN-09 | Export Bukti Pengeluaran PDF | Klik tombol "Cetak PDF" pada baris data | Sistem mengunduh file PDF dengan rincian pengeluaran dan status persetujuan | Pass |

### 3. Halaman Gaji Guru
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| FIN-10 | Mencatat pembayaran gaji baru | Memilih Guru, Periode (Bulan & Tahun), Jumlah, Tanggal, Keterangan | Data tersimpan di tabel `gaji_guru` dan otomatis membuat record di tabel `pengeluaran` dengan kode `GAJI-NIP-YYYYMM` | Pass |
| FIN-11 | Pengaturan Gaji Default | Guru ID, Nominal Gaji Pokok, Keterangan | Data gaji default tersimpan untuk digunakan sebagai auto-fill pada input gaji bulanan berikutnya | Pass |
| FIN-12 | Cetak Slip Gaji PDF | Klik tombol "Cetak Slip" | Dokumen slip gaji formal terunduh dengan rincian nominal dan periode bulan | Pass |
| FIN-13 | Akses Guru (Self-View) | Login sebagai Guru -> Menu Gaji Saya | Guru hanya dapat melihat riwayat gajinya sendiri, tidak dapat melihat gaji guru lain | Pass |

### 4. Halaman Aset Sekolah
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| AST-01 | Menambah aset baru | Nama Aset, Kategori (Elektronik, Furniture, dll), Tanggal Perolehan, Harga, Kondisi (Baik, Rusak Ringan/Berat), Lokasi | Aset terdaftar dan status kondisi muncul di statistik dashboard aset | Pass |
| AST-02 | Filter & Cari Aset | Nama, Lokasi, atau Kondisi | Menampilkan daftar aset yang sesuai, statistik kondisi (Baik/Rusak) terupdate otomatis | Pass |
| AST-03 | Update Kondisi Aset | Mengubah field Kondisi | Perubahan tersimpan, membantu pemantauan inventaris sekolah | Pass |

### 5. Halaman Cashflow (Arus Kas)
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| CF-01 | Rekapitulasi Saldo | Memilih Range Tanggal | Menampilkan Total Pemasukan, Total Pengeluaran, dan Saldo Akhir (Net) secara akurat | Pass |
| CF-02 | Analisis Arus Kas | Group By (Day/Month/Year) | Tabel menampilkan pergerakan uang masuk dan keluar per periode yang dipilih dengan running balance (Saldo Berjalan) | Pass |

### 6. Halaman Laporan Pemasukan (Income Report)
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| REP-01 | Rekap Pemasukan | Filter Tanggal, Jenis Pembayaran, Metode | Menampilkan daftar transaksi detail dan rincian "Total Per Jenis" (misal: Total SPP, Total Gedung, dll) | Pass |
| REP-02 | Export Laporan Pemasukan | Klik "Export Excel" atau "Export PDF" | Dokumen terunduh dengan layout landscape, memuat semua kolom transaksi dan grand total | Pass |

### 7. Halaman Laporan Pengeluaran (Expense Report)
| ID | Skenario Pengujian | Input | Hasil yang Diharapkan | Status |
|----|--------------------|-------|-----------------------|--------|
| REP-03 | Rekap Pengeluaran | Filter Tanggal, Kategori Pengeluaran | Menampilkan detail pengeluaran beserta rincian total per kategori untuk analisis biaya | Pass |
| REP-04 | Export Laporan Pengeluaran | Klik "Export Excel" atau "Export PDF" | Dokumen terunduh dengan data transaksi lengkap beserta petugas (user) yang mencatat | Pass |

---
**Kesimpulan Akhir:**
Sistem Pengelolaan Keuangan Sriwijaya Kids telah diuji melalui metode Black Box dan terbukti mampu menangani seluruh alur kerja finansial (Pemasukan, Pengeluaran, Gaji, Aset, dan Pelaporan) dengan validasi data yang ketat dan integrasi notifikasi antar role.
