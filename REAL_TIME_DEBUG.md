# 🔍 DEBUGGING REAL-TIME - Error 422 Masih Muncul

## Langkah 1: Cek Request Body di Network Tab

1. Buka DevTools (F12)
2. Klik tab **Network**
3. Klik checkbox ☑️ **Preserve log**
4. Clear network (Ctrl+L)
5. Coba ubah peran akses di aplikasi
6. Cari request dengan nama `/role` (method PUT)
7. Klik request tersebut
8. Lihat tab **Request** dan **Response**

**Kirimkan screenshot atau informasi:**

- **Request Headers:** Content-Type apa? Ada Authorization header?
- **Request Body:** Apa isi JSON yang dikirim?
- **Response Body:** Apa error message yang dikembalikan?

---

## Langkah 2: Cek Console untuk Error Detail

1. Buka DevTools (F12)
2. Klik tab **Console**
3. Coba ubah peran akses
4. Lihat error message yang muncul
5. **Screenshot atau copy-paste error message**

---

## Langkah 3: Cek Backend Logs

Jalankan command ini untuk melihat log backend:

```bash
# Terminal/PowerShell
docker logs -f user-account-service
```

atau

```bash
# Lihat file log
docker exec user-account-service tail -f storage/logs/laravel.log
```

**Copy-paste output yang muncul saat Anda mencoba ubah peran**

---

## Kemungkinan Penyebab

Tanpa melihat request/response yang sebenarnya, kemungkinan error 422 adalah:

### ❌ **Kemungkinan 1: Field 'role' dikirim dengan value null/undefined**

```javascript
// SALAH - roleName belum ter-set
const [selectedRole, setSelectedRole] = useState("");
await updateUserRole(userId, selectedRole); // ← Dikirim string kosong!
```

### ❌ **Kemungkinan 2: Nama role tidak valid**

```javascript
// SALAH - Nama role yang dikirim tidak ada di database
await updateUserRole(userId, "admin"); // ← Database hanya punya: pendeta, jemaat_aktif, ketua_rayon
```

### ❌ **Kemungkinan 3: Field name salah**

```javascript
// SALAH - Seharusnya 'role', bukan 'roleName'
await axiosInstance.put(`/user/admin/users/${userId}/role`, { roleName });

// BENAR
await axiosInstance.put(`/user/admin/users/${userId}/role`, { role: roleName });
```

### ❌ **Kemungkinan 4: axiosInstance belum dikonfigurasi**

```javascript
// Jika config/axiosInstance.js belum ada atau tidak benar
// Request tidak akan punya Authorization header
```

---

## ✅ Solusi Cepat - Copy-Paste Ini

### 1. Pastikan file `config/axiosInstance.js` ada dan benar:

```javascript
import axios from "axios";

const axiosInstance = axios.create({
  baseURL: "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

// Tambahkan token ke setiap request
axiosInstance.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default axiosInstance;
```

### 2. Pastikan `userService.js` benar:

```javascript
import axiosInstance from "../config/axiosInstance";

export const updateUserRole = async (userId, roleName) => {
  console.log("DEBUG: Mengirim update role", { userId, roleName });

  try {
    const response = await axiosInstance.put(
      `/user/admin/users/${userId}/role`,
      { role: roleName }, // ← PENTING: Field HARUS 'role'
    );
    console.log("SUCCESS:", response.data);
    return response.data;
  } catch (error) {
    console.error("ERROR:", error.response?.data || error.message);
    throw error;
  }
};
```

### 3. Pastikan di JemaatPage.jsx ada validasi sebelum kirim:

```javascript
const handleChangeRole = async () => {
  // ✅ VALIDASI PERTAMA: Pastikan userId ada
  if (!selectedUserId) {
    alert("Pilih pengguna terlebih dahulu!");
    return;
  }

  // ✅ VALIDASI KEDUA: Pastikan role ada dan bukan string kosong
  if (!selectedRole || selectedRole === "") {
    alert("Pilih role terlebih dahulu!");
    return;
  }

  // ✅ VALIDASI KETIGA: Pastikan role valid
  const validRoles = ["pendeta", "jemaat_aktif", "ketua_rayon"];
  if (!validRoles.includes(selectedRole)) {
    alert(`Role tidak valid! Gunakan: ${validRoles.join(", ")}`);
    return;
  }

  // DEBUG: Lihat apa yang akan dikirim
  console.log("Akan dikirim:", { userId: selectedUserId, role: selectedRole });

  try {
    const result = await updateUserRole(selectedUserId, selectedRole);
    alert("✅ Role berhasil diubah!");
    // Reload data
    await loadUsers();
  } catch (error) {
    alert("❌ Gagal ubah role: " + error);
  }
};
```

---

## 📋 Informasi yang Perlu Anda Berikan

Agar saya bisa membantu lebih tepat, berikan:

1. **Screenshot DevTools Network tab:**
   - Request body (apa yang dikirim?)
   - Response body (error message apa?)

2. **Output console error (F12 → Console)**
   - Copy-paste error message lengkap

3. **Backend logs** (jalankan: `docker logs -f user-account-service`)
   - Lihat apa yang di-log saat error terjadi

4. **Lokasi file:**
   - Dimana file `config/axiosInstance.js` Anda?
   - Apakah sudah ada atau belum?

---

**Tunggu saya sampai Anda memberikan informasi di atas, baru saya bisa fix lebih akurat! 🔍**
