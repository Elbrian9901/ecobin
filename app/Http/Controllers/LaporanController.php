<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Riwayat;
use App\Models\Tong;

class LaporanController extends Controller
{
    public function index()
    {
        $tongs = Tong::orderBy('kode')->get();
        return view('laporan', compact('tongs'));
    }

    public function download(Request $request)
    {
        $request->validate([
            'dari'    => ['required', 'date'],
            'sampai'  => ['required', 'date', 'after_or_equal:dari'],
            'tong_id' => ['required', 'string'],
            'jenis'   => ['required', 'string', 'in:semua,sensor,pengangkutan,tong_penuh'],
        ], [
            'dari.required'    => 'Tanggal mulai wajib diisi.',
            'sampai.required'  => 'Tanggal akhir wajib diisi.',
            'tong_id.required' => 'Pilih tong sampah terlebih dahulu.',
            'jenis.required'   => 'Jenis laporan wajib dipilih.',
        ]);

        $query = Riwayat::with('tong')
            ->whereDate('waktu', '>=', $request->dari)
            ->whereDate('waktu', '<=', $request->sampai);

        // Filter tong
        $tongObj = null;
        if ($request->tong_id !== 'semua') {
            $tongObj = Tong::where('kode', $request->tong_id)->first();
            $query->where('tong_id', optional($tongObj)->id);
        }

        // Filter jenis
        if ($request->jenis !== 'semua') {
            $query->where('jenis', $request->jenis);
        }

        $riwayat = $query->latest('waktu')->get();

        // Label untuk header PDF
        $tongInfo = $request->tong_id === 'semua'
            ? 'Semua Tong'
            : ($tongObj ? $tongObj->nama . ' (' . $tongObj->kode . ')' : $request->tong_id);

        $jenisLabel = match($request->jenis) {
            'sensor'       => 'Data Sensor',
            'pengangkutan' => 'Pengangkutan',
            'tong_penuh'   => 'Tong Penuh',
            default        => 'Semua Jenis',
        };

        // Nama file: ecobin_[nama-tong]_[dari]_sd_[sampai].pdf
        $tongSlug = $request->tong_id === 'semua'
            ? 'semua-tong'
            : strtolower(str_replace([' ', '/'], '-', optional($tongObj)->nama ?? $request->tong_id));
        $namaFile = 'ecobin_' . $tongSlug . '_' . $request->dari . '_sd_' . $request->sampai . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan-pdf', [
            'riwayat'    => $riwayat,
            'dari'       => $request->dari,
            'sampai'     => $request->sampai,
            'tongInfo'   => $tongInfo,
            'jenisLabel' => $jenisLabel,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($namaFile);
    }
}