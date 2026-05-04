# Laporan Pengembangan Proyek: Metode Agile Scrum
**Proyek:** Aplikasi Pengelolaan Keuangan Sriwijaya Kids
**Status:** Sprint Aktif

Dokumen ini mencatat progres pengembangan aplikasi dengan mengikuti kerangka kerja **Agile Scrum** sebagaimana diilustrasikan dalam metodologi standar industri.

---

## 1. Scrum Roles (Peran)
*   **Product Owner:** USER (Menentukan prioritas fitur dan validasi hasil)
*   **Scrum Master:** AI Assistant (Memfasilitasi proses, memecahkan hambatan teknis)
*   **Development Team:** AI Assistant (Eksekusi kode, perbaikan bug, dan optimasi)

---

## 2. Product Backlog
Daftar fitur yang direncanakan dan dikembangkan, dikategorikan berdasarkan modul utama aplikasi:

### A. Sistem Akademik
| ID | Nama Fitur | Deskripsi | User | Prioritas |
|:---|:---|:---|:---|:---|
| PB-01 | Login Multi User | Sistem login untuk admin, guru, dan siswa | Semua | Tinggi |
| PB-02 | Manajemen Data Siswa | Tambah, edit, hapus, dan lihat data siswa | Admin | Tinggi |
| PB-03 | Manajemen Data Guru | Pengelolaan profil dan akun data guru | Admin | Tinggi |
| PB-04 | Manajemen Kelas | Mengatur data kelas dan pembagian siswa | Admin | Tinggi |
| PB-05 | Jadwal Pelajaran | Menampilkan dan mengatur jadwal pelajaran | Admin, Guru, Siswa | Tinggi |
| PB-06 | Sistem Absensi | Input kehadiran siswa (hadir, izin, sakit) | Guru | Tinggi |
| PB-07 | Input Nilai | Input nilai tugas, UTS, dan UAS | Guru | Tinggi |
| PB-08 | Laporan Nilai | Menampilkan nilai siswa (raport) | Guru, Siswa | Tinggi |
| PB-09 | Dashboard Akademik | Ringkasan data akademik sekolah | Admin | Sedang |
| PB-10 | Pencarian Data | Fitur search data siswa/guru | Admin | Sedang |
| PB-11 | Filter Data | Filter berdasarkan kelas/semester | Admin, Guru | Sedang |
| PB-12 | Manajemen Semester | Pengaturan tahun ajaran & semester | Admin | Tinggi |

### B. Learning Management System (LMS)
| ID | Nama Fitur | Deskripsi | User | Prioritas |
|:---|:---|:---|:---|:---|
| LMS-01 | Upload Materi | Berbagi materi pelajaran (File/Link) | Guru | Tinggi |
| LMS-02 | Manajemen Tugas | Membuat dan mengatur tugas belajar | Guru | Tinggi |
| LMS-03 | Pengumpulan Tugas | Siswa mengunggah hasil pengerjaan tugas | Siswa | Tinggi |
| LMS-04 | Penilaian LMS | Guru memberikan nilai pada tugas siswa | Guru | Tinggi |
| LMS-05 | Monitoring Tugas | Melihat status pengumpulan siswa | Guru, Admin | Sedang |
| LMS-06 | Dashboard LMS | Ringkasan aktivitas belajar mengajar | Semua | Sedang |

### C. Pengelolaan Keuangan & Aset
| ID | Nama Fitur | Deskripsi | User | Prioritas |
|:---|:---|:---|:---|:---|
| KEU-01 | Manajemen Jenis Bayar | Pengaturan SPP, Uang Bangunan, dll | Admin | Tinggi |
| KEU-02 | Input Pemasukan | Pencatatan pembayaran dari siswa | Bendahara | Tinggi |
| KEU-03 | Input Pengeluaran | Pencatatan biaya operasional sekolah | Bendahara | Tinggi |
| KEU-04 | Manajemen Gaji Guru | Sistem payroll otomatis untuk guru | Admin | Tinggi |
| KEU-05 | Laporan Cashflow | Grafik arus kas harian/bulanan | Admin, Kepsek | Tinggi |
| KEU-06 | Export Laporan | Cetak laporan ke format PDF & Excel | Admin, Bendahara | Tinggi |
| KEU-07 | Manajemen Aset | Inventarisasi barang dan gedung sekolah | Admin | Sedang |
| KEU-08 | Riwayat Aktivitas | Log otomatis setiap transaksi keuangan | Semua | Sedang |

---

## 3. Sprint Backlog
Sprint backlog merupakan tahapan perencanaan dan penyusunan daftar pekerjaan yang harus diselesaikan dalam satu sprint. Product backlog dipecah menjadi beberapa bagian untuk selanjutnya diproses dalam fase sprint. Durasi sprint ini ditetapkan selama **1 minggu** (Sprint Aktif).

Daftar pekerjaan (Tasks) yang dipilih untuk diselesaikan pada sprint ini dibagi menjadi 3 kategori utama:

### A. Sistem Akademik
| ID Task | Fitur | Deskripsi Pekerjaan | Prioritas | Estimasi Waktu |
|:---|:---|:---|:---|:---|
| SB-AK-01 | Manajemen Siswa | Perbaikan fitur edit data siswa | Tinggi | 1 Hari |
| SB-AK-02 | Raport | Penyesuaian layout cetak raport dan filter data | Tinggi | 1 Hari |
| SB-AK-03 | Absensi | Optimasi loading data absensi per kelas | Sedang | 1 Hari |

