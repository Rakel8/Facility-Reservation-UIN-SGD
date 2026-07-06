<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed some faculties
        Faculty::create(['nama_fakultas' => 'Sains dan Teknologi']);
        Faculty::create(['nama_fakultas' => 'Ushuluddin']);
    }

    /** @test */
    public function test_it_can_register_a_new_user()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telepon' => '081234567890',
            'tipe_user' => 'internal',
            'nim_nip' => '1122334455'
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'tipe_user' => 'internal',
            'nim_nip' => '1122334455'
        ]);
    }

    /** @test */
    public function test_it_enforces_room_permissions_for_external_borrowers()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'peminjam',
            'tipe_user' => 'eksternal'
        ]);

        // Internal only room
        $room = Room::create([
            'nama_ruangan' => 'Lecture Hall',
            'kapasitas' => 100,
            'fasilitas' => 'AC',
            'tingkat' => 'universitas',
            'eksternal_diizinkan' => false
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/reservations', [
                'room_id' => $room->id,
                'tanggal_mulai' => '2026-06-15T08:00:00',
                'tanggal_selesai' => '2026-06-15T10:00:00',
                'tujuan' => 'Rapat Eksternal',
                'instansi' => 'PT. ABC',
                'deskripsi_acara' => 'Deskripsi',
                'tipe_peminjam' => 'eksternal',
                'proposal_file' => $file
            ]);

        $response->assertStatus(422); // Validation / Business Logic failure
    }

    /** @test */
    public function test_it_routes_faculty_reservation_to_admin_fakultas()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'peminjam',
            'tipe_user' => 'internal'
        ]);

        $faculty = Faculty::first();

        $room = Room::create([
            'nama_ruangan' => 'Aula Fakultas',
            'kapasitas' => 50,
            'fasilitas' => 'AC',
            'tingkat' => 'fakultas',
            'fakultas_id' => $faculty->id,
            'eksternal_diizinkan' => true
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/reservations', [
                'room_id' => $room->id,
                'tanggal_mulai' => '2026-06-15T08:00:00',
                'tanggal_selesai' => '2026-06-15T10:00:00',
                'tujuan' => 'Rapat Himpunan',
                'instansi' => 'HIMA IF',
                'deskripsi_acara' => 'Diskusi',
                'tipe_peminjam' => 'internal',
                'proposal_file' => $file
            ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('reservations', [
            'room_id' => $room->id,
            'approver_role' => 'admin_fakultas'
        ]);
    }

    /** @test */
    public function test_it_routes_university_weekend_reservation_to_admin_bisnis()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'peminjam',
            'tipe_user' => 'internal'
        ]);

        $room = Room::create([
            'nama_ruangan' => 'Aula Utama',
            'kapasitas' => 200,
            'fasilitas' => 'AC',
            'tingkat' => 'universitas',
            'eksternal_diizinkan' => true
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        // Saturday booking (Weekend)
        $response = $this->actingAs($user)
            ->postJson('/api/v1/reservations', [
                'room_id' => $room->id,
                'tanggal_mulai' => '2026-06-20T08:00:00', // Saturday
                'tanggal_selesai' => '2026-06-20T10:00:00',
                'tujuan' => 'Seminar Weekend',
                'instansi' => 'HIMA IF',
                'deskripsi_acara' => 'Seminar',
                'tipe_peminjam' => 'internal',
                'proposal_file' => $file
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('reservations', [
            'room_id' => $room->id,
            'approver_role' => 'admin_bisnis'
        ]);
    }

    /** @test */
    public function test_it_routes_university_weekday_reservation_to_admin_universitas()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'peminjam',
            'tipe_user' => 'internal'
        ]);

        $room = Room::create([
            'nama_ruangan' => 'Aula Utama',
            'kapasitas' => 200,
            'fasilitas' => 'AC',
            'tingkat' => 'universitas',
            'eksternal_diizinkan' => true
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        // Monday booking (Weekday)
        $response = $this->actingAs($user)
            ->postJson('/api/v1/reservations', [
                'room_id' => $room->id,
                'tanggal_mulai' => '2026-06-15T08:00:00', // Monday
                'tanggal_selesai' => '2026-06-15T10:00:00',
                'tujuan' => 'Kuliah Umum',
                'instansi' => 'UIN SGD',
                'deskripsi_acara' => 'Kuliah',
                'tipe_peminjam' => 'internal',
                'proposal_file' => $file
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('reservations', [
            'room_id' => $room->id,
            'approver_role' => 'admin_universitas'
        ]);
    }

    /** @test */
    public function test_it_filters_pending_approvals_correctly_for_admin_fakultas()
    {
        Storage::fake('public');

        $faculty = Faculty::first();
        $otherFaculty = Faculty::create(['nama_fakultas' => 'Fakultas Lain']);

        $adminFakultas = User::factory()->create([
            'role' => 'admin_fakultas',
            'fakultas_id' => $faculty->id
        ]);

        $roomMyFaculty = Room::create([
            'nama_ruangan' => 'Aula Fakultas Saya',
            'kapasitas' => 50,
            'fasilitas' => 'AC',
            'tingkat' => 'fakultas',
            'fakultas_id' => $faculty->id,
            'eksternal_diizinkan' => true
        ]);

        $roomOtherFaculty = Room::create([
            'nama_ruangan' => 'Aula Fakultas Lain',
            'kapasitas' => 50,
            'fasilitas' => 'AC',
            'tingkat' => 'fakultas',
            'fakultas_id' => $otherFaculty->id,
            'eksternal_diizinkan' => true
        ]);

        $res1 = Reservation::create([
            'user_id' => User::factory()->create()->id,
            'room_id' => $roomMyFaculty->id,
            'tanggal_mulai' => '2026-06-15 08:00:00',
            'tanggal_selesai' => '2026-06-15 10:00:00',
            'tujuan' => 'Tujuan 1',
            'instansi' => 'Instansi 1',
            'deskripsi_acara' => 'Detail 1',
            'tipe_peminjam' => 'internal',
            'status_approval' => 'pending',
            'approver_role' => 'admin_fakultas'
        ]);

        $res2 = Reservation::create([
            'user_id' => User::factory()->create()->id,
            'room_id' => $roomOtherFaculty->id,
            'tanggal_mulai' => '2026-06-15 08:00:00',
            'tanggal_selesai' => '2026-06-15 10:00:00',
            'tujuan' => 'Tujuan 2',
            'instansi' => 'Instansi 2',
            'deskripsi_acara' => 'Detail 2',
            'tipe_peminjam' => 'internal',
            'status_approval' => 'pending',
            'approver_role' => 'admin_fakultas'
        ]);

        $response = $this->actingAs($adminFakultas)
            ->getJson('/api/v1/approvals/pending');

        $response->assertStatus(200);
        
        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertContains($res1->id, $ids);
        $this->assertNotContains($res2->id, $ids);
    }

    /** @test */
    public function test_superadmin_can_create_room_with_image_urls()
    {
        $superadmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($superadmin)
            ->postJson('/api/v1/rooms', [
                'nama_ruangan' => 'Aula Unik',
                'kapasitas' => 120,
                'fasilitas' => 'AC, Sound System',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'gambar_1' => 'https://example.com/image1.jpg',
                'gambar_2' => 'https://example.com/image2.jpg',
            ]);

        $response->assertStatus(201);
        
        $room = Room::where('nama_ruangan', 'Aula Unik')->first();
        $this->assertNotNull($room);
        
        $images = json_decode($room->gambar, true);
        $this->assertEquals('https://example.com/image1.jpg', $images[0]);
        $this->assertEquals('https://example.com/image2.jpg', $images[1]);
        
        // Test updating image URLs
        $updateResponse = $this->actingAs($superadmin)
            ->putJson('/api/v1/rooms/' . $room->id, [
                'nama_ruangan' => 'Aula Unik Updated',
                'kapasitas' => 120,
                'fasilitas' => 'AC, Sound System',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'gambar_1' => 'https://example.com/updated1.jpg',
                'gambar_2' => 'https://example.com/image2.jpg',
            ]);

        $updateResponse->assertStatus(200);
        $room->refresh();
        $updatedImages = json_decode($room->gambar, true);
        $this->assertEquals('https://example.com/updated1.jpg', $updatedImages[0]);
        $this->assertEquals('https://example.com/image2.jpg', $updatedImages[1]);
    }

    /** @test */
    public function test_superadmin_can_create_room_with_google_drive_links()
    {
        $superadmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $response = $this->actingAs($superadmin)
            ->postJson('/api/v1/rooms', [
                'nama_ruangan' => 'Aula GDrive Test',
                'kapasitas' => 80,
                'fasilitas' => 'AC',
                'tingkat' => 'universitas',
                'eksternal_diizinkan' => true,
                'status_aktif' => true,
                'gambar_1' => 'https://drive.google.com/file/d/1A2B3C4D5E6F7G8H9I/view?usp=sharing',
                'gambar_2' => 'https://drive.google.com/open?id=9Z8Y7X6W5V4U3T2S1R',
            ]);

        $response->assertStatus(201);
        
        $room = Room::where('nama_ruangan', 'Aula GDrive Test')->first();
        $this->assertNotNull($room);
        
        $images = json_decode($room->gambar, true);
        $this->assertEquals('https://lh3.googleusercontent.com/d/1A2B3C4D5E6F7G8H9I', $images[0]);
        $this->assertEquals('https://lh3.googleusercontent.com/d/9Z8Y7X6W5V4U3T2S1R', $images[1]);
    }

    /** @test */
    public function test_superadmin_can_update_and_delete_admins()
    {
        $superadmin = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $adminFakultas = User::factory()->create([
            'name' => 'Admin Awal',
            'email' => 'adminawal@example.com',
            'role' => 'admin_fakultas',
        ]);

        $faculty = Faculty::first();

        // 1. Update Admin (Change name, email, and assign faculty scope)
        $response = $this->actingAs($superadmin)
            ->putJson('/api/v1/admins/' . $adminFakultas->id, [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@example.com',
                'role' => 'admin_fakultas',
                'fakultas_id' => $faculty->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $adminFakultas->id,
            'name' => 'Admin Baru',
            'email' => 'adminbaru@example.com',
            'fakultas_id' => $faculty->id,
        ]);

        // 2. Delete Admin
        $deleteResponse = $this->actingAs($superadmin)
            ->deleteJson('/api/v1/admins/' . $adminFakultas->id);

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $adminFakultas->id,
        ]);
    }
}
