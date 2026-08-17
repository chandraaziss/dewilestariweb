@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 60px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div style="width: 100%; max-width: 480px; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #2e7d32; font-size: 24px; font-weight: bold; margin-bottom: 8px;">Daftar Akun Baru</h2>
            <p style="color: #666; font-size: 14px;">Buat akun untuk mengelola dan melihat semua pesanan Anda</p>
        </div>

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Google SSO Register Button -->
        <div style="margin-bottom: 20px;">
            <a href="/auth/google" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; background: #ffffff; color: #374151; border: 1.5px solid #d1d5db; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: bold; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.04);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#9ca3af';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#d1d5db';">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Daftar Instan dengan Google
            </a>
        </div>

        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <div style="flex: 1; border-bottom: 1px solid #e5e7eb;"></div>
            <span style="padding: 0 12px; color: #9ca3af; font-size: 12px; text-transform: uppercase; font-weight: bold;">Atau Daftar Form Manual</span>
            <div style="flex: 1; border-bottom: 1px solid #e5e7eb;"></div>
        </div>

        <form method="POST" action="/register">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Nomor HP / WhatsApp</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <!-- Address Section -->
            <div style="margin-bottom: 24px; padding: 16px; background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <label for="address" style="font-weight: bold; color: #166534; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">
                        📍 Alamat Utama
                    </label>
                    <span style="background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">⭐ UTAMA</span>
                </div>

                <div style="margin-bottom: 10px;">
                    <label style="font-size: 12px; font-weight: bold; color: #4b5563; display: block; margin-bottom: 4px;">Label Alamat:</label>
                    <select name="address_label" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; outline: none; background: white;">
                        <option value="Rumah (Utama)">🏠 Rumah (Alamat Utama)</option>
                        <option value="Kantor (Utama)">🏢 Kantor (Alamat Utama)</option>
                        <option value="Toko (Utama)">🏪 Toko (Alamat Utama)</option>
                        <option value="Lainnya (Utama)">📍 Lainnya</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;">
                    <button type="button" id="useCurrentLocationBtn" style="padding: 7px 12px; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        🎯 Gunakan Lokasi Saat Ini
                    </button>
                    <button type="button" id="toggleMapBtn" style="padding: 7px 12px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        🗺️ Pilih via Peta (OpenStreetMap)
                    </button>
                </div>

                <div id="registerMapContainer" style="display: none; margin-bottom: 12px; background: white; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                    <div style="display: flex; gap: 6px; margin-bottom: 6px;">
                        <input type="text" id="registerMapSearch" placeholder="Cari nama jalan / daerah..." style="flex: 1; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; outline: none;">
                        <button type="button" id="registerMapSearchBtn" style="padding: 7px 12px; background: #2e7d32; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer;">Cari</button>
                    </div>
                    <div id="registerMap" style="height: 220px; border-radius: 6px; border: 1px solid #d1d5db; overflow: hidden;"></div>
                    <small style="display: block; margin-top: 6px; color: #6b7280; font-size: 11px;">💡 Klik di peta atau geser penanda untuk memilih lokasi secara presisi.</small>
                </div>

                <textarea id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap pengiriman (Jalan, RT/RW, Kec, Kota/Kab)" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13.5px; outline: none; transition: border-color 0.2s; font-family: inherit; box-sizing: border-box; background: white;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">{{ old('address') }}</textarea>
            </div>

            <!-- Additional / Secondary Addresses Section -->
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-weight: bold; color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        📌 Alamat Tambahan (Opsional)
                    </span>
                    <button type="button" id="addExtraAddressBtn" style="padding: 6px 12px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                        ➕ Tambah Alamat Lain
                    </button>
                </div>
                <div id="extraAddressesWrapper" style="display: flex; flex-direction: column; gap: 12px;"></div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Kata Sandi (Min. 6 Karakter)</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <div style="margin-bottom: 26px;">
                <label for="password_confirmation" style="display: block; font-weight: bold; color: #4b5563; font-size: 13px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(46, 125, 50, 0.2);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 12px rgba(46, 125, 50, 0.3)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(46, 125, 50, 0.2)'">
                Daftar Akun Baru
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <p style="color: #6b7280; font-size: 14px; margin-bottom: 0;">
                Sudah punya akun? 
                <a href="/login" style="color: #2e7d32; font-weight: bold; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#1b5e20'" onmouseout="this.style.color='#2e7d32'">Masuk Ke Akun</a>
            </p>
        </div>
    </div>
</div>

