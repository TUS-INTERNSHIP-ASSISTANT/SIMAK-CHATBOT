# SIMAK CHATBOT
SIMAK (Sistem Informasi Magang dan Kerja Praktik) adalah layanan chatbot berbasis Artificial Intelligence (AI) yang membantu mahasiswa memperoleh informasi terkait Magang dan Kerja Praktik secara cepat dan praktis.
# Rules Contributor
**Writing Rules**
- Penamaan file tidak boleh di singkat gunakan tanda `-` ketika ada spasi : `contoh : /home-page`
- Untuk penamaan file lebih baik gunakan lowercase agar konsisten
- Hindari penamaan singkat (`landing-page.blade.php`) menjadi (`lndg-pge.blade.php`)

**File Rules**
- Segala sesuatu yang berhubungan dengan gambar simpan di dalam folder `public/assets/images/`
- Folder `layouts` berfungsi untuk menyimpan file kerangka (layout utama) yang digunakan sebagai template dasar.
- Folder `review/app` berfungsi untuk menyimpan halaman inti aplikasi (fitur utama yang hanya bisa diakses setelah login atau otentikasi).
- Folder `review/components` berfungsi untuk menyimpan komponen UI pendukung yang dapat digunakan ulang di berbagai halaman.
- Folder `review/pages` berfungsi untuk menyimpan halaman umum atau public-facing yang bisa diakses tanpa login.

**Catatan Penamaan Branch**
- Silahkan membuat `branch` anda sendiri sebelum mengerjakan fitur
- contoh : `nama/feature` = `budi/landing-pages`

# Warning
- Jangan pernah menghapus folder atau file apapun yg sudah ada atau bawaan dari laravel
- Jika ingin melakukan `git push` dan pull request pada project ini silahkan lakukan `git pull` ke branch `main` terlebih dahulu di lokal komputer
- Silahkan git push ke branch anda sendiri jangan langsung ke branch `main`
- Jika terjadi konflik silahkan perbaiki terlebih dahulu sebelum `push` ke branch anda
- Jika sudah selesai semua silahkan berikan `commit` yang jelas dan `pull request` ke branch `main`

## Instalation
Clone Repository
```sh
git clone https://github.com/TUS-INTERNSHIP-ASSISTANT/SIMAK-CHATBOT.git
```
Tulis perintah dibawah in untuk menginstal depedensi yang di perlukan 
```sh
composer install

npm install
```

## Running Project
```sh
php artisan serve    

npm run dev
```
