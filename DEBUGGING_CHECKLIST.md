| Response 422, error message tidak jelas | Middleware atau formatter mengubah response | Update Laravel ke versi terbaru |
| Response berhasil tapi data tidak berubah | Token tidak valid atau user tidak punya permission | Login kembali dengan user yang benar |
| Network error / request timeout | Backend service down atau URL salah | Periksa apakah semua service berjalan dengan `docker-compose ps` |

---

## 🧪 Test dengan cURL di Terminal

```bash
# 1. Dapatkan token terlebih dahulu dengan login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gpdi.com","password":"password123"}'

# Response akan berisi token, copy token tersebut

# 2. Gunakan token untuk update role
curl -X PUT http://localhost:8000/api/user/admin/users/YOUR_USER_ID/role \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"role":"jemaat_aktif"}' # Ganti "jemaat_aktif" dengan nama peran yang tersedia (misal: pendeta, ketua_rayon)
```
