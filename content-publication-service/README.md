# Content & Publication Service — GPdI Sibulele Balige

Microservice Laravel 12 untuk mengelola konten publik dan publikasi Sistem Informasi GPdI.

## Fitur
- Beranda (ringkasan semua konten)
- Profil Gereja
- Jadwal Ibadah & Kegiatan
- Galeri Kegiatan
- Informasi Pelayanan
- Pengumuman (publik / jemaat / rayon)
- Renungan Harian

## Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve --port=8001
```

## Autentikasi

Service ini **tidak** mengelola login. Autentikasi dilakukan oleh **Auth Service**.
API Gateway meneruskan header berikut ke setiap request:

| Header | Isi | Contoh |
|--------|-----|--------|
| `X-User-Id` | ID user yang login | `42` |
| `X-Role` | Role user | `pendeta` / `jemaat` / `ketua_rayon` |

## API Endpoints

### Public (tanpa login)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/beranda` | Ringkasan beranda |
| GET | `/api/profil-gereja` | Profil gereja |
| GET | `/api/jadwal` | Semua jadwal aktif |
| GET | `/api/galeri` | Galeri kegiatan |
| GET | `/api/informasi-pelayanan` | Info pelayanan |
| GET | `/api/pengumuman` | Pengumuman publik |

### Jemaat (X-Role: jemaat)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/api/pengumuman/jemaat` | Pengumuman jemaat |
| GET | `/api/renungan` | Daftar renungan |
| GET | `/api/renungan/hari-ini` | Renungan hari ini |

### Ketua Rayon (X-Role: ketua_rayon)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| POST | `/api/pengumuman` | Buat pengumuman |
| PUT | `/api/pengumuman/{id}` | Edit pengumuman |
| DELETE | `/api/pengumuman/{id}` | Hapus pengumuman |

### Pendeta / Admin (X-Role: pendeta)
| Method | Endpoint | Keterangan |
|--------|----------|------------|
| PUT | `/api/profil-gereja` | Update profil |
| POST/PUT/DELETE | `/api/jadwal` | Kelola jadwal |
| POST/DELETE | `/api/galeri` | Kelola galeri |
| POST/PUT/DELETE | `/api/informasi-pelayanan` | Kelola pelayanan |
| POST/PUT/DELETE | `/api/renungan` | Kelola renungan |
