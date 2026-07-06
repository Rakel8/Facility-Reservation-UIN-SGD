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
        // 1. Create Faculties
        $facultiesData = [
            'Fakultas Ilmu Sosial dan Ilmu Politik' => 'FISIP',
            'Fakultas Sains dan Teknologi' => 'FST',
            'Fakultas Psikologi' => 'FPsi',
            'Fakultas Dakwah dan Komunikasi' => 'FDK',
            'Fakultas Adab dan Humaniora' => 'FAH',
            'Fakultas Ekonomi dan Bisnis Islam' => 'FEBI',
            'Fakultas Tarbiyah dan Keguruan' => 'FTK',
            'Fakultas Ushuluddin' => 'FU',
            'Fakultas Syariah dan Hukum' => 'FSH',
            'Lembaga Penelitian dan Pengabdian Kepada Masyarakat' => 'LP2M',
            'Pusat Teknologi Informasi dan Pangkalan Data' => 'PTIPD',
            'Pascasarjana' => 'Pascasarjana',
        ];

        $faculties = [];
        foreach ($facultiesData as $fullName => $shortName) {
            $faculties[$shortName] = \App\Models\Faculty::create([
                'nama_fakultas' => $fullName,
            ]);
        }

        // 2. Create official 22 rooms (halls)
        $roomsData = [
            // 1.1 Universitas (12 rooms)
            [
                'nama_ruangan' => 'Aula Anwar Musyadad',
                'kapasitas' => 1500,
                'fasilitas' => 'Tribun Penonton, Panggung Utama, Sound System Konser, AC Sentral',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'deskripsi' => 'Gedung Serbaguna utama Universitas dengan kapasitas sangat besar, cocok untuk wisuda dan konser.',
                'pic_nama' => 'H. Mumuh, S.Sos.',
                'pic_telepon' => '081234567890',
            ],
            [
                'nama_ruangan' => 'Aula Abjan Solaeman',
                'kapasitas' => 200,
                'fasilitas' => 'AC Sentral, Sound System Konser, Panggung Besar, Screen LED',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'deskripsi' => 'Aula Universitas serbaguna kapasitas sedang untuk seminar nasional dan acara seni.',
                'pic_nama' => 'Dadan Ramdan, M.Ag.',
                'pic_telepon' => '082234567891',
            ],
            [
                'nama_ruangan' => 'Aula SC A',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Proyektor, Kursi Lipat',
                'tingkat' => 'kemahasiswaan',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Student Center gedung A untuk kegiatan kemahasiswaan dan organisasi.',
                'pic_nama' => 'Andri Yanuardi, S.E.',
                'pic_telepon' => '083234567892',
            ],
            [
                'nama_ruangan' => 'Aula SC B',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Proyektor, Kursi Lipat',
                'tingkat' => 'kemahasiswaan',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Student Center gedung B untuk rapat dan latihan kesenian.',
                'pic_nama' => 'Andri Yanuardi, S.E.',
                'pic_telepon' => '083234567892',
            ],
            [
                'nama_ruangan' => 'Aula SC Lantai 4',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Panggung Portable, Proyektor, WiFi',
                'tingkat' => 'kemahasiswaan',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula utama di Lantai 4 Student Center, ideal untuk diskusi panel dan pelantikan.',
                'pic_nama' => 'Cecep Supriadi, M.M.',
                'pic_telepon' => '084234567893',
            ],
            [
                'nama_ruangan' => 'Taman Agro Kampus 2',
                'kapasitas' => 200,
                'fasilitas' => 'Panggung Terbuka, Gazebo, Kursi Taman, Listrik Outdoor',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'deskripsi' => 'Area outdoor hijau di Kampus 2, sangat cocok untuk pameran, festival, dan gathering.',
                'pic_nama' => 'Dr. Ir. Yayat, M.P.',
                'pic_telepon' => '085234567894',
            ],
            [
                'nama_ruangan' => 'Aula PPG',
                'kapasitas' => 300,
                'fasilitas' => 'AC, Sound System, Smart TV, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FTK',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula khusus untuk pelatihan guru dan rapat internal universitas (tidak untuk eksternal).',
                'pic_nama' => 'Drs. H. Endang, M.Ed.',
                'pic_telepon' => '086234567895',
            ],
            [
                'nama_ruangan' => 'Aula Pascasarjana Selatan',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Meja Konferensi, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'Pascasarjana',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Gedung Pascasarjana Selatan khusus untuk ujian sidang dan seminar akademis internal.',
                'pic_nama' => 'Prof. Dr. H. Uus, M.Ag.',
                'pic_telepon' => '087234567896',
            ],
            [
                'nama_ruangan' => 'Aula Pascasarjana Utara',
                'kapasitas' => 150,
                'fasilitas' => 'AC, Sound System, Meja Konferensi, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'Pascasarjana',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Gedung Pascasarjana Utara khusus untuk seminar, lokakarya, dan ujian akademik internal.',
                'pic_nama' => 'Prof. Dr. H. Uus, M.Ag.',
                'pic_telepon' => '087234567896',
            ],
            [
                'nama_ruangan' => 'Aula Lecture Hall / LP2M',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, LED Proyektor, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'LP2M',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Gedung kuliah umum tingkat universitas khusus kegiatan akademik dosen dan mahasiswa internal.',
                'pic_nama' => 'H. Mulyana, M.T.',
                'pic_telepon' => '088234567897',
            ],
            [
                'nama_ruangan' => 'Aula PTPID',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Smart TV, Lab Komputer, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'PTIPD',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula serbaguna PTPID untuk pelatihan IT dan rapat internal teknis.',
                'pic_nama' => 'Yudi A. Subekti, M.Kom.',
                'pic_telepon' => '089234567898',
            ],


            // 1.2 Fakultas (10 rooms)
            [
                'nama_ruangan' => 'Aula Fak. Sosial Ilmu Politik',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Proyektor, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FISIP',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula FISIP untuk menunjang kegiatan seminar, rapat organisasi mahasiswa, dan kuliah tamu.',
                'pic_nama' => 'Dr. H. Asep, M.Si.',
                'pic_telepon' => '081298765430',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Sains dan Teknologi',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Proyektor, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FST',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Utama Fakultas Sains dan Teknologi, ideal untuk seminar, pameran poster ilmiah, dan rapat dekanat.',
                'pic_nama' => 'Drs. Jaenudin, M.T.',
                'pic_telepon' => '081298765431',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Psikologi',
                'kapasitas' => 70,
                'fasilitas' => 'AC, Sound System, Proyektor, Whiteboard',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FPsi',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Fakultas Psikologi untuk seminar interaktif, workshop konseling, dan bedah buku.',
                'pic_nama' => 'Dr. Hj. Rini, M.Psi.',
                'pic_telepon' => '081298765432',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Dakwah & Komunikasi',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FDK',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula FDK untuk menunjang pementasan dakwah, praktek khotbah, dan seminar komunikasi.',
                'pic_nama' => 'Drs. H. Maman, M.Ag.',
                'pic_telepon' => '081298765433',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Adab dan Humaniora',
                'kapasitas' => 120,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FAH',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Fakultas Adab dan Humaniora untuk kegiatan seni budaya sastra, seminar, dan bedah karya ilmiah.',
                'pic_nama' => 'H. Endang Supriatna, M.Hum.',
                'pic_telepon' => '081298765434',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Ekonomi dan Bisnis Islam',
                'kapasitas' => 100,
                'fasilitas' => 'AC Sentral, Sound System, LED Screen, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FEBI',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula modern FEBI dengan fasilitas multimedia lengkap, sangat bagus untuk kuliah umum ekonomi syariah.',
                'pic_nama' => 'Dr. H. Usep, M.Si.',
                'pic_telepon' => '081298765435',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Tarbiyah Lama',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Panggung Utama, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FTK',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula FTK Gedung Lama untuk rapat besar fakultas, sosialisasi kurikulum, dan kegiatan mahasiswa.',
                'pic_nama' => 'H. Dedi Effendi, M.H.',
                'pic_telepon' => '081298765436',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Tarbiyah Baru',
                'kapasitas' => 100,
                'fasilitas' => 'AC Sentral, Sound System, LCD Projector, WiFi',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FTK',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula FTK Gedung Baru berkapasitas besar dengan setup modern untuk seminar nasional pendidikan.',
                'pic_nama' => 'H. Dedi Effendi, M.H.',
                'pic_telepon' => '081298765436',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Ushuluddin',
                'kapasitas' => 100,
                'fasilitas' => 'AC, Sound System, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FU',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Fakultas Ushuluddin untuk seminar keagamaan, diskusi filsafat, dan kajian tasawuf.',
                'pic_nama' => 'Drs. H. Ahmad, M.Ag.',
                'pic_telepon' => '081298765437',
            ],
            [
                'nama_ruangan' => 'Aula Fak. Syarkum',
                'kapasitas' => 200,
                'fasilitas' => 'AC, Sound System, Mimbar, Proyektor',
                'tingkat' => 'fakultas',
                'fakultas_short' => 'FSH',
                'eksternal_diizinkan' => false,
                'status_aktif' => true,
                'deskripsi' => 'Aula Fakultas Syariah dan Hukum (Gedung Syarkum), ideal untuk peradilan semu dan rapat akademik.',
                'pic_nama' => 'H. Dedi Effendi, M.H.',
                'pic_telepon' => '081298765438',
            ],
        ];

        // Load photo links from foto-aula.md if it exists
        $photos = [];
        $mdPath = base_path('foto-aula.md');
        if (file_exists($mdPath)) {
            $content = file_get_contents($mdPath);
            // Split by ### headers to find each room's section
            $sections = explode('### ', $content);
            foreach ($sections as $section) {
                $lines = explode("\n", trim($section));
                if (count($lines) > 0) {
                    $roomName = trim($lines[0]);
                    $links = [];
                    foreach ($lines as $line) {
                        if (preg_match('/\* \*\*gambar \d\*\*:\s*(https?:\/\/\S+)/i', $line, $matches)) {
                            $link = trim($matches[1]);
                            // Convert Google Drive view link to direct link
                            if (preg_match('/drive\.google\.com\/file\/d\/([^\/\?]+)/i', $link, $idMatches)) {
                                $link = "https://lh3.googleusercontent.com/d/" . $idMatches[1];
                            } elseif (preg_match('/drive\.google\.com\/uc\?.*?id=([^\&\s]+)/i', $link, $idMatches)) {
                                $link = "https://lh3.googleusercontent.com/d/" . $idMatches[1];
                            }
                            $links[] = $link;
                        }
                    }
                    if (!empty($links)) {
                        $photos[$roomName] = $links;
                    }
                }
            }
        }

        $rooms = [];
        foreach ($roomsData as $rData) {
            $mappedData = [
                'nama_ruangan' => $rData['nama_ruangan'],
                'kapasitas' => $rData['kapasitas'],
                'fasilitas' => $rData['fasilitas'],
                'tingkat' => $rData['tingkat'],
                'eksternal_diizinkan' => $rData['eksternal_diizinkan'],
                'status_aktif' => $rData['status_aktif'],
                'deskripsi' => $rData['deskripsi'],
                'pic_nama' => $rData['pic_nama'],
                'pic_telepon' => $rData['pic_telepon'],
            ];

            if ($rData['tingkat'] === 'fakultas' && isset($rData['fakultas_short'])) {
                $mappedData['fakultas_id'] = $faculties[$rData['fakultas_short']]->id;
            }

            // Assign parsed images if available
            if (isset($photos[$rData['nama_ruangan']])) {
                $mappedData['gambar'] = json_encode($photos[$rData['nama_ruangan']]);
            }

            $rooms[] = Room::create($mappedData);
        }

        // 3. Create test users with revised roles
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        $adminFakultas = User::create([
            'name' => 'Admin FST (Fakultas)',
            'email' => 'admin@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FST']->id,
        ]);
        $fstRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Sains dan Teknologi'])->get();
        $adminFakultas->rooms()->attach($fstRooms->pluck('id'));

        $adminUmum = User::create([
            'name' => 'Admin Bagian Umum (Aljamiah)',
            'email' => 'adminumum@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_universitas',
        ]);
        $umumRooms = Room::whereIn('nama_ruangan', ['Aula Anwar Musyadad', 'Aula Abjan Solaeman', 'Taman Agro Kampus 2'])->get();
        $adminUmum->rooms()->attach($umumRooms->pluck('id'));

        $adminKemahasiswaan = User::create([
            'name' => 'Admin Kemahasiswaan (Aljamiah)',
            'email' => 'adminkemahasiswaan@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_kemahasiswaan',
        ]);
        $mhsRooms = Room::whereIn('nama_ruangan', ['Aula SC A', 'Aula SC B', 'Aula SC Lantai 4'])->get();
        $adminKemahasiswaan->rooms()->attach($mhsRooms->pluck('id'));

        $adminPasca = User::create([
            'name' => 'Admin Pascasarjana',
            'email' => 'adminpasca@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['Pascasarjana']->id,
        ]);
        $pascaRooms = Room::whereIn('nama_ruangan', ['Aula Pascasarjana Selatan', 'Aula Pascasarjana Utara'])->get();
        $adminPasca->rooms()->attach($pascaRooms->pluck('id'));

        $adminLp2m = User::create([
            'name' => 'Admin LP2M',
            'email' => 'adminlp2m@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['LP2M']->id,
        ]);
        $lp2mRooms = Room::whereIn('nama_ruangan', ['Aula Lecture Hall / LP2M'])->get();
        $adminLp2m->rooms()->attach($lp2mRooms->pluck('id'));

        $adminPtipd = User::create([
            'name' => 'Admin PTIPD',
            'email' => 'adminptipd@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['PTIPD']->id,
        ]);
        $ptipdRooms = Room::whereIn('nama_ruangan', ['Aula PTPID'])->get();
        $adminPtipd->rooms()->attach($ptipdRooms->pluck('id'));

        $adminTarbiyah = User::create([
            'name' => 'Admin Fakultas Tarbiyah',
            'email' => 'admintarbiyah@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FTK']->id,
        ]);
        $tarbiyahRooms = Room::whereIn('nama_ruangan', ['Aula PPG', 'Aula Fak. Tarbiyah Lama', 'Aula Fak. Tarbiyah Baru'])->get();
        $adminTarbiyah->rooms()->attach($tarbiyahRooms->pluck('id'));

        $adminFisip = User::create([
            'name' => 'Admin FISIP (Fakultas)',
            'email' => 'adminfisip@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FISIP']->id,
        ]);
        $fisipRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Sosial Ilmu Politik'])->get();
        $adminFisip->rooms()->attach($fisipRooms->pluck('id'));

        $adminPsikologi = User::create([
            'name' => 'Admin Psikologi (Fakultas)',
            'email' => 'adminpsikologi@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FPsi']->id,
        ]);
        $psikologiRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Psikologi'])->get();
        $adminPsikologi->rooms()->attach($psikologiRooms->pluck('id'));

        $adminDakom = User::create([
            'name' => 'Admin DAKOM (Fakultas)',
            'email' => 'admindakom@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FDK']->id,
        ]);
        $dakomRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Dakwah & Komunikasi'])->get();
        $adminDakom->rooms()->attach($dakomRooms->pluck('id'));

        $adminAdhum = User::create([
            'name' => 'Admin Adhum (Fakultas)',
            'email' => 'adminadhum@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FAH']->id,
        ]);
        $adhumRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Adab dan Humaniora'])->get();
        $adminAdhum->rooms()->attach($adhumRooms->pluck('id'));

        $adminEbi = User::create([
            'name' => 'Admin EBI (Fakultas)',
            'email' => 'adminebi@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FEBI']->id,
        ]);
        $ebiRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Ekonomi dan Bisnis Islam'])->get();
        $adminEbi->rooms()->attach($ebiRooms->pluck('id'));

        $adminUshuluddin = User::create([
            'name' => 'Admin Ushuluddin (Fakultas)',
            'email' => 'adminushuluddin@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FU']->id,
        ]);
        $ushuluddinRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Ushuluddin'])->get();
        $adminUshuluddin->rooms()->attach($ushuluddinRooms->pluck('id'));

        $adminSyarkum = User::create([
            'name' => 'Admin Syarkum (Fakultas)',
            'email' => 'adminsyarkum@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculties['FSH']->id,
        ]);
        $syarkumRooms = Room::whereIn('nama_ruangan', ['Aula Fak. Syarkum'])->get();
        $adminSyarkum->rooms()->attach($syarkumRooms->pluck('id'));

        $adminBisnis = User::create([
            'name' => 'Admin Pusat Bisnis',
            'email' => 'adminbisnis@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin_bisnis',
        ]);

        $peminjam = User::create([
            'name' => 'Mahasiswa Peminjam',
            'email' => 'mahasiswa@uin.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
        ]);

        // 4. Create initial reservations
        $now = now();
        $fstRoom = Room::where('nama_ruangan', 'Aula Fak. Sains dan Teknologi')->first();
        $musyadadRoom = Room::where('nama_ruangan', 'Aula Anwar Musyadad')->first();
        $ppgRoom = Room::where('nama_ruangan', 'Aula PPG')->first();

        // Pending Faculty Reservation
        Reservation::create([
            'user_id' => $peminjam->id,
            'room_id' => $fstRoom->id,
            'tanggal_mulai' => $now->copy()->addDays(1)->setHour(10)->setMinute(0)->setSecond(0),
            'tanggal_selesai' => $now->copy()->addDays(1)->setHour(12)->setMinute(0)->setSecond(0),
            'tujuan' => 'Seminar Teknologi Masa Depan FST',
            'instansi' => 'Himpunan Mahasiswa Teknik Informatika',
            'deskripsi_acara' => 'Seminar nasional tentang AI dan Agen Koding Mandiri.',
            'tipe_peminjam' => 'internal',
            'approver_role' => 'admin_fakultas',
            'status_approval' => 'pending',
        ]);

        // Approved Faculty Reservation
        $appReservation1 = Reservation::create([
            'user_id' => $peminjam->id,
            'room_id' => $fstRoom->id,
            'tanggal_mulai' => $now->copy()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0),
            'tanggal_selesai' => $now->copy()->addDays(2)->setHour(16)->setMinute(0)->setSecond(0),
            'tujuan' => 'Workshop Git & GitHub',
            'instansi' => 'HMIF',
            'deskripsi_acara' => 'Pelatihan kolaborasi code untuk mahasiswa baru.',
            'tipe_peminjam' => 'internal',
            'approver_role' => 'admin_fakultas',
            'status_approval' => 'approved',
            'tanggal_persetujuan' => $now,
            'nama_penyetuju' => 'Drs. Jaenudin, M.T.',
            'jabatan_penyetuju' => 'Admin Dekanat FST',
        ]);
        // Trigger boot logic for approved reservation code number
        $nomor = 'IZIN-' . str_pad($appReservation1->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
        $appReservation1->update(['nomor_surat' => $nomor]);

        // Pending University internal weekday reservation -> goes to admin_universitas
        Reservation::create([
            'user_id' => $peminjam->id,
            'room_id' => $musyadadRoom->id,
            // Ensure weekday: Monday of next week
            'tanggal_mulai' => $now->copy()->next(1)->setHour(9)->setMinute(0)->setSecond(0), // 1 = Monday
            'tanggal_selesai' => $now->copy()->next(1)->setHour(11)->setMinute(0)->setSecond(0),
            'tujuan' => 'Rapat Kerja Himpunan Universitas',
            'instansi' => 'Badan Eksekutif Mahasiswa UIN',
            'deskripsi_acara' => 'Rapat koordinasi persiapan Dies Natalis UIN Sunan Gunung Djati.',
            'tipe_peminjam' => 'internal',
            'approver_role' => 'admin_universitas',
            'status_approval' => 'pending',
        ]);

        // Pending University external reservation -> goes to admin_bisnis
        Reservation::create([
            'user_id' => $peminjam->id,
            'room_id' => $musyadadRoom->id,
            // Weekday
            'tanggal_mulai' => $now->copy()->next(2)->setHour(13)->setMinute(0)->setSecond(0), // 2 = Tuesday
            'tanggal_selesai' => $now->copy()->next(2)->setHour(15)->setMinute(0)->setSecond(0),
            'tujuan' => 'Pameran Buku Gramedia',
            'instansi' => 'PT. Gramedia Asri Media',
            'deskripsi_acara' => 'Pameran buku dan alat tulis keliling di area kampus.',
            'tipe_peminjam' => 'eksternal',
            'approver_role' => 'admin_bisnis',
            'status_approval' => 'pending',
        ]);

        // Pending University internal weekend reservation -> goes to admin_bisnis
        Reservation::create([
            'user_id' => $peminjam->id,
            'room_id' => $musyadadRoom->id,
            // Weekend: Saturday next week
            'tanggal_mulai' => $now->copy()->next(6)->setHour(10)->setMinute(0)->setSecond(0), // 6 = Saturday
            'tanggal_selesai' => $now->copy()->next(6)->setHour(12)->setMinute(0)->setSecond(0),
            'tujuan' => 'Seminar Entrepreneurship Mahasiswa',
            'instansi' => 'Koperasi Mahasiswa',
            'deskripsi_acara' => 'Seminar bisnis pada hari sabtu untuk mahasiswa umum.',
            'tipe_peminjam' => 'internal',
            'approver_role' => 'admin_bisnis',
            'status_approval' => 'pending',
        ]);
    }
}
