<div align="center">

# ⚙️ CMMS AISVAR V2
### Enterprise Heavy Equipment, Plant Maintenance & Supply Chain Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

<p align="center">
  <strong>Sistem Manajemen Pemeliharaan Terpadu & Rantai Pasok Khusus Alat Berat, Armada Industri, dan Pertambangan.</strong>
</p>

[Fitur Utama](#-fitur-utama) • [Modul Sistem](#-modul-sistem) • [Instalasi](#-panduan-instalasi) • [Teknologi](#-teknologi) • [Struktur Proyek](#-struktur-arsitektur)

---

</div>

## 📖 Tentang Aplikasi

**CMMS AISVAR V2** adalah aplikasi *Computerized Maintenance Management System* (CMMS) modern berbasis web yang dirancang khusus untuk mengoptimalkan keandalan armada alat berat *(Equipment Reliability)*, pengendalian sisa umur komponen *(Component Lifecycle)*, investigasi kerusakan teknis *(Failure Analysis)*, dan efisiensi rantai pasok suku cadang *(SCM Logistics & Procurement)*.

Aplikasi ini mengintegrasikan komunikasi operasional lapangan antara departemen **PLANT (Maintenance)** dan **SCM (Logistics)** dengan antarmuka yang responsif, cepat (*full reactive Livewire*), serta didukung oleh dasbor analitik dan messenger internal.

---

## 🌟 Fitur Utama

```
                                  ┌───────────────────────────────────────────────────────────┐
                                  │               CMMS AISVAR V2 ECOSYSTEM                    │
                                  └─────────────────────────────┬─────────────────────────────┘
                                                                │
         ┌──────────────────────────────┼───────────────────────┴───────────────┬──────────────────────────────┐
         ▼                              ▼                                       ▼                              ▼
  [ PLANT MAINTENANCE ]       [ SCM LOGISTICS ]                       [ REALTIME MESSENGER ]           [ ADMIN CONTROL ]
  • Work Order (Task/Subtask) • Master Spareparts & Multi-Rack        • Direct 1-on-1 Chat             • RBAC Roles & Permissions
  • Component Tracker (Life %)• Stock Opname & Berita Acara           • Group Workshop Channel         • General System Settings
  • CCR (Condition Report)    • MOL Partial Issue & Deficit to PR     • File & Image Attachments       • User & Employee Master
  • FAR (5-Why Failure / CAPA)• PR ➔ RFQ ➔ PO ➔ DO ➔ GR Pipeline      • Floating Notification          • Dynamic Branding & Logos
  • OSR (Outside Repair / QC) • Reorder Point (Min/Max Stock)         • Global /chat URL               • Audit Logs & Security
```

---

## 🚀 Modul Sistem

### 🛠️ 1. Modul PLANT Maintenance (`/plt`)
*Tema Tampilan: **Deep Royal Midnight Slate** dengan aksen Electric Blue.*

* **Work Order Management (`/plt/workorder`)**:
  - Manajemen Perintah Kerja hierarkis (Problem, Task, Subtask, dan Alokasi Suku Cadang).
  - Pelacakan status kesiapan unit: *Breakdown*, *Ready to Operate*, dan *Kendala/Obstacle Tracking*.
* **Component Tracker & Lifecycle (`/plt/components`)**:
  - Pelacakan posisi silsilah komponen putar (*Engine, Transmission, Hydraulic Pump, Final Drive*).
  - Visual **Life Meter** (Jam kerja berjalan vs *Target Life Hours*).
  - Catatan transaksi rotasi/swap komponen (Pasang, Lepas, Kirim Workshop, Afkir/Scrap).
* **CCR - Component Condition Report (`/plt/ccr`)**:
  - Evaluasi berkala kondisi fisik komponen (*Wear Percentage %*, kebocoran, getaran, dan kebersihan oli).
  - Rekomendasi tindakan *Planner* (*Continue Run, Planned Changeout, Urgent Replace, Rebuild Overhaul*).
  - **1-Click Auto Work Order**: Mengubah temuan kritis CCR langsung menjadi Work Order operasional.
* **FAR - Failure Analysis Report (`/plt/far`)**:
  - Investigasi mendalam kegagalan dini (*Premature Failure*) atau kerusakan fatal (*Catastrophic Breakdown*).
  - Formulir interaktif **5-Why Root Cause Analysis** & Faktor Fishbone Diagram.
  - Perumusan **CAPA** *(Corrective & Preventive Actions)* dan perhitungan estimasi kerugian biaya serta jam downtime.
* **OSR - Outside Repair Order (`/plt/osr`)**:
  - Surat Perintah Kerja Keluar untuk bengkel bubut / subkontrak vendor (*Machine Shop, Hardchroming, Line Boring, Rewinding*).
  - Pelacakan Surat Jalan Kirim (*Dispatch*) & Surat Jalan Terima (*Received*).
  - Quality Check (QC) Sign-off dan pencatatan masa garansi vendor.

---

### 📦 2. Modul SCM Logistics & Warehouse (`/scm`)
*Tema Tampilan: **Deep Forest Jade Slate** dengan aksen Emerald Green.*

* **Master Spareparts Catalog (`/scm/parts`)**: Katalog suku cadang lengkap dengan multi-lokasi rak gudang *(Rack / Bin Location)*, batas minimum stok *(Reorder Point)*, dan histori vendor.
* **Stock Opname (`/scm/stock-opname`)**: Perhitungan stok fisik periodik dengan kalkulasi otomatis selisih *(discrepancy)* dan generate Berita Acara Opname.
* **MOL - Mechanic Order Part (`/scm/mol`)**:
  - Formulir permintaan suku cadang oleh mekanik berbasis Work Order.
  - **Smart Partial Approval & Issue**: Jika stok gudang hanya tersedia sebagian, gudang mengeluarkan stok yang ada dan **otomatis meneruskan kekurangan part menjadi Purchase Request (PR)**.
* **PR - Purchase Request (`/scm/pr`)**: Pengajuan pengadaan suku cadang ke departemen Purchasing.
* **RFQ - Request For Quotation (`/scm/rfq`)**: Permintaan penawaran harga vendor dan perbandingan komparasi harga terbaik.
* **PO - Purchase Order (`/scm/po`)**: Penerbitan pesanan pembelian resmi kepada vendor terpilih.
* **DO - Delivery Order (`/scm/do`)**: Manajemen surat jalan pengiriman suku cadang dari pusat/vendor ke site operasional.
* **GR - Goods Receipt (`/scm/gr`)**: Penerimaan fisik barang di gudang dengan validasi PO dan penambahan stok otomatis.

---

### 💬 3. Global Chat Messenger (`/chat`)
- Komunikasi *real-time* antar tim mekanik, planner, dan logistik gudang.
- Mendukung percakapan langsung (*1-on-1 Direct Chat*) dan grup koordinasi (*Group Channels*).
- Kirim foto bukti kerusakan, file dokumen PDF, dan notifikasi cepat.

---

### ⚙️ 4. Filament Admin Panel (`/admin`)
- **Role-Based Access Control (RBAC)**: Pengaturan hak akses granular untuk Super Admin, Plant Planner, SCM Officer, dan Mekanik.
- **General Settings**: Konfigurasi nama perusahaan, logo instansi, favicon browser, dan email sistem.
- **Master Data**: Unit Alat Berat (*Equipment*), Site / Lokasi Operasional, Vendor Rekanan, dan Data Karyawan.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| **Backend Framework** | [Laravel 12 / 13](https://laravel.com) (PHP 8.4+) |
| **Reactive Frontend** | [Livewire 3](https://livewire.laravel.com) |
| **Admin Panel Engine**| [Filament PHP v3](https://filamentphp.com) |
| **UI & Layouts**      | Metronic 8 Theme, Bootstrap 5, [Tailwind CSS](https://tailwindcss.com) |
| **Database**          | MySQL / MariaDB (UUID Primary Keys, Soft Deletes) |
| **Asset Bundler**     | [Vite](https://vitejs.dev) |
| **Code Standards**    | [Laravel Pint](https://laravel.com/docs/pint) |

---

## 💻 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lingkungan lokal:

### 1. Kloning Repositori
```bash
git clone https://github.com/your-username/cmms-aisvar-v2.git
cd cmms-aisvar-v2
```

### 2. Install Dependensi PHP & Node
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cmms_aisvar
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Migrasi Database & Seeder
```bash
php artisan migrate --seed
```

### 6. Build Frontend Assets & Jalankan Server
```bash
npm run build
php artisan serve
```

Aplikasi dapat diakses melalui browser di: `http://127.0.0.1:8000`

---

## 🔐 Akun Default untuk Pengujian

| Role | Email / Username | Password | Modul Utama |
|---|---|---|---|
| **Super Admin** | `admin@example.com` / `admin` | `password` | `/admin` & Semua Modul |
| **Plant Planner / Mekanik** | `plant@example.com` | `password` | `/plt` & `/chat` |
| **SCM Logistics Officer** | `scm@example.com` | `password` | `/scm` & `/chat` |

---

## 📂 Struktur Arsitektur

```
app/
├── Filament/               # Resource & Halaman Admin Panel Filament
├── Http/Controllers/       # Controller Standar (Auth, Profile, Export)
├── Livewire/
│   ├── Plt/                # Livewire Komponen PLANT (Dashboard, Components, CCR, FAR, OSR)
│   ├── Scm/                # Livewire Komponen SCM (Dashboard, Parts, Opname, MOL, PR, RFQ, PO, DO, GR)
│   └── User/               # Komponen Global (WorkOrder, ChatPage)
├── Models/                 # Eloquent Models (BaseModel with UUID & SoftDeletes)
└── Settings/               # Spatie Laravel Settings (General & Portal Settings)

resources/
├── views/
│   ├── layouts/            # Template Layout (user.blade.php with Plant/SCM theme toggles)
│   ├── livewire/           # Blade Views untuk seluruh komponen Livewire
│   └── filament/           # Kustomisasi view Filament
```

---

## 🤝 Kontribusi & Lisensi

Proyek ini dikembangkan untuk kebutuhan operasional manajemen alat berat dan pabrik industri.  
Dilisensikan di bawah [MIT License](LICENSE).

<div align="center">
  <sub>Developed with ❤️ for High-Performance Maintenance Engineering.</sub>
</div>
