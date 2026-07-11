# Alur Kerja Sistem Rental Kendaraan

## 1. Diagram Alur Utama Sistem

```mermaid
graph TD
    A["👤 User Baru"] -->|Request| B["POST /api/register"]
    B -->|Input Data| C["Nama, Email, Password, Role"]
    C -->|Validasi Email| D{Email Sudah Ada?}
    D -->|Ya| E["❌ Error 409"]
    D -->|Tidak| F["✅ User Terdaftar"]
    F -->|Role: admin/staff| G["Masuk ke Login"]
    
    G -->|Request| H["POST /api/login"]
    H -->|Input| I["Email + Password"]
    I -->|Validasi| J{Valid?}
    J -->|Tidak| K["❌ Error 401: Email/Password Salah"]
    J -->|Ya| L["🔐 Generate JWT Token"]
    L -->|Token = User ID + Email + Role + Expiry| M["✅ Login Berhasil"]
    M -->|Token dikirim ke Client| N["Client Menyimpan Token"]
    
    N -->|Token diperlukan di Header| O["Authorization: Bearer token"]
    O -->|Token Valid?| P{Validasi Token}
    P -->|Tidak Valid| Q["❌ Error 401: Unauthorized"]
    P -->|Valid| R["✅ Akses Diterima"]
    
    R -->|Role: admin| S["🔑 Akses Admin Menu"]
    R -->|Role: staff| T["👥 Akses Staff Menu"]
    
    S -->|Akses Penuh| U["Manage Kendaraan<br/>Manage User<br/>Manage Pelanggan<br/>Manage Transaksi<br/>Manage Pembayaran<br/>Manage Pengembalian"]
    T -->|Akses Terbatas| V["Lihat Kendaraan<br/>Lihat Transaksi<br/>Lihat Pembayaran<br/>Lihat Pengembalian"]
```

---

## 2. Alur Manajemen Kendaraan (CRUD)

```mermaid
graph TD
    A["📋 Menu Kendaraan"] -->|Pilihan| B{Operasi?}
    
    B -->|CREATE| C["POST /api/kendaraan"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Input| E["Nama Kendaraan<br/>Merk<br/>Jenis<br/>Plat Nomor<br/>Harga Sewa"]
    E -->|Validasi| F{Plat Sudah Ada?}
    F -->|Ya| G["❌ Error 409: Duplikat"]
    F -->|Tidak| H["✅ Kendaraan Ditambah<br/>Status: Tersedia"]
    
    B -->|READ| I["GET /api/kendaraan<br/>atau<br/>GET /api/kendaraan/:id"]
    I -->|Auth Required| J["✅ Tampilkan Data<br/>Kendaraan"]
    
    B -->|UPDATE| K["PUT /api/kendaraan/:id"]
    K -->|Admin Only| L["✅ Authorization Check"]
    L -->|Input| M["Update Nama/Merk<br/>Jenis/Plat/Harga"]
    M -->|Validasi| N{Valid Data?}
    N -->|Tidak| O["❌ Error"]
    N -->|Ya| P["✅ Kendaraan Diupdate"]
    
    B -->|DELETE| Q["DELETE /api/kendaraan/:id"]
    Q -->|Admin Only| R["✅ Authorization Check"]
    R -->|Proses| S["✅ Kendaraan Dihapus"]
    
    H --> T["Database: kendaraan"]
    P --> T
    S --> T
    J --> T
```

---

## 3. Alur Manajemen User (CRUD)

