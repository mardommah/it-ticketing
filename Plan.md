# Plan Aplikasi e-Ticketing Kendala IT

## Tujuan Aplikasi
Membangun sistem tiket (ticketing) kendala IT yang terintegrasi langsung dengan WhatsApp secara otomatis. Sistem ini ditujukan agar pelaporan kendala menjadi lebih cepat, mudah didata, dan terpantau dengan baik, dari tahap pelaporan hingga penyelesaian.

## Fitur Utama

### 1. Pendeteksi Kendala Otomatis (Event-Driven via WhatsApp)
- **Konsep Real-Time:** Sistem akan selalu "mendengar" (listen) secara aktif pada grup WhatsApp IT yang telah ditentukan. Saat ada anggota grup yang melaporkan masalah (misalnya dengan format tertentu atau sekadar pesan di grup), sistem akan mendeteksinya secara instan tanpa perlu melakukan pengecekan berulang (polling).
- **Pembuatan Tiket Otomatis:** Pesan keluhan dari grup WA secara langsung diubah menjadi sebuah "Tiket" baru dalam sistem untuk segera ditindaklanjuti.

### 2. Rekapitulasi Kendala IT
- **Daftar Masalah Berjalan:** Semua tiket yang dibuat akan direkap dalam satu tempat (Dashboard).
- **Detail Pendataan:** Menyimpan informasi penting seperti nama pelapor, waktu pelaporan, deskripsi masalah, hingga urgensi dari masalah tersebut.

### 3. Penugasan (Assign) & Update Status
- **Pembagian Tugas (Assign):** Admin atau anggota tim IT dapat mengambil alih tiket dan menugaskan kendala tersebut kepada teknisi tertentu.
- **Pemantauan Status:** Teknisi dapat memperbarui status pengerjaan (misalnya: *Belum Ditangani*, *Sedang Dikerjakan*, *Selesai*).
- **Notifikasi Balik:** Setiap kali status tiket berubah atau tiket telah diselesaikan, sistem akan mengirimkan pemberitahuan otomatis kembali ke grup WA agar semua orang mengetahui perkembangan penyelesaiannya.

### 4. Analisis Kendala IT
- **Laporan dan Metrik:** Menyediakan tampilan grafik atau ringkasan data kendala yang terjadi.
- **Kinerja:** Menilai jenis kendala yang paling sering terjadi (misalnya: Masalah Jaringan, Printer, dll), serta mengukur seberapa cepat tim IT mampu menyelesaikan sebuah tiket (SLA - Service Level Agreement).

---

## Teknologi yang Digunakan (Tech Stack)

### Baileys (Node.js) - *Sebagai Sistem Komunikasi WA*
Pustaka JavaScript ini berjalan di belakang layar untuk menghubungi WhatsApp. Baileys bekerja secara *event-driven*, di mana ia akan bereaksi secara instan ketika ada pesan masuk di grup, dan meneruskannya ke sistem pusat.

### Laravel (PHP) - *Sebagai Sistem Pusat (Core/Backend)*
Berperan sebagai otak utama aplikasi. Laravel bertugas menampung data yang dikirim oleh Baileys, menyimpan data rekap tiket, mengatur siapa mengerjakan apa (Assign), dan memproses semua logika laporan serta analisis kendala.

### Tailwind CSS - *Sebagai Tampilan Utama (Frontend)*
Digunakan dalam antarmuka web (khusus tim IT) karena desainnya yang sangat modern dan cepat dibangun. Tailwind akan membuat Dashboard tiket terlihat bersih, profesional, dan nyaman digunakan baik dari komputer maupun perangkat seluler.

---

## Alur Kerja Sistem (Workflow)
1. **Pelaporan:** Pengguna melaporkan masalah ke Grup WhatsApp IT ("Printer di lantai 2 rusak").
2. **Pendeteksian:** Sistem Bot (Baileys) otomatis mendeteksi pesan masuk tersebut dan meneruskannya ke server web secara instan.
3. **Pencatatan:** Server web (Laravel) mencatatnya dan membukakan Tiket baru di Web Dashboard.
4. **Penanganan:** Teknisi IT membuka Web Dashboard, melakukan penugasan terhadap dirinya (Assign), lalu mengubah status tiket menjadi "Pengecekan".
5. **Pemberitahuan Status:** Sistem akan membalas di Grup WhatsApp: *"Kendala sedang dicek oleh Teknisi Budi."*
6. **Penyelesaian:** Jika sudah selesai, status di-update menjadi Selesai. Laporan dan analisis otomatis masuk untuk evaluasi kinerja bulanan tim IT.

alur kerja nya adalah, catat semua fitur ke dalam issue github, kerjakan oleh AI -> catat update nya pada issue -> lakukan pull request
