<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use App\Models\Notifikasi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['siswa.kelas', 'jenis', 'user']);

        // Filter by search (Kode Transaksi or Nama Siswa)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q2) use ($search) {
                      $q2->where('nama', 'like', '%' . $search . '%')
                         ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_bayar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_bayar', '<=', $request->tanggal_akhir);
        }

        // Filter by Jenis Pembayaran
        $this->applyJenisPembayaranFilter($query, $request);

        // Filter by Metode Bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        $jenisPembayaran = $this->getJenisPembayaranOptions();

        // Statistics - dengan filter yang sama
        $statsQuery = Pembayaran::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q2) use ($search) {
                      $q2->where('nama', 'like', '%' . $search . '%')
                         ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }
        if ($request->filled('tanggal_mulai')) {
            $statsQuery->where('tanggal_bayar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $statsQuery->where('tanggal_bayar', '<=', $request->tanggal_akhir);
        }
        $this->applyJenisPembayaranFilter($statsQuery, $request);
        if ($request->filled('metode_bayar')) {
            $statsQuery->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        $totalSemua = $statsQuery->sum('jumlah');
        $totalTunai = (clone $statsQuery)->where('metode_bayar', 'Tunai')->count();
        $totalTransfer = (clone $statsQuery)->where('metode_bayar', 'Transfer')->count();
        $totalQRIS = (clone $statsQuery)->where('metode_bayar', 'QRIS')->count();

        return view('pembayaran.index', compact(
            'pembayaran', 
            'jenisPembayaran',
            'totalSemua',
            'totalTunai',
            'totalTransfer',
            'totalQRIS'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswa = Siswa::where('is_active', true)->orderBy('nama')->get();
        $jenisPembayaran = $this->getJenisPembayaranOptions();

        return view('pembayaran.create', compact('siswa', 'jenisPembayaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi unik
        $validated['kode_transaksi'] = 'PAY-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        
        // Map metode_pembayaran ke metode_bayar dengan format yang sesuai enum database
        $metodeMap = [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
        $validated['metode_bayar'] = $metodeMap[$validated['metode_pembayaran']] ?? 'Tunai';
        unset($validated['metode_pembayaran']);

        $validated['jenis_pembayaran_id'] = JenisPembayaran::representativeIdFor((int) $validated['jenis_pembayaran_id'])
            ?? (int) $validated['jenis_pembayaran_id'];
        
        $validated['user_id'] = Auth::id();

        $pembayaran = Pembayaran::create($validated);
        $this->notifyPemasukanBaru($pembayaran);

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'jenis', 'user'])->findOrFail($id);

        return view('pembayaran.show', compact('pembayaran'));
    }

    /**
     * Export detail pembayaran as PDF.
     */
    public function exportPdf(string $id)
    {
        $pembayaran = Pembayaran::with(['siswa.kelas', 'jenis', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pembayaran.pdf.detail', compact('pembayaran'))
            ->setPaper('a4', 'portrait');

        $safeKode = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $pembayaran->kode_transaksi);
        $filename = 'bukti-pembayaran-' . ($safeKode ?: (string) $pembayaran->id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $siswa = Siswa::where('is_active', true)->orderBy('nama')->get();
        $jenisPembayaran = $this->getJenisPembayaranOptions();

        $selectedJenisPembayaranId = JenisPembayaran::representativeIdFor((int) $pembayaran->jenis_pembayaran_id)
            ?? (int) $pembayaran->jenis_pembayaran_id;

        return view('pembayaran.edit', compact('pembayaran', 'siswa', 'jenisPembayaran', 'selectedJenisPembayaranId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        // Map metode_pembayaran ke metode_bayar dengan format yang sesuai enum database
        $metodeMap = [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
        $validated['metode_bayar'] = $metodeMap[$validated['metode_pembayaran']] ?? 'Tunai';
        unset($validated['metode_pembayaran']);

        $validated['jenis_pembayaran_id'] = JenisPembayaran::representativeIdFor((int) $validated['jenis_pembayaran_id'])
            ?? (int) $validated['jenis_pembayaran_id'];

        $pembayaran->update($validated);
        $this->notifyPemasukanDiperbarui($pembayaran);

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus.');
    }

    private function notifyPemasukanBaru(Pembayaran $pembayaran): void
    {
        $pembayaran->loadMissing(['siswa', 'jenis']);

        $recipientIds = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Bendahara', 'Kepala Sekolah']);
            })
            ->pluck('id')
            ->push((int) auth()->id())
            ->filter()
            ->unique();

        $jumlah = 'Rp ' . number_format((float) $pembayaran->jumlah, 0, ',', '.');
        $namaSiswa = $pembayaran->siswa->nama ?? 'Siswa';
        $jenis = JenisPembayaran::normalizeNama((string) ($pembayaran->jenis->nama ?? ''));

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'user_id' => (int) $recipientId,
                'judul' => 'Pemasukan Baru Tercatat',
                'isi' => sprintf('%s (%s) - %s', $namaSiswa, $jenis, $jumlah),
                'tipe' => 'pembayaran',
                'terkait_dengan' => Pembayaran::class,
                'terkait_id' => $pembayaran->id,
                'is_read' => false,
            ]);
        }
    }

    private function notifyPemasukanDiperbarui(Pembayaran $pembayaran): void
    {
        $pembayaran->loadMissing(['siswa', 'jenis']);

        $recipientIds = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Bendahara', 'Kepala Sekolah']);
            })
            ->pluck('id')
            ->push((int) auth()->id())
            ->filter()
            ->unique();

        $jumlah = 'Rp ' . number_format((float) $pembayaran->jumlah, 0, ',', '.');
        $namaSiswa = $pembayaran->siswa->nama ?? 'Siswa';
        $jenis = JenisPembayaran::normalizeNama((string) ($pembayaran->jenis->nama ?? ''));

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'user_id' => (int) $recipientId,
                'judul' => 'Pemasukan Diperbarui',
                'isi' => sprintf('%s (%s) - %s', $namaSiswa, $jenis, $jumlah),
                'tipe' => 'pembayaran',
                'terkait_dengan' => Pembayaran::class,
                'terkait_id' => $pembayaran->id,
                'is_read' => false,
            ]);
        }
    }

    private function getJenisPembayaranOptions()
    {
        return JenisPembayaran::dropdownOptions();
    }

    private function applyJenisPembayaranFilter($query, Request $request): void
    {
        if (!$request->filled('jenis_pembayaran_id')) {
            return;
        }

        $filterValue = trim((string) $request->input('jenis_pembayaran_id'));
        if ($filterValue === '') {
            return;
        }

        if (ctype_digit($filterValue)) {
            $jenisIds = JenisPembayaran::equivalentIds((int) $filterValue);

            if (empty($jenisIds)) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->whereIn('jenis_pembayaran_id', $jenisIds);
            return;
        }

        $normalized = JenisPembayaran::normalizeNama($filterValue);
        $jenisIds = JenisPembayaran::query()
            ->withTrashed()
            ->get(['id', 'nama'])
            ->filter(fn ($item) => JenisPembayaran::normalizeNama((string) $item->nama) === $normalized)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (empty($jenisIds)) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->whereIn('jenis_pembayaran_id', $jenisIds);
    }
}
