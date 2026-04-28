# Perbaikan Error 422 - Update Peran Pengguna

## 📋 Ringkasan Masalah

**Error:** `PUT http://localhost:8000/api/user/admin/users/019dcf28-0653-7274-be8e-12d2f7b3a6ac/role 422 (Unprocessable Content)`

Error 422 berarti validasi data gagal di backend. Ini terjadi karena backend tidak menerima data yang sesuai format.

---

## ✅ Perbaikan yang Sudah Dilakukan di Backend

### 1. File: `user-account-service/app/Http/Controllers/UserController.php`

**Perubahan:**

- ✅ Menambahkan error handling yang lebih baik
- ✅ Menambahkan logging untuk debugging
- ✅ Custom validation messages untuk user yang lebih jelas
- ✅ Pengecekan null pada variable `$role` sebelum diakses
- ✅ Try-catch untuk menangani exception
- ✅ Return response dengan status code yang sesuai

**Validasi yang diterapkan:**

```php
'role' => 'required|string|exists:roles,name'
```

**Role yang tersedia di database:**

- `pendeta`
- `jemaat_aktif`
- `ketua_rayon`

---

## 🔍 Status Frontend - SUDAH BENAR ✅

Kode `userService.js` frontend yang user berikan sudah **BENAR**, tidak perlu diubah:

```javascript
export const updateUserRole = async (userId, roleName) => {
  try {
    const response = await axiosInstance.put(
      `/user/admin/users/${userId}/role`,
      { role: roleName }, // ✅ Field 'role' sudah benar
    );
    return response.data;
  } catch (error) {
    throw error.response?.data?.message || "Gagal mengubah role pengguna";
  }
};
```

✅ **Yang sudah benar:**

- ✅ Endpoint: `/user/admin/users/${userId}/role`
- ✅ Method: PUT
- ✅ Body: `{ role: roleName }`
- ✅ Field name harus `role` (bukan `roleName`)

---

## ⚠️ Checklist untuk Troubleshooting Error 422

Jika masih error 422 setelah kode userService benar, periksa hal-hal ini:

### 1️⃣ **axiosInstance Configuration**

Pastikan file konfigurasi axios sudah memiliki:

```javascript
// File: config/axiosInstance.js
const axiosInstance = axios.create({
  baseURL: "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

// Interceptor untuk menambahkan token
axiosInstance.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

### 2️⃣ **Saat memanggil updateUserRole di JemaatPage.jsx**

```javascript
// ✅ BENAR: roleName sudah ada value
const roleName = "ketua_rayon"; // atau dari select dropdown
await updateUserRole(userId, roleName);

// ❌ SALAH: roleName undefined atau kosong
const roleName = undefined;
await updateUserRole(userId, roleName); // Akan error 422!

// ❌ SALAH: Nama role salah
await updateUserRole(userId, "admin"); // 'admin' tidak ada di database!
```

### 3️⃣ **Validasi value yang dikirim**

Pastikan role hanya salah satu dari:

- `pendeta` ✓
- `jemaat_aktif` ✓
- `ketua_rayon` ✓

Tidak boleh:

- `admin` ✗
- `superadmin` ✗
- `moderator` ✗

### 4️⃣ **Verifikasi di Browser Console**

```javascript
// Tambahkan console.log untuk debugging di JemaatPage.jsx
const handleChangeRole = async () => {
  console.log("Before calling updateUserRole:");
  console.log("userId:", selectedUserId);
  console.log("roleName:", selectedRole);
  console.log(
    "Is roleName valid?",
    ["pendeta", "jemaat_aktif", "ketua_rayon"].includes(selectedRole),
  );

  try {
    const result = await updateUserRole(selectedUserId, selectedRole);
    console.log("Success:", result);
  } catch (error) {
    console.error("Error:", error);
  }
};
```

Buka DevTools (F12) → Console tab, kemudian buat perubahan role dan lihat apakah:

- `userId` ada value
- `roleName` ada value dan benar

### 5️⃣ **Check Network Request di DevTools**

1. Buka DevTools (F12) → Network tab
2. Lakukan perubahan role
3. Cari request PUT ke `/user/admin/users/.../role`
4. Klik request tersebut
5. Lihat tab "Request" untuk memastikan body adalah: `{"role": "ketua_rayon"}`

### 6️⃣ **Contoh Implementasi yang Benar**

Lihat file template: `TEMPLATE_JemaatPage.jsx` untuk contoh implementasi lengkap dengan error handling.

---

## 2. **Header Content-Type**

- HARUS: `Content-Type: application/json`
- Jangan: `application/x-www-form-urlencoded`

3. **Endpoint URL**
   - Benar: `PUT http://localhost:8000/api/user/admin/users/{userId}/role`
   - Jangan lupa Bearer Token di Authorization header

