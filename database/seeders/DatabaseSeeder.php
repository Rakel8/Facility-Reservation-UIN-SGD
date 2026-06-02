<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        $admin = User::create([
            'name' => 'Admin Fakultas',
            'email' => 'admin@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
        ]);

        $peminjam = User::create([
            'name' => 'Mahasiswa Peminjam',
            'email' => 'mahasiswa@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
        ]);

        // Create dummy rooms
        $rooms = [
            [
                'nama_ruangan' => 'Aula Fakultas Sains dan Teknologi',
                'kapasitas' => 300,
                'fasilitas' => 'AC, Sound System, Proyektor, WiFi',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Ushuluddin',
                'kapasitas' => 250,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Tarbiyah dan Keguruan',
                'kapasitas' => 450,
                'fasilitas' => 'AC, Sound System, Panggung Utama, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Dakwah dan Komunikasi',
                'kapasitas' => 300,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula FEBI (Fakultas Ekonomi & Bisnis Islam)',
                'kapasitas' => 350,
                'fasilitas' => 'AC Sentral, Sound System, LED Screen, WiFi',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Adab dan Humaniora',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Psikologi',
                'kapasitas' => 150,
                'fasilitas' => 'AC, Sound System, Proyektor, Whiteboard',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Fakultas Syariah dan Hukum',
                'kapasitas' => 300,
                'fasilitas' => 'AC, Sound System, Mimbar, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula FISIP (Fakultas Ilmu Sosial & Ilmu Politik)',
                'kapasitas' => 250,
                'fasilitas' => 'AC, Sound System, Proyektor, WiFi',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Pascasarjana',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Meja Konferensi, Proyektor',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula PPG',
                'kapasitas' => 150,
                'fasilitas' => 'AC, Sound System, Smart TV, WiFi',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Abjan Soelaeman',
                'kapasitas' => 1000,
                'fasilitas' => 'AC Sentral, Sound System Konser, Panggung Besar, Screen LED',
                'status_aktif' => true,
            ],
            [
                'nama_ruangan' => 'Aula Anwar Musaddad',
                'kapasitas' => 3000,
                'fasilitas' => 'Tribun Penonton, Panggung Utama, Sound System Konser, AC Sentral',
                'status_aktif' => true,
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        // Create dummy reservations
        $roomIds = Room::pluck('id')->toArray();
        $now = now();

        $reservations = [
            ['user_id' => 3, 'room_id' => $roomIds[0], 'tanggal_mulai' => $now->copy()->addDays(1)->setHour(10)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(1)->setHour(12)->setMinute(0), 'tujuan' => 'Seminar Nasional Teknologi', 'status_approval' => 'pending'],
            ['user_id' => 3, 'room_id' => $roomIds[1], 'tanggal_mulai' => $now->copy()->addDays(2)->setHour(14)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(2)->setHour(16)->setMinute(0), 'tujuan' => 'Workshop Pengembangan Web', 'status_approval' => 'approved'],
            ['user_id' => 3, 'room_id' => $roomIds[2], 'tanggal_mulai' => $now->copy()->addDays(3)->setHour(9)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(3)->setHour(11)->setMinute(0), 'tujuan' => 'Praktikum Sistem Operasi', 'status_approval' => 'rejected', 'alasan_penolakan' => 'Ruangan sudah dipesan lebih dulu'],
            ['user_id' => 3, 'room_id' => $roomIds[3], 'tanggal_mulai' => $now->copy()->addDays(4)->setHour(13)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(4)->setHour(15)->setMinute(0), 'tujuan' => 'Rapat Koordinasi Prodi', 'status_approval' => 'approved'],
            ['user_id' => 3, 'room_id' => $roomIds[4], 'tanggal_mulai' => $now->copy()->addDays(5)->setHour(10)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(5)->setHour(12)->setMinute(0), 'tujuan' => 'Sesi Baca Buku Kolaboratif', 'status_approval' => 'pending'],
            ['user_id' => 3, 'room_id' => $roomIds[0], 'tanggal_mulai' => $now->copy()->addDays(6)->setHour(15)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(6)->setHour(17)->setMinute(0), 'tujuan' => 'Peluncuran Produk Akademik', 'status_approval' => 'approved'],
            ['user_id' => 3, 'room_id' => $roomIds[1], 'tanggal_mulai' => $now->copy()->addDays(7)->setHour(8)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(7)->setHour(10)->setMinute(0), 'tujuan' => 'Diskusi Kelompok Mahasiswa', 'status_approval' => 'approved'],
            ['user_id' => 3, 'room_id' => $roomIds[2], 'tanggal_mulai' => $now->copy()->addDays(8)->setHour(11)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(8)->setHour(13)->setMinute(0), 'tujuan' => 'Lab Session Database', 'status_approval' => 'pending'],
            ['user_id' => 3, 'room_id' => $roomIds[3], 'tanggal_mulai' => $now->copy()->addDays(9)->setHour(14)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(9)->setHour(16)->setMinute(0), 'tujuan' => 'Pertemuan Dekan dengan Dosen', 'status_approval' => 'rejected', 'alasan_penolakan' => 'Jadwal bentrok dengan acara penting'],
            ['user_id' => 3, 'room_id' => $roomIds[4], 'tanggal_mulai' => $now->copy()->addDays(10)->setHour(9)->setMinute(0), 'tanggal_selesai' => $now->copy()->addDays(10)->setHour(11)->setMinute(0), 'tujuan' => 'Acara Literasi Digital', 'status_approval' => 'approved'],
        ];

        foreach ($reservations as $reservationData) {
            Reservation::create($reservationData);
        }
    }
}
