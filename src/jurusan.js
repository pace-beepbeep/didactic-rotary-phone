import './style.css';
import { initScrollReveal } from './animation.js';

// Fungsi untuk logika dropdown navbar dan event listener lainnya
function setupUIListeners() {
    const dropdownLinks = document.querySelectorAll('.nav-link-dropdown');
    const closeAllDropdowns = () => {
        document.querySelectorAll('.dropdown-panel').forEach(panel => {
            panel.classList.remove('visible', 'opacity-100', 'translate-y-0');
            panel.classList.add('invisible', 'opacity-0', '-translate-y-2');
        });
    };
    dropdownLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const targetPanel = link.nextElementSibling;
            const isTargetPanelOpen = !targetPanel.classList.contains('invisible');
            closeAllDropdowns();
            if (!isTargetPanelOpen && targetPanel) {
                targetPanel.classList.remove('invisible', 'opacity-0', '-translate-y-2');
                targetPanel.classList.add('visible', 'opacity-100', 'translate-y-0');
            }
        });
    });
    document.addEventListener('click', () => closeAllDropdowns());
}

// Fungsi untuk memuat konten jurusan dari JSON dan menampilkannya
async function loadJurusanContent() {
    const params = new URLSearchParams(window.location.search);
    // Default ke 'rpl' jika tidak ada parameter id di URL
    const currentJurusanId = params.get('id') || 'rpl';

    const contentJurusan = document.getElementById('content-jurusan');
    const contentError = document.getElementById('content-error');
    
    try {
        const response = await fetch('http://localhost:5173/api/get_jurusan.php');
        if (!response.ok) throw new Error('Data tidak ditemukan');
        const allJurusanData = await response.json();

        populateSidebar(allJurusanData, currentJurusanId);

        const currentData = allJurusanData[currentJurusanId];
        if (currentData) {
            populateMainContent(currentData);
        } else {
            showError();
        }

    } catch (error) {
        showError();
        console.error('Gagal memuat data jurusan:', error);
    }

    function showError() {
        contentJurusan.classList.add('hidden');
        contentError.classList.remove('hidden');
    }
}

function populateSidebar(allData, currentId) {
    const sidebarList = document.getElementById('jurusan-list-sidebar');
    sidebarList.innerHTML = ''; 

    for (const id in allData) {
        const jurusan = allData[id];
        const li = document.createElement('li');
        const a = document.createElement('a');
        
        a.href = `/jurusan.html?id=${id}`;
        a.textContent = jurusan.nama;
        a.className = 'block px-3 py-2 rounded-md font-medium transition-colors duration-200';

        if (id === currentId) {
            a.classList.add('bg-purple-600', 'text-white');
        } else {
            a.classList.add('text-gray-700', 'hover:bg-gray-100');
        }
        
        li.appendChild(a);
        sidebarList.appendChild(li);
    }
}

function populateMainContent(data) {
    document.title = `${data.nama} - SMKN 1 Dlanggu`;
    document.getElementById('breadcrumb').textContent = `Home / Jurusan / ${data.nama}`;
    document.getElementById('nama-jurusan').textContent = data.nama;
    document.getElementById('deskripsi-jurusan').textContent = data.deskripsi;
    
    if (data.gallery && data.gallery.length > 0) {
        document.getElementById('gambar-utama').src = data.gallery[0];
    }

    const keahlianList = document.getElementById('keahlian-list');
    keahlianList.innerHTML = '';
    data.keahlian.forEach(skill => {
        const li = document.createElement('li');
        li.textContent = skill;
        keahlianList.appendChild(li);
    });

    const guruSection = document.getElementById('guru-section');
    const guruGrid = document.getElementById('guru-grid');
    guruGrid.innerHTML = '';

    if (data.guru && data.guru.length > 0) {
        guruSection.classList.remove('hidden');
        data.guru.forEach(guru => {
            const guruCard = document.createElement('div');
            guruCard.innerHTML = `
                <img src="${guru.foto}" alt="${guru.nama}" class="w-full h-48 object-cover rounded-lg mx-auto shadow-md mb-2">
                <p class="font-semibold text-sm text-gray-700">${guru.nama}</p>
            `;
            guruGrid.appendChild(guruCard);
        });
    } else {
        guruSection.classList.add('hidden');
    }
}

// ===================================
// INISIALISASI HALAMAN
// ===================================
document.addEventListener('DOMContentLoaded', async () => {
    // Jalankan setup UI yang statis terlebih dahulu
    setupUIListeners();

    // 1. Tunggu (await) sampai semua konten dinamis selesai dimuat
    await loadJurusanContent();

    // 2. Setelah konten dijamin ada, baru jalankan animasi
    initScrollReveal();
});