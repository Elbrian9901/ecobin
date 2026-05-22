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
    .brand {
        font-size: 20px;
        font-weight: bold;
        color: #0d2e1a;
        letter-spacing: 0.3px;
    }
    .brand span { color: #22a846; }
    .subtitle {
        font-size: 9.5px;
        color: #7a9a85;
        margin-top: 3px;
    }
    .print-info {
        text-align: right;
        font-size: 9.5px;
        color: #7a9a85;
        line-height: 1.6;
    }

    /* META INFO */
    .meta-grid {
        display: flex;
        gap: 0;
        background: #f7faf8;
        border: 1px solid #d6e8dc;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 18px;
    }
    .meta-item {
        flex: 1;
        padding: 10px 14px;
        border-right: 1px solid #d6e8dc;
    }
    .meta-item:last-child { border-right: none; }
    .meta-label {
        font-size: 8.5px;
        color: #7a9a85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .meta-value {
        font-size: 11px;
        font-weight: bold;
        color: #0d2e1a;
    }

    /* STATS ROW */
    .stats-row {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
    }
    .stat-box {
        flex: 1;
        text-align: center;
        padding: 10px 6px;
        border-radius: 6px;
        border: 1px solid #e0ede5;
    }
    .stat-box .num {
        font-size: 18px;
        font-weight: bold;
        color: #0d2e1a;
        line-height: 1.2;
    }
    .stat-box .lbl {
        font-size: 8.5px;
        color: #7a9a85;
        margin-top: 2px;
    }
    .stat-box.green  { background: #f0faf4; border-color: #b8dcbf; }
    .stat-box.blue   { background: #eff6ff; border-color: #bfdbfe; }
    .stat-box.amber  { background: #fffbeb; border-color: #fcd34d; }
    .stat-box.red    { background: #fff1f1; border-color: #fca5a5; }
    .stat-box.gray   { background: #f8f8f8; border-color: #e0e0e0; }

    /* TABLE */
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #0d2e1a; }
    thead th {
        padding: 8px 10px;
        text-align: left;
        font-size: 9.5px;
        color: #fff;
        font-weight: bold;
        letter-spacing: 0.3px;
    }
    tbody tr { border-bottom: 1px solid #edf2ee; }
    tbody tr:nth-child(even) { background: #f9fbf9; }
    tbody td {
        padding: 7px 10px;
        font-size: 10px;
        color: #2d2d2d;
        vertical-align: middle;
    }

    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 99px;
        font-size: 8.5px;
        font-weight: bold;
    }
    .badge-sensor       { background: #dbeafe; color: #1d4ed8; }
    .badge-pengangkutan { background: #dcfce7; color: #15803d; }
    .badge-tong_penuh   { background: #fee2e2; color: #b91c1c; }

    .bar-wrap { display: flex; align-items: center; gap: 5px; }
    .bar { height: 6px; background: #e5ede5; border-radius: 3px; overflow: hidden; width: 42px; }
    .bar-fill { height: 100%; border-radius: 3px; }

    /* EMPTY */
    .empty-state {
        text-align: center;
        padding: 36px;
        color: #7a9a85;
    }
    .empty-state p:first-child { font-size: 13px; color: #0d2e1a; margin-bottom: 5px; }

    /* FOOTER */
    .footer {
        margin-top: 22px;
        padding-top: 10px;
        border-top: 1px solid #e0ede5;
        display: flex;
        justify-content: space-between;
        font-size: 8.5px;
        color: #aabfb0;
    }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div>
        <div class="brand">Eco<span>Bin</span></div>
        <div class="subtitle">Sistem Monitoring Tong Sampah Cerdas</div>
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
        <div class="meta-value">
            {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
        </div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Tong Sampah</div>
        <div class="meta-value">{{ $tongInfo }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Jenis Laporan</div>
        <div class="meta-value">{{ $jenisLabel }}</div>
    </div>
    <div class="meta-item">
        <div class="meta-label">Total Data</div>
        <div class="meta-value">{{ $riwayat->count() }} record</div>
    </div>
</div>

{{-- STATS (hanya tampil kalau ada data) --}}
@if($riwayat->count() > 0)
<div class="stats-row">
    <div class="stat-box green">
        <div class="num">{{ $riwayat->count() }}</div>
        <div class="lbl">Total Record</div>
    </div>
    <div class="stat-box blue">
        <div class="num">{{ $riwayat->where('jenis','sensor')->count() }}</div>
        <div class="lbl">Data Sensor</div>
    </div>
    <div class="stat-box amber">
        <div class="num">{{ $riwayat->where('jenis','pengangkutan')->count() }}</div>
        <div class="lbl">Pengangkutan</div>
    </div>
    <div class="stat-box red">
        <div class="num">{{ $riwayat->where('jenis','tong_penuh')->count() }}</div>
        <div class="lbl">Tong Penuh</div>
    </div>
    <div class="stat-box gray">
        <div class="num">{{ round($riwayat->avg('level'), 1) }}%</div>
        <div class="lbl">Rata-rata Level</div>
    </div>
</div>
@endif

{{-- TABLE --}}
@if($riwayat->count() > 0)
<table>
    <thead>
        <tr>
            <th style="width:4%">#</th>
            <th style="width:10%">Kode</th>
            <th style="width:20%">Nama Tong</th>
            <th style="width:18%">Lokasi</th>
            <th style="width:13%">Jenis</th>
            <th style="width:15%">Level</th>
            <th style="width:8%">Berat (kg)</th>
            <th style="width:12%">Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayat as $i => $r)
        @php
            $lvl = $r->level ?? 0;
            $color = $lvl >= 100 ? '#dc2626' : ($lvl >= 80 ? '#f59e0b' : '#22a846');
        @endphp
        <tr>
            <td style="color:#aab">{{ $i + 1 }}</td>
            <td><strong>{{ $r->tong->kode ?? '-' }}</strong></td>
            <td>{{ $r->tong->nama ?? '-' }}</td>
            <td style="color:#4a8c62">{{ $r->tong->lokasi ?? '-' }}</td>
            <td>
                <span class="badge badge-{{ $r->jenis }}">
                    {{ $r->jenis === 'sensor' ? 'Sensor' : ($r->jenis === 'pengangkutan' ? 'Angkut' : 'Penuh') }}
                </span>
            </td>
            <td>
                <div class="bar-wrap">
                    <div class="bar">
                        <div class="bar-fill" style="width:{{ min($lvl,100) }}%; background:{{ $color }}"></div>
                    </div>
                    <span style="font-weight:bold; color:{{ $color }}">{{ $lvl }}%</span>
                </div>
            </td>
            <td style="text-align:center">{{ $r->berat ? number_format($r->berat,1) : '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($r->waktu)->format('d/m/y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty-state">
    <p>Tidak ada data ditemukan</p>
    <p>Tidak ada riwayat untuk filter yang dipilih pada periode tersebut.</p>
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <span>EcoBin — Sistem Monitoring Tong Sampah Cerdas</span>
    <span>{{ now()->format('d M Y, H:i') }} WIB</span>
</div>

</body>
</html>