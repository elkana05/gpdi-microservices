import React, { useEffect, useState } from 'react';
import { eventApi } from '../api/eventApi';

export default function PublicSchedules() {
    const [schedules, setSchedules] = useState([]);
    const [meta, setMeta] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchSchedules();
    }, []);

    const fetchSchedules = async () => {
        try {
            // Memanggil API publik
            const response = await eventApi.getWorshipSchedules();
            
            // Sesuai blueprint, array data ada di properti 'data', dan info halaman di 'meta'
            setSchedules(response.data);
            setMeta(response.meta);
        } catch (error) {
            console.error("Gagal mengambil data jadwal", error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div style={{ padding: '20px' }}>Memuat jadwal...</div>;

    return (
        <div style={{ padding: '20px', fontFamily: 'sans-serif' }}>
            <h2>Jadwal Ibadah Raya (Publik)</h2>
            <table border="1" cellPadding="10" style={{ width: '100%', borderCollapse: 'collapse' }}>
                <thead style={{ backgroundColor: '#f0f0f0' }}>
                    <tr>
                        <th>Judul</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    {schedules.length > 0 ? schedules.map((item) => (
                        <tr key={item.id}>
                            <td>{item.title}</td>
                            <td>{item.location}</td>
                            <td>{item.event_date}</td>
                            <td>{item.start_time} - {item.end_time}</td>
                        </tr>
                    )) : (
                        <tr><td colSpan="4" style={{textAlign: 'center'}}>Belum ada jadwal diterbitkan.</td></tr>
                    )}
                </tbody>
            </table>
            {meta && <p>Halaman: {meta.current_page} dari {meta.last_page}</p>}
        </div>
    );
}