```mermaid
graph TD
    A["👤 Menu User Management"] -->|Pilihan| B{Operasi?}
    
    B -->|READ ALL| C["GET /api/users"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Query| E["✅ Tampilkan Semua User<br/>dengan Role"]
    
    B -->|READ ONE| F["GET /api/users/:id"]
    F -->|Admin Only| G["✅ Authorization Check"]
    G -->|Query by ID| H["✅ Tampilkan Detail User"]
    
    B -->|UPDATE| I["PUT /api/users/:id"]
    I -->|Admin Only| J["✅ Authorization Check"]
    J -->|Input| K["Update Nama<br/>Email<br/>Role<br/>Password"]
    K -->|Validasi| L{Email Unik?}
    L -->|Tidak| M["❌ Error 409"]
    L -->|Ya| N["✅ User Diupdate"]
    
    B -->|DELETE| O["DELETE /api/users/:id"]
    O -->|Admin Only| P["✅ Authorization Check"]
    P -->|Proses| Q["✅ User Dihapus<br/>dari Database"]
    
    E --> R["Database: user"]
    H --> R
    N --> R
    Q --> R
```

---

## 4. Alur Manajemen Pelanggan (CRUD)

```mermaid
graph TD
    A["👥 Menu Pelanggan"] -->|Pilihan| B{Operasi?}
    
    B -->|CREATE| C["POST /api/pelanggan"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Input| E["Nama Pelanggan<br/>No HP<br/>Alamat<br/>Email"]
    E -->|Validasi| F{Data Valid?}
    F -->|Tidak| G["❌ Error Validasi"]
    F -->|Ya| H["✅ Pelanggan Ditambah"]
    
    B -->|READ| I["GET /api/pelanggan<br/>atau<br/>GET /api/pelanggan/:id"]
    I -->|Admin Only| J["✅ Authorization Check"]
    J -->|Query| K["✅ Tampilkan Data Pelanggan"]
    
    B -->|UPDATE| L["PUT /api/pelanggan/:id"]
    L -->|Admin Only| M["✅ Authorization Check"]
    M -->|Input| N["Update Nama/No HP<br/>Alamat/Email"]
    N -->|Validasi| O{Valid?}
    O -->|Tidak| P["❌ Error"]
    O -->|Ya| Q["✅ Pelanggan Diupdate"]
    
    B -->|DELETE| R["DELETE /api/pelanggan/:id"]
    R -->|Admin Only| S["✅ Authorization Check"]
    S -->|Proses| T["✅ Pelanggan Dihapus<br/>dari Database"]
    
    H --> U["Database: pelanggan"]
    K --> U
    Q --> U
    T --> U
```

---

## 5. Alur Manajemen Transaksi (CRUD)

```mermaid
graph TD
    A["💼 Menu Transaksi"] -->|Pilihan| B{Operasi?}
    
    B -->|CREATE| C["POST /api/transaksi"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Input| E["Pilih Pelanggan<br/>Pilih Kendaraan<br/>Tgl Mulai Sewa<br/>Tgl Akhir Sewa<br/>Total Harga"]
    E -->|Validasi| F{Kendaraan Tersedia?}
    F -->|Tidak| G["❌ Error: Kendaraan Tidak Tersedia"]
    F -->|Ya| H["✅ Transaksi Dibuat<br/>Status: Aktif"]
    H -->|Update Status| I["Kendaraan: tersedia → disewa"]
    
    B -->|READ| J["GET /api/transaksi<br/>atau<br/>GET /api/transaksi/:id"]
    J -->|Auth Required| K["✅ Authorization Check"]
    K -->|Query| L["✅ Tampilkan Transaksi"]
    
    B -->|UPDATE| M["PUT /api/transaksi/:id"]
    M -->|Admin Only| N["✅ Authorization Check"]
    N -->|Input| O["Update Tanggal/Total<br/>Status Transaksi"]
    O -->|Validasi| P{Valid?}
    P -->|Tidak| Q["❌ Error"]
    P -->|Ya| R["✅ Transaksi Diupdate"]
    
    B -->|DELETE| S["DELETE /api/transaksi/:id"]
    S -->|Admin Only| T["✅ Authorization Check"]
    T -->|Proses| U["✅ Transaksi Dihapus<br/>Kembalikan Status Kendaraan"]
    U -->|Update Status| V["Kendaraan: disewa → tersedia"]
    
    H --> W["Database: transaksi"]
    I --> X["Database: kendaraan"]
    R --> W
    V --> X
```

---