4. **Contoh Request dengan fetch API:**

   ```javascript
   const updateUserRole = async (userId, roleName) => {
     try {
       const response = await fetch(
         `http://localhost:8000/api/user/admin/users/${userId}/role`,
         {
           method: "PUT",
           headers: {
             "Content-Type": "application/json",
             Authorization: `Bearer ${token}`,
           },
           body: JSON.stringify({
             role: roleName, // ← PENTING: Field name harus 'role'
           }),
         },
       );

       if (!response.ok) {
         const error = await response.json();
         console.error("Error:", error);
         return error;
       }

       const data = await response.json();
       console.log("Success:", data);
       return data;
     } catch (error) {
       console.error("Request failed:", error);
     }
   };
   ```

5. **Contoh Request dengan Axios:**

   ```javascript
   import axios from "axios";

   const updateUserRole = async (userId, roleName) => {
     try {
       const response = await axios.put(
         `http://localhost:8000/api/user/admin/users/${userId}/role`,
         { role: roleName }, // ← PENTING: Body harus memiliki field 'role'
         {
           headers: {
             Authorization: `Bearer ${token}`,
             "Content-Type": "application/json",
           },
         },
       );

       console.log("Success:", response.data);
       return response.data;
     } catch (error) {
       if (error.response) {
         console.error("Error Response:", error.response.data);
         console.error("Status:", error.response.status);
       }
       throw error;
     }
   };
   ```

---

## 🧪 Cara Testing Manual

### Dengan cURL:

```bash
curl -X PUT http://localhost:8000/api/user/admin/users/019dcf28-0653-7274-be8e-12d2f7b3a6ac/role \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"role": "ketua_rayon"}'
```

### Dengan Postman:

1. Method: **PUT**
2. URL: `http://localhost:8000/api/user/admin/users/019dcf28-0653-7274-be8e-12d2f7b3a6ac/role`
3. Headers:
   - `Content-Type: application/json`
   - `Authorization: Bearer YOUR_JWT_TOKEN`
4. Body (raw JSON):
   ```json
   {
     "role": "ketua_rayon"
   }
   ```

---

## 📊 Troubleshooting

| Error              | Penyebab                               | Solusi                                                       |
| ------------------ | -------------------------------------- | ------------------------------------------------------------ |
| 422 Unprocessable  | Field `role` tidak dikirim atau kosong | Pastikan body request memiliki `{"role": "nama_role"}`       |
| 422 Unprocessable  | Nama role salah                        | Gunakan salah satu: `pendeta`, `jemaat_aktif`, `ketua_rayon` |
| 404 User not found | User ID tidak valid                    | Periksa ID pengguna di database                              |
| 401 Unauthorized   | Token tidak valid atau expired         | Login kembali untuk mendapat token baru                      |
| 500 Server Error   | Exception di server                    | Lihat log di `storage/logs/laravel.log`                      |

---

## 📝 Debugging dengan Logs

Untuk melihat request apa yang diterima backend, cek log file:

```
storage/logs/laravel.log
```

Backend sudah menambahkan logging yang menampilkan:

- Request body yang diterima
- Validation errors jika ada
- Role yang tersedia di database
- Status perubahan role

---

## 🎯 Ringkasan Checklist

- [ ] Frontend mengirim field `"role"` dalam JSON body
- [ ] Value role adalah salah satu dari: `pendeta`, `jemaat_aktif`, `ketua_rayon`
- [ ] Header `Content-Type: application/json` ada di request
- [ ] Token JWT dikirim di Authorization header
- [ ] Request method adalah PUT
- [ ] URL endpoint benar: `/api/user/admin/users/{id}/role`
- [ ] Test dengan cURL atau Postman berhasil
- [ ] Cek log di `storage/logs/laravel.log` untuk error detail
