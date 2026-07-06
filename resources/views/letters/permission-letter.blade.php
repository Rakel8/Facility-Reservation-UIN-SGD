<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Reset Global */
        body {
            margin: 0;
            padding: 0;
            font-size: 11pt;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }
        
        /* Body & Teks */
        .body { 
            font-size: 11pt; 
            text-align: justify; 
            line-height: 1.15;
        }
        /* Hapus semua margin bawaan paragraf jika masih ada yang terlewat */
        .body p { margin: 0; padding: 0; }   
        
        /* Info kegiatan */
        .info-kegiatan { 
            margin: 3px 0 3px 25px; 
            overflow: visible;
            white-space: nowrap;   /* ← tambah ini */
        }
        .info-kegiatan table { 
            border-collapse: collapse; 
            table-layout: auto;    /* ← pastikan auto */
            width: auto;           /* ← bukan 100% */
        }
        .info-kegiatan td { vertical-align: top; padding: 1px 0; font-size: 11pt; }
        .info-kegiatan td:nth-child(2) { padding: 1px 8px; }

        /* TTD — rata kanan */
        .ttd {
            margin-top: 2px;
            text-align: right;     
            font-size: 11pt;
        }
        .ttd p { margin: 0; line-height: 1.2; }
        .ttd .jabatan-atas { font-weight: bold; }
        .ttd .jabatan-sub  { font-style: italic; font-size: 10pt; }
        .ttd .ruang-ttd    { height: 45px; }
        .ttd .nama         { font-weight: bold; text-decoration: underline; }
        .ttd .nip          { font-size: 10pt; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>
<div class="container">

    {{-- ════════════ KOP SURAT ════════════ --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1.5px solid #000; margin-bottom: 8px;">
        <tr>
            <td width="18%" align="center" style="vertical-align: middle; padding-bottom: 4px;">
               <img src="{{ public_path('images/logo/Logo-uinsgd_official.jpg') }}" width="85">
            </td>
            <td width="82%" align="center" style="vertical-align: middle; padding-bottom: 4px; padding-right: 18%;"> 
                <h1 style="font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.1;">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h1>
                <h1 style="font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.1;">UNIVERSITAS ISLAM NEGERI</h1>
                <h1 style="font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.1;">SUNAN GUNUNG DJATI BANDUNG</h1>
                @if($reservation->room && $reservation->room->tingkat === 'fakultas' && $reservation->room->faculty)
                    <h1 style="font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.1; text-transform: uppercase;">
                        {{ $reservation->room->faculty->nama_fakultas }}
                    </h1>
                @endif
                <p style="font-size: 8pt; margin: 2px 0 0 0; line-height: 1.1;">
                    Jalan A.H. Nasution No. 105 Cibiru Bandung 40614 Telp. 022-7800525 Fax. 022-7803936 website: http://uinsgd.ac.id
                </p>
            </td>
        </tr>
    </table>

    {{-- ════════════ META SURAT & TANGGAL ════════════ --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 4px; padding: 0; font-size: 11pt;">
        <tr>
            <td width="70%" valign="top">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="70" valign="top">Nomor</td>
                        <td width="15" valign="top">:</td>
                        <td valign="top">{{ $reservation->nomor_surat ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td valign="top">Lampiran</td>
                        <td valign="top">:</td>
                        <td valign="top">-</td>
                    </tr>
                    <tr>
                        <td valign="top">Hal</td>
                        <td valign="top">:</td>
                        <td valign="top" style="white-space: nowrap; width: 9999px;"><strong>Pemberian Izin Penggunaan Ruangan</strong></td>
                    </tr>
                </table>
            </td>
            <td width="30%" valign="top" align="right">
                Bandung, {{ $reservation->tanggal_persetujuan
                ? $reservation->tanggal_persetujuan->translatedFormat('j F Y')
                : now()->translatedFormat('j F Y') }}
            </td>
        </tr>
    </table>

    {{-- ════════════ TUJUAN ════════════ --}}
    <div class="tujuan" style="margin: 0 0 4px 0; padding: 0; font-size: 11pt; line-height: 1.1;">
        Yth. Sdr. {{ $reservation->user->name }}<br>
        {{ $reservation->user->unit ?? 'Mahasiswa' }}<br>
        UIN Sunan Gunung Djati Bandung<br>
        di Tempat
    </div>

    {{-- ════════════ BODY ════════════ --}}
    <div class="body">
        
        <div style="margin: 0; padding: 0; line-height: 1.15;">
            <em>Assalamu'alaikum Wr. Wb.</em><br>
            Memperhatikan permohonan Saudara perihal Permohonan Izin Penggunaan Ruangan,
            dengan ini Pimpinan 
            @if($reservation->room && $reservation->room->tingkat === 'fakultas' && $reservation->room->faculty)
                {{ $reservation->room->faculty->nama_fakultas }}
            @endif
            Universitas Islam Negeri Sunan Gunung Djati Bandung memberikan
            izin penggunaan fasilitas ruangan untuk keperluan
            <strong>{{ $reservation->tujuan }}</strong> yang akan diselenggarakan pada:
        </div>

        <div class="info-kegiatan" style="margin: 3px 0 3px 25px; overflow: visible;">
            <table cellpadding="0" cellspacing="0" style="table-layout: fixed; width: auto;">
                <tr>
                    <td width="100">Hari, Tanggal</td>
                    <td width="10">:</td>
                    <td style="white-space: nowrap;">{{ $reservation->tanggal_mulai->translatedFormat('l, j F Y') }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td style="white-space: nowrap;">
                        {{ $reservation->tanggal_mulai->format('H:i') }} WIB
                        s.d.
                        {{ $reservation->tanggal_selesai->format('H:i') }} WIB
                    </td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td style="white-space: nowrap;">{{ $reservation->room->nama_ruangan }}</td>
                </tr>
                <tr>
                    <td>Kapasitas</td>
                    <td>:</td>
                    <td style="white-space: nowrap;">{{ $reservation->room->kapasitas }} orang</td>
                </tr>
                @if($reservation->room->fasilitas)
                <tr>
                    <td style="width: 100px;">Fasilitas</td>
                    <td style="width: 10px;">:</td>
                    <td style="white-space: nowrap; width: auto;">{{ $reservation->room->fasilitas }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div style="margin: 0; padding: 0; line-height: 1.15; text-align: justify;">
            Dengan ketentuan sebagai berikut:
            <ol style="margin: 2px 0 0 0; padding-left: 20px; line-height: 1.15;">
                <li style="margin-bottom: 2px;">Menjaga ketertiban, kebersihan, dan keamanan ruangan beserta fasilitas di dalamnya selama kegiatan berlangsung.</li>
                <li style="margin-bottom: 2px;">Tidak melakukan aktivitas yang bertentangan dengan norma dan peraturan akademik yang berlaku di lingkungan Universitas.</li>
                <li style="margin-bottom: 2px;">Tidak mengubah setup atau pemindahan furniture tanpa izin dari pihak berwenang.</li>
                <li style="margin-bottom: 2px;">Mengembalikan kondisi ruangan seperti semula setelah kegiatan selesai.</li>
            </ol>
            Demikian surat pemberian izin ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
            Atas perhatian dan kerjasamanya kami ucapkan terima kasih.<br>
            <em>Wassalamu'alaikum Wr. Wb.</em>
        </div>

    </div> {{-- ════════════ TANDA TANGAN ════════════ --}}
    <div class="ttd">
        <p class="jabatan-atas">{{ $reservation->jabatan_penyetuju ?? 'Wakil Rektor II,' }}</p>
        <p class="jabatan-sub">{!! $reservation->sub_jabatan_penyetuju ?? 'Bidang Administrasi Umum, Perencanaan, dan Keuangan' !!}</p>
        <div class="ruang-ttd"></div>
        <p class="nama">{{ $reservation->nama_penyetuju ?? 'Admin Akademik' }}</p>
        <p class="nip">NIP. {{ $reservation->nip_penyetuju ?? '-' }}</p>
    </div>