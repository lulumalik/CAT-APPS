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
| **Deploy Coolify** | [deploy/COOLIFY.md](./deploy/COOLIFY.md) | Setup Dockerfile + volume storage di Coolify |

---

## Ringkasan Alur Besar Platform

```
Beranda (publik)
    ↓
Daftar Akun → Verifikasi Email → Pendaftaran (Administrasi → Psikologi → Kesehatan → Fisik)
    ↓
Dashboard terbuka → Kelas Saya → Ruang Kelas (materi & tes)
    ↓
Staff input nilai & laporan → Orang tua pantau perkembangan ananda
    ↓
Peserta / admin unduh laporan dashboard sebagai PDF
```

Pada tahap **Administrasi**, berkas diunggah **satu per satu** (tersimpan langsung). Kelengkapan dokumen ditinjau **admin secara manual** — lihat [PANDUAN-USER.md](./PANDUAN-USER.md) & [PANDUAN-ADMIN.md](./PANDUAN-ADMIN.md).

---

## Peran & Hak Akses

| Fitur | Peserta | Orang Tua | Mentor | Admin |
|-------|:-------:|:---------:|:------:|:-----:|
| Beranda & info publik | ✅ | ✅ | ✅ | ✅ |
| Daftar & login | ✅ | ✅ | ✅ | ✅ |
| Alur pendaftaran | ✅ | — | — | — |
| Kelas & materi | ✅* | — | ✅ | ✅ |
| Kerjakan tes | ✅* | — | — | — |
| Dashboard perkembangan | ✅* | ✅** | — | ✅*** |
| Unduh laporan PDF dashboard | ✅* | — | — | ✅*** |
| Kelola kelas | — | — | ✅ | ✅ |
| Input nilai & peringkat | — | — | ✅ | ✅ |
| Laporan harian peserta | — | — | ✅ | ✅ |
| Undang orang tua | — | — | ✅ | ✅ |
| Bank soal & manajemen tes | — | — | — | ✅ |
| Manajemen user & sertifikat | — | — | — | ✅ |
| Review berkas pendaftaran (manual) | — | — | — | ✅ |

**Berkas pendaftaran:** disimpan privat di `storage/app/private/registration/` (bukan URL publik). Hanya pemilik & admin yang login dapat membuka — lihat [PANDUAN-ADMIN.md §16](./PANDUAN-ADMIN.md#16-troubleshooting-teknis).

\* Setelah pendaftaran selesai disetujui  
\** Hanya untuk ananda yang sudah terhubung  
\*** Melalui menu Pengguna → Dashboard Siswa (`/dashboard/student/{id}`)

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

*Terakhir diperbarui: Juni 2026*