<!-- Leaflet OpenStreetMap CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let regMap = null;
    let regMarker = null;
    const defaultCenter = [-6.8774, 107.5467]; // Cimahi/Bandung

    const toggleMapBtn = document.getElementById('toggleMapBtn');
    const mapContainer = document.getElementById('registerMapContainer');
    const useGPSBtn = document.getElementById('useCurrentLocationBtn');
    const addressInput = document.getElementById('address');
    const searchInput = document.getElementById('registerMapSearch');
    const searchBtn = document.getElementById('registerMapSearchBtn');

    function initMap(lat, lon) {
        if (!regMap && typeof L !== 'undefined') {
            regMap = L.map('registerMap').setView([lat, lon], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(regMap);

            regMarker = L.marker([lat, lon], { draggable: true }).addTo(regMap);

            regMarker.on('dragend', function (e) {
                const pos = regMarker.getLatLng();
                updateAddressFromCoords(pos.lat, pos.lng);
            });

            regMap.on('click', function (e) {
                setMapLocation(e.latlng.lat, e.latlng.lng, true);
            });
        } else if (regMap) {
            regMap.setView([lat, lon], 14);
            if (regMarker) regMarker.setLatLng([lat, lon]);
        }

        setTimeout(() => {
            if (regMap) regMap.invalidateSize();
        }, 200);
    }

    function setMapLocation(lat, lon, fetchAddress = true) {
        mapContainer.style.display = 'block';
        initMap(lat, lon);
        if (regMarker) regMarker.setLatLng([lat, lon]);
        if (regMap) regMap.setView([lat, lon], 15);
        if (fetchAddress) {
            updateAddressFromCoords(lat, lon);
        }
    }

    function updateAddressFromCoords(lat, lon) {
        addressInput.placeholder = 'Mengambil alamat dari peta...';
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                addressInput.value = data.display_name;
            }
        })
        .catch(() => {})
        .finally(() => {
            addressInput.placeholder = 'Masukkan alamat lengkap (Jalan, RT/RW, Kec, Kota/Kab)';
        });
    }

    toggleMapBtn.addEventListener('click', function () {
        if (mapContainer.style.display === 'none' || !mapContainer.style.display) {
            mapContainer.style.display = 'block';
            initMap(defaultCenter[0], defaultCenter[1]);
        } else {
            mapContainer.style.display = 'none';
        }
    });

    useGPSBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur Geolocation GPS.');
            return;
        }

        const originalText = useGPSBtn.innerHTML;
        useGPSBtn.innerHTML = '⏳ Mengambil lokasi...';
        useGPSBtn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                useGPSBtn.innerHTML = originalText;
                useGPSBtn.disabled = false;
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                setMapLocation(lat, lon, true);
            },
            function (error) {
                useGPSBtn.innerHTML = originalText;
                useGPSBtn.disabled = false;
                alert('Gagal mengambil lokasi saat ini. Pastikan izin lokasi (GPS) telah diizinkan di browser Anda.');
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    });

    if (searchBtn && searchInput) {
        const doSearch = function () {
            const query = searchInput.value.trim();
            if (!query) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    setMapLocation(lat, lon, false);
                    addressInput.value = data[0].display_name;
                } else {
                    alert('Alamat tidak ditemukan. Coba gunakan kata kunci lain.');
                }
            })
            .catch(() => alert('Gagal mencari lokasi. Silakan coba lagi.'));
        };

        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            doSearch();
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                doSearch();
            }
        });
    }

    // Dynamic Extra Addresses Handler
    const addExtraAddressBtn = document.getElementById('addExtraAddressBtn');
    const extraWrapper = document.getElementById('extraAddressesWrapper');
    let extraIndex = 0;

    if (addExtraAddressBtn && extraWrapper) {
        addExtraAddressBtn.addEventListener('click', function() {
            extraIndex++;
            const card = document.createElement('div');
            card.style.cssText = 'padding: 14px; background: #fffbeb; border-radius: 10px; border: 1px solid #fef3c7; position: relative;';
            card.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 12px; font-weight: bold; color: #b45309;">📌 Alamat Tambahan #${extraIndex}</div>
                    <button type="button" onclick="this.closest('div[style*=\\'padding\\']').remove()" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: bold; cursor: pointer;">🗑️ Hapus</button>
                </div>
                <div style="margin-bottom: 8px;">
                    <label style="font-size: 11px; font-weight: bold; color: #4b5563; display: block; margin-bottom: 4px;">Label Alamat:</label>
                    <select name="extra_addresses[${extraIndex}][label]" style="width: 100%; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; outline: none; background: white;">
                        <option value="Kantor">🏢 Alamat Kantor</option>
                        <option value="Rumah Ortuk/Kerabat">🏠 Alamat Rumah Ortut / Kerabat</option>
                        <option value="Toko / Gudang">🏪 Toko / Gudang</option>
                        <option value="Alamat Lain">📍 Alamat Lain</option>
                    </select>
                </div>
                <div>
                    <textarea name="extra_addresses[${extraIndex}][address]" rows="2" required placeholder="Masukkan rincian alamat..." style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12.5px; outline: none; font-family: inherit; box-sizing: border-box; background: white;"></textarea>
                </div>
            `;
            extraWrapper.appendChild(card);
        });
    }
});
</script>
@endsection