## 6. Alur Manajemen Pembayaran (CRUD)

```mermaid
graph TD
    A["💰 Menu Pembayaran"] -->|Pilihan| B{Operasi?}
    
    B -->|CREATE| C["POST /api/pembayaran"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Input| E["Pilih Transaksi<br/>Nominal Bayar<br/>Metode Bayar<br/>Tanggal Bayar"]
    E -->|Validasi| F{Transaksi Valid?}
    F -->|Tidak| G["❌ Error: Transaksi Tidak Valid"]
    F -->|Ya| H{Nominal >= Total?}
    H -->|Tidak| I["❌ Error: Nominal Kurang"]
    H -->|Ya| J["✅ Pembayaran Dicatat<br/>Status: Lunas/Termin"]
    
    B -->|READ| K["GET /api/pembayaran<br/>atau<br/>GET /api/pembayaran/:id"]
    K -->|Auth Required| L["✅ Authorization Check"]
    L -->|Query| M["✅ Tampilkan Data Pembayaran"]
    
    B -->|UPDATE| N["PUT /api/pembayaran/:id"]
    N -->|Admin Only| O["✅ Authorization Check"]
    O -->|Input| P["Update Nominal/Metode<br/>Tanggal/Status"]
    P -->|Validasi| Q{Valid?}
    Q -->|Tidak| R["❌ Error"]
    Q -->|Ya| S["✅ Pembayaran Diupdate"]
    
    B -->|DELETE| T["DELETE /api/pembayaran/:id"]
    T -->|Admin Only| U["✅ Authorization Check"]
    U -->|Proses| V["✅ Pembayaran Dihapus<br/>dari Database"]
    
    J --> W["Database: pembayaran"]
    M --> W
    S --> W
    V --> W
```

---

## 7. Alur Manajemen Pengembalian (CRUD)

```mermaid
graph TD
    A["🔄 Menu Pengembalian"] -->|Pilihan| B{Operasi?}
    
    B -->|CREATE| C["POST /api/pengembalian"]
    C -->|Admin Only| D["✅ Authorization Check"]
    D -->|Input| E["Pilih Transaksi<br/>Tgl Pengembalian<br/>Kondisi Kendaraan<br/>Biaya Tambahan"]
    E -->|Validasi| F{Transaksi Aktif?}
    F -->|Tidak| G["❌ Error: Transaksi Tidak Aktif"]
    F -->|Ya| H["✅ Pengembalian Dicatat<br/>Status: Returned"]
    H -->|Update Status| I["Kendaraan: disewa → tersedia<br/>Transaksi: aktif → selesai"]
    
    B -->|READ| J["GET /api/pengembalian<br/>atau<br/>GET /api/pengembalian/:id"]
    J -->|Auth Required| K["✅ Authorization Check"]
    K -->|Query| L["✅ Tampilkan Data Pengembalian"]
    
    B -->|UPDATE| M["PUT /api/pengembalian/:id"]
    M -->|Admin Only| N["✅ Authorization Check"]
    N -->|Input| O["Update Tanggal/Kondisi<br/>Biaya Tambahan"]
    O -->|Validasi| P{Valid?}
    P -->|Tidak| Q["❌ Error"]
    P -->|Ya| R["✅ Pengembalian Diupdate"]
    
    B -->|DELETE| S["DELETE /api/pengembalian/:id"]
    S -->|Admin Only| T["✅ Authorization Check"]
    T -->|Proses| U["✅ Pengembalian Dihapus<br/>Kembalikan Status ke Aktif"]
    U -->|Update Status| V["Kendaraan: tersedia → disewa<br/>Transaksi: selesai → aktif"]
    
    H --> W["Database: pengembalian"]
    I --> X["Database: kendaraan & transaksi"]
    R --> W
    V --> X
```

---

## 8. Alur Lengkap: Dari Register Hingga Selesai (Complete User Journey)