### B. Learning Management System (LMS)
| ID Task | Fitur | Deskripsi Pekerjaan | Prioritas | Estimasi Waktu |
|:---|:---|:---|:---|:---|
| SB-LMS-01 | Pengumpulan | Perbaikan fitur upload tugas siswa | Tinggi | 1 Hari |
| SB-LMS-02 | Penilaian | Integrasi nilai LMS ke raport akademik | Tinggi | 2 Hari |
| SB-LMS-03 | Monitoring | Perbaikan bug pada tampilan progres belajar | Sedang | 1 Hari |

### C. Pengelolaan Keuangan & Aset
| ID Task | Fitur | Deskripsi Pekerjaan | Prioritas | Estimasi Waktu |
|:---|:---|:---|:---|:---|
| SB-KEU-01 | Export Excel | Implementasi export excel pada Laporan Cashflow | Tinggi | 1 Hari |
| SB-KEU-02 | Riwayat Keu | Otomatisasi notifikasi riwayat pengeluaran | Tinggi | 1 Hari |
| SB-KEU-03 | Manajemen Aset | Fix statistik kondisi aset dan fitur hapus | Tinggi | 1 Hari |
| SB-KEU-04 | Fix Loading | Perbaikan global loading overlay pada ekspor file | Tinggi | 1 Hari |

## 4. Sprint (Pelaksanaan)
Sprint merupakan tahapan penyelesaian pekerjaan yang telah ditentukan sebelumnya pada sprint backlog. Pada tahapan ini pembuatan aplikasi dilakukan secara intensif, mulai dari tahap perancangan teknis, penulisan kode (coding), hingga pengujian (testing).

Pelaksanaan sprint kali ini dibagi menjadi 3 bagian:

### A. Sistem Akademik (Execution)
| Tahapan | Aktivitas | Status |
|:---|:---|:---|
| Perancangan | Re-design form edit siswa dan struktur database raport | Selesai |
| Coding | Implementasi perbaikan form dan filter data akademik | Selesai |
| Testing | Pengujian fungsionalitas edit dan cetak raport | Selesai |

### B. Learning Management System (Execution)
| Tahapan | Aktivitas | Status |
|:---|:---|:---|
| Perancangan | Alur integrasi nilai tugas LMS ke modul raport | Selesai |
| Coding | Pengembangan fitur upload dan sistem grading otomatis | Selesai |
| Testing | Uji coba pengunggahan file dan kalkulasi nilai akhir | Selesai |

### C. Pengelolaan Keuangan & Aset (Execution)
| Tahapan | Aktivitas | Status |
|:---|:---|:---|
| Perancangan | Struktur file Excel untuk laporan cashflow dan skema notifikasi | Selesai |
| Coding | Pembuatan sistem export excel dan otomatisasi log riwayat | Selesai |
| Testing | Validasi data export dan uji coba hapus aset serta loading fix | Selesai |

---

## 5. Sprint Execution (Sprint 1-4 Weeks)
*   **Daily Scrum:** Dilakukan melalui interaksi chat untuk sinkronisasi progres teknis dan identifikasi error (misal: penanganan loading stuck pada export).
*   **Iterasi:** Pengembangan dilakukan secara inkremental, di mana setiap fitur yang selesai langsung diuji.

---

## 6. Review dan Retrospective
Pada akhir fase sprint, perangkat lunak diuji coba dan diberi penilaian oleh pengguna menggunakan metode **Black Box Testing**. Secara retrospektif, dilakukan pengumpulan umpan balik terhadap kebutuhan fungsional. Jika terdapat perbaikan atau penambahan, hal tersebut akan dimasukkan ke dalam backlog tambahan untuk sprint berikutnya.

Berikut adalah hasil Review & Retrospective untuk 3 bagian utama:

### A. Sistem Akademik (Review & Retro)
| Aspek | Hasil Review (Black Box) | Retrospective / Umpan Balik |
|:---|:---|:---|
| Fungsionalitas | Form edit siswa & cetak raport berjalan 100% | Perlu penambahan fitur export PDF raport secara massal |
| User Interface | Tampilan raport sudah sesuai standar | Tambahkan preview raport sebelum dicetak |

### B. Learning Management System (Review & Retro)
| Aspek | Hasil Review (Black Box) | Retrospective / Umpan Balik |
|:---|:---|:---|
| Fungsionalitas | Upload tugas dan penilaian otomatis berhasil | Siswa memerlukan notifikasi via WhatsApp/Email jika ada tugas baru |
| Performa | Loading materi lancar untuk file < 10MB | Optimasi kompresi file otomatis saat upload |

### C. Pengelolaan Keuangan & Aset (Review & Retro)
| Aspek | Hasil Review (Black Box) | Retrospective / Umpan Balik |
|:---|:---|:---|
| Fungsionalitas | Export Excel Pemasukan & Pengeluaran akurat | Tambahkan fitur filter 'Status' pada laporan export |
| Sistem | Loading overlay tidak lagi macet saat download | Integrasi riwayat pengeluaran sudah bagus, pertahankan |

---

## 7. Finished Work (Increment)
Hasil kerja yang telah diselesaikan dan siap digunakan:
*   **[Selesai]** Logika statistik Aset Sekolah (menghitung total real-time).
*   **[Selesai]** Aksi Hapus Aset dengan konfirmasi keamanan.
*   **[Selesai]** Perbaikan Global Loading Overlay (mengenali route `/export/`).
*   **[Selesai]** Fitur Ekspor Excel Laporan Pemasukan.
*   **[Selesai]** Fitur Ekspor Excel Laporan Pengeluaran.
*   **[Selesai]** Automatisasi Notifikasi Riwayat Pengeluaran.

---
> **Catatan:** Dokumen ini bersifat dinamis dan diperbarui seiring dengan selesainya tugas-tugas baru dalam Product Backlog.
