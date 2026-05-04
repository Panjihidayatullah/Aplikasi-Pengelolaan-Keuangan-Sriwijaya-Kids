<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use App\Models\GajiGuruDefault;
use App\Models\Guru;
use App\Models\JenisPengeluaran;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GajiGuruController extends Controller
{
    // ─────────────────────────────────────────
    //  RIWAYAT GAJI (Admin/Bendahara)
    // ─────────────────────────────────────────

    public function index(Request $request)
    {
        $this->ensureFinanceAccess();

        $query = GajiGuru::query()
            ->with(['guru', 'pengeluaran.jenis', 'dibayarOleh']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('guru', fn ($g) =>
                    $g->where('nama', 'like', '%'.$search.'%')
                      ->orWhere('nip',  'like', '%'.$search.'%')
                )->orWhereHas('pengeluaran', fn ($p) =>
                    $p->where('kode_transaksi', 'like', '%'.$search.'%')
                      ->orWhere('keterangan',   'like', '%'.$search.'%')
                );
            });
        }

        if ($request->filled('guru_id'))      $query->where('guru_id',       $request->integer('guru_id'));
        if ($request->filled('periode_bulan')) $query->where('periode_bulan', $request->integer('periode_bulan'));
        if ($request->filled('periode_tahun')) $query->where('periode_tahun', $request->integer('periode_tahun'));

        $gajiGuru = $query->orderByDesc('periode_tahun')
            ->orderByDesc('periode_bulan')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalDibayarkan = $gajiGuru->getCollection()->sum(
            fn (GajiGuru $item) => (float) data_get($item, 'pengeluaran.jumlah', 0)
        );

        $guruOptions  = Guru::query()->where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'nip']);
        $bulanOptions = $this->bulanOptions();

        // Gaji default semua guru (untuk auto-fill)
        $gajiDefaultMap = GajiGuruDefault::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('guru_id')
            ->map(fn ($d) => (float) $d->nominal);

        return view('gaji-guru.index', compact(
            'gajiGuru', 'guruOptions', 'bulanOptions', 'totalDibayarkan', 'gajiDefaultMap'
        ));
    }

    /**
     * Bayar gaji guru — store ke pengeluaran + gaji_guru.
     */
    public function store(Request $request)
    {
        $this->ensureFinanceAccess();

        $validated = $request->validate([
            'guru_id'       => 'required|exists:guru,id',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'jumlah'        => 'required|numeric|min:1',
            'tanggal'       => 'required|date',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        $guru = Guru::findOrFail($validated['guru_id']);

        // Cek duplikat periode
        $exists = GajiGuru::query()
            ->where('guru_id',       $validated['guru_id'])
            ->where('periode_bulan', $validated['periode_bulan'])
            ->where('periode_tahun', $validated['periode_tahun'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error',
                "Gaji {$guru->nama} bulan {$this->bulanOptions()[$validated['periode_bulan']]} {$validated['periode_tahun']} sudah pernah dibayar."
            );
        }

        // Cari jenis pengeluaran Gaji Guru
        JenisPengeluaran::ensureKategoriInti();
        $jenisPengeluaran = JenisPengeluaran::query()
            ->where('is_active', true)
            ->whereRaw("LOWER(nama) LIKE '%gaji guru%' OR LOWER(nama) LIKE '%guru%'")
            ->orderBy('id')
            ->first();

        if (!$jenisPengeluaran) {
            $jenisPengeluaran = JenisPengeluaran::query()->where('is_active', true)->orderBy('id')->first();
        }

        $bulanStr = $this->bulanOptions()[$validated['periode_bulan']];
        $kode     = 'GAJI-' . $guru->nip ?: strtoupper(substr(str_replace(' ', '', $guru->nama), 0, 4));
        $kode    .= '-' . $validated['periode_tahun'] . str_pad($validated['periode_bulan'], 2, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($validated, $guru, $jenisPengeluaran, $kode, $bulanStr) {
            $pengeluaran = Pengeluaran::create([
                'kode_transaksi'      => $kode,
                'jenis_pengeluaran_id' => $jenisPengeluaran?->id,
                'user_id'             => auth()->id(),
                'tanggal'             => $validated['tanggal'],
                'jumlah'              => $validated['jumlah'],
                'keterangan'          => $validated['keterangan']
                    ?: "Gaji {$guru->nama} — {$bulanStr} {$validated['periode_tahun']}",
                'status'              => 'approved',
            ]);

            GajiGuru::create([
                'pengeluaran_id'      => $pengeluaran->id,
                'guru_id'             => $validated['guru_id'],
                'periode_bulan'       => $validated['periode_bulan'],
                'periode_tahun'       => $validated['periode_tahun'],
                'dibayar_oleh_user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('gaji-guru.index')
            ->with('success', "Gaji {$guru->nama} ({$bulanStr} {$validated['periode_tahun']}) berhasil disimpan.");
    }

    /**
     * Hapus record gaji guru.
     */
    public function destroy(GajiGuru $gajiGuru)
    {
        $this->ensureFinanceAccess();

        DB::transaction(function () use ($gajiGuru) {
            $pengeluaranId = $gajiGuru->pengeluaran_id;
            $gajiGuru->delete();
            if ($pengeluaranId) {
                Pengeluaran::find($pengeluaranId)?->delete();
            }
        });

        return redirect()->route('gaji-guru.index')
            ->with('success', 'Data gaji guru berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    //  GAJI DEFAULT (CRUD)
    // ─────────────────────────────────────────

    public function defaultIndex()
    {
        $this->ensureFinanceAccess();
    
        $defaults   = GajiGuruDefault::query()
            ->with('guru')
            ->orderBy('guru_id')
            ->get();
    
        $guruOptions = Guru::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip']);
    
        $bulanOptions = $this->bulanOptions();
    
        return view('gaji-guru.default-index', compact('defaults', 'guruOptions', 'bulanOptions'));
    }

    public function defaultStore(Request $request)
    {
        $this->ensureFinanceAccess();

        // Bersihkan titik dari nominal sebelum validasi
        if ($request->has('nominal')) {
            $request->merge([
                'nominal' => str_replace('.', '', $request->nominal)
            ]);
        }

        $validated = $request->validate([
            'guru_id'       => 'required|exists:guru,id',
            'nominal'       => 'required|numeric|min:0',
            'keterangan'    => 'nullable|string|max:255',
            'tanggal_gaji'  => 'nullable|integer|between:1,28',
            'auto_gaji'     => 'boolean',
        ]);

        // Gunakan waktu saat ini secara otomatis sesuai permintaan
        $periode_bulan = (int)date('n');
        $periode_tahun = (int)date('Y');
        $tanggal_bayar = date('Y-m-d');

        DB::transaction(function() use ($validated, $request, $periode_bulan, $periode_tahun, $tanggal_bayar) {
            // 1. Simpan Gaji Default (Daftar Gaji)
            GajiGuruDefault::create([
                'guru_id'     => $validated['guru_id'],
                'nominal'     => $validated['nominal'],
                'keterangan'  => $validated['keterangan'],
                'tanggal_gaji'=> $validated['tanggal_gaji'],
                'auto_gaji'   => $request->has('auto_gaji'),
                'is_active'   => true,
                'deleted_at'  => null,
            ]);

            // 2. Langsung buat catatan pembayaran (GajiGuru) - Periode Otomatis (Sekarang)
            $guru = Guru::findOrFail($validated['guru_id']);
            
            JenisPengeluaran::ensureKategoriInti();
            $jenisPengeluaran = JenisPengeluaran::where('is_active', true)
                ->whereRaw("LOWER(nama) LIKE '%gaji guru%' OR LOWER(nama) LIKE '%guru%'")
                ->orderBy('id')->first() 
                ?? JenisPengeluaran::where('is_active', true)->orderBy('id')->first();

            if (!$jenisPengeluaran) {
                throw new \Exception("Kategori pengeluaran gaji tidak ditemukan.");
            }

            $bulanStr = $this->bulanOptions()[$periode_bulan];
            $suffix   = strtoupper(Str::random(4));
            $kode     = 'GAJI-' . ($guru->nip ?: strtoupper(substr(str_replace(' ', '', $guru->nama), 0, 4)))
                        . '-' . $periode_tahun . str_pad($periode_bulan, 2, '0', STR_PAD_LEFT)
                        . '-' . $suffix;

            $pengeluaran = Pengeluaran::create([
                'kode_transaksi'      => $kode,
                'jenis_pengeluaran_id' => $jenisPengeluaran->id,
                'user_id'             => auth()->id(),
                'tanggal'             => $tanggal_bayar,
                'jumlah'              => $validated['nominal'],
                'keterangan'          => "Gaji {$guru->nama} — {$bulanStr} {$periode_tahun}",
                'status'              => 'Disetujui', 
            ]);

            GajiGuru::create([
                'pengeluaran_id'      => $pengeluaran->id,
                'guru_id'             => $validated['guru_id'],
                'periode_bulan'       => $periode_bulan,
                'periode_tahun'       => $periode_tahun,
                'dibayar_oleh_user_id' => auth()->id(),
            ]);
        });

        // Selalu redirect ke riwayat gaji agar user bisa lihat updatenya
        return redirect()->route('gaji-guru.index')
            ->with('success', 'Pembayaran gaji berhasil dicatat secara otomatis untuk periode ' . date('F Y') . '.');
    }

    public function defaultEdit(GajiGuruDefault $default)
    {
        $this->ensureFinanceAccess();
        $default->load('guru');
        return view('gaji-guru.default-edit', compact('default'));
    }

    public function defaultUpdate(Request $request, GajiGuruDefault $default)
    {
        $this->ensureFinanceAccess();

        $validated = $request->validate([
            'nominal'     => 'required|numeric|min:0',
            'keterangan'  => 'nullable|string|max:255',
            'tanggal_gaji'=> 'nullable|integer|between:1,28',
            'auto_gaji'   => 'boolean',
            'is_active'   => 'boolean',
        ]);

        $default->update([
            'nominal'     => $validated['nominal'],
            'keterangan'  => $validated['keterangan'],
            'tanggal_gaji'=> $validated['tanggal_gaji'],
            'auto_gaji'   => $request->has('auto_gaji'),
            'is_active'   => $validated['is_active'],
        ]);

        return redirect()->route('gaji-guru.default.index')
            ->with('success', 'Gaji default berhasil diperbarui.');
    }

    public function defaultDestroy(GajiGuruDefault $default)
    {
        $this->ensureFinanceAccess();
        $default->delete();
        return redirect()->route('gaji-guru.default.index')
            ->with('success', 'Gaji default berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    //  DETAIL & PDF
    // ─────────────────────────────────────────

    public function show(GajiGuru $gajiGuru)
    {
        $this->ensureFinanceAccess();
        $gajiGuru->load(['guru', 'pengeluaran.jenis', 'pengeluaran.user', 'dibayarOleh']);
        return view('gaji-guru.show', ['gajiGuru' => $gajiGuru, 'isSelfView' => false]);
    }

    public function exportPdf(GajiGuru $gajiGuru)
    {
        $this->ensureFinanceAccess();
        $gajiGuru->load(['guru', 'pengeluaran.jenis', 'pengeluaran.user', 'dibayarOleh']);
        $pdf = Pdf::loadView('gaji-guru.pdf.detail', compact('gajiGuru'))->setPaper('a4', 'portrait');
        $kode     = preg_replace('/[^A-Za-z0-9\-]/', '', (string) data_get($gajiGuru, 'pengeluaran.kode_transaksi'));
        $filename = 'slip-gaji-guru-'.($kode ?: (string) $gajiGuru->id).'.pdf';
        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────
    //  GURU (lihat gaji sendiri)
    // ─────────────────────────────────────────

    public function myIndex(Request $request)
    {
        abort_unless(auth()->user()->hasRole('Guru'), 403);

        $guru = Guru::query()->where('user_id', auth()->id())->first();

        $query = GajiGuru::query()->with(['guru', 'pengeluaran.jenis', 'dibayarOleh']);
        if ($guru) {
            $query->where('guru_id', $guru->id);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('periode_bulan')) $query->where('periode_bulan', $request->integer('periode_bulan'));
        if ($request->filled('periode_tahun')) $query->where('periode_tahun', $request->integer('periode_tahun'));

        $gajiGuru = $query->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->orderByDesc('id')
            ->paginate(10)->withQueryString();

        $totalDibayarkan = $gajiGuru->getCollection()->sum(
            fn (GajiGuru $item) => (float) data_get($item, 'pengeluaran.jumlah', 0)
        );

        $bulanOptions = $this->bulanOptions();
        return view('gaji-guru.my-index', compact('gajiGuru', 'bulanOptions', 'guru', 'totalDibayarkan'));
    }

    public function myShow(GajiGuru $gajiGuru)
    {
        abort_unless(auth()->user()->hasRole('Guru'), 403);
        $guru = Guru::query()->where('user_id', auth()->id())->first();
        abort_unless($guru && (int) $gajiGuru->guru_id === (int) $guru->id, 403);
        $gajiGuru->load(['guru', 'pengeluaran.jenis', 'pengeluaran.user', 'dibayarOleh']);
        return view('gaji-guru.show', ['gajiGuru' => $gajiGuru, 'isSelfView' => true]);
    }

    public function myExportPdf(GajiGuru $gajiGuru)
    {
        abort_unless(auth()->user()->hasRole('Guru'), 403);
        $guru = Guru::query()->where('user_id', auth()->id())->first();
        abort_unless($guru && (int) $gajiGuru->guru_id === (int) $guru->id, 403);
        $gajiGuru->load(['guru', 'pengeluaran.jenis', 'pengeluaran.user', 'dibayarOleh']);
        $pdf      = Pdf::loadView('gaji-guru.pdf.detail', compact('gajiGuru'))->setPaper('a4', 'portrait');
        $kode     = preg_replace('/[^A-Za-z0-9\-]/', '', (string) data_get($gajiGuru, 'pengeluaran.kode_transaksi'));
        $filename = 'slip-gaji-saya-'.($kode ?: (string) $gajiGuru->id).'.pdf';
        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────

    /**
     * API: return gaji default untuk guru tertentu (untuk auto-fill form).
     */
    public function getGajiDefault(Request $request)
    {
        $this->ensureFinanceAccess();
        $guruId  = $request->integer('guru_id');
        $default = GajiGuruDefault::query()
            ->where('guru_id', $guruId)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'nominal'    => $default ? (float) $default->nominal : null,
            'keterangan' => $default?->keterangan,
        ]);
    }

    private function ensureFinanceAccess(): void
    {
        abort_unless(is_admin() || is_bendahara(), 403);
    }

    private function bulanOptions(): array
    {
        return [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];
    }
}