```mermaid
graph TD
    A["🚀 START: Pengguna Baru"] -->|Buka Aplikasi| B["REGISTER"]
    
    B -->|Input Data| C["Nama: Agus<br/>Email: agus@rental.com<br/>Password: ****<br/>Role: admin/staff"]
    C -->|Validasi & Hash| D{Email Valid?}
    D -->|Tidak| E["❌ Email sudah terdaftar"]
    E -->|Coba Lagi| C
    D -->|Ya| F["✅ REGISTER SUKSES<br/>🔐 User Tersimpan di DB"]
    
    F -->|Next Step| G["LOGIN"]
    G -->|Input| H["Email: agus@rental.com<br/>Password: ****"]
    H -->|Verifikasi Password| I{Email & Password Valid?}
    I -->|Tidak| J["❌ Gagal Login"]
    J -->|Coba Lagi| G
    I -->|Ya| K["✅ LOGIN SUKSES"]
    K -->|Generate| L["🔐 JWT TOKEN<br/>exp: + 24 jam<br/>payload: id, email, role"]
    L -->|Kirim ke Client| M["Token disimpan di Local Storage<br/>atau Session"]
    
    M -->|Semua request ke API| N["👉 Header:<br/>Authorization: Bearer token"]
    
    N -->|Role: ADMIN| O["✅ Admin Dashboard"]
    N -->|Role: STAFF| P["✅ Staff Dashboard"]
    
    O -->|Manage| Q["1️⃣ Kendaraan<br/>- Tambah (CREATE)<br/>- Lihat (READ)<br/>- Edit (UPDATE)<br/>- Hapus (DELETE)"]
    
    O -->|Manage| R["2️⃣ User Staff<br/>- Lihat Semua (READ)<br/>- Edit (UPDATE)<br/>- Hapus (DELETE)"]
    
    O -->|Manage| S["3️⃣ Pelanggan<br/>- Tambah (CREATE)<br/>- Lihat (READ)<br/>- Edit (UPDATE)<br/>- Hapus (DELETE)"]
    
    O -->|Manage| T["4️⃣ Transaksi<br/>- Buat Rental (CREATE)<br/>- Lihat (READ)<br/>- Edit (UPDATE)<br/>- Batalkan (DELETE)"]
    
    O -->|Manage| U["5️⃣ Pembayaran<br/>- Catat Bayar (CREATE)<br/>- Lihat (READ)<br/>- Edit (UPDATE)<br/>- Hapus (DELETE)"]
    
    O -->|Manage| V["6️⃣ Pengembalian<br/>- Catat Return (CREATE)<br/>- Lihat (READ)<br/>- Edit (UPDATE)<br/>- Hapus (DELETE)"]
    
    P -->|Can Only| W["👁️ View Kendaraan<br/>👁️ View Transaksi<br/>👁️ View Pembayaran<br/>👁️ View Pengembalian"]
    
    Q -->|Operasi| X["POST /api/kendaraan<br/>GET /api/kendaraan<br/>PUT /api/kendaraan/:id<br/>DELETE /api/kendaraan/:id"]
    
    R -->|Operasi| Y["GET /api/users<br/>PUT /api/users/:id<br/>DELETE /api/users/:id"]
    
    S -->|Operasi| Z["POST /api/pelanggan<br/>GET /api/pelanggan<br/>PUT /api/pelanggan/:id<br/>DELETE /api/pelanggan/:id"]
    
    T -->|Operasi| AA["POST /api/transaksi<br/>GET /api/transaksi<br/>PUT /api/transaksi/:id<br/>DELETE /api/transaksi/:id"]
    
    U -->|Operasi| AB["POST /api/pembayaran<br/>GET /api/pembayaran<br/>PUT /api/pembayaran/:id<br/>DELETE /api/pembayaran/:id"]
    
    V -->|Operasi| AC["POST /api/pengembalian<br/>GET /api/pengembalian<br/>PUT /api/pengembalian/:id<br/>DELETE /api/pengembalian/:id"]
    
    W -->|End| AD["🏁 END: User Melakukan Aktivitas<br/>Sesuai Role"]
    X --> AE["Database"]
    Y --> AE
    Z --> AE
    AA --> AE
    AB --> AE
    AC --> AE
```

