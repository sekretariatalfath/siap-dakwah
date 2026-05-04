<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi - {{ $idSesi }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #000; 
            line-height: 1.4; 
            margin: 0; 
            padding: 10px;
        }
        
        /* KOP SURAT - GARIS HITAM TEGAS */
        .kop-surat { 
            border-bottom: 4px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
            text-align: center; 
        }
        .kop-surat h2 { 
            margin: 0; 
            text-transform: uppercase; 
            font-size: 18px; 
            color: #800000; /* Tetap Marun Al-Fath untuk Identitas */
        }
        .kop-surat h1 { 
            margin: 2px 0; 
            text-transform: uppercase; 
            font-size: 24px; 
            color: #000;
        }
        .kop-surat p { 
            margin: 0; 
            font-size: 10px; 
            color: #333; 
            font-style: italic; 
        }

        /* BASMALLAH */
        .basmallah {
            text-align: center;
            font-size: 14px;
            font-family: 'Times New Roman', serif;
            margin: 15px 0;
            font-weight: normal;
        }

        /* INFO KEGIATAN */
        .info-box { margin-bottom: 20px; width: 100%; border-collapse: collapse; }
        .info-box td { font-size: 11px; vertical-align: top; padding: 2px 0; }
        .title-rekap { 
            text-align: center; 
            font-weight: bold; 
            text-decoration: underline; 
            margin-bottom: 15px; 
            font-size: 14px; 
            text-transform: uppercase;
        }

        /* TABEL DATA - GRID HITAM */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th { 
            background-color: #800000; 
            color: white; 
            font-size: 10px; 
            padding: 10px 5px; 
            text-transform: uppercase; 
            border: 1px solid #000; /* Garis Hitam */
        }
        table.data-table td { 
            font-size: 10px; 
            padding: 8px 5px; 
            border: 1px solid #000; /* Garis Hitam */
            text-align: left; 
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        /* TANDA TANGAN */
        .ttd-container { margin-top: 40px; width: 100%; position: relative; }
        .ttd-box { 
            width: 200px; 
            text-align: center; 
            float: right; 
            font-size: 11px; 
        }
        .space-ttd { height: 70px; }
        .clearfix { clear: both; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <h2>Lembaga Dakwah Kampus</h2>
        <h1>Telkom University</h1>
        <p>Sekretariat: Jalan Telekomunikasi No. 1, Terusan Buah Batu, Bandung 40257</p>
    </div>

    {{-- PEMBUKA --}}
    <div class="basmallah">Bismillahirrahmanirrahim</div>

    <div class="title-rekap">LAPORAN REKAPITULASI KEHADIRAN</div>

    {{-- INFO ACARA --}}
    <table class="info-box">
        <tr>
            <td width="18%">Nama Kegiatan</td>
            <td width="2%">:</td>
            <td class="font-bold">{{ $sesiInfo->nama_kegiatan ?? '-' }}</td>
            <td width="15%">ID Presensi</td>
            <td width="2%">:</td>
            <td class="font-bold">{{ $idSesi }}</td>
        </tr>
        <tr>
            <td>Admin</td>
            <td>:</td>
            <td>{{ $sesiInfo->unit_host ?? Auth::user()->unit }}</td>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $sesiInfo->tgl_pelaksanaan ?? date('d-m-Y') }}</td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIM</th>
                <th>Nama Lengkap</th>
                <th width="15%">Amanah</th>
                <th width="15%">Asal Wajihah</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($daftarHadir as $h)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center font-bold">{{ $h['nim'] }}</td>
                <td style="text-transform: uppercase;">{{ $h['nama'] }}</td>
                <td class="text-center">{{ $h['amanah'] }}</td>
                <td class="text-center">{{ $h['wajihah'] }}</td>
                <td class="text-center font-bold">{{ strtoupper($h['status']) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Data presensi belum tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd-container">
        <div class="ttd-box">
            {{-- Tanggal otomatis Indonesia jika pakai Carbon di Controller --}}
            <p>Bandung, {{ date('d F Y') }}</p>
            
            {{-- Menggunakan Nama Unit Lengkap --}}
            <p>Admin {{ $namaUnitLengkap }},</p> 
            
            <div class="space-ttd"></div>
            
            {{-- Nama Penanda Tangan (Sudah di-strtoupper dari Controller) --}}
            <p class="font-bold"><u>{{ $namaTtd }}</u></p>
            <p>NIM. {{ $nimTtd }}</p>
        </div>
        <div class="clearfix"></div>
    </div>

</body>
</html>