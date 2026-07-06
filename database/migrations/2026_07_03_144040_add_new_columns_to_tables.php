<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('tingkat', ['fakultas', 'universitas', 'kemahasiswaan'])->default('fakultas');
            $table->foreignId('fakultas_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_telepon')->nullable();
            $table->boolean('eksternal_diizinkan')->default(true);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('peminjam')->change();
            $table->foreignId('fakultas_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->string('no_telepon')->nullable();
            $table->string('tipe_user')->default('internal'); // internal or eksternal
            $table->string('nim_nip')->nullable();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('instansi')->nullable();
            $table->text('deskripsi_acara')->nullable();
            $table->string('tipe_peminjam')->default('internal'); // internal or eksternal
            $table->string('approver_role')->nullable();
            $table->string('proposal_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['instansi', 'deskripsi_acara', 'tipe_peminjam', 'approver_role', 'proposal_file']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn(['fakultas_id', 'no_telepon', 'tipe_user', 'nim_nip']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['fakultas_id']);
            $table->dropColumn(['tingkat', 'fakultas_id', 'deskripsi', 'gambar', 'pic_nama', 'pic_telepon', 'eksternal_diizinkan']);
        });
    }
};