---

## 9. Tabel Ringkasan Endpoint & Access Control

| Endpoint | Method | Role | Deskripsi | Input |
|----------|--------|------|-----------|-------|
| `/api/register` | POST | Public | Daftar User Baru | nama, email, password, role |
| `/api/login` | POST | Public | Login & Dapatkan Token | email, password |
| **USER** |
| `/api/users` | GET | Admin | Lihat Semua User | - |
| `/api/users/:id` | GET | Admin | Lihat User Detail | - |
| `/api/users/:id` | PUT | Admin | Update User | nama, email, role, password |
| `/api/users/:id` | DELETE | Admin | Hapus User | - |
| **KENDARAAN** |
| `/api/kendaraan` | GET | Auth | Lihat Semua Kendaraan | - |
| `/api/kendaraan/:id` | GET | Auth | Lihat Detail Kendaraan | - |
| `/api/kendaraan` | POST | Admin | Tambah Kendaraan | nama_kendaraan, merk, jenis, plat_nomor, harga_sewa |
| `/api/kendaraan/:id` | PUT | Admin | Update Kendaraan | nama_kendaraan, merk, jenis, plat_nomor, harga_sewa |
| `/api/kendaraan/:id` | DELETE | Admin | Hapus Kendaraan | - |
| **PELANGGAN** |
| `/api/pelanggan` | GET | Auth | Lihat Semua Pelanggan | - |
| `/api/pelanggan/:id` | GET | Auth | Lihat Detail Pelanggan | - |
| `/api/pelanggan` | POST | Admin | Tambah Pelanggan | nama_pelanggan, no_hp, alamat, email |
| `/api/pelanggan/:id` | PUT | Admin | Update Pelanggan | nama_pelanggan, no_hp, alamat, email |
| `/api/pelanggan/:id` | DELETE | Admin | Hapus Pelanggan | - |
| **TRANSAKSI** |
| `/api/transaksi` | GET | Auth | Lihat Semua Transaksi | - |
| `/api/transaksi/:id` | GET | Auth | Lihat Detail Transaksi | - |
| `/api/transaksi` | POST | Admin | Buat Transaksi Rental | pelanggan_id, kendaraan_id, tgl_mulai, tgl_akhir, total_harga |
| `/api/transaksi/:id` | PUT | Admin | Update Transaksi | pelanggan_id, kendaraan_id, tgl_mulai, tgl_akhir, total_harga, status |
| `/api/transaksi/:id` | DELETE | Admin | Batalkan Transaksi | - |
| **PEMBAYARAN** |
| `/api/pembayaran` | GET | Auth | Lihat Semua Pembayaran | - |
| `/api/pembayaran/:id` | GET | Auth | Lihat Detail Pembayaran | - |
| `/api/pembayaran` | POST | Admin | Catat Pembayaran | transaksi_id, nominal, metode, tgl_bayar |
| `/api/pembayaran/:id` | PUT | Admin | Update Pembayaran | nominal, metode, tgl_bayar, status |
| `/api/pembayaran/:id` | DELETE | Admin | Hapus Pembayaran | - |
| **PENGEMBALIAN** |
| `/api/pengembalian` | GET | Auth | Lihat Semua Pengembalian | - |
| `/api/pengembalian/:id` | GET | Auth | Lihat Detail Pengembalian | - |
| `/api/pengembalian` | POST | Admin | Catat Pengembalian | transaksi_id, tgl_kembali, kondisi, biaya_tambahan |
| `/api/pengembalian/:id` | PUT | Admin | Update Pengembalian | tgl_kembali, kondisi, biaya_tambahan |
| `/api/pengembalian/:id` | DELETE | Admin | Hapus Pengembalian | - |

---

## 10. Flow Autentikasi & JWT Token

