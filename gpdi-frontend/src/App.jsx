import React, { useState, useEffect } from 'react';
import PublicSchedules from './pages/PublicSchedules';
import RayonSchedules from './pages/RayonSchedules';

export default function App() {
    const [isLoggedIn, setIsLoggedIn] = useState(false);

    // Cek apakah ada token saat aplikasi pertama kali dimuat
    useEffect(() => {
        const token = localStorage.getItem('jwt_token');
        if (token) {
            setIsLoggedIn(true);
        }
    }, []);

    // Simulasi Login: Menyimpan dummy JWT Token ke localStorage
    const handleLogin = () => {
        // Dalam implementasi aslinya, token ini didapat dari response User Service saat login.
        // Ini adalah dummy token untuk memastikan header Authorization aktif.
        const dummyToken = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.dummy_payload.dummy_signature";
        localStorage.setItem('jwt_token', dummyToken);
        setIsLoggedIn(true);
        alert('Simulasi Login Berhasil! JWT Token telah diset.');
        window.location.reload(); // Refresh agar state dan data terefresh
    };

    // Simulasi Logout: Menghapus token dari localStorage
    const handleLogout = () => {
        localStorage.removeItem('jwt_token');
        setIsLoggedIn(false);
        alert('Logout Berhasil! JWT Token dihapus.');
        window.location.reload();
    };

    return (
        <div style={{ fontFamily: 'Arial, sans-serif', margin: '0 auto', maxWidth: '1000px', padding: '20px' }}>
            <header style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '2px solid #000', paddingBottom: '10px', marginBottom: '20px' }}>
                <h1>⛪ GPdI Sibulele - Portal Microservice</h1>
                <div>
                    {isLoggedIn ? (
                        <div>
                            <span style={{ marginRight: '15px', color: 'green', fontWeight: 'bold' }}>Status: Logged In (Ketua Rayon)</span>
                            <button onClick={handleLogout} style={{ padding: '8px 15px', cursor: 'pointer', backgroundColor: '#dc3545', color: '#fff', border: 'none' }}>Logout</button>
                        </div>
                    ) : (
                        <div>
                            <span style={{ marginRight: '15px', color: 'red', fontWeight: 'bold' }}>Status: Public (Belum Login)</span>
                            <button onClick={handleLogin} style={{ padding: '8px 15px', cursor: 'pointer', backgroundColor: '#28a745', color: '#fff', border: 'none' }}>Simulasi Login</button>
                        </div>
                    )}
                </div>
            </header>

            <main>
                {/* Halaman Publik selalu dirender */}
                <PublicSchedules />
                
                {/* Halaman Internal (Rayon) disimulasikan tampil atau aktif jika sudah login */}
                <div style={{ marginTop: '40px' }}>
                    {isLoggedIn ? (
                        <RayonSchedules />
                    ) : (
                        <div style={{ padding: '20px', backgroundColor: '#ffeeba', border: '1px solid #ffc107', color: '#856404' }}>
                            <p><strong>Akses Ditolak:</strong> Anda harus login sebagai Ketua Rayon untuk melihat Manajemen Jadwal Rayon.</p>
                        </div>
                    )}
                </div>
            </main>
        </div>
    );
}