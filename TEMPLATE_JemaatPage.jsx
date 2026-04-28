/**
 * Contoh Implementasi Update Role di JemaatPage.jsx
 * Ini adalah template untuk menunjukkan cara yang BENAR menggunakan updateUserRole
 */

import React, { useState, useEffect } from "react";
import {
  getAllUsers,
  updateUserRole,
  handleRoleUpdateError,
} from "../services/userService";

const JemaatPage = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedUserId, setSelectedUserId] = useState(null);
  const [selectedRole, setSelectedRole] = useState("");
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  const validRoles = ["pendeta", "jemaat_aktif", "ketua_rayon"];

  // Load data jemaat saat component mount
  useEffect(() => {
    loadUsers();
  }, []);

  // Fungsi untuk load data jemaat
  const loadUsers = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await getAllUsers();
      console.log("Data jemaat:", data);
      setUsers(data.data || data);
    } catch (err) {
      console.error("Error loading users:", err);
      setError(`Gagal memuat data jemaat: ${err}`);
    } finally {
      setLoading(false);
    }
  };

  // Fungsi untuk handle perubahan role
  const handleChangeRole = async (e) => {
    e.preventDefault();

    // VALIDASI: Pastikan user sudah dipilih
    if (!selectedUserId) {
      setError("❌ Silakan pilih pengguna terlebih dahulu");
      setSuccess(null);
      return;
    }

    // VALIDASI: Pastikan role sudah dipilih
    if (!selectedRole) {
      setError("❌ Silakan pilih role terlebih dahulu");
      setSuccess(null);
      return;
    }

    try {
      setLoading(true);
      setError(null);
      setSuccess(null);

      console.log("🔄 Memperbarui role...", {
        userId: selectedUserId,
        newRole: selectedRole,
      });

      // Panggil fungsi updateUserRole
      const result = await updateUserRole(selectedUserId, selectedRole);

      console.log("✅ Update role berhasil:", result);
      setSuccess(`✅ Role berhasil diubah menjadi ${selectedRole}`);

      // Reload data setelah update berhasil
      await loadUsers();

      // Reset form
      setSelectedUserId(null);
      setSelectedRole("");
    } catch (err) {
      console.error("❌ Gagal update role:", err);
      setError(handleRoleUpdateError(err));
      setSuccess(null);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="jemaat-page">
      <h1>Manajemen Jemaat - Update Role</h1>

      {/* Alert Error */}
      {error && (
        <div className="alert alert-danger" role="alert">
          {error}
        </div>
      )}

      {/* Alert Success */}
      {success && (
        <div className="alert alert-success" role="alert">
          {success}
        </div>
      )}

      {/* Form Update Role */}
      <form onSubmit={handleChangeRole} className="mb-4">
        <div className="row g-3">
          {/* Pilih Pengguna */}
          <div className="col-md-6">
            <label htmlFor="userSelect" className="form-label">
              Pilih Pengguna
            </label>
            <select
              id="userSelect"
              className="form-select"
              value={selectedUserId}
              onChange={(e) => setSelectedUserId(e.target.value)}
              disabled={loading}
            >
              <option value="">-- Pilih Pengguna --</option>
              {users.map((user) => (
                <option key={user.id} value={user.id}>
                  {user.name} ({user.email})
                </option>
              ))}
            </select>
          </div>

          {/* Pilih Role */}
          <div className="col-md-6">
            <label htmlFor="roleSelect" className="form-label">
              Role Baru
            </label>
            <select
              id="roleSelect"
              className="form-select"
              value={selectedRole}
              onChange={(e) => setSelectedRole(e.target.value)}
              disabled={loading}
            >
              <option value="">-- Pilih Role --</option>
              {validRoles.map((role) => (
                <option key={role} value={role}>
                  {role}
                </option>
              ))}
            </select>
          </div>

          {/* Submit Button */}
          <div className="col-12">
            <button
              type="submit"
              className="btn btn-primary"
              disabled={loading || !selectedUserId || !selectedRole}
            >
              {loading ? "⏳ Memproses..." : "💾 Update Role"}
            </button>
            <button
              type="button"
              className="btn btn-secondary ms-2"
              onClick={loadUsers}
              disabled={loading}
            >
              🔄 Reload Data
            </button>
          </div>
        </div>
      </form>

      {/* Data Table */}
      {loading && (
        <div className="spinner-border" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      )}

      {!loading && users.length > 0 && (
        <table className="table table-striped">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Role Saat Ini</th>
              <th>Status</th>
              <th>Dibuat</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user) => (
              <tr key={user.id}>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>
                  <span className="badge bg-info">{user.role || "public"}</span>
                </td>
                <td>
                  {user.is_active ? (
                    <span className="badge bg-success">Aktif</span>
                  ) : (
                    <span className="badge bg-danger">Nonaktif</span>
                  )}
                </td>
                <td>{new Date(user.created_at).toLocaleDateString("id-ID")}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}

      {!loading && users.length === 0 && (
        <div className="alert alert-info" role="alert">
          Tidak ada data jemaat yang ditemukan
        </div>
      )}
    </div>
  );
};

export default JemaatPage;
