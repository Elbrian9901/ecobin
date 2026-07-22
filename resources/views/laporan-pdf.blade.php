<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 11px;
        color: #2d2d2d;
        background: #fff;
        padding: 28px 32px;
    }

    /* HEADER */
    .header {
        border-bottom: 2px solid #22a846;
        padding-bottom: 14px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .brand { font-size: 20px; font-weight: bold; color: #0d2e1a; letter-spacing: 0.3px; }
    .brand span { color: #22a846; }
    .subtitle { font-size: 9.5px; color: #7a9a85; margin-top: 3px; }
    .print-info { text-align: right; font-size: 9.5px; color: #7a9a85; line-height: 1.6; }

    /* META INFO */
    .meta-grid {
        display: flex; gap: 0;
        background: #f7faf8; border: 1px solid #d6e8dc; border-radius: 6px;
        overflow: hidden; margin-bottom: 18px;
    }
    .meta-item { flex: 1; padding: 10px 14px; border-right: 1px solid #d6e8dc; }
    .meta-item:last-child { border-right: none; }
    .meta-label { font-size: 8.5px; color: #7a9a85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;}
    .meta-value { font-size: 11px; font-weight: bold; color: #0d2e1a; }

    /* KETERANGAN */
    .keterangan-box {
        background: #fffbeb; border: 1px solid #fcd34d; border-radius: 6px;
        padding: 10px 14px; margin-bottom: 18px;
    }
    .keterangan-label {
        font-size: 8.5px; color: #b45309; text-transform: uppercase;
        letter-spacing: 0.5px; font-weight: bold; margin-bottom: 4px;
    }
    .keterangan-text { font-size: 10px; color: #4a3200; line-height: 1.5; }

    /* SECTION PER TONG */
    .tong-section { margin-bottom: 26px; page-break-inside: avoid; }
    .tong-title-bar {
        display: flex; justify-content: space-between; align-items: center;
        background: #0d2e1a; color: #fff; padding: 9px 14px; border-radius: 6px 6px 0 0;
    }
    .tong-title-bar .kode { font-size: 13px; font-weight: bold; }
    .tong-title-bar .lokasi { font-size: 9.5px; color: #b8d6c2; }

    /* STATS ROW */
    .stats-row { display: flex; gap: 8px; margin: 12px 0; }
    .stat-box { flex: 1; text-align: center; padding: 9px 6px; border-radius: 6px; border: 1px solid #e0ede5; }
    .stat-box .num { font-size: 16px; font-weight: bold; color: #0d2e1a; line-height: 1.2; }
    .stat-box .lbl { font-size: 8px; color: #7a9a85; margin-top: 2px; }
    .stat-box.green { background: #f0faf4; border-color: #b8dcbf; }
    .stat-box.red   { background: #fff1f1; border-color: #fca5a5; }
    .stat-box.amber { background: #fffbeb; border-color: #fcd34d; }
    .stat-box.gray  { background: #f8f8f8; border-color: #e0e0e0; }

    /* TABLE */
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    thead tr { background: #22a846; }
    thead th { padding: 7px 10px; text-align: left; font-size: 9px; color: #fff; font-weight: bold; letter-spacing: 0.3px; }
    tbody tr { border-bottom: 1px solid #edf2ee; }
    tbody tr:nth-child(even) { background: #f9fbf9; }
    tbody td { padding: 6px 10px; font-size: 9.5px; color: #2d2d2d; vertical-align: middle; }

    .table-subtitle { font-size: 9.5px; font-weight: bold; color: #1a3d28; margin: 10px 0 4px; }

    .badge { display: inline-block; padding: 2px 7px; border-radius: 99px; font-size: 8px; font-weight: bold; }
    .badge-tepat    { background: #dcfce7; color: #15803d; }
    .badge-terlambat{ background: #fee2e2; color: #b91c1c; }
    .badge-belum    { background: #fef3c7; color: #b45309; }
    .badge-rutin    { background: #dbeafe; color: #1d4ed8; }

    .bar-wrap { display: flex; align-items: center; gap: 5px; }
    .bar { height: 6px; background: #e5ede5; border-radius: 3px; overflow: hidden; width: 38px; }
    .bar-fill { height: 100%; border-radius: 3px; }

    .empty-mini { padding: 14px; text-align: center; color: #a8bfb0; font-size: 9.5px; border: 1px dashed #e0ede5; border-radius: 6px; }

    .footer {
        margin-top: 18px; padding-top: 10px; border-top: 1px solid #e0ede5;
        display: flex; justify-content: space-between; font-size: 8.5px; color: #aabfb0;
    }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div>
        <div class="brand">Eco<span>Bin</span></div>
        <div class="subtitle">Laporan Riwayat &amp; Keterlambatan Pengangkutan Tong Sampah</div>
    </div>
    <div class="print-info">
        Dicetak: {{ now()->format('d M Y, H:i') }} WIB<br>
        Laporan dihasilkan otomatis oleh sistem
    </div>
</div>

{{-- META INFO --}}
<div class="meta-grid">
    <div class="meta-item">
        <div class="meta-label">Periode</div>
        <div class="meta-value">{{ \Carbon\Carbon::parse($dari)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Tong Sampah</div>
        <div class="meta-value">{{ $tongInfo }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Standar Waktu Angkut</div>
        <div class="meta-value">Maks. {{ $batasTerlambatJam }} jam sejak Penuh</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Jumlah Tong Dilaporkan</div>
        <div class="meta-value">{{ count($laporanPerTong) }} tong</div>
    </div>
</div>

<div class="keterangan-box">
    <div class="keterangan-label">Keterangan</div>
    <div class="keterangan-text">{{ $keterangan ?: '-' }}</div>
</div>

{{-- PER TONG --}}
@forelse($laporanPerTong as $lap)
<div class="tong-section">
    <div class="tong-title-bar">
        <span class="kode">{{ $lap['tong']->kode }} &ndash; {{ $lap['tong']->nama }}</span>
        <span class="lokasi">{{ $lap['tong']->lokasi ?? 'Lokasi tidak diset' }}</span>
    </div>

    {{-- Ringkasan siklus pengangkutan --}}
    <div class="stats-row">
        <div class="stat-box green">
            <div class="num">{{ $lap['total_tepat_waktu'] }}</div>
            <div class="lbl">Tepat Waktu</div>
        </div>
        <div class="stat-box red">
            <div class="num">{{ $lap['total_terlambat'] }}</div>
            <div class="lbl">Terlambat</div>
        </div>
        <div class="stat-box amber">
            <div class="num">{{ $lap['total_belum_diangkut'] }}</div>
            <div class="lbl">Belum Diangkut</div>
        </div>
        <div class="stat-box gray">
            <div class="num">{{ $lap['rata_rata_durasi'] !== null ? $lap['rata_rata_durasi'] . ' j' : '-' }}</div>
            <div class="lbl">Rata-rata Durasi</div>
        </div>
    </div>

    {{-- Tabel siklus pengangkutan --}}
    <div class="table-subtitle">Siklus Pengangkutan Terakhir</div>
    @if(count($lap['siklus']) > 0)
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:25%">Waktu Penuh</th>
                <th style="width:25%">Waktu Diangkut</th>
                <th style="width:20%">Durasi Respon</th>
                <th style="width:25%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lap['siklus'] as $i => $s)
            <tr>
                <td style="color:#aab">{{ $i + 1 }}</td>
                <td>{{ $s['waktu_penuh'] ? \Carbon\Carbon::parse($s['waktu_penuh'])->format('d/m/y H:i') : '-' }}</td>
                <td>{{ $s['waktu_angkut'] ? \Carbon\Carbon::parse($s['waktu_angkut'])->format('d/m/y H:i') : 'Belum diangkut' }}</td>
                <td>{{ $s['durasi_jam'] !== null ? $s['durasi_jam'] . ' jam' : '-' }}</td>
                <td>
                    @php
                        $badgeClass = match($s['status']) {
                            'Tepat Waktu' => 'badge-tepat',
                            'Terlambat'   => 'badge-terlambat',
                            'Belum Diangkut' => 'badge-belum',
                            default       => 'badge-rutin',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $s['status'] }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-mini">Tidak ada siklus pengangkutan pada periode ini.</div>
    @endif

    {{-- Tabel riwayat sensor (diagregasi per jam) --}}
    <div class="table-subtitle">Riwayat Level Sensor (5 Pembacaan Terakhir)</div>
    @if($lap['riwayat_sensor']->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width:8%">#</th>
                <th style="width:32%">Waktu</th>
                <th style="width:60%">Level</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lap['riwayat_sensor'] as $i => $r)
            @php
                $lvl = $r->level ?? 0;
                $color = $lvl >= 100 ? '#dc2626' : ($lvl >= 80 ? '#f59e0b' : '#22a846');
            @endphp
            <tr>
                <td style="color:#aab">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($r->waktu)->format('d/m/y H:i') }}</td>
                <td>
                    <div class="bar-wrap">
                        <div class="bar"><div class="bar-fill" style="width:{{ min($lvl,100) }}%; background:{{ $color }}"></div></div>
                        <span style="font-weight:bold; color:{{ $color }}">{{ $lvl }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-mini">Tidak ada data sensor pada periode ini.</div>
    @endif
</div>
@empty
<div class="empty-mini">Tidak ada tong ditemukan untuk filter yang dipilih.</div>
@endforelse

{{-- FOOTER --}}
<div class="footer">
    <span>EcoBin &mdash; Sistem Monitoring Tong Sampah Cerdas</span>
    <span>{{ now()->format('d M Y, H:i') }} WIB</span>
</div>

</body>
</html>