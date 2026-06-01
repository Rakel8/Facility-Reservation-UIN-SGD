<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }
        .kop {
            margin-top: 0;
            padding-top: 0;
        }
        .kop-logo img {
            width: 70px;
            height: auto;
            margin-right: 12px;
        }
        .kop-text { text-align: center; flex: 1; }
        .kop-text .ministry  { font-size: 10pt; letter-spacing: 1px; margin: 0; }
        .kop-text .university { font-size: 13pt; font-weight: bold; margin: 2px 0; }
        .kop-text .address   { font-size: 8.5pt; margin-top: 3px; line-height: 1.3; }

        /* Meta surat */
        .meta-surat {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;   /* dikurangi */
            font-size: 11pt;
        }
        .meta-kiri table { border-collapse: collapse; }
        .meta-kiri td { vertical-align: top; padding: 1px 0; }
        .meta-kiri td:nth-child(2) { padding: 1px 6px; }

        /* Tujuan */
        .tujuan { margin-bottom: 10px; font-size: 11pt; }
        .tujuan p { margin: 0; line-height: 1.5; }

        /* Body */
        .body { font-size: 11pt; text-align: justify; line-height: 1.5; }
        .body p { margin: 5px 0; }   /* dikurangi dari 8px */

        /* Info kegiatan */
        .info-kegiatan { margin: 8px 0 8px 25px; }
        .info-kegiatan table { border-collapse: collapse; }
        .info-kegiatan td { vertical-align: top; padding: 1px 0; font-size: 11pt; }
        .info-kegiatan td:nth-child(2) { padding: 1px 8px; }

        /* Ketentuan */
        .ketentuan { margin: 5px 0 5px 25px; }
        .ketentuan ol { margin: 0; padding-left: 18px; }
        .ketentuan ol li { margin-bottom: 3px; }

        /* TTD — rata kanan */
        .ttd {
            margin-top: 20px;
            text-align: right;     /* seluruh blok ke kanan */
            font-size: 11pt;
        }
        .ttd p { margin: 0; line-height: 1.5; }
        .ttd .jabatan-atas { font-weight: bold; }
        .ttd .jabatan-sub  { font-style: italic; font-size: 10pt; }
        .ttd .ruang-ttd    { height: 60px; }   /* dikurangi dari 70px */
        .ttd .nama         { font-weight: bold; text-decoration: underline; }
        .ttd .nip          { font-size: 10pt; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
    </style>
</head>
<body>
<div class="container">

    {{-- ════════════ KOP SURAT ════════════ --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1.5px solid #000; margin-bottom: 20px;">
        <tr>
            <td width="18%" align="center" style="vertical-align: middle; padding-bottom: 10px;">
                
               <img src="{{ public_path('images/logo/Logo-uinsgd_official.jpg') }}" width="85">
                
            </td>
            <td width="82%" align="center" style="vertical-align: middle; padding-bottom: 10px; padding-right: 18%;"> 
                <h1 style="font-size: 16pt; font-weight: bold; margin: 0; line-height: 1.2;">KEMENTERIAN AGAMA RI</h1>
                <h1 style="font-size: 16pt; font-weight: bold; margin: 0; line-height: 1.2;">UNIVERSITAS ISLAM NEGERI</h1>
                <h1 style="font-size: 16pt; font-weight: bold; margin: 0; line-height: 1.2;">SUNAN GUNUNG DJATI BANDUNG</h1>
                <p style="font-size: 10pt; margin: 3px 0 0 0;">
                    Jalan A.H.Nasution No. 105 Cibiru Bandung 40614 Telp. (022) 7800525 Fax. (022) 7803936<br>
                    website: <span style="color: blue; text-decoration: underline;">www.uinsgd.ac.id</span> e-mail: info@uinsgd.ac.id
                </p>
            </td>
        </tr>
    </table>

    {{-- ════════════ META SURAT & TANGGAL ════════════ --}}
    <div class="meta-surat">
        <div class="meta-kiri">
            <table>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $reservation->nomor_surat ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Hal</td>
                    <td>:</td>
                    <td><strong>Pemberian Izin Penggunaan Ruangan</strong></td>
                </tr>
            </table>
        </div>
        <div class="meta-kanan">
            Bandung, {{ $reservation->tanggal_persetujuan
                ? $reservation->tanggal_persetujuan->translatedFormat('j F Y')
                : now()->translatedFormat('j F Y') }}
        </div>
    </div>

    {{-- ════════════ TUJUAN ════════════ --}}
    <div class="tujuan">
        <p>Yth. Sdr. {{ $reservation->user->name }}</p>
        <p>{{ $reservation->user->unit ?? 'Mahasiswa' }}</p>
        <p>UIN Sunan Gunung Djati Bandung</p>
        <p>di Tempat</p>
    </div>

    {{-- ════════════ BODY ════════════ --}}
    <div class="body">

        <p class="salam"><em>Assalamu'alaikum Wr. Wb.</em></p>

        <p>
            Memperhatikan permohonan Saudara perihal Permohonan Izin Penggunaan Ruangan,
            dengan ini Pimpinan Universitas Islam Negeri Sunan Gunung Djati Bandung memberikan
            izin penggunaan fasilitas ruangan untuk keperluan
            <strong>{{ $reservation->tujuan }}</strong> yang akan diselenggarakan pada:
        </p>

        <div class="info-kegiatan">
            <table>
                <tr>
                    <td>Hari, Tanggal</td>
                    <td>:</td>
                    <td>
                        {{ $reservation->tanggal_mulai->translatedFormat('l, j F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>
                        {{ $reservation->tanggal_mulai->format('H:i') }} WIB
                        s.d.
                        {{ $reservation->tanggal_selesai->format('H:i') }} WIB
                    </td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td>{{ $reservation->room->nama_ruangan }}</td>
                </tr>
                <tr>
                    <td>Kapasitas</td>
                    <td>:</td>
                    <td>{{ $reservation->room->kapasitas }} orang</td>
                </tr>
                @if($reservation->room->fasilitas)
                <tr>
                    <td>Fasilitas</td>
                    <td>:</td>
                    <td>{{ $reservation->room->fasilitas }}</td>
                </tr>
                @endif
            </table>
        </div>

        <p>Dengan ketentuan sebagai berikut:</p>

        <div class="ketentuan">
            <ol>
                <li>Menjaga ketertiban, kebersihan, dan keamanan ruangan beserta fasilitas di dalamnya selama kegiatan berlangsung.</li>
                <li>Tidak melakukan aktivitas yang bertentangan dengan norma dan peraturan akademik yang berlaku di lingkungan Universitas.</li>
                <li>Tidak mengubah setup atau pemindahan furniture tanpa izin dari pihak berwenang.</li>
                <li>Mengembalikan kondisi ruangan seperti semula setelah kegiatan selesai.</li>
            </ol>
        </div>

        <p class="penutup">
            Demikian surat pemberian izin ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
            Atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>

        <p><em>Wassalamu'alaikum Wr. Wb.</em></p>

    </div>

    {{-- ════════════ TANDA TANGAN ════════════ --}}
    <div class="ttd">
        <p class="jabatan-atas">{{ $reservation->jabatan_penyetuju ?? 'Wakil Rektor II,' }}</p>
        <p class="jabatan-sub">{!! $reservation->sub_jabatan_penyetuju ?? 'Bidang Administrasi Umum, Perencanaan, dan Keuangan' !!}</p>
        <div class="ruang-ttd"></div>
        <p class="nama">{{ $reservation->nama_penyetuju ?? 'Admin Akademik' }}</p>
        <p class="nip">NIP. {{ $reservation->nip_penyetuju ?? '-' }}</p>
    </div>

    {{-- ════════════ FOOTER ════════════ --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Reservasi Fasilitas dan Ruangan Terpadu</p>
        <p>Generated: {{ now()->translatedFormat('j F Y H:i:s') }} WIB</p>
    </div>

</div>
</body>
</html>