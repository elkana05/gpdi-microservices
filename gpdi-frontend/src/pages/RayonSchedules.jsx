import React, { useEffect, useState } from 'react';
import { eventApi } from '../api/eventApi';

export default function RayonSchedules() {
    const [schedules, setSchedules] = useState([]);
    
    // State untuk form input
    const [formData, setFormData] = useState({
        rayon_id: 1, // Default sementara (Nantinya dinamis berdasarkan data profil ketua rayon)
        title: '',
        location: '',
        event_date: '',
        start_time: '',
        end_time: ''
    });

    useEffect(() => {
        loadSchedules();
    }, []);

    const loadSchedules = async () => {
        try {
            const response = await eventApi.getRayonSchedules();
            setSchedules(response.data);
        } catch (error) {
            console.error("Gagal memuat jadwal rayon. Pastikan Anda sudah login.");
        }
    };

    const handleCreate = async (e) => {
        e.preventDefault();
        try {
            await eventApi.createRayonSchedule(formData);
            alert("Jadwal Rayon berhasil ditambahkan!");
            // Reset form
            setFormData({ ...formData, title: '', location: '', event_date: '', start_time: '', end_time: '' });
            // Refresh tabel
            loadSchedules(); 
        } catch (error) {
            if(error.errors) {
                alert("Validasi Gagal! Silakan cek console untuk detailnya.");
                console.log("Validation Errors:", error.errors);
            } else {
                alert("Gagal menyimpan data. Pastikan Role Anda adalah ketua_rayon.");
            }
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm("Apakah Anda yakin ingin menghapus jadwal ini?")) return;
        try {
            await eventApi.deleteRayonSchedule(id);
            alert("Jadwal berhasil dihapus!");
            loadSchedules();
        } catch (error) {
            alert("Gagal menghapus data.");
        }
    };

    return (
        <div style={{ padding: '20px', border: '1px solid #ccc', backgroundColor: '#fafafa' }}>
            <h2>Manajemen Jadwal Rayon (Area Private)</h2>
            <p>Halaman ini disimulasikan sebagai Dashboard Ketua Rayon.</p>
            
            {/* Form Tambah Jadwal */}
            <form onSubmit={handleCreate} style={{ marginBottom: '20px', padding: '15px', background: '#fff', border: '1px solid #ddd' }}>
                <h3>Tambah Jadwal Baru</h3>
                <div style={{ display: 'grid', gap: '10px', gridTemplateColumns: '1fr 1fr' }}>
                    <input type="text" placeholder="Judul Ibadah" value={formData.title} required onChange={e => setFormData({...formData, title: e.target.value})} />
                    <input type="text" placeholder="Lokasi" value={formData.location} required onChange={e => setFormData({...formData, location: e.target.value})} />
                    <input type="date" value={formData.event_date} required onChange={e => setFormData({...formData, event_date: e.target.value})} />
                    <div style={{ display: 'flex', gap: '10px' }}>
                        <input type="time" title="Waktu Mulai" value={formData.start_time} required onChange={e => setFormData({...formData, start_time: e.target.value})} />
                        <span> - </span>
                        <input type="time" title="Waktu Selesai" value={formData.end_time} required onChange={e => setFormData({...formData, end_time: e.target.value})} />
                    </div>
                </div>
                <button type="submit" style={{ marginTop: '15px', padding: '8px 16px', cursor: 'pointer' }}>Simpan Jadwal</button>
            </form>

            {/* Tabel Data */}
            <table border="1" cellPadding="10" style={{ width: '100%', borderCollapse: 'collapse', background: '#fff' }}>
                <thead style={{ backgroundColor: '#e9ecef' }}>
                    <tr>
                        <th>Judul</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {schedules.length === 0 ? (
                        <tr><td colSpan="5" style={{ textAlign: 'center' }}>Tidak ada data jadwal rayon / Anda belum memiliki akses.</td></tr>
                    ) : (
                        schedules.map(item => (
                            <tr key={item.id}>
                                <td>{item.title}</td>
                                <td>{item.location}</td>
                                <td>{item.event_date}</td>
                                <td>{item.start_time} - {item.end_time}</td>
                                <td>
                                    <button onClick={() => handleDelete(item.id)} style={{ color: 'red', cursor: 'pointer' }}>Hapus</button>
                                </td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}