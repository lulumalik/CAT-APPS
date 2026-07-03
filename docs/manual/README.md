# Manual Book — Pratistha Cendekia Prestasi

Panduan lengkap penggunaan platform **Pratistha Cendekia Prestasi** (CAT Apps) dari awal sampai akhir.

**Website:** [https://pratisthaindonesia.com](https://pratisthaindonesia.com)

---

## Daftar Panduan

| Peran | File | Untuk siapa? |
|-------|------|--------------|
| **Peserta / Calon Taruna** | [PANDUAN-USER.md](./PANDUAN-USER.md) | Siswa yang mendaftar kursus persiapan AKPOL |
| **Orang Tua / Wali** | [PANDUAN-ORANG-TUA.md](./PANDUAN-ORANG-TUA.md) | Ayah, ibu, atau wali yang memantau perkembangan ananda |
| **Mentor / Pengajar** | [PANDUAN-MENTOR.md](./PANDUAN-MENTOR.md) | Pengajar yang mengelola kelas, nilai, dan laporan |
| **Admin** | [PANDUAN-ADMIN.md](./PANDUAN-ADMIN.md) | Administrator penuh sistem |
| **Deploy Coolify** | [deploy/COOLIFY.md](../deploy/COOLIFY.md) | Setup Dockerfile + volume storage di Coolify |

---

## Ringkasan Alur Besar Platform

```
Beranda (publik) + Tryout Gratis
    ↓
Daftar Akun → Verifikasi Email → Pendaftaran
    (Administrasi → Psikologi → Kesehatan → Fisik)
    ↓
Dashboard terbuka → Kelas Saya (materi & quiz kelas) + menu Ujian (lintas mapel)
    ↓
Staff input nilai jasmani & laporan → Quiz/ujian selesai otomatis masuk laporan & grafik
    ↓
Orang tua pantau perkembangan ananda & unduh laporan PDF
```

### Sistem Penilaian di Dashboard

Semua nilai akademik (quiz & ujian) ditampilkan dalam **skala 1–100** (bukan persentase). Tidak ada tampilan `66%` atau `2/3 benar` di dashboard peserta/orang tua.

| Sumber nilai | Masuk ke | Keterangan |
|--------------|----------|------------|
| **Quiz kelas** (per mapel) | Nilai per Mata Pelajaran | Hanya quiz yang dilampirkan ke kelas (bukan tryout gratis) |
| **Ujian** lintas mapel | Nilai Ujian | Dari menu Ujian peserta |
| **Jasmani** (manual) | Hasil Jasmani | Diinput mentor/admin di halaman peringkat |

Setelah peserta **menyelesaikan quiz atau ujian**, sistem otomatis membuat **laporan harian** dan memperbarui grafik perkembangan.

### Perbedaan Quiz/Test vs Ujian

| Jenis | Siapa buat | Dipakai di | Keterangan |
|-------|------------|------------|------------|
| **Quiz/Test** | Admin/mentor di `/tests` | Kelas kursus (per mata pelajaran) | Dilampirkan ke kelas sebagai **quiz** |
| **Ujian** | Admin/mentor di `/exams` | Menu **Ujian** peserta (`/ujian`) | Gabungan lintas mata pelajaran; database & API terpisah |

Pada tahap **Administrasi**, berkas diunggah **satu per satu** (tersimpan langsung). Kelengkapan dokumen ditinjau **admin secara manual** — lihat [PANDUAN-USER.md](./PANDUAN-USER.md) & [PANDUAN-ADMIN.md](./PANDUAN-ADMIN.md).

---

## Peran & Hak Akses

| Fitur | Peserta | Orang Tua | Mentor | Admin |
|-------|:-------:|:---------:|:------:|:-----:|
| Beranda & info publik | ✅ | ✅ | ✅ | ✅ |
| Daftar & login | ✅ | ✅ | ✅ | ✅ |
| Alur pendaftaran | ✅ | — | — | — |
| Kelas & materi | ✅* | — | ✅ | ✅ |
| Quiz kelas | ✅* | — | — | — |
| Ujian lintas mapel | ✅* | — | — | — |
| Dashboard perkembangan | ✅* | ✅** | — | ✅*** |
| Unduh laporan PDF perkembangan | — | ✅** | — | —**** |
| Kelola kelas | — | — | ✅ | ✅ |
| Input nilai jasmani & peringkat | — | — | ✅ | ✅ |
| Laporan harian peserta | — | — | ✅ | ✅ |
| Undang orang tua | — | — | ✅ | ✅ |
| Bank soal | — | — | ✅ | ✅ |
| Quiz/Test (`/tests`) | — | — | ✅ | ✅ |
| Ujian (`/exams`) | — | — | ✅ | ✅ |
| Manajemen user & review pendaftaran | — | — | — | ✅ |

**Berkas pendaftaran:** disimpan privat di `storage/app/private/registration/` (bukan URL publik). Hanya pemilik & admin yang login dapat membuka — lihat [PANDUAN-ADMIN.md §16](./PANDUAN-ADMIN.md#16-troubleshooting-teknis).

\* Setelah pendaftaran selesai disetujui  
\** Hanya untuk ananda yang terhubung (login dengan **username**); unduh PDF di halaman **Perkembangan ananda** (`/child/{id}`)  
\*** Melalui menu Pengguna → Dashboard Siswa (`/dashboard/student/{id}`) — tampilan web, tanpa tombol unduh PDF di UI admin  
\**** Admin dapat melihat konten yang sama di Dashboard Siswa; file PDF diunduh oleh **orang tua** dari halaman perkembangan ananda

---

## Kontak Resmi

| Kanal | Informasi |
|-------|-----------|
| Website | pratisthaindonesia.com |
| Email | administrator@pratisthaindonesia.com, admin.pratistha@gmail.com |
| WhatsApp | +628138964488 |
| Instagram | pratistha.cendikia |
| Rekening BRI | 1107-01-000931-56-9 (PT. Pratistha Training Center Indonesia) |

---

*Terakhir diperbarui: Juli 2026*
