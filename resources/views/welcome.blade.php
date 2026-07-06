<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reservation - UIN SGD Bandung</title>
    
    <!-- Vite Assets (Tailwind CSS v4) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for lightweight state management -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios for HTTP Requests -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    
    <!-- Lucide Icons for premium visual icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts: Instrument Sans (bawaan) & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', sans-serif;
            background-color: #f8fafc;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="appState" class="text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Premium Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-30 px-4 py-3.5 shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3 cursor-pointer" @click="activeTab = 'home'">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Room Reservation</h1>
                    <p class="text-xs text-slate-500 font-medium">UIN Sunan Gunung Djati Bandung</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- User Profile Dropdown Button (Airbnb Style) -->
                <template x-if="isLoggedIn">
                    <div class="relative" x-data="{ showUserMenu: false }" @click.away="showUserMenu = false">
                        <button @click="showUserMenu = !showUserMenu" class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 rounded-full hover:shadow-md transition-all bg-white relative">
                            <i data-lucide="menu" class="w-4 h-4 text-slate-600"></i>
                            <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-extrabold text-xs shadow-inner">
                                <span x-text="user.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <span x-show="approvals.length > 0" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full border border-white"></span>
                        </button>

                        <!-- Floating Dropdown Menu -->
                        <div x-show="showUserMenu" x-cloak class="absolute right-0 mt-2 w-60 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50">
                            <!-- User Info Header -->
                            <div class="px-4 py-2 border-b border-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sudah Masuk</p>
                                <p class="text-sm font-extrabold text-slate-900 truncate" x-text="user.name"></p>
                                <p class="text-[9px] font-extrabold text-indigo-600 uppercase tracking-wider mt-0.5" x-text="user.role.replace('_', ' ')"></p>
                            </div>
                            
                            <!-- Menu Links -->
                            <div class="py-1">
                                <button @click="activeTab = 'home'; showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-2">
                                    <i data-lucide="home" class="w-4 h-4 text-slate-400"></i> Beranda / Cari Aula
                                </button>
                                <button @click="activeTab = 'history'; showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-2">
                                    <i data-lucide="history" class="w-4 h-4 text-slate-400"></i> Histori Reservasi
                                </button>
                                
                                <template x-if="['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'].includes(user.role)">
                                    <button @click="activeTab = 'approvals'; showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-2 relative">
                                        <i data-lucide="clipboard-check" class="w-4 h-4 text-slate-400"></i> Persetujuan (Approvals)
                                        <span x-show="approvals.length > 0" class="absolute right-4 w-2 h-2 bg-amber-500 rounded-full"></span>
                                    </button>
                                </template>
                                
                                <template x-if="['super_admin', 'superadmin'].includes(user.role)">
                                    <button @click="activeTab = 'rooms'; showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-2">
                                        <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i> Kelola Ruangan
                                    </button>
                                </template>

                                <template x-if="['super_admin', 'superadmin'].includes(user.role)">
                                    <button @click="activeTab = 'admins'; showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-2">
                                        <i data-lucide="users" class="w-4 h-4 text-slate-400"></i> Kelola Admin
                                    </button>
                                </template>
                            </div>

                            <div class="border-t border-slate-50 my-1"></div>

                            <div class="py-1">
                                <button @click="logout(); showUserMenu = false" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-xs font-bold text-rose-600 flex items-center gap-2">
                                    <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i> Keluar Akun
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="!isLoggedIn">
                    <button @click="showLoginModal = true" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk Akun
                    </button>
                </template>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-6xl w-full mx-auto p-4 sm:p-6 pb-24 sm:pb-8">
        
        <!-- Tab 1: Home (Redesain ala Airbnb) -->
        <div x-show="activeTab === 'home'" x-cloak class="space-y-8">
            
            <!-- Heading & Search Bar -->
            <div class="space-y-6">
                <div class="text-center space-y-2 max-w-xl mx-auto">
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">Temukan Aula Terbaik</h2>
                    <p class="text-sm sm:text-base text-slate-500 font-medium">Reservasi mudah untuk rapat, kuliah umum, seminar, dan acara kampus lainnya.</p>
                </div>

                <!-- Airbnb style Search Bar -->
                <div class="max-w-4xl mx-auto bg-white rounded-3xl sm:rounded-full shadow-lg border border-slate-100 p-2.5 flex flex-col md:flex-row items-center gap-2 justify-between">
                    <!-- Location Field -->
                    <div class="flex-grow flex items-center gap-3 px-6 py-2 w-full border-b md:border-b-0 md:border-r border-slate-100">
                        <div class="text-indigo-600">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-grow">
                            <label class="block text-[9px] uppercase font-extrabold tracking-wider text-slate-400">Pencarian Aula</label>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama aula atau fakultas..." class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none placeholder-slate-400">
                        </div>
                    </div>

                    <!-- Date Picker Field -->
                    <div class="flex-grow flex items-center gap-3 px-6 py-2 w-full border-b md:border-b-0 md:border-r border-slate-100">
                        <div class="text-indigo-600">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-grow">
                            <label class="block text-[9px] uppercase font-extrabold tracking-wider text-slate-400">Tanggal Sewa</label>
                            <input type="date" x-model="selectedDate" @change="buildTimeSlots()" class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none">
                        </div>
                    </div>

                    <!-- Capacity Field -->
                    <div class="flex-grow flex items-center gap-3 px-6 py-2 w-full">
                        <div class="text-indigo-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-grow">
                            <label class="block text-[9px] uppercase font-extrabold tracking-wider text-slate-400">Kapasitas Kursi</label>
                            <input type="number" x-model="searchCapacity" placeholder="Minimal Kursi" class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-800 focus:outline-none placeholder-slate-400">
                        </div>
                    </div>

                    <!-- Search Action Button -->
                    <button class="w-full md:w-auto p-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl md:rounded-full transition-all shrink-0 shadow-md shadow-indigo-100 flex items-center justify-center gap-2 md:gap-0 font-bold text-sm">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        <span class="md:hidden">Cari Sekarang</span>
                    </button>
                </div>
            </div>

            <!-- Airbnb Categories -->
            <div class="flex items-center justify-center gap-8 border-b border-slate-100 pb-3 max-w-2xl mx-auto overflow-x-auto scrollbar-none shrink-0">
                <button 
                    @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' ? 'text-indigo-600 border-b-2 border-indigo-600 font-extrabold pb-2' : 'text-slate-400 hover:text-slate-600 font-semibold pb-2'"
                    class="flex flex-col items-center gap-1.5 transition-all text-[11px] uppercase tracking-wider shrink-0">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    <span>Semua Aula</span>
                </button>
                <button 
                    @click="selectedCategory = 'fakultas'"
                    :class="selectedCategory === 'fakultas' ? 'text-indigo-600 border-b-2 border-indigo-600 font-extrabold pb-2' : 'text-slate-400 hover:text-slate-600 font-semibold pb-2'"
                    class="flex flex-col items-center gap-1.5 transition-all text-[11px] uppercase tracking-wider shrink-0">
                    <i data-lucide="building" class="w-5 h-5"></i>
                    <span>Tingkat Fakultas</span>
                </button>
                <button 
                    @click="selectedCategory = 'universitas'"
                    :class="selectedCategory === 'universitas' ? 'text-indigo-600 border-b-2 border-indigo-600 font-extrabold pb-2' : 'text-slate-400 hover:text-slate-600 font-semibold pb-2'"
                    class="flex flex-col items-center gap-1.5 transition-all text-[11px] uppercase tracking-wider shrink-0">
                    <i data-lucide="landmark" class="w-5 h-5"></i>
                    <span>Tingkat Universitas</span>
                </button>
                <button 
                    @click="selectedCategory = 'kemahasiswaan'"
                    :class="selectedCategory === 'kemahasiswaan' ? 'text-indigo-600 border-b-2 border-indigo-600 font-extrabold pb-2' : 'text-slate-400 hover:text-slate-600 font-semibold pb-2'"
                    class="flex flex-col items-center gap-1.5 transition-all text-[11px] uppercase tracking-wider shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    <span>Tingkat Kemahasiswaan</span>
                </button>
            </div>

            <!-- Section 0: Semua Aula (Hanya tampil saat Kategori "Semua Aula" aktif) -->
            <div x-show="selectedCategory === 'all' && filteredRooms().length > 0" class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Semua Aula UIN Bandung</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="filteredRooms().length + ' aula ditemukan'"></span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <template x-for="room in filteredRooms()" :key="'all-' + room.id">
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group cursor-pointer relative" @click="selectRoom(room.id)">
                            
                            <!-- Room Image Section -->
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                <img :src="getRoomImages(room)[0]" crossorigin="anonymous" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Foto Aula">
                            </div>

                            <!-- Room Details Section -->
                            <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="room.nama_ruangan"></h4>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate" x-text="room.tingkat === 'fakultas' ? (room.faculty ? room.faculty.nama_fakultas : 'Tingkat Fakultas') : (room.tingkat === 'kemahasiswaan' ? 'Tingkat Kemahasiswaan' : 'Tingkat Universitas')"></p>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-2" x-text="room.deskripsi || 'Fasilitas lengkap untuk berbagai kegiatan kampus.'"></p>
                                </div>

                                <div class="pt-3 border-t border-slate-50 flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-indigo-600 flex items-center gap-1">
                                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                        <span x-text="room.kapasitas + ' Kursi'"></span>
                                    </span>
                                    <span :class="room.eksternal_diizinkan ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full'" x-text="room.eksternal_diizinkan ? 'Pihak Luar Diizinkan' : 'Khusus Internal'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 1: Aula Tingkat Universitas -->
            <div x-show="filteredRooms().filter(r => r.tingkat === 'universitas').length > 0" class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Aula Tingkat Universitas</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="filteredRooms().filter(r => r.tingkat === 'universitas').length + ' aula ditemukan'"></span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <template x-for="room in filteredRooms().filter(r => r.tingkat === 'universitas')" :key="'univ-' + room.id">
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group cursor-pointer relative" @click="selectRoom(room.id)">
                            
                            <!-- Room Image Section -->
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                <img :src="getRoomImages(room)[0]" crossorigin="anonymous" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Foto Aula">
                            </div>

                            <!-- Room Details Section -->
                            <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="room.nama_ruangan"></h4>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Tingkat Universitas</p>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-2" x-text="room.deskripsi || 'Fasilitas lengkap untuk berbagai kegiatan kampus.'"></p>
                                </div>

                                <div class="pt-3 border-t border-slate-50 flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-indigo-600 flex items-center gap-1">
                                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                        <span x-text="room.kapasitas + ' Kursi'"></span>
                                    </span>
                                    <span :class="room.eksternal_diizinkan ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full'" x-text="room.eksternal_diizinkan ? 'Pihak Luar Diizinkan' : 'Khusus Internal'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 2: Aula Tingkat Fakultas -->
            <div x-show="filteredRooms().filter(r => r.tingkat === 'fakultas').length > 0" class="space-y-6 pt-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Aula Tingkat Fakultas / Lembaga</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="filteredRooms().filter(r => r.tingkat === 'fakultas').length + ' aula ditemukan'"></span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <template x-for="room in filteredRooms().filter(r => r.tingkat === 'fakultas')" :key="'fak-' + room.id">
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group cursor-pointer relative" @click="selectRoom(room.id)">
                            
                            <!-- Room Image Section -->
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                <img :src="getRoomImages(room)[0]" crossorigin="anonymous" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Foto Aula">
                            </div>

                            <!-- Room Details Section -->
                            <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="room.nama_ruangan"></h4>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate" x-text="room.faculty ? room.faculty.nama_fakultas : 'Tingkat Fakultas'"></p>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-2" x-text="room.deskripsi || 'Fasilitas lengkap untuk berbagai kegiatan kampus.'"></p>
                                </div>

                                <div class="pt-3 border-t border-slate-50 flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-indigo-600 flex items-center gap-1">
                                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                        <span x-text="room.kapasitas + ' Kursi'"></span>
                                    </span>
                                    <span :class="room.eksternal_diizinkan ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full'" x-text="room.eksternal_diizinkan ? 'Pihak Luar Diizinkan' : 'Khusus Internal'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 3: Aula Tingkat Kemahasiswaan -->
            <div x-show="filteredRooms().filter(r => r.tingkat === 'kemahasiswaan').length > 0" class="space-y-6 pt-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Aula Tingkat Kemahasiswaan</h3>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="filteredRooms().filter(r => r.tingkat === 'kemahasiswaan').length + ' aula ditemukan'"></span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <template x-for="room in filteredRooms().filter(r => r.tingkat === 'kemahasiswaan')" :key="'mhs-' + room.id">
                        <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group cursor-pointer relative" @click="selectRoom(room.id)">
                            
                            <!-- Room Image Section -->
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                <img :src="getRoomImages(room)[0]" crossorigin="anonymous" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Foto Aula">
                            </div>

                            <!-- Room Details Section -->
                            <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate" x-text="room.nama_ruangan"></h4>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Tingkat Kemahasiswaan</p>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-2" x-text="room.deskripsi || 'Fasilitas lengkap untuk berbagai kegiatan kampus.'"></p>
                                </div>

                                <div class="pt-3 border-t border-slate-50 flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-indigo-600 flex items-center gap-1">
                                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                        <span x-text="room.kapasitas + ' Kursi'"></span>
                                    </span>
                                    <span :class="room.eksternal_diizinkan ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full'" x-text="room.eksternal_diizinkan ? 'Pihak Luar Diizinkan' : 'Khusus Internal'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Room Detail Modal (Kalender Interaktif & Timeline dipindah ke sini) -->
        <div x-show="showRoomDetailsModal && detailedRoom" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div @click.away="showRoomDetailsModal = false" class="bg-white rounded-3xl max-w-4xl w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh] flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                    <div>
                        <span :class="[
                            detailedRoom && detailedRoom.tingkat === 'fakultas' ? 'bg-amber-100 text-amber-800' : '',
                            detailedRoom && detailedRoom.tingkat === 'universitas' ? 'bg-blue-100 text-blue-800' : '',
                            detailedRoom && detailedRoom.tingkat === 'kemahasiswaan' ? 'bg-purple-100 text-purple-800' : ''
                        ]" class="px-2.5 py-1 rounded-xl text-[10px] uppercase font-extrabold tracking-wide" x-text="detailedRoom ? detailedRoom.tingkat : ''"></span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-1" x-text="detailedRoom ? detailedRoom.nama_ruangan : ''"></h3>
                    </div>
                    <button @click="showRoomDetailsModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start overflow-y-auto">
                    <!-- Left Side: Room details & Image -->
                    <div class="md:col-span-6 space-y-4">
                        <div class="relative aspect-[16/10] w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-100 shadow-inner group">
                            <img :src="getRoomImages(detailedRoom)[currentImageIndex]" crossorigin="anonymous" class="w-full h-full object-cover transition-all duration-300" alt="Gambar Aula">
                            
                            <!-- Left Arrow Button -->
                            <button 
                                x-show="getRoomImages(detailedRoom).length > 1" 
                                @click.stop="prevImage(getRoomImages(detailedRoom))" 
                                class="absolute left-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-md transition-all active:scale-90 hover:scale-105 z-10 flex items-center justify-center">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            
                            <!-- Right Arrow Button -->
                            <button 
                                x-show="getRoomImages(detailedRoom).length > 1" 
                                @click.stop="nextImage(getRoomImages(detailedRoom))" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-md transition-all active:scale-90 hover:scale-105 z-10 flex items-center justify-center">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>

                            <!-- Dot Indicators -->
                            <div x-show="getRoomImages(detailedRoom).length > 1" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                                <template x-for="(img, idx) in getRoomImages(detailedRoom)" :key="idx">
                                    <span 
                                        :class="currentImageIndex === idx ? 'bg-indigo-600 w-2 h-2 scale-110 shadow-sm' : 'bg-slate-300 w-1.5 h-1.5'" 
                                        class="rounded-full transition-all duration-300">
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Fasilitas Utama</h4>
                            <p class="text-xs font-semibold text-slate-500 leading-relaxed" x-text="detailedRoom ? detailedRoom.fasilitas : '-'"></p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Deskripsi Aula</h4>
                            <p class="text-xs font-semibold text-slate-500 leading-relaxed text-justify" x-text="detailedRoom ? detailedRoom.deskripsi : '-'"></p>
                        </div>
                        <template x-if="detailedRoom && detailedRoom.pic_nama">
                            <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-2xl text-xs font-semibold text-indigo-900 space-y-1">
                                <p class="text-[10px] text-indigo-500 uppercase font-bold tracking-wider">Contact Person:</p>
                                <p x-text="'Nama: ' + detailedRoom.pic_nama"></p>
                                <p x-text="'Telp/WA: ' + detailedRoom.pic_telepon"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Right Side: Calendar & Timeline -->
                    <div class="md:col-span-6 space-y-6">
                        <!-- Calendar -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-slate-900 capitalize" x-text="getMonthYearString()"></h4>
                                <div class="flex items-center gap-2">
                                    <button @click="prevMonth()" class="p-1.5 hover:bg-slate-200 rounded-full transition-colors">
                                        <i data-lucide="chevron-left" class="w-4 h-4 text-indigo-600"></i>
                                    </button>
                                    <button @click="nextMonth()" class="p-1.5 hover:bg-slate-200 rounded-full transition-colors">
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-indigo-600"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Availability dots label -->
                            <div class="flex items-center gap-3 text-[10px] font-bold text-slate-500">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Kosong</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> Pending</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-rose-500 rounded-full"></span> Terisi</span>
                            </div>

                            <div class="grid grid-cols-7 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                            </div>

                            <div class="grid grid-cols-7 gap-y-1 text-center text-xs font-semibold">
                                <template x-for="blank in calendarBlankDays">
                                    <span class="py-2"></span>
                                </template>
                                <template x-for="day in calendarDaysInMonth" :key="day.dateString">
                                    <div class="relative py-0.5 flex items-center justify-center">
                                        <button 
                                            @click="selectDate(day.dateString)"
                                            :class="[
                                                selectedDate === day.dateString ? 'bg-indigo-900 text-white shadow-md shadow-indigo-950' : 'text-slate-800 hover:bg-slate-200',
                                                day.isToday ? 'border border-indigo-400' : ''
                                            ]"
                                            class="w-8 h-8 rounded-full flex flex-col items-center justify-center transition-all relative">
                                            <span x-text="day.dayNum"></span>
                                            <div class="absolute bottom-1 flex gap-0.5 justify-center w-full">
                                                <template x-for="status in day.statuses">
                                                    <span 
                                                        :class="[
                                                            status === 'approved' ? 'bg-rose-500' : '',
                                                            status === 'pending' ? 'bg-amber-500' : '',
                                                            status === 'available' ? 'bg-emerald-500' : ''
                                                        ]"
                                                        class="w-0.5 h-0.5 rounded-full">
                                                    </span>
                                                </template>
                                            </div>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Timeline Status -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">Jadwal Ketersediaan</span>
                                <span class="text-xs font-bold text-slate-800" x-text="formatDateIndo(selectedDate)"></span>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="slot in timeSlots" :key="slot.time">
                                    <div 
                                        :class="[
                                            slot.status === 'approved' ? 'bg-rose-50/50 border-rose-100' : '',
                                            slot.status === 'pending' ? 'bg-amber-50/50 border-amber-100' : '',
                                            slot.status === 'available' ? 'bg-emerald-50/50 border-emerald-100/70' : ''
                                        ]"
                                        class="flex items-center justify-between p-2.5 border rounded-xl text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900" x-text="slot.time"></span>
                                            <div class="w-[1px] h-4 bg-slate-200"></div>
                                            <span 
                                                :class="[
                                                    slot.status === 'approved' ? 'bg-rose-100 text-rose-800' : '',
                                                    slot.status === 'pending' ? 'bg-amber-100 text-amber-800' : '',
                                                    slot.status === 'available' ? 'bg-emerald-100 text-emerald-800' : ''
                                                ]"
                                                class="px-2 py-0.5 rounded-lg font-bold" 
                                                x-text="slot.statusLabel">
                                            </span>
                                        </div>
                                        <span class="font-bold text-slate-600 text-right truncate max-w-[150px]" x-text="slot.status === 'approved' ? slot.reservation.tujuan : (slot.status === 'pending' ? 'Sedang Diajukan' : 'Tersedia')"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 shrink-0">
                    <button 
                        @click="showRoomDetailsModal = false; openBookingModal()"
                        class="w-full py-3.5 bg-indigo-900 text-white rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-indigo-950 shadow-lg shadow-indigo-900/20 active:scale-95 transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        Ajukan Reservasi untuk Aula Ini
                    </button>
                </div>
            </div>
        </div>



        <!-- Tab 3: History (Riwayat Pemesanan & Unduh PDF) -->
        <div x-show="activeTab === 'history'" x-cloak class="space-y-6">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900">Histori Reservasi</h2>
                <p class="text-sm text-slate-500 font-medium">Kelola dan pantau pengajuan reservasi ruangan Anda.</p>
            </div>

            <template x-if="reservations.length === 0">
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm text-center space-y-3">
                    <div class="p-4 bg-indigo-50 text-indigo-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto">
                        <i data-lucide="folder-open" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum Ada Histori</h3>
                    <p class="text-sm text-slate-500 font-medium max-w-sm mx-auto">Jadwal pengajuan peminjaman ruangan Anda akan muncul di halaman ini.</p>
                </div>
            </template>

            <template x-if="reservations.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="res in reservations" :key="res.id">
                        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-xl" x-text="res.room ? res.room.nama_ruangan : 'Ruangan'"></span>
                                    
                                    <!-- Status Badge -->
                                    <span 
                                        :class="[
                                            res.status_approval === 'approved' ? 'bg-emerald-100 text-emerald-800' : '',
                                            res.status_approval === 'pending' ? 'bg-amber-100 text-amber-800' : '',
                                            res.status_approval === 'rejected' ? 'bg-rose-100 text-rose-800' : ''
                                        ]"
                                        class="px-2.5 py-1 rounded-xl text-xs font-extrabold uppercase tracking-wide" 
                                        x-text="res.status_approval">
                                    </span>
                                </div>
                                
                                <h3 class="font-extrabold text-slate-900 text-base" x-text="res.tujuan"></h3>
                                
                                <template x-if="['admin_fakultas', 'admin_universitas', 'admin_kemahasiswaan'].includes(user.role)">
                                    <div class="flex items-center gap-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50/50 px-2 py-1 rounded-lg w-fit">
                                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                        <span x-text="'Peminjam: ' + (res.user ? res.user.name : '-')"></span>
                                    </div>
                                </template>
                                
                                <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                        <span x-text="formatDateIndo(res.tanggal_mulai.split(' ')[0])"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                        <span x-text="formatTimeOnly(res.tanggal_mulai) + ' - ' + formatTimeOnly(res.tanggal_selesai)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-3 flex flex-col gap-2 w-full">
                                <div class="flex gap-2 w-full">
                                    <template x-if="res.status_approval === 'approved'">
                                        <button 
                                            @click="downloadPDF(res.id)" 
                                            class="flex-grow flex items-center justify-center gap-1.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm active:scale-95 transition-all animate-pulse">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                            Unduh Surat Izin (PDF)
                                        </button>
                                    </template>
                                    <template x-if="res.proposal_file">
                                        <button 
                                            @click="downloadProposalFile(res.id)" 
                                            class="flex-grow flex items-center justify-center gap-1.5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-100 rounded-xl text-xs font-bold shadow-sm active:scale-95 transition-all">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                            Berkas Proposal (PDF)
                                        </button>
                                    </template>
                                </div>
                                <template x-if="res.status_approval === 'pending'">
                                    <div class="space-y-2 w-full">
                                        <span class="text-xs font-semibold text-slate-400 flex items-center gap-1.5">
                                            <i data-lucide="loader" class="w-4 h-4 animate-spin text-indigo-500"></i>
                                            Menunggu Persetujuan <span x-text="res.approver_role ? res.approver_role.replace('_', ' ').toUpperCase() : 'Admin'"></span>
                                        </span>
                                        <template x-if="res.room && res.room.pic_nama">
                                            <div class="p-3 bg-indigo-50/50 border border-indigo-100 rounded-2xl text-xs font-semibold text-indigo-800 space-y-1">
                                                <p class="text-[10px] text-indigo-500 uppercase font-bold tracking-wider">Hubungi Contact Person:</p>
                                                <p x-text="'Nama: ' + res.room.pic_nama"></p>
                                                <p x-text="'Telp: ' + res.room.pic_telepon"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="res.status_approval === 'rejected'">
                                    <div class="p-2.5 bg-rose-50 border border-rose-100 rounded-xl w-full text-xs font-semibold text-rose-800">
                                        <strong>Alasan Ditolak:</strong> <span x-text="res.alasan_penolakan || 'Tidak ada alasan.'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Tab 4: Approvals (Khusus Peran Admin Fakultas) -->
        <div x-show="activeTab === 'approvals'" x-cloak class="space-y-6">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900">Persetujuan Reservasi</h2>
                <p class="text-sm text-slate-500 font-medium">Halaman khusus Admin Fakultas untuk memverifikasi dan menyetujui pengajuan.</p>
            </div>

            <template x-if="approvals.length === 0">
                <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm text-center space-y-3">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto">
                        <i data-lucide="check-check" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Semua Beres!</h3>
                    <p class="text-sm text-slate-500 font-medium max-w-sm mx-auto">Tidak ada pengajuan reservasi pending yang memerlukan persetujuan saat ini.</p>
                </div>
            </template>

            <template x-if="approvals.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="app in approvals" :key="app.id">
                        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-xl" x-text="app.room ? app.room.nama_ruangan : 'Ruangan'"></span>
                                    <span class="text-xs font-bold text-slate-400" x-text="'Oleh: ' + app.user.name"></span>
                                </div>
                                
                                <h3 class="font-extrabold text-slate-900 text-base" x-text="app.tujuan"></h3>

                                <!-- Peminjam details, instansi, proposal deskripsi -->
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl space-y-1.5 text-xs font-semibold text-slate-600">
                                    <p><strong class="text-slate-800">Peminjam:</strong> <span x-text="app.user.name + ' (' + (app.user.tipe_user === 'internal' ? 'Internal - ' + (app.user.nim_nip || '-') : 'Eksternal') + ')'"></span></p>
                                    <p><strong class="text-slate-800">No Telp:</strong> <span x-text="app.user.no_telepon || '-'"></span></p>
                                    <p><strong class="text-slate-800">Instansi:</strong> <span x-text="app.instansi || '-'"></span></p>
                                    <p><strong class="text-slate-800">Detail Acara:</strong> <span x-text="app.deskripsi_acara || '-'"></span></p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                        <span x-text="formatDateIndo(app.tanggal_mulai.split(' ')[0])"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                        <span x-text="formatTimeOnly(app.tanggal_mulai) + ' - ' + formatTimeOnly(app.tanggal_selesai)"></span>
                                    </div>
                                </div>

                                <template x-if="app.proposal_file">
                                    <button 
                                        @click="downloadProposalFile(app.id)" 
                                        class="w-full flex items-center justify-center gap-1.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-100 rounded-xl text-xs font-bold transition-all">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Unduh Berkas Proposal (PDF)
                                    </button>
                                </template>
                            </div>

                            <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                                <button 
                                    @click="openApprovalModal(app)" 
                                    class="flex-grow py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95">
                                    Setujui & TTD
                                </button>
                                <button 
                                    @click="rejectReservation(app.id)" 
                                    class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-100 transition-all active:scale-95">
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Tab 5: Rooms (Khusus Peran Superadmin untuk kelola ruangan) -->
        <div x-show="activeTab === 'rooms'" x-cloak class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-slate-900">Kelola Ruangan</h2>
                    <p class="text-sm text-slate-500 font-medium">Halaman khusus Superadmin untuk menambah, mengedit, atau menghapus data ruangan.</p>
                </div>
                <button 
                    @click="showAddRoomModal = true" 
                    class="px-4 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Ruangan Baru
                </button>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold text-xs uppercase tracking-wider">
                                <th class="p-4">Nama Ruangan</th>
                                <th class="p-4">Kapasitas</th>
                                <th class="p-4">Fasilitas</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            <template x-for="room in rooms" :key="room.id">
                                <tr>
                                    <td class="p-4 text-slate-950 font-bold" x-text="room.nama_ruangan"></td>
                                    <td class="p-4" x-text="room.kapasitas + ' Kursi'"></td>
                                    <td class="p-4 text-xs font-medium text-slate-500" x-text="room.fasilitas"></td>
                                    <td class="p-4">
                                        <span 
                                            :class="room.status_aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'" 
                                            class="px-2.5 py-1 rounded-xl text-xs uppercase font-extrabold tracking-wide" 
                                            x-text="room.status_aktif ? 'Aktif' : 'Nonaktif'">
                                        </span>
                                    </td>
                                    <td class="p-4 text-right flex items-center justify-end gap-1.5">
                                        <button @click="openEditRoomModal(room)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteRoom(room.id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 6: Account (Informasi Akun & Keluar) -->


        <!-- Tab 7: Admins (Khusus Peran Superadmin untuk kelola admin) -->
        <div x-show="activeTab === 'admins'" x-cloak class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-2xl font-extrabold text-slate-900">Kelola Admin</h2>
                    <p class="text-sm text-slate-500 font-medium">Halaman khusus Superadmin untuk mengelola akun administrator.</p>
                </div>
                <button 
                    @click="newAdmin = { name: '', email: '', password: '', role: 'admin_fakultas', fakultas_id: '', room_ids: [] }; showAddAdminModal = true" 
                    class="px-4 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Admin Baru
                </button>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold text-xs uppercase tracking-wider">
                                <th class="p-4">Nama</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Role / Peran</th>
                                <th class="p-4">Cakupan Kelola</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            <template x-for="adm in admins" :key="adm.id">
                                <tr>
                                    <td class="p-4 text-slate-950 font-bold" x-text="adm.name"></td>
                                    <td class="p-4" x-text="adm.email"></td>
                                    <td class="p-4 text-xs">
                                        <span 
                                            :class="[
                                                adm.role === 'admin_fakultas' ? 'bg-amber-100 text-amber-800' : '',
                                                adm.role === 'admin_universitas' ? 'bg-blue-100 text-blue-800' : '',
                                                adm.role === 'admin_bisnis' ? 'bg-purple-100 text-purple-800' : '',
                                                adm.role === 'admin_kemahasiswaan' ? 'bg-rose-100 text-rose-800' : ''
                                            ]"
                                            class="px-2.5 py-1 rounded-xl uppercase font-extrabold tracking-wide" 
                                            x-text="adm.role.replace('_', ' ')">
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs font-semibold text-slate-500">
                                        <template x-if="adm.role === 'admin_fakultas'">
                                            <span x-text="adm.faculty ? adm.faculty.nama_fakultas : 'Fakultas Belum Dipilih'"></span>
                                        </template>
                                        <template x-if="['admin_universitas', 'admin_kemahasiswaan'].includes(adm.role)">
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                <template x-for="rm in adm.rooms" :key="rm.id">
                                                    <span class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded text-[10px]" x-text="rm.nama_ruangan"></span>
                                                </template>
                                                <template x-if="!adm.rooms || adm.rooms.length === 0">
                                                    <span class="text-rose-500">Tidak ada aula di-assign</span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="adm.role === 'admin_bisnis'">
                                            <span>Semua Aula Universitas (Sabtu/Minggu & Eksternal)</span>
                                        </template>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button @click="openEditAdminModal(adm)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="deleteAdmin(adm.id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>



    <!-- Login Modal (With Quick Login Peran Shortcuts) -->
    <div x-show="showLoginModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showLoginModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Masuk Aplikasi</h3>
                <button @click="showLoginModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>



            <!-- Manual Login Form -->
            <form @submit.prevent="login()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">NIM atau Alamat Email</label>
                    <input type="text" x-model="loginEmail" placeholder="Masukkan NIM atau Email" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kata Sandi</label>
                    <input type="password" x-model="loginPassword" placeholder="••••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-indigo-100">
                    Masuk
                </button>
            </form>
            <div class="text-center pt-2">
                <button @click="showLoginModal = false; showRegisterModal = true" class="text-xs text-indigo-600 hover:underline font-bold">Belum punya akun? Daftar di sini</button>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div x-show="showBookModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showBookModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Formulir Peminjaman Cepat</h3>
                <button @click="showBookModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="submitReservation" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tipe Peminjam</label>
                        <select x-model="newReservation.tipe_peminjam" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                            <option value="internal">Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Instansi / Organisasi</label>
                        <input type="text" x-model="newReservation.instansi" placeholder="Contoh: HIMA IF" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Ruangan Terpilih</label>
                    <select x-model="newReservation.room_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <template x-for="room in rooms.filter(r => newReservation.tipe_peminjam !== 'eksternal' || r.eksternal_diizinkan)" :key="room.id">
                            <option :value="room.id" x-text="room.nama_ruangan" :selected="room.id === newReservation.room_id"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tanggal Kegiatan</label>
                        <input type="date" x-model="newReservation.tanggal" readonly class="w-full p-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tujuan Peminjaman</label>
                        <input type="text" x-model="newReservation.tujuan" placeholder="Contoh: Rapat HIMA" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Waktu Mulai</label>
                        <input type="time" x-model="newReservation.waktu_mulai" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Waktu Selesai</label>
                        <input type="time" x-model="newReservation.waktu_selesai" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Detail Proposal / Deskripsi Acara</label>
                    <textarea x-model="newReservation.deskripsi_acara" placeholder="Deskripsikan detail kegiatan..." required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold h-20"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Upload Proposal (PDF, Max 2MB)</label>
                    <input type="file" id="proposal_file_modal" accept="application/pdf" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Kirim Permohonan
                </button>
            </form>
        </div>
    </div>

    <!-- Approval Signee Modal -->
    <div x-show="showApproveModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showApproveModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Tanda Tangan Penyetuju</h3>
                <button @click="showApproveModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="approveReservation()" class="space-y-4">
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl text-xs text-slate-500 leading-relaxed font-semibold">
                    💡 Informasi ini akan dicetak langsung di lembaran berkas surat izin peminjaman ruangan format PDF resmi.
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Lengkap Penyetuju (Beserta Gelar)</label>
                    <input type="text" x-model="approvalData.nama_penyetuju" placeholder="Contoh: Dr. H. Dadang, M.Ag." required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Jabatan Resmi Penyetuju</label>
                    <input type="text" x-model="approvalData.jabatan_penyetuju" placeholder="Contoh: Wakil Dekan I Bidang Akademik" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <button type="submit" class="w-full py-3.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95">
                    Setujui & Hasilkan PDF
                </button>
            </form>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div x-show="showAddRoomModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showAddRoomModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Tambah Ruangan Baru</h3>
                <button @click="showAddRoomModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="createRoom" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Ruangan</label>
                    <input type="text" x-model="newRoom.nama_ruangan" placeholder="Contoh: Lab Komputer 1" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kapasitas Kursi</label>
                        <input type="number" x-model="newRoom.kapasitas" placeholder="Contoh: 40" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tingkat Kelola</label>
                        <select x-model="newRoom.tingkat" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                            <option value="fakultas">Tingkat Fakultas</option>
                            <option value="universitas">Tingkat Universitas</option>
                            <option value="kemahasiswaan">Tingkat Kemahasiswaan</option>
                        </select>
                    </div>
                </div>
                <div x-show="newRoom.tingkat === 'fakultas'">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Fakultas Pemilik</label>
                    <select x-model="newRoom.fakultas_id" :required="newRoom.tingkat === 'fakultas'" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="">-- Pilih Fakultas --</option>
                        <template x-for="f in faculties" :key="f.id">
                            <option :value="f.id" x-text="f.nama_fakultas"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Fasilitas Pendukung</label>
                    <input type="text" x-model="newRoom.fasilitas" placeholder="Contoh: AC, Proyektor, WiFi, Sound System" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Deskripsi Singkat</label>
                    <textarea x-model="newRoom.deskripsi" placeholder="Deskripsikan kondisi ruangan..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold h-20"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama PIC</label>
                        <input type="text" x-model="newRoom.pic_nama" placeholder="Nama Contact Person" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">No WA/Telp PIC</label>
                        <input type="text" x-model="newRoom.pic_telepon" placeholder="Nomor Telepon PIC" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="add_room_eksternal" x-model="newRoom.eksternal_diizinkan" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="add_room_eksternal" class="text-xs font-bold text-slate-600 cursor-pointer">Izinkan Sewa Eksternal</label>
                </div>
                <div class="space-y-3 p-3 bg-slate-50/50 border border-slate-100 rounded-2xl">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Foto/Gambar Aula (Maks 3 Gambar)</label>
                    
                    <!-- Slot Gambar 1 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 1</span>
                        <input type="file" id="add_room_image_1" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="newRoom.gambar_1" placeholder="Atau tempel URL link gambar 1 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                    
                    <!-- Slot Gambar 2 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 2</span>
                        <input type="file" id="add_room_image_2" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="newRoom.gambar_2" placeholder="Atau tempel URL link gambar 2 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                    
                    <!-- Slot Gambar 3 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 3</span>
                        <input type="file" id="add_room_image_3" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="newRoom.gambar_3" placeholder="Atau tempel URL link gambar 3 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                </div>
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Simpan Ruangan
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div x-show="showEditRoomModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showEditRoomModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Ubah Ruangan</h3>
                <button @click="showEditRoomModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="updateRoom()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Ruangan</label>
                    <input type="text" x-model="editRoom.nama_ruangan" placeholder="Contoh: Lab Komputer 1" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kapasitas Kursi</label>
                        <input type="number" x-model="editRoom.kapasitas" placeholder="Contoh: 40" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tingkat Kelola</label>
                        <select x-model="editRoom.tingkat" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                            <option value="fakultas">Tingkat Fakultas</option>
                            <option value="universitas">Tingkat Universitas</option>
                            <option value="kemahasiswaan">Tingkat Kemahasiswaan</option>
                        </select>
                    </div>
                </div>
                <div x-show="editRoom.tingkat === 'fakultas'">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Fakultas Pemilik</label>
                    <select x-model="editRoom.fakultas_id" :required="editRoom.tingkat === 'fakultas'" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="">-- Pilih Fakultas --</option>
                        <template x-for="f in faculties" :key="f.id">
                            <option :value="f.id" x-text="f.nama_fakultas"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Fasilitas Pendukung</label>
                    <input type="text" x-model="editRoom.fasilitas" placeholder="Contoh: AC, Proyektor, WiFi, Sound System" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Deskripsi Singkat</label>
                    <textarea x-model="editRoom.deskripsi" placeholder="Deskripsikan kondisi ruangan..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold h-20"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama PIC</label>
                        <input type="text" x-model="editRoom.pic_nama" placeholder="Nama Contact Person" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">No WA/Telp PIC</label>
                        <input type="text" x-model="editRoom.pic_telepon" placeholder="Nomor Telepon PIC" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="edit_room_eksternal" x-model="editRoom.eksternal_diizinkan" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_room_eksternal" class="text-xs font-bold text-slate-600 cursor-pointer">Izinkan Sewa Eksternal</label>
                </div>
                <div class="space-y-3 p-3 bg-slate-50/50 border border-slate-100 rounded-2xl">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Ubah Foto/Gambar (Maks 3 Gambar)</label>
                    
                    <!-- Slot Gambar 1 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 1</span>
                        <input type="file" id="edit_room_image_1" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="editRoom.gambar_1" placeholder="Atau tempel URL link gambar 1 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                    
                    <!-- Slot Gambar 2 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 2</span>
                        <input type="file" id="edit_room_image_2" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="editRoom.gambar_2" placeholder="Atau tempel URL link gambar 2 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                    
                    <!-- Slot Gambar 3 -->
                    <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Slot Gambar 3</span>
                        <input type="file" id="edit_room_image_3" accept="image/*" class="w-full p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                        <input type="text" x-model="editRoom.gambar_3" placeholder="Atau tempel URL link gambar 3 di sini..." class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-indigo-500 mt-1">
                    </div>
                </div>
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Perbarui Ruangan
                </button>
            </form>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div x-show="showAddAdminModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showAddAdminModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Tambah Akun Admin</h3>
                <button @click="showAddAdminModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="createAdmin()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" x-model="newAdmin.name" placeholder="Nama Lengkap" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" x-model="newAdmin.email" placeholder="admin@uin.ac.id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Password</label>
                    <input type="password" x-model="newAdmin.password" placeholder="••••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Role Peran</label>
                    <select x-model="newAdmin.role" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="admin_fakultas">Admin Fakultas (Kelola 1 Fakultas)</option>
                        <option value="admin_universitas">Admin Universitas (Kelola Aula Universitas Hari Kerja)</option>
                        <option value="admin_kemahasiswaan">Admin Kemahasiswaan (Kelola Aula Kemahasiswaan Weekday & Weekend)</option>
                        <option value="admin_bisnis">Admin Bisnis (Kelola Aula Universitas Weekend & Eksternal)</option>
                    </select>
                </div>

                <div x-show="newAdmin.role === 'admin_fakultas'">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Pilih Fakultas</label>
                    <select x-model="newAdmin.fakultas_id" :required="newAdmin.role === 'admin_fakultas'" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="">-- Pilih Fakultas --</option>
                        <template x-for="f in faculties" :key="f.id">
                            <option :value="f.id" x-text="f.nama_fakultas"></option>
                        </template>
                    </select>
                </div>

                <div x-show="['admin_universitas', 'admin_kemahasiswaan'].includes(newAdmin.role)">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide" x-text="newAdmin.role === 'admin_kemahasiswaan' ? 'Assign Aula Kemahasiswaan (Pilih aula yang di-manage)' : 'Assign Aula Universitas (Pilih aula yang di-manage)'"></label>
                    <div class="grid grid-cols-1 gap-2 border border-slate-200 bg-slate-50 p-3 rounded-xl max-h-40 overflow-y-auto">
                        <template x-for="room in rooms.filter(r => newAdmin.role === 'admin_kemahasiswaan' ? r.tingkat === 'kemahasiswaan' : r.tingkat === 'universitas')" :key="room.id">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" :value="room.id" x-model="newAdmin.room_ids" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span x-text="room.nama_ruangan"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Tambah Admin
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div x-show="showEditAdminModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showEditAdminModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Ubah Akun Admin</h3>
                <button @click="showEditAdminModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form @submit.prevent="updateAdmin()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" x-model="editAdmin.name" placeholder="Nama Lengkap" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" x-model="editAdmin.email" placeholder="admin@uin.ac.id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Password (Isi hanya jika ingin diganti)</label>
                    <input type="password" x-model="editAdmin.password" placeholder="••••••••" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Role Peran</label>
                    <select x-model="editAdmin.role" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="admin_fakultas">Admin Fakultas (Kelola 1 Fakultas)</option>
                        <option value="admin_universitas">Admin Universitas (Kelola Aula Universitas Hari Kerja)</option>
                        <option value="admin_kemahasiswaan">Admin Kemahasiswaan (Kelola Aula Kemahasiswaan Weekday & Weekend)</option>
                        <option value="admin_bisnis">Admin Bisnis (Kelola Aula Universitas Weekend & Eksternal)</option>
                    </select>
                </div>

                <div x-show="editAdmin.role === 'admin_fakultas'">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Pilih Fakultas</label>
                    <select x-model="editAdmin.fakultas_id" :required="editAdmin.role === 'admin_fakultas'" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <option value="">-- Pilih Fakultas --</option>
                        <template x-for="f in faculties" :key="f.id">
                            <option :value="f.id" x-text="f.nama_fakultas"></option>
                        </template>
                    </select>
                </div>

                <div x-show="['admin_universitas', 'admin_kemahasiswaan'].includes(editAdmin.role)">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide" x-text="editAdmin.role === 'admin_kemahasiswaan' ? 'Assign Aula Kemahasiswaan (Pilih aula yang di-manage)' : 'Assign Aula Universitas (Pilih aula yang di-manage)'"></label>
                    <div class="grid grid-cols-1 gap-2 border border-slate-200 bg-slate-50 p-3 rounded-xl max-h-40 overflow-y-auto">
                        <template x-for="room in rooms.filter(r => editAdmin.role === 'admin_kemahasiswaan' ? r.tingkat === 'kemahasiswaan' : r.tingkat === 'universitas')" :key="room.id">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" :value="room.id" x-model="editAdmin.room_ids" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span x-text="room.nama_ruangan"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Perbarui Admin
                </button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="showRegisterModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showRegisterModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Daftar Akun Baru</h3>
                <button @click="showRegisterModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="register()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" x-model="registerName" placeholder="Nama Lengkap" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Alamat Email</label>
                    <input type="email" x-model="registerEmail" placeholder="nama@email.com" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kata Sandi</label>
                        <input type="password" x-model="registerPassword" placeholder="••••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Konfirmasi</label>
                        <input type="password" x-model="registerPasswordConfirmation" placeholder="••••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">No. Telepon / WA</label>
                    <input type="text" x-model="registerPhone" placeholder="08xxxxxxxxxx" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tipe Peminjam</label>
                    <select x-model="registerUserType" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="internal">Civitas Akademika Kampus (Internal)</option>
                        <option value="eksternal">Pihak Luar Kampus (Eksternal)</option>
                    </select>
                </div>
                <div x-show="registerUserType === 'internal'">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">NIM / NIP</label>
                    <input type="text" x-model="registerNimNip" placeholder="Masukkan NIM atau NIP" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-indigo-100">
                    Daftar Sekarang
                </button>
            </form>
            <div class="text-center pt-2">
                <button @click="showRegisterModal = false; showLoginModal = true" class="text-xs text-indigo-600 hover:underline font-bold">Sudah punya akun? Masuk di sini</button>
            </div>
        </div>
    </div>

    <!-- API Client & Application Logic JavaScript -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('appState', () => ({
                // Auth States
                token: localStorage.getItem('auth_token') || '',
                isLoggedIn: !!localStorage.getItem('auth_token'),
                
                // Safe JSON parsing to prevent crash from stale data
                user: (function() {
                    try {
                        return JSON.parse(localStorage.getItem('auth_user')) || { name: '', email: '', role: '' };
                    } catch (e) {
                        return { name: '', email: '', role: '' };
                    }
                })(),
                
                // Active Views
                activeTab: 'home',
                showLoginModal: false,
                showRegisterModal: false,
                showBookModal: false,
                showApproveModal: false,
                showAddRoomModal: false,
                showEditRoomModal: false,
                showAddAdminModal: false,
                showEditAdminModal: false,
                
                // Form Fields
                loginEmail: '',
                loginPassword: '',
                registerName: '',
                registerEmail: '',
                registerPassword: '',
                registerPasswordConfirmation: '',
                registerPhone: '',
                registerUserType: 'internal',
                registerNimNip: '',
                
                newReservation: {
                    room_id: '',
                    tanggal: '',
                    tujuan: '',
                    waktu_mulai: '08:00',
                    waktu_selesai: '09:30',
                    instansi: '',
                    deskripsi_acara: '',
                    tipe_peminjam: 'internal'
                },
                approvalData: {
                    id: null,
                    nama_penyetuju: 'Dr. H. Ahmad Fauzi, M.Ag.',
                    jabatan_penyetuju: 'Wakil Dekan Bidang Kemahasiswaan & Kerjasama'
                },
                newRoom: {
                    nama_ruangan: '',
                    kapasitas: 40,
                    fasilitas: 'AC, Proyektor, WiFi, Sound System',
                    tingkat: 'fakultas',
                    fakultas_id: '',
                    deskripsi: '',
                    pic_nama: '',
                    pic_telepon: '',
                    eksternal_diizinkan: true,
                    gambar_1: '',
                    gambar_2: '',
                    gambar_3: ''
                },
                editRoom: {
                    id: null,
                    nama_ruangan: '',
                    kapasitas: 40,
                    fasilitas: '',
                    tingkat: 'fakultas',
                    fakultas_id: '',
                    deskripsi: '',
                    gambar: '',
                    gambar_1: '',
                    gambar_2: '',
                    gambar_3: '',
                    pic_nama: '',
                    pic_telepon: '',
                    eksternal_diizinkan: true
                },
                newAdmin: {
                    name: '',
                    email: '',
                    password: '',
                    role: 'admin_fakultas',
                    fakultas_id: '',
                    room_ids: []
                },
                editAdmin: {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    role: 'admin_fakultas',
                    fakultas_id: '',
                    room_ids: []
                },

                // Business Data
                rooms: [],
                allReservations: [], // Global cache for calendar highlighting
                reservations: [],    // Current logged-in user history
                approvals: [],       // Pending approvals list
                admins: [],          // Admin list for Superadmin
                faculties: [],       // Faculties list
                selectedRoomId: null, // Selected Room Filter ID
                selectedDate: '',     // Format: YYYY-MM-DD
                selectedCategory: 'all',
                searchQuery: '',
                searchCapacity: '',
                showRoomDetailsModal: false,
                detailedRoom: null,
                currentImageIndex: 0,
                favorites: [],
                
                // Calendar Display States
                currentMonth: 0, // 0-indexed (June = 5)
                currentYear: 2026,
                calendarBlankDays: [],
                calendarDaysInMonth: [],

                // Time slots lists (Calculated dynamically)
                timeSlots: [],

                // Alpine.js native initialization method (runs automatically)
                init() {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const date = String(today.getDate()).padStart(2, '0');
                    this.selectedDate = `${year}-${month}-${date}`;
                    this.currentMonth = today.getMonth();
                    this.currentYear = today.getFullYear();

                    this.favorites = JSON.parse(localStorage.getItem('favorite_rooms')) || [];

                    // Axios global headers config
                    if (this.token) {
                        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                        this.fetchUserData();
                    }

                    // Fetch base data
                    this.fetchRooms();
                    this.fetchGlobalReservations();
                    this.fetchFaculties();

                    if (this.isLoggedIn) {
                        this.fetchHistory();
                        this.fetchApprovals();
                        this.fetchAdmins();
                    }
                    
                    // Render icons
                    setTimeout(() => { lucide.createIcons(); }, 300);

                    // Watch activeTab to re-render Lucide icons
                    this.$watch('activeTab', value => {
                        setTimeout(() => { lucide.createIcons(); }, 100);
                    });
                },

                // API Calls
                async login() {
                    try {
                        const res = await axios.post('/api/v1/auth/login', {
                            email: this.loginEmail,
                            password: this.loginPassword
                        });
                        
                        this.token = res.data.data?.token || res.data.token;
                        this.user = res.data.data?.user || res.data.user;
                        localStorage.setItem('auth_token', this.token);
                        localStorage.setItem('auth_user', JSON.stringify(this.user));
                        
                        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                        this.isLoggedIn = true;
                        this.showLoginModal = false;
                        
                        alert('Selamat datang kembali, ' + this.user.name + '!');
                        this.init();
                    } catch (err) {
                        alert('Login Gagal: ' + (err.response?.data?.message || err.message));
                    }
                },

                async register() {
                    try {
                        const res = await axios.post('/api/v1/auth/register', {
                            name: this.registerName,
                            email: this.registerEmail,
                            password: this.registerPassword,
                            password_confirmation: this.registerPasswordConfirmation,
                            no_telepon: this.registerPhone,
                            tipe_user: this.registerUserType,
                            nim_nip: this.registerNimNip
                        });
                        
                        this.token = res.data.data?.token || res.data.token;
                        this.user = res.data.data?.user || res.data.user;
                        localStorage.setItem('auth_token', this.token);
                        localStorage.setItem('auth_user', JSON.stringify(this.user));
                        
                        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                        this.isLoggedIn = true;
                        this.showRegisterModal = false;
                        
                        alert('Registrasi berhasil! Selamat datang ' + this.user.name + '!');
                        this.init();
                    } catch (err) {
                        alert('Registrasi Gagal: ' + (err.response?.data?.message || err.message));
                    }
                },

                async quickLogin(email) {
                    this.loginEmail = email;
                    this.loginPassword = 'password123';
                    await this.login();
                },

                async logout() {
                    try {
                        if (this.token) {
                            await axios.post('/api/v1/auth/logout');
                        }
                    } catch (e) {
                        console.error('Logout api error:', e);
                    } finally {
                        this.token = '';
                        this.isLoggedIn = false;
                        this.user = { name: '', email: '', role: '' };
                        localStorage.clear();
                        delete axios.defaults.headers.common['Authorization'];
                        alert('Anda berhasil keluar.');
                        window.location.reload();
                    }
                },

                async fetchUserData() {
                    try {
                        const res = await axios.get('/api/v1/auth/me');
                        this.user = res.data.data || res.data;
                        localStorage.setItem('auth_user', JSON.stringify(this.user));
                    } catch (err) {
                        if (err.response?.status === 401) {
                            this.logout();
                        }
                    }
                },

                async fetchRooms() {
                    try {
                        const res = await axios.get('/api/v1/rooms');
                        this.rooms = res.data.data || res.data;
                    } catch (err) {
                        console.error('Fetch rooms error:', err);
                    }
                },

                async fetchFaculties() {
                    try {
                        const res = await axios.get('/api/v1/faculties');
                        this.faculties = res.data.data || res.data;
                    } catch (err) {
                        console.error('Fetch faculties error:', err);
                    }
                },

                async fetchAdmins() {
                    if (!this.isLoggedIn || !['super_admin', 'superadmin'].includes(this.user.role)) return;
                    try {
                        const res = await axios.get('/api/v1/admins');
                        this.admins = res.data.data || res.data;
                    } catch (err) {
                        console.error('Fetch admins error:', err);
                    }
                },

                async fetchGlobalReservations() {
                    try {
                        const res = await axios.get('/api/v1/reservations/schedule');
                        this.allReservations = res.data.data || res.data;
                        this.buildCalendar();
                        this.buildTimeSlots();
                    } catch (e) {
                        console.error('Fetch global res error:', e);
                    }
                },

                async fetchHistory() {
                    if (!this.isLoggedIn) return;
                    try {
                        const res = await axios.get('/api/v1/reservations/history');
                        this.reservations = res.data.data || res.data;
                        this.buildCalendar();
                        this.buildTimeSlots();
                    } catch (err) {
                        console.error('Fetch history error:', err);
                    }
                },

                async fetchApprovals() {
                    if (!this.isLoggedIn || !['admin_fakultas', 'admin_universitas', 'admin_bisnis', 'admin_kemahasiswaan'].includes(this.user.role)) return;
                    try {
                        const res = await axios.get('/api/v1/approvals/pending');
                        this.approvals = res.data.data || res.data;
                    } catch (err) {
                        console.error('Fetch approvals error:', err);
                    }
                },

                async submitReservation() {
                    if (!this.isLoggedIn) {
                        this.showLoginModal = true;
                        alert('Silakan login terlebih dahulu untuk mengajukan reservasi.');
                        return;
                    }
                    if (this.user.role !== 'peminjam') {
                        alert('Hanya akun dengan peran Mahasiswa (Peminjam) yang dapat mengajukan reservasi!');
                        return;
                    }
                    
                    try {
                        const startDT = `${this.newReservation.tanggal}T${this.newReservation.waktu_mulai}:00`;
                        const endDT = `${this.newReservation.tanggal}T${this.newReservation.waktu_selesai}:00`;
                        
                        const formData = new FormData();
                        formData.append('room_id', parseInt(this.newReservation.room_id));
                        formData.append('tanggal_mulai', startDT);
                        formData.append('tanggal_selesai', endDT);
                        formData.append('tujuan', this.newReservation.tujuan);
                        formData.append('instansi', this.newReservation.instansi);
                        formData.append('deskripsi_acara', this.newReservation.deskripsi_acara);
                        formData.append('tipe_peminjam', this.newReservation.tipe_peminjam);

                        const fileInputTab = document.getElementById('proposal_file_tab');
                        const fileInputModal = document.getElementById('proposal_file_modal');
                        const fileInput = (fileInputTab && fileInputTab.files.length > 0) ? fileInputTab : fileInputModal;

                        if (fileInput && fileInput.files[0]) {
                            formData.append('proposal_file', fileInput.files[0]);
                        }

                        await axios.post('/api/v1/reservations', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                        
                        alert('Peminjaman ruangan sukses diajukan! Menunggu persetujuan Admin.');
                        this.showBookModal = false;
                        
                        // Clear form
                        this.newReservation = {
                            room_id: '',
                            tanggal: this.selectedDate,
                            tujuan: '',
                            waktu_mulai: '08:00',
                            waktu_selesai: '09:30',
                            instansi: this.user.tipe_user === 'internal' ? 'UIN Sunan Gunung Djati' : '',
                            deskripsi_acara: '',
                            tipe_peminjam: this.user.tipe_user || 'internal'
                        };
                        if (fileInputTab) fileInputTab.value = '';
                        if (fileInputModal) fileInputModal.value = '';

                        this.fetchHistory();
                        this.fetchGlobalReservations();
                        this.activeTab = 'history';
                    } catch (err) {
                        alert('Pengajuan Gagal: ' + (err.response?.data?.message || err.message));
                    }
                },

                openApprovalModal(app) {
                    this.approvalData.id = app.id;
                    this.showApproveModal = true;
                },

                async approveReservation() {
                    try {
                        await axios.put(`/api/v1/approvals/${this.approvalData.id}/approve`, {
                            nama_penyetuju: this.approvalData.nama_penyetuju,
                            jabatan_penyetuju: this.approvalData.jabatan_penyetuju
                        });
                        
                        alert('Surat izin sukses ditandatangani & PDF sukses diterbitkan!');
                        this.showApproveModal = false;
                        this.fetchApprovals();
                        this.fetchHistory();
                        this.fetchGlobalReservations();
                        this.fetchRooms();
                    } catch (err) {
                        alert('Approval Gagal: ' + (err.response?.data?.message || err.message));
                    }
                },

                async rejectReservation(id) {
                    const reason = prompt('Masukkan alasan penolakan peminjaman:');
                    if (reason === null) return;
                    
                    try {
                        await axios.put(`/api/v1/approvals/${id}/reject`, {
                            alasan_penolakan: reason
                        });
                        
                        alert('Pengajuan berhasil ditolak.');
                        this.fetchApprovals();
                        this.fetchHistory();
                        this.fetchGlobalReservations();
                    } catch (err) {
                        alert('Gagal Menolak: ' + (err.response?.data?.message || err.message));
                    }
                },

                async createRoom() {
                    try {
                        const formData = new FormData();
                        formData.append('nama_ruangan', this.newRoom.nama_ruangan);
                        formData.append('kapasitas', this.newRoom.kapasitas);
                        formData.append('fasilitas', this.newRoom.fasilitas);
                        formData.append('tingkat', this.newRoom.tingkat);
                        if (this.newRoom.fakultas_id) {
                            formData.append('fakultas_id', this.newRoom.fakultas_id);
                        }
                        formData.append('deskripsi', this.newRoom.deskripsi);
                        formData.append('pic_nama', this.newRoom.pic_nama);
                        formData.append('pic_telepon', this.newRoom.pic_telepon);
                        formData.append('eksternal_diizinkan', this.newRoom.eksternal_diizinkan ? '1' : '0');
                        
                        for (let i = 1; i <= 3; i++) {
                            const fileInput = document.getElementById(`add_room_image_${i}`);
                            if (fileInput && fileInput.files[0]) {
                                formData.append(`gambar_${i}`, fileInput.files[0]);
                            } else if (this.newRoom[`gambar_${i}`]) {
                                formData.append(`gambar_${i}`, this.newRoom[`gambar_${i}`]);
                            }
                        }

                        await axios.post('/api/v1/rooms', formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        
                        alert('Ruangan sukses ditambahkan!');
                        this.showAddRoomModal = false;
                        this.fetchRooms();
                        
                        // Reset inputs
                        this.newRoom = { nama_ruangan: '', kapasitas: 40, fasilitas: 'AC, Proyektor, WiFi, Sound System', tingkat: 'fakultas', fakultas_id: '', deskripsi: '', pic_nama: '', pic_telepon: '', eksternal_diizinkan: true, gambar_1: '', gambar_2: '', gambar_3: '' };
                        for (let i = 1; i <= 3; i++) {
                            const fileInput = document.getElementById(`add_room_image_${i}`);
                            if (fileInput) fileInput.value = '';
                        }
                    } catch (err) {
                        alert('Gagal menambah ruangan: ' + (err.response?.data?.message || err.message));
                    }
                },

                openEditRoomModal(room) {
                    this.editRoom.id = room.id;
                    this.editRoom.nama_ruangan = room.nama_ruangan;
                    this.editRoom.kapasitas = room.kapasitas;
                    this.editRoom.fasilitas = room.fasilitas;
                    this.editRoom.tingkat = room.tingkat || 'fakultas';
                    this.editRoom.fakultas_id = room.fakultas_id || '';
                    this.editRoom.deskripsi = room.deskripsi || '';
                    this.editRoom.gambar = room.gambar || '';
                    
                    let currentImages = [];
                    if (room.gambar) {
                        try {
                            if (room.gambar.startsWith('[')) {
                                currentImages = JSON.parse(room.gambar);
                            } else {
                                currentImages = [room.gambar];
                            }
                        } catch (e) {
                            currentImages = [room.gambar];
                        }
                    }
                    this.editRoom.gambar_1 = currentImages[0] || '';
                    this.editRoom.gambar_2 = currentImages[1] || '';
                    this.editRoom.gambar_3 = currentImages[2] || '';
                    
                    this.editRoom.pic_nama = room.pic_nama || '';
                    this.editRoom.pic_telepon = room.pic_telepon || '';
                    this.editRoom.eksternal_diizinkan = room.eksternal_diizinkan !== false;
                    this.showEditRoomModal = true;
                },

                async updateRoom() {
                    try {
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        formData.append('nama_ruangan', this.editRoom.nama_ruangan);
                        formData.append('kapasitas', this.editRoom.kapasitas);
                        formData.append('fasilitas', this.editRoom.fasilitas);
                        formData.append('tingkat', this.editRoom.tingkat);
                        if (this.editRoom.fakultas_id) {
                            formData.append('fakultas_id', this.editRoom.fakultas_id);
                        } else {
                            formData.append('fakultas_id', '');
                        }
                        formData.append('deskripsi', this.editRoom.deskripsi);
                        formData.append('pic_nama', this.editRoom.pic_nama);
                        formData.append('pic_telepon', this.editRoom.pic_telepon);
                        formData.append('eksternal_diizinkan', this.editRoom.eksternal_diizinkan ? '1' : '0');
                        
                        for (let i = 1; i <= 3; i++) {
                            const fileInput = document.getElementById(`edit_room_image_${i}`);
                            if (fileInput && fileInput.files[0]) {
                                formData.append(`gambar_${i}`, fileInput.files[0]);
                            } else {
                                const val = this.editRoom[`gambar_${i}`] || '';
                                formData.append(`gambar_${i}`, val);
                            }
                        }

                        await axios.post(`/api/v1/rooms/${this.editRoom.id}`, formData, {
                            headers: { 'Content-Type': 'multipart/form-data' }
                        });
                        
                        alert('Ruangan sukses diperbarui!');
                        this.showEditRoomModal = false;
                        this.fetchRooms();
                        
                        // Reset file inputs
                        for (let i = 1; i <= 3; i++) {
                            const fileInput = document.getElementById(`edit_room_image_${i}`);
                            if (fileInput) fileInput.value = '';
                        }
                    } catch (err) {
                        alert('Gagal memperbarui ruangan: ' + (err.response?.data?.message || err.message));
                    }
                },

                async deleteRoom(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus ruangan ini?')) return;
                    try {
                        await axios.delete(`/api/v1/rooms/${id}`);
                        alert('Ruangan berhasil dihapus.');
                        this.fetchRooms();
                    } catch (err) {
                        alert('Gagal menghapus: ' + (err.response?.data?.message || err.message));
                    }
                },

                // Admin CRUD methods for Superadmin
                async createAdmin() {
                    try {
                        await axios.post('/api/v1/admins', this.newAdmin);
                        alert('Admin sukses ditambahkan!');
                        this.showAddAdminModal = false;
                        this.fetchAdmins();
                        this.newAdmin = { name: '', email: '', password: '', role: 'admin_fakultas', fakultas_id: '', room_ids: [] };
                    } catch (err) {
                        alert('Gagal menambah admin: ' + (err.response?.data?.message || err.message));
                    }
                },

                openEditAdminModal(admin) {
                    this.editAdmin.id = admin.id;
                    this.editAdmin.name = admin.name;
                    this.editAdmin.email = admin.email;
                    this.editAdmin.password = '';
                    this.editAdmin.role = admin.role;
                    this.editAdmin.fakultas_id = admin.fakultas_id || '';
                    this.editAdmin.room_ids = admin.rooms ? admin.rooms.map(r => r.id) : [];
                    this.showEditAdminModal = true;
                },

                async updateAdmin() {
                    try {
                        await axios.put(`/api/v1/admins/${this.editAdmin.id}`, this.editAdmin);
                        alert('Admin sukses diperbarui!');
                        this.showEditAdminModal = false;
                        this.fetchAdmins();
                    } catch (err) {
                        alert('Gagal memperbarui admin: ' + (err.response?.data?.message || err.message));
                    }
                },

                async deleteAdmin(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus admin ini?')) return;
                    try {
                        await axios.delete(`/api/v1/admins/${id}`);
                        alert('Admin berhasil dihapus.');
                        this.fetchAdmins();
                    } catch (err) {
                        alert('Gagal menghapus: ' + (err.response?.data?.message || err.message));
                    }
                },

                async downloadPDF(reservationId) {
                    try {
                        const response = await axios.get(`/api/v1/reservations/${reservationId}/letter/pdf`, {
                            responseType: 'blob'
                        });
                        
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `surat_izin_reservasi_${reservationId}.pdf`);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } catch (err) {
                        alert('Gagal mengunduh PDF: ' + (err.response?.data?.message || err.message));
                    }
                },

                async downloadProposalFile(reservationId) {
                    try {
                        const response = await axios.get(`/api/v1/reservations/${reservationId}/proposal`, {
                            responseType: 'blob'
                        });
                        
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `proposal_reservasi_${reservationId}.pdf`);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } catch (err) {
                        alert('Gagal mengunduh proposal: ' + (err.response?.data?.message || err.message));
                    }
                },

                // Calendar Mechanics
                selectRoom(roomId) {
                    if (roomId === null) {
                        this.selectedRoomId = null;
                        this.detailedRoom = null;
                        this.currentImageIndex = 0;
                        this.buildCalendar();
                        this.buildTimeSlots();
                        return;
                    }
                    this.selectedRoomId = roomId;
                    const room = this.rooms.find(r => r.id === roomId);
                    if (room) {
                        this.detailedRoom = room;
                        this.currentImageIndex = 0;
                        this.buildCalendar();
                        this.buildTimeSlots();
                        this.showRoomDetailsModal = true;
                    }
                },

                proxyGoogleDriveUrl(url) {
                    if (!url) return '';
                    if (url.includes('lh3.googleusercontent.com/d/')) {
                        const parts = url.split('/d/');
                        const id = parts[parts.length - 1];
                        return `/api/v1/rooms/image-proxy?id=${id}`;
                    }
                    return url;
                },

                getRoomImages(room) {
                    if (!room) return [];
                    if (room.gambar) {
                        try {
                            if (room.gambar.startsWith('[')) {
                                const urls = JSON.parse(room.gambar);
                                return urls.map(url => this.proxyGoogleDriveUrl(url));
                            }
                        } catch (e) {}
                        return [this.proxyGoogleDriveUrl(room.gambar)];
                    }
                    // Unique 3 images fallback per room id
                    const fallbacks = [
                        [
                            'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80'
                        ],
                        [
                            'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=600&q=80'
                        ],
                        [
                            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80',
                            'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80'
                        ]
                    ];
                    return fallbacks[room.id % 3];
                },

                prevImage(images) {
                    if (!images || images.length <= 1) return;
                    if (this.currentImageIndex === 0) {
                        this.currentImageIndex = images.length - 1;
                    } else {
                        this.currentImageIndex--;
                    }
                },

                nextImage(images) {
                    if (!images || images.length <= 1) return;
                    if (this.currentImageIndex === images.length - 1) {
                        this.currentImageIndex = 0;
                    } else {
                        this.currentImageIndex++;
                    }
                },

                toggleFavorite(roomId) {
                    if (this.favorites.includes(roomId)) {
                        this.favorites = this.favorites.filter(id => id !== roomId);
                    } else {
                        this.favorites.push(roomId);
                    }
                    localStorage.setItem('favorite_rooms', JSON.stringify(this.favorites));
                },

                filteredRooms() {
                    return this.rooms.filter(room => {
                        // Category filter
                        if (this.selectedCategory === 'fakultas' && room.tingkat !== 'fakultas') return false;
                        if (this.selectedCategory === 'universitas' && room.tingkat !== 'universitas') return false;
                        if (this.selectedCategory === 'kemahasiswaan' && room.tingkat !== 'kemahasiswaan') return false;

                        // Text search
                        if (this.searchQuery) {
                            const query = this.searchQuery.toLowerCase();
                            const matchName = room.nama_ruangan.toLowerCase().includes(query);
                            const matchFaculty = room.faculty && room.faculty.nama_fakultas.toLowerCase().includes(query);
                            const matchDesc = room.deskripsi && room.deskripsi.toLowerCase().includes(query);
                            if (!matchName && !matchFaculty && !matchDesc) return false;
                        }

                        // Capacity search
                        if (this.searchCapacity && room.kapasitas < parseInt(this.searchCapacity)) {
                            return false;
                        }

                        return true;
                    });
                },

                selectDate(dateStr) {
                    this.selectedDate = dateStr;
                    this.buildTimeSlots();
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.buildCalendar();
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.buildCalendar();
                },

                getMonthYearString() {
                    const monthsIndo = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    return `${monthsIndo[this.currentMonth]} ${this.currentYear}`;
                },

                buildCalendar() {
                    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    
                    this.calendarBlankDays = Array.from({ length: firstDayOfMonth }, (_, i) => i);
                    
                    const days = [];
                    const today = new Date();
                    const todayString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

                    for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
                        const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
                        
                        const activeReservations = this.allReservations.filter(res => {
                            const resDate = res.tanggal_mulai.includes('T') ? res.tanggal_mulai.split('T')[0] : res.tanggal_mulai.split(' ')[0];
                            const matchRoom = this.selectedRoomId === null || res.room_id === this.selectedRoomId;
                            return resDate === dateString && matchRoom;
                        });

                        const statuses = activeReservations.map(r => r.status_approval);
                        
                        days.push({
                            dayNum,
                            dateString,
                            isToday: dateString === todayString,
                            statuses: statuses.slice(0, 3) // Max 3 indicator dots
                        });
                    }
                    this.calendarDaysInMonth = days;
                    
                    setTimeout(() => { lucide.createIcons(); }, 100);
                },

                buildTimeSlots() {
                    const slots = [
                        { time: '08:00', start: 8.0, end: 9.5 },
                        { time: '09:30', start: 9.5, end: 11.0 },
                        { time: '11:00', start: 11.0, end: 12.5 },
                        { time: '13:00', start: 13.0, end: 14.5 },
                        { time: '15:00', start: 15.0, end: 16.5 },
                        { time: '18:30', start: 18.5, end: 20.0 }
                    ];

                    this.timeSlots = slots.map(slot => {
                        let status = 'available';
                        let statusLabel = 'Tersedia';
                        let matchedRes = null;

                        if (this.selectedRoomId !== null) {
                            const resForDate = this.allReservations.filter(res => {
                                const resDate = res.tanggal_mulai.includes('T') ? res.tanggal_mulai.split('T')[0] : res.tanggal_mulai.split(' ')[0];
                                return resDate === this.selectedDate && res.room_id === this.selectedRoomId;
                            });

                            for (let res of resForDate) {
                                const timePart = res.tanggal_mulai.includes('T') ? res.tanggal_mulai.split('T')[1].substring(0, 5) : res.tanggal_mulai.split(' ')[1].substring(0, 5);
                                const endTimePart = res.tanggal_selesai.includes('T') ? res.tanggal_selesai.split('T')[1].substring(0, 5) : res.tanggal_selesai.split(' ')[1].substring(0, 5);
                                
                                const startParts = timePart.split(':');
                                const endParts = endTimePart.split(':');
                                
                                const resStartDecimal = parseFloat(startParts[0]) + parseFloat(startParts[1]) / 60.0;
                                const resEndDecimal = parseFloat(endParts[0]) + parseFloat(endParts[1]) / 60.0;

                                const isOverlap = Math.max(slot.start, resStartDecimal) < Math.min(slot.end, resEndDecimal);
                                if (isOverlap) {
                                    matchedRes = res;
                                    status = res.status_approval;
                                    statusLabel = res.status_approval === 'approved' ? res.tujuan : 'Menunggu Persetujuan';
                                    break;
                                }
                            }
                        }

                        return {
                            time: slot.time,
                            status,
                            statusLabel: status === 'approved' ? 'Terisi' : (status === 'pending' ? 'Pending' : 'Tersedia'),
                            reservation: matchedRes
                        };
                    });
                    
                    setTimeout(() => { lucide.createIcons(); }, 100);
                },

                openBookingModal() {
                    if (!this.selectedRoomId) {
                        alert('Silakan pilih salah satu ruangan terlebih dahulu pada filter di atas.');
                        return;
                    }
                    this.newReservation.room_id = this.selectedRoomId;
                    this.newReservation.tanggal = this.selectedDate;
                    
                    // Prefill default fields
                    if (this.isLoggedIn) {
                        this.newReservation.tipe_peminjam = this.user.tipe_user || 'internal';
                        this.newReservation.instansi = this.user.tipe_user === 'internal' ? 'UIN SGD Bandung' : '';
                    }
                    
                    this.showBookModal = true;
                },

                // Date Utilities
                formatDateIndo(dateStr) {
                    if (!dateStr) return '';
                    if (dateStr.includes('T')) dateStr = dateStr.split('T')[0];
                    if (dateStr.includes(' ')) dateStr = dateStr.split(' ')[0];
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    
                    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const monthsIndo = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                    const dayName = daysIndo[d.getDay()];
                    const monthName = monthsIndo[d.getMonth()];
                    
                    return `${dayName}, ${parts[2]} ${monthName}`;
                },

                formatTimeOnly(dateTimeStr) {
                    if (!dateTimeStr) return '';
                    const timePart = dateTimeStr.split(' ')[1] || dateTimeStr.split('T')[1];
                    if (!timePart) return dateTimeStr;
                    return timePart.substring(0, 5); // Returns HH:MM
                }
            }));
        });
    </script>
</body>
</html>