```mermaid
graph LR
    A["1. POST /api/login<br/>email + password"] -->|Verify| B["2. Database Check<br/>Email exist?<br/>Password verify?"]
    B -->|✅ Valid| C["3. Generate JWT<br/>payload: id, email, role<br/>exp: +24h"]
    B -->|❌ Invalid| D["Error 401"]
    C -->|4. Return Token| E["Client<br/>token saved"]
    E -->|5. Send Header| F["Authorization:<br/>Bearer token"]
    F -->|6. Check Token| G["Verify JWT<br/>signature & exp"]
    G -->|✅ Valid| H["Grant Access"]
    G -->|❌ Invalid/Expired| I["Error 401"]
    H -->|Role check| J{Admin?}
    J -->|Yes| K["🔓 Full Access"]
    J -->|No| L["🔒 Limited Access"]
```

---

## 11. Database Relationships

```mermaid
erDiagram
    USER ||--o{ TRANSAKSI : creates
    USER {
        int id_user PK
        string nama
        string email UK
        string password
        string role
    }
    
    PELANGGAN ||--o{ TRANSAKSI : rents
    PELANGGAN {
        int id_pelanggan PK
        string nama_pelanggan
        string no_hp
        string alamat
        string email
    }
    
    KENDARAAN ||--o{ TRANSAKSI : includes
    KENDARAAN {
        int id_kendaraan PK
        string nama_kendaraan
        string merk
        string jenis
        string plat_nomor UK
        decimal harga_sewa
        string status
    }
    
    TRANSAKSI ||--o{ PEMBAYARAN : requires
    TRANSAKSI {
        int id_transaksi PK
        int id_pelanggan FK
        int id_kendaraan FK
        date tgl_mulai
        date tgl_akhir
        decimal total_harga
        string status
    }
    
    TRANSAKSI ||--o{ PENGEMBALIAN : completes
    PEMBAYARAN {
        int id_pembayaran PK
        int id_transaksi FK
        decimal nominal
        string metode
        date tgl_bayar
        string status
    }
    
    PENGEMBALIAN {
        int id_pengembalian PK
        int id_transaksi FK
        date tgl_kembali
        string kondisi
        decimal biaya_tambahan
    }
```

---

## 📝 Catatan Penting

### Status Kendaraan:
- **tersedia**: Kendaraan siap disewa
- **disewa**: Sedang disewa (ada transaksi aktif)
- **maintenance**: Dalam perbaikan

### Status Transaksi:
- **aktif**: Rental sedang berlangsung
- **selesai**: Rental selesai & kendaraan sudah dikembalikan
- **batal**: Rental dibatalkan

### Status Pembayaran:
- **lunas**: Sudah dibayar penuh
- **termin**: Pembayaran dalam cicilan
- **pending**: Menunggu pembayaran

### Autentikasi:
- **Token JWT** digunakan untuk autentikasi semua API
- **Authorization Check**: Setiap request diverifikasi tokennya
- **Role-Based Access Control**: Admin punya akses penuh, Staff hanya view
- **Token Expiry**: 24 jam (bisa dikonfigurasi di JWT_EXPIRE)

### Validasi Penting:
1. Email unik saat register & update user
2. Plat nomor unik saat tambah & update kendaraan
3. Kendaraan harus tersedia saat membuat transaksi
4. Transaksi harus aktif saat membuat pengembalian
5. Nominal pembayaran harus sesuai atau lebih dari total harga

---

## 🎯 Ringkasan Flow Utama

1. **REGISTER** → Validasi email unik → Hash password → Simpan ke DB
2. **LOGIN** → Verifikasi email & password → Generate JWT → Return token
3. **REQUEST** → Kirim token di header → Validasi token → Check role
4. **ADMIN** → Akses penuh CRUD semua entity
5. **STAFF** → Hanya READ akses ke Kendaraan, Transaksi, Pembayaran, Pengembalian
6. **CRUD OPERATIONS** → Validasi input → Update/Delete di DB → Return response

---

*Dokumen ini menjelaskan alur kerja lengkap sistem rental kendaraan Anda untuk keperluan laporan.*
