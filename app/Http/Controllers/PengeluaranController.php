<?php

namespace App\Http\Controllers;

use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Pengeluaran;
use App\Models\JenisPengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::with(['jenis', 'user']);

        // Filter by search (Kode Transaksi or Keterangan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal', '<=', $request->tanggal_akhir);
        }

        // Filter by Jenis Pengeluaran
        if ($request->filled('jenis_pengeluaran_id')) {
            $jenisIds = JenisPengeluaran::equivalentIds($request->integer('jenis_pengeluaran_id'));

            if (empty($jenisIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('jenis_pengeluaran_id', $jenisIds);
            }
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengeluaran = $query->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        $jenisPengeluaran = JenisPengeluaran::dropdownOptions();

        // Statistics - dengan filter yang sama
        $statsQuery = Pengeluaran::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('tanggal_mulai')) {
            $statsQuery->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $statsQuery->where('tanggal', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('jenis_pengeluaran_id')) {
            $jenisIds = JenisPengeluaran::equivalentIds($request->integer('jenis_pengeluaran_id'));

            if (empty($jenisIds)) {
                $statsQuery->whereRaw('1 = 0');
            } else {
                $statsQuery->whereIn('jenis_pengeluaran_id', $jenisIds);
            }
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        $totalSemua = $statsQuery->sum('jumlah');
        $totalApproved = (clone $statsQuery)->where('status', 'Disetujui')->count();
        $totalPending = (clone $statsQuery)->where('status', 'Pending')->count();
        $totalRejected = (clone $statsQuery)->where('status', 'Ditolak')->count();

        return view('pengeluaran.index', compact(
            'pengeluaran', 
            'jenisPengeluaran',
            'totalSemua',
            'totalApproved',
            'totalPending',
            'totalRejected'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisPengeluaran = JenisPengeluaran::dropdownOptions()->filter(fn($j) => $j->nama !== JenisPengeluaran::KATEGORI_GAJI_GURU);

        return view('pengeluaran.create', compact('jenisPengeluaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pengeluaran_id' => 'required|exists:jenis_pengeluaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'guru_id' => 'nullable|exists:guru,id',
            'periode_bulan' => 'nullable|integer|between:1,12',
            'periode_tahun' => 'nullable|integer|between:2000,2100',
            'detail_gaji_guru' => 'nullable|string|max:1000',
            'status' => 'required|string|in:Disetujui,Pending,Ditolak',
        ]);

        $isGajiGuru = $this->isGajiGuruKategoriId((int) $validated['jenis_pengeluaran_id']);
        if ($isGajiGuru) {
            $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'periode_bulan' => 'required|integer|between:1,12',
                'periode_tahun' => 'required|integer|between:2000,2100',
            ]);
        }

        // Generate kode transaksi unik
        $validated['kode_transaksi'] = 'OUT-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        
        // Map deskripsi ke keterangan sesuai nama kolom di database
        $validated['keterangan'] = $validated['deskripsi'];
        unset($validated['deskripsi']);

        $validated['jenis_pengeluaran_id'] = JenisPengeluaran::representativeIdFor((int) $validated['jenis_pengeluaran_id'])
            ?? (int) $validated['jenis_pengeluaran_id'];
        
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('pengeluaran', 'public');
        }

        DB::transaction(function () use ($validated, $request, $isGajiGuru) {
            $pengeluaran = Pengeluaran::create($validated);
            $this->syncGajiGuru($pengeluaran, $request, $isGajiGuru);
            $this->notifyPengeluaranBaru($pengeluaran);
        });

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengeluaran = Pengeluaran::with(['jenis', 'user', 'gajiGuru.guru'])->findOrFail($id);

        return view('pengeluaran.show', compact('pengeluaran'));
    }

    /**
     * Export detail pengeluaran as PDF.
     */
    public function exportPdf(string $id)
    {
        $pengeluaran = Pengeluaran::with(['jenis', 'user', 'gajiGuru.guru'])->findOrFail($id);

        $pdf = Pdf::loadView('pengeluaran.pdf.detail', compact('pengeluaran'))
            ->setPaper('a4', 'portrait');

        $safeKode = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $pengeluaran->kode_transaksi);
        $filename = 'bukti-pengeluaran-' . ($safeKode ?: (string) $pengeluaran->id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengeluaran = Pengeluaran::with('gajiGuru')->findOrFail($id);
        $jenisPengeluaran = JenisPengeluaran::dropdownOptions()->filter(fn($j) => $j->nama !== JenisPengeluaran::KATEGORI_GAJI_GURU);
        $selectedJenisPengeluaranId = JenisPengeluaran::representativeIdFor((int) $pengeluaran->jenis_pengeluaran_id)
            ?? (int) $pengeluaran->jenis_pengeluaran_id;

        return view('pengeluaran.edit', compact('pengeluaran', 'jenisPengeluaran', 'selectedJenisPengeluaranId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengeluaran = Pengeluaran::with('gajiGuru')->findOrFail($id);

        $validated = $request->validate([
            'jenis_pengeluaran_id' => 'required|exists:jenis_pengeluaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'guru_id' => 'nullable|exists:guru,id',
            'periode_bulan' => 'nullable|integer|between:1,12',
            'periode_tahun' => 'nullable|integer|between:2000,2100',
            'detail_gaji_guru' => 'nullable|string|max:1000',
            'status' => 'required|string|in:Disetujui,Pending,Ditolak',
        ]);

        $isGajiGuru = $this->isGajiGuruKategoriId((int) $validated['jenis_pengeluaran_id']);
        if ($isGajiGuru) {
            $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'periode_bulan' => 'required|integer|between:1,12',
                'periode_tahun' => 'required|integer|between:2000,2100',
            ]);
        }

        // Map deskripsi ke keterangan sesuai nama kolom di database
        $validated['keterangan'] = $validated['deskripsi'];
        unset($validated['deskripsi']);

        $validated['jenis_pengeluaran_id'] = JenisPengeluaran::representativeIdFor((int) $validated['jenis_pengeluaran_id'])
            ?? (int) $validated['jenis_pengeluaran_id'];

        if ($request->hasFile('bukti_file')) {
            // Delete old file
            if ($pengeluaran->bukti_file) {
                Storage::disk('public')->delete($pengeluaran->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('pengeluaran', 'public');
        }

        DB::transaction(function () use ($pengeluaran, $validated, $request, $isGajiGuru) {
            $pengeluaran->update($validated);
            $this->syncGajiGuru($pengeluaran, $request, $isGajiGuru);
            $this->notifyPengeluaranDiperbarui($pengeluaran);
        });

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        
        // Delete file if exists
        if ($pengeluaran->bukti_file) {
            Storage::disk('public')->delete($pengeluaran->bukti_file);
        }
        
        $pengeluaran->gajiGuru()->delete();
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function isGajiGuruKategoriId(int $jenisPengeluaranId): bool
    {
        $jenis = JenisPengeluaran::query()->withTrashed()->find($jenisPengeluaranId);

        if (!$jenis) {
            return false;
        }

        return JenisPengeluaran::normalizeNama((string) $jenis->nama) === JenisPengeluaran::KATEGORI_GAJI_GURU;
    }

    private function syncGajiGuru(Pengeluaran $pengeluaran, Request $request, bool $isGajiGuru): void
    {
        if (!$isGajiGuru) {
            GajiGuru::query()->where('pengeluaran_id', $pengeluaran->id)->delete();
            return;
        }

        $payload = [
            'guru_id' => (int) $request->integer('guru_id'),
            'periode_bulan' => (int) $request->integer('periode_bulan'),
            'periode_tahun' => (int) $request->integer('periode_tahun'),
            'detail' => $request->input('detail_gaji_guru') ?: null,
            'dibayar_oleh_user_id' => Auth::id(),
        ];

        $existing = GajiGuru::query()
            ->withTrashed()
            ->where('pengeluaran_id', $pengeluaran->id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            $existing->update($payload);
            return;
        }

        GajiGuru::create(array_merge([
            'pengeluaran_id' => $pengeluaran->id,
        ], $payload));
    }

    private function notifyPengeluaranBaru(Pengeluaran $pengeluaran): void
    {
        $pengeluaran->loadMissing(['jenis']);

        $recipientIds = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Bendahara', 'Kepala Sekolah']);
            })
            ->pluck('id')
            ->push((int) auth()->id())
            ->filter()
            ->unique();

        $jumlah = 'Rp ' . number_format((float) $pengeluaran->jumlah, 0, ',', '.');
        $jenis = JenisPengeluaran::normalizeNama((string) ($pengeluaran->jenis->nama ?? ''));
        $keterangan = $pengeluaran->keterangan ? ' - ' . $pengeluaran->keterangan : '';

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'user_id' => (int) $recipientId,
                'judul' => 'Pengeluaran Baru Tercatat',
                'isi' => sprintf('%s%s (%s)', $jenis, $keterangan, $jumlah),
                'tipe' => 'pengeluaran',
                'terkait_dengan' => Pengeluaran::class,
                'terkait_id' => $pengeluaran->id,
                'is_read' => false,
            ]);
        }
    }

    private function notifyPengeluaranDiperbarui(Pengeluaran $pengeluaran): void
    {
        $pengeluaran->loadMissing(['jenis']);

        $recipientIds = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Bendahara', 'Kepala Sekolah']);
            })
            ->pluck('id')
            ->push((int) auth()->id())
            ->filter()
            ->unique();

        $jumlah = 'Rp ' . number_format((float) $pengeluaran->jumlah, 0, ',', '.');
        $jenis = JenisPengeluaran::normalizeNama((string) ($pengeluaran->jenis->nama ?? ''));
        $keterangan = $pengeluaran->keterangan ? ' - ' . $pengeluaran->keterangan : '';

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'user_id' => (int) $recipientId,
                'judul' => 'Pengeluaran Diperbarui',
                'isi' => sprintf('%s%s (%s)', $jenis, $keterangan, $jumlah),
                'tipe' => 'pengeluaran',
                'terkait_dengan' => Pengeluaran::class,
                'terkait_id' => $pengeluaran->id,
                'is_read' => false,
            ]);
        }
    }

    private function bulanOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }
}
