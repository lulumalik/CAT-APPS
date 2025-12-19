## Tujuan

* Mengubah frontend menjadi SPA penuh dengan Vue 3.

* Menambahkan vue-router untuk navigasi client-side.

* Menambahkan Pinia sebagai store manajemen state.

## Dependensi

* `vue`, `vue-router`, `pinia` sebagai runtime deps.

* `@vitejs/plugin-vue` sebagai plugin Vite.

## Perubahan Konfigurasi

* Update `vite.config.js` untuk mengaktifkan `@vitejs/plugin-vue` dan tetap memakai `laravel-vite-plugin` + Tailwind.

* Tambahkan alias `@` ke `resources/js` untuk import yang rapi.

## Struktur File Baru

* `resources/js/app.js`: entry SPA, inisialisasi Vue, Pinia, Router, mount ke `#app`.

* `resources/js/App.vue`: root component menampilkan `<router-view/>` dan navigasi.

* `resources/js/router/index.js`: definisi routes (contoh `HomeView`).

* `resources/js/views/HomeView.vue`: halaman contoh.

* `resources/js/stores/app.js`: store Pinia contoh.

## Integrasi ke Blade

* Sederhanakan `resources/views/welcome.blade.php` untuk menyediakan kontainer `<div id="app"></div>`.

* Pertahankan `@vite(['resources/css/app.css', 'resources/js/app.js'])` agar assets di-load.

## Langkah Implementasi

1. Pasang deps: `vue`, `vue-router`, `pinia`, dan `@vitejs/plugin-vue`.
2. Edit `vite.config.js`: aktifkan `vue()` dan alias `@`.
3. Buat file `App.vue`, router, store, dan update `app.js` untuk createApp + Pinia + Router + mount.
4. Ubah `welcome.blade.php` agar hanya berisi `<div id="app"></div>` dan `@vite`.
5. Jalankan dev server dan verifikasi.

## Validasi

* Jalankan `npm run dev` dan buka halaman welcome untuk melihat app Vue.

* Uji navigasi router dan interaksi store (counter di `HomeView`).

## Catatan Versi Node

* Saat ini Vite 7 dan `laravel-vite-plugin@2` memerlukan Node 20.19+ atau 22.12+.

* Opsi yang disarankan: upgrade Node ke 22 LTS.

* Opsi alternatif: downgrade Vite dan plugin ke versi kompatibel Node 16 (kurang disarankan karena ekosistem Laravel terbaru mengarah ke Node 20+).

## Opsional (lanjutan)

* Migrasikan halaman Blade lain ke komponen Vue dan routes.

* Tambahkan layout, guard auth di router, dan modul store tambahan sesuai kebutuhan.

