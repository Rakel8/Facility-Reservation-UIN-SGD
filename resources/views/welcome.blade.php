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
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight">Room Reservation</h1>
                    <p class="text-xs text-slate-500 font-medium">UIN Sunan Gunung Djati Bandung</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- User Profile / Login Button -->
                <template x-if="isLoggedIn">
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-slate-800" x-text="user.name"></p>
                            <p class="text-xs font-medium text-indigo-600 uppercase tracking-wider" x-text="user.role"></p>
                        </div>
                        <div @click="activeTab = 'account'" class="w-10 h-10 rounded-full bg-indigo-100 border-2 border-indigo-200 flex items-center justify-center cursor-pointer text-indigo-700 font-bold overflow-hidden shadow-sm hover:scale-105 transition-transform">
                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
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
        
        <!-- Tab 1: Home (Pilih Ruangan & Kalender - Tampilan Utama Persis Tangkapan Layar) -->
        <div x-show="activeTab === 'home'" x-cloak class="space-y-6">
            
            <!-- Heading -->
            <div class="space-y-1">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pilih Ruangan</h2>
                <p class="text-sm sm:text-base text-slate-500 font-medium">Temukan waktu yang tepat untuk pertemuan Anda.</p>
            </div>

            <!-- Filter Pills (Rooms) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button 
                    @click="selectRoom(null)"
                    :class="selectedRoomId === null ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-all shrink-0">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    Semua Ruangan
                </button>
                <template x-for="room in rooms" :key="room.id">
                    <button 
                        @click="selectRoom(room.id)"
                        :class="selectedRoomId === room.id ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-all shrink-0">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span x-text="room.nama_ruangan"></span>
                    </button>
                </template>
            </div>

            <!-- Desktop 2-Column Grid / Mobile 1-Column -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Side: Calendar & Info Cards -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- Calendar Widget (Alpine.js Powered) -->
                    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-4">
                        <!-- Calendar Header -->
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-slate-900 capitalize" x-text="getMonthYearString()"></h3>
                            <div class="flex items-center gap-3">
                                <button @click="prevMonth()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <i data-lucide="chevron-left" class="w-5 h-5 text-indigo-600"></i>
                                </button>
                                <button @click="nextMonth()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Availability Indicators -->
                        <div class="flex items-center justify-start gap-4 text-xs font-semibold text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span> Kosong
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span> Pending
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span> Terisi
                            </span>
                        </div>

                        <!-- Days of Week Headers -->
                        <div class="grid grid-cols-7 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-y-2 text-center text-sm font-semibold">
                            <!-- Blank Days Padding -->
                            <template x-for="blank in calendarBlankDays">
                                <span class="py-2.5"></span>
                            </template>
                            
                            <!-- Calendar Days -->
                            <template x-for="day in calendarDaysInMonth" :key="day.dateString">
                                <div class="relative py-1 flex items-center justify-center">
                                    <button 
                                        @click="selectDate(day.dateString)"
                                        :class="[
                                            selectedDate === day.dateString ? 'bg-indigo-900 text-white shadow-md shadow-indigo-900/30' : 'text-slate-800 hover:bg-slate-50',
                                            day.isToday ? 'border-2 border-indigo-400' : ''
                                        ]"
                                        class="w-10 h-10 rounded-full flex flex-col items-center justify-center transition-all relative">
                                        <span x-text="day.dayNum"></span>
                                        
                                        <!-- Real-time Reservation Dot indicators -->
                                        <div class="absolute bottom-1.5 flex gap-0.5 justify-center w-full">
                                            <template x-for="status in day.statuses">
                                                <span 
                                                    :class="[
                                                        status === 'approved' ? 'bg-rose-500' : '',
                                                        status === 'pending' ? 'bg-amber-500' : '',
                                                        status === 'available' ? 'bg-emerald-500' : ''
                                                    ]"
                                                    class="w-1 h-1 rounded-full">
                                                </span>
                                            </template>
                                        </div>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Information Cards (Sesuai Tangkapan Layar) -->
                    <div class="space-y-4">
                        <!-- Card 1 -->
                        <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100/50 flex items-start gap-4">
                            <div class="p-3 bg-white text-indigo-600 rounded-xl shadow-sm">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Ketentuan Ruangan</h4>
                                <p class="text-xs text-slate-500 mt-0.5 font-medium leading-relaxed">Pemesanan maksimal 3 hari sebelum hari H penggunaan.</p>
                            </div>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100/50 flex items-start gap-4">
                            <div class="p-3 bg-white text-indigo-600 rounded-xl shadow-sm">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Persiapan Cepat</h4>
                                <p class="text-xs text-slate-500 mt-0.5 font-medium leading-relaxed">Gunakan template reservasi sebelumnya untuk menghemat waktu.</p>
                            </div>
                        </div>
                        
                        <!-- Card 3 -->
                        <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100/50 flex items-start gap-4">
                            <div class="p-3 bg-white text-indigo-600 rounded-xl shadow-sm">
                                <i data-lucide="smile" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Bantuan Admin</h4>
                                <p class="text-xs text-slate-500 mt-0.5 font-medium leading-relaxed">Hubungi admin IT melalui pesan internal jika butuh alat khusus.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Side: Availability Timeline & Submit Button -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-5 border border-slate-100 shadow-sm space-y-6">
                    
                    <!-- Availability Status Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Status Ketersediaan</span>
                            <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" x-text="formatDateIndo(selectedDate)"></h3>
                        </div>
                        <div class="p-2.5 bg-slate-50 text-indigo-600 rounded-xl border border-slate-100">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>

                    <!-- Room Context Notice if 'All Rooms' selected -->
                    <div x-show="selectedRoomId === null" class="p-3.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold rounded-2xl flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        Pilih ruangan spesifik di filter atas untuk melihat jadwal slot waktu yang detail.
                    </div>

                    <!-- Time Slots Timeline -->
                    <div class="space-y-3">
                        <template x-for="slot in timeSlots" :key="slot.time">
                            <div 
                                :class="[
                                    slot.status === 'approved' ? 'bg-rose-50/50 border-rose-100' : '',
                                    slot.status === 'pending' ? 'bg-amber-50/50 border-amber-100' : '',
                                    slot.status === 'available' ? 'bg-emerald-50/50 border-emerald-100/70' : ''
                                ]"
                                class="flex items-center justify-between p-3.5 border rounded-2xl transition-all hover:scale-[1.01]">
                                
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-bold text-slate-900" x-text="slot.time"></span>
                                    
                                    <div class="w-[2px] h-6 bg-slate-200"></div>
                                    
                                    <!-- Badge Status -->
                                    <span 
                                        :class="[
                                            slot.status === 'approved' ? 'bg-rose-100 text-rose-800' : '',
                                            slot.status === 'pending' ? 'bg-amber-100 text-amber-800' : '',
                                            slot.status === 'available' ? 'bg-emerald-100 text-emerald-800' : ''
                                        ]"
                                        class="px-3 py-1 rounded-xl text-xs font-bold capitalize" 
                                        x-text="slot.statusLabel">
                                    </span>
                                </div>

                                <div class="text-right">
                                    <template x-if="slot.status === 'approved'">
                                        <span class="text-xs font-bold text-slate-500" x-text="slot.reservation.tujuan"></span>
                                    </template>
                                    <template x-if="slot.status === 'pending'">
                                        <span class="text-xs font-bold text-slate-400">Sedang Diajukan</span>
                                    </template>
                                    <template x-if="slot.status === 'available'">
                                        <span class="text-xs font-bold text-emerald-600">Siap Dipakai</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Ajukan Reservasi Button -->
                    <button 
                        @click="openBookingModal()"
                        class="w-full py-4 bg-indigo-900 text-white rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-indigo-950 shadow-lg shadow-indigo-900/20 active:scale-95 transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        Ajukan Reservasi
                    </button>

                </div>

            </div>

        </div>

        <!-- Tab 2: Book (Formulir Pengajuan Cepat) -->
        <div x-show="activeTab === 'book'" x-cloak class="max-w-2xl mx-auto bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
            <div class="space-y-1">
                <h2 class="text-2xl font-extrabold text-slate-900">Form Pengajuan Reservasi</h2>
                <p class="text-sm text-slate-500 font-medium">Isi formulir berikut untuk meminjam ruangan.</p>
            </div>
            
            <form @submit.prevent="submitReservation" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pilih Ruangan</label>
                    <select x-model="newReservation.room_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Ruangan --</option>
                        <template x-for="room in rooms" :key="room.id">
                            <option :value="room.id" x-text="room.nama_ruangan"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tanggal</label>
                        <input type="date" x-model="newReservation.tanggal" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tujuan Peminjaman</label>
                        <input type="text" x-model="newReservation.tujuan" placeholder="Contoh: Rapat HIMA, Kuliah Umum" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Waktu Mulai</label>
                        <input type="time" x-model="newReservation.waktu_mulai" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Waktu Selesai</label>
                        <input type="time" x-model="newReservation.waktu_selesai" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all active:scale-95">
                    Kirim Permohonan Reservasi
                </button>
            </form>
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
                                
                                <template x-if="user.role === 'superadmin' || user.role === 'admin_fakultas'">
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

                            <div class="border-t border-slate-100 pt-3 flex gap-2">
                                <template x-if="res.status_approval === 'approved'">
                                    <button 
                                        @click="downloadPDF(res.id)" 
                                        class="flex-grow flex items-center justify-center gap-1.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm active:scale-95 transition-all">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Unduh Surat Izin (PDF)
                                    </button>
                                </template>
                                <template x-if="res.status_approval === 'pending'">
                                    <span class="text-xs font-semibold text-slate-400 flex items-center gap-1.5">
                                        <i data-lucide="loader" class="w-4 h-4 animate-spin text-indigo-500"></i>
                                        Menunggu Persetujuan Fakultas
                                    </span>
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
                                    <td class="p-4 text-right">
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
        <div x-show="activeTab === 'account'" x-cloak class="max-w-md mx-auto bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6 text-center">
            
            <div class="w-20 h-20 rounded-full bg-indigo-100 border-4 border-indigo-200 text-indigo-700 flex items-center justify-center text-3xl font-extrabold mx-auto shadow-md">
                <span x-text="user.name.charAt(0).toUpperCase()"></span>
            </div>
            
            <div class="space-y-1">
                <h3 class="text-xl font-extrabold text-slate-900" x-text="user.name"></h3>
                <p class="text-sm text-slate-500 font-semibold" x-text="user.email"></p>
                <div class="inline-block px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-600 font-extrabold uppercase tracking-wider text-xs rounded-xl mt-2" x-text="'Peran: ' + user.role"></div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <button 
                    @click="logout()" 
                    class="w-full py-3 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-100 font-bold rounded-xl text-sm flex items-center justify-center gap-2 transition-all active:scale-95">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Keluar dari Akun
                </button>
            </div>
        </div>

    </main>

    <!-- Bottom Navigation Bar (Mimicking User Screen Navigation) -->
    <div class="bg-white border-t border-slate-100 fixed bottom-0 left-0 right-0 z-40 py-2 px-6 flex items-center justify-around shadow-lg">
        <button 
            @click="activeTab = 'home'"
            :class="activeTab === 'home' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
            class="flex flex-col items-center gap-1 transition-all">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span class="text-[10px] uppercase font-bold tracking-wider">Home</span>
        </button>

        <button 
            @click="activeTab = 'book'"
            :class="activeTab === 'book' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
            class="flex flex-col items-center gap-1 transition-all">
            <i data-lucide="plus-square" class="w-5 h-5"></i>
            <span class="text-[10px] uppercase font-bold tracking-wider">Book</span>
        </button>

        <!-- Dynamic Approval Tab for Faculty Admin -->
        <template x-if="isLoggedIn && user.role === 'admin_fakultas'">
            <button 
                @click="activeTab = 'approvals'"
                :class="activeTab === 'approvals' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
                class="flex flex-col items-center gap-1 transition-all relative">
                <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                <span class="text-[10px] uppercase font-bold tracking-wider">Approval</span>
                <template x-if="approvals.length > 0">
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
                </template>
            </button>
        </template>

        <!-- Dynamic Rooms Tab for Superadmin -->
        <template x-if="isLoggedIn && user.role === 'superadmin'">
            <button 
                @click="activeTab = 'rooms'"
                :class="activeTab === 'rooms' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
                class="flex flex-col items-center gap-1 transition-all">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="text-[10px] uppercase font-bold tracking-wider">Rooms</span>
            </button>
        </template>

        <button 
            @click="activeTab = 'history'"
            :class="activeTab === 'history' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
            class="flex flex-col items-center gap-1 transition-all">
            <i data-lucide="history" class="w-5 h-5"></i>
            <span class="text-[10px] uppercase font-bold tracking-wider">History</span>
        </button>

        <button 
            @click="activeTab = 'account'"
            :class="activeTab === 'account' ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600'" 
            class="flex flex-col items-center gap-1 transition-all">
            <i data-lucide="user" class="w-5 h-5"></i>
            <span class="text-[10px] uppercase font-bold tracking-wider">Account</span>
        </button>
    </div>

    <!-- Login Modal (With Quick Login Peran Shortcuts) -->
    <div x-show="showLoginModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showLoginModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xl font-extrabold text-slate-900">Masuk Aplikasi</h3>
                <button @click="showLoginModal = false" class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Quick Login Peran -->
            <div class="space-y-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Uji Coba Cepat (Quick Login)</span>
                <div class="grid grid-cols-1 gap-2">
                    <button @click="quickLogin('mahasiswa@uin.ac.id')" class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-200 transition-all text-left">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Peminjam (Mahasiswa)</p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">mahasiswa@uin.ac.id</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <button @click="quickLogin('admin@uin.ac.id')" class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-200 transition-all text-left">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Admin Fakultas (Approval)</p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">admin@uin.ac.id</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </button>
                    <button @click="quickLogin('superadmin@uin.ac.id')" class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-200 transition-all text-left">
                        <div>
                            <p class="text-sm font-bold text-slate-800">Superadmin (Manajemen)</p>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">superadmin@uin.ac.id</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </button>
                </div>
            </div>

            <div class="relative flex items-center justify-center py-2">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <span class="relative bg-white px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Atau Manual</span>
            </div>

            <!-- Manual Login Form -->
            <form @submit.prevent="login()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Alamat Email</label>
                    <input type="email" x-model="loginEmail" placeholder="nama@uin.ac.id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kata Sandi</label>
                    <input type="password" x-model="loginPassword" placeholder="••••••••" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-indigo-100">
                    Masuk
                </button>
            </form>
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
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Ruangan Terpilih</label>
                    <select x-model="newReservation.room_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                        <template x-for="room in rooms" :key="room.id">
                            <option :value="room.id" x-text="room.nama_ruangan" :selected="room.id === newReservation.room_id"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tanggal Kegiatan</label>
                    <input type="date" x-model="newReservation.tanggal" readonly class="w-full p-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tujuan Peminjaman</label>
                    <input type="text" x-model="newReservation.tujuan" placeholder="Contoh: Rapat HIMA / Diskusi Kelompok" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
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
        <div @click.away="showAddRoomModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 border border-slate-100 shadow-2xl space-y-6">
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
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Kapasitas Kursi</label>
                    <input type="number" x-model="newRoom.kapasitas" placeholder="Contoh: 40" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Fasilitas Pendukung</label>
                    <input type="text" x-model="newRoom.fasilitas" placeholder="Contoh: AC, Proyektor, WiFi, Sound System" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold">
                </div>
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-md transition-all active:scale-95">
                    Simpan Ruangan
                </button>
            </form>
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
                showBookModal: false,
                showApproveModal: false,
                showAddRoomModal: false,
                
                // Form Fields
                loginEmail: '',
                loginPassword: '',
                newReservation: {
                    room_id: '',
                    tanggal: '',
                    tujuan: '',
                    waktu_mulai: '08:00',
                    waktu_selesai: '09:30'
                },
                approvalData: {
                    id: null,
                    nama_penyetuju: 'Dr. H. Ahmad Fauzi, M.Ag.',
                    jabatan_penyetuju: 'Wakil Dekan Bidang Kemahasiswaan & Kerjasama'
                },
                newRoom: {
                    nama_ruangan: '',
                    kapasitas: 40,
                    fasilitas: 'AC, Proyektor, WiFi, Sound System'
                },

                // Business Data
                rooms: [],
                allReservations: [], // Global cache for calendar highlighting
                reservations: [],    // Current logged-in user history
                approvals: [],       // Pending approvals list
                selectedRoomId: null, // Selected Room Filter ID
                selectedDate: '',     // Format: YYYY-MM-DD
                
                // Calendar Display States
                currentMonth: 0, // 0-indexed (June = 5)
                currentYear: 2026,
                calendarBlankDays: [],
                calendarDaysInMonth: [],

                // Time slots lists (Calculated dynamically)
                timeSlots: [],

                // Alpine.js native initialization method (runs automatically)
                init() {
                    // Set default selected date to today (or 2026-06-02 as per system metadata)
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const date = String(today.getDate()).padStart(2, '0');
                    this.selectedDate = `${year}-${month}-${date}`;
                    this.currentMonth = today.getMonth();
                    this.currentYear = today.getFullYear();

                    // Axios global headers config
                    if (this.token) {
                        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                        this.fetchUserData();
                    }

                    // Fetch base data
                    this.fetchRooms();
                    this.fetchGlobalReservations();

                    if (this.isLoggedIn) {
                        this.fetchHistory();
                        this.fetchApprovals();
                    }
                    
                    // Render icons
                    setTimeout(() => { lucide.createIcons(); }, 300);
                },

                // API Calls
                async login() {
                    try {
                        const res = await axios.post('/api/v1/auth/login', {
                            email: this.loginEmail,
                            password: this.loginPassword
                        });
                        
                        // Laravel AuthController wraps user and token in the 'data' key
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
                        // Laravel me() endpoint wraps the user model in the 'data' key
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
                    if (!this.isLoggedIn || this.user.role !== 'admin_fakultas') return;
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
                        
                        await axios.post('/api/v1/reservations', {
                            room_id: parseInt(this.newReservation.room_id),
                            tanggal_mulai: startDT,
                            tanggal_selesai: endDT,
                            tujuan: this.newReservation.tujuan
                        });
                        
                        alert('Peminjaman ruangan sukses diajukan! Menunggu persetujuan Admin Fakultas.');
                        this.showBookModal = false;
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
                        await axios.post('/api/v1/rooms', this.newRoom);
                        alert('Ruangan sukses ditambahkan!');
                        this.showAddRoomModal = false;
                        this.fetchRooms();
                        this.newRoom = { nama_ruangan: '', kapasitas: 40, fasilitas: 'AC, Proyektor, WiFi, Sound System' };
                    } catch (err) {
                        alert('Gagal menambah ruangan: ' + (err.response?.data?.message || err.message));
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

                async downloadPDF(reservationId) {
                    try {
                        const response = await axios.get(`/api/v1/reservations/${reservationId}/letter/pdf`, {
                            responseType: 'blob'
                        });
                        
                        // Create a temporary object URL and link to trigger browser download
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

                // Calendar Mechanics
                selectRoom(roomId) {
                    this.selectedRoomId = roomId;
                    this.buildCalendar();
                    this.buildTimeSlots();
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
                    
                    // Blank leading days padding
                    this.calendarBlankDays = Array.from({ length: firstDayOfMonth }, (_, i) => i);
                    
                    // Build Days objects
                    const days = [];
                    const today = new Date();
                    const todayString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

                    for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
                        const dateString = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
                        
                        // Find reservations for this date and selected room from global schedule
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
                    // Define slot templates
                    const slots = [
                        { time: '08:00', start: 8.0, end: 9.5 },
                        { time: '09:30', start: 9.5, end: 11.0 },
                        { time: '11:00', start: 11.0, end: 12.5 },
                        { time: '13:00', start: 13.0, end: 14.5 },
                        { time: '15:00', start: 15.0, end: 16.5 },
                        { time: '18:30', start: 18.5, end: 20.0 }
                    ];

                    this.timeSlots = slots.map(slot => {
                        // Find if there is any overlapping reservation on selectedDate for selectedRoomId from global schedule
                        let status = 'available';
                        let statusLabel = 'Tersedia';
                        let matchedRes = null;

                        if (this.selectedRoomId !== null) {
                            const resForDate = this.allReservations.filter(res => {
                                const resDate = res.tanggal_mulai.includes('T') ? res.tanggal_mulai.split('T')[0] : res.tanggal_mulai.split(' ')[0];
                                return resDate === this.selectedDate && res.room_id === this.selectedRoomId;
                            });

                            for (let res of resForDate) {
                                // Extract hours and minutes
                                // format: "2026-06-15 14:00:00" or ISO "2026-06-15T14:00:00"
                                const timePart = res.tanggal_mulai.includes('T') ? res.tanggal_mulai.split('T')[1].substring(0, 5) : res.tanggal_mulai.split(' ')[1].substring(0, 5);
                                const endTimePart = res.tanggal_selesai.includes('T') ? res.tanggal_selesai.split('T')[1].substring(0, 5) : res.tanggal_selesai.split(' ')[1].substring(0, 5);
                                
                                const startParts = timePart.split(':');
                                const endParts = endTimePart.split(':');
                                
                                const resStartDecimal = parseFloat(startParts[0]) + parseFloat(startParts[1]) / 60.0;
                                const resEndDecimal = parseFloat(endParts[0]) + parseFloat(endParts[1]) / 60.0;

                                // Overlap logic
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
