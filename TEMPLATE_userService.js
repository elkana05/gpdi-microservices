/**
 * User Service - Frontend API Calls
 * Menangani semua request yang berhubungan dengan user/jemaat
 */

import axiosInstance from "../config/axiosInstance";

/**
 * Mengambil semua data pengguna/jemaat untuk tabel Admin
 * @returns {Promise} Response data berisi list pengguna
 */
export const getAllUsers = async () => {
  try {
    const response = await axiosInstance.get("/user/admin/users");
    console.log("✅ getAllUsers berhasil:", response.data);
    return response.data;
  } catch (error) {
    console.error("❌ getAllUsers gagal:", error.response?.data);
    throw error.response?.data?.message || "Gagal memuat data jemaat";
  }
};

/**
 * Mengubah role pengguna
 * @param {string} userId - ID pengguna yang akan diubah rolenya
 * @param {string} roleName - Nama role baru (pendeta, jemaat_aktif, ketua_rayon)
 * @returns {Promise} Response data berisi status update
 */
export const updateUserRole = async (userId, roleName) => {
  // VALIDASI: Pastikan userId dan roleName tidak kosong
  if (!userId || !roleName) {
    console.error("❌ Parameter tidak valid:", { userId, roleName });
    throw new Error("userId dan roleName tidak boleh kosong");
  }

  // VALIDASI: Pastikan roleName adalah salah satu dari yang valid
  const validRoles = ["pendeta", "jemaat_aktif", "ketua_rayon"];
  if (!validRoles.includes(roleName)) {
    console.error("❌ Role tidak valid. Role yang tersedia:", validRoles);
    throw new Error(`Role harus salah satu dari: ${validRoles.join(", ")}`);
  }

  try {
    console.log("📤 Sending update role request:", {
      userId,
      newRole: roleName,
      url: `/user/admin/users/${userId}/role`,
      body: { role: roleName },
    });

    const response = await axiosInstance.put(
      `/user/admin/users/${userId}/role`,
      { role: roleName }, // PENTING: Body harus memiliki field 'role'
    );

    console.log("✅ updateUserRole berhasil:", response.data);
    return response.data;
  } catch (error) {
    console.error("❌ updateUserRole gagal:", {
      status: error.response?.status,
      statusText: error.response?.statusText,
      data: error.response?.data,
      message: error.message,
    });

    // Tangani error 422 (Validation Error)
    if (error.response?.status === 422) {
      const errors = error.response?.data?.errors;
      console.error("Validation errors:", errors);
      throw new Error(`Validasi gagal: ${JSON.stringify(errors)}`);
    }

    // Tangani error lainnya
    throw error.response?.data?.message || "Gagal mengubah role pengguna";
  }
};

/**
 * Helper function untuk menampilkan error message yang user-friendly
 */
export const handleRoleUpdateError = (error) => {
  if (error.message.includes("userId dan roleName tidak boleh kosong")) {
    return "Error: Data tidak lengkap. Silakan pilih pengguna dan role yang benar.";
  }

  if (error.message.includes("Role harus salah satu dari")) {
    return `Error: Role yang dipilih tidak valid. Role yang tersedia: pendeta, jemaat_aktif, ketua_rayon`;
  }

  if (error.message.includes("Validasi gagal")) {
    return `Error Validasi: ${error.message}`;
  }

  return `Error: ${error.message || "Terjadi kesalahan saat mengubah role"}`;
};
