<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'user']);

        // Filter by search (NIS or Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%');
            });
        }

        // Filter by Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $siswa = $query->orderBy('nama')->paginate(10)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('students.index', compact('siswa', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('students.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|string|max:50|unique:siswa',
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nama_ayah' => 'nullable|string|max:200',
            'telepon_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:200',
            'telepon_ibu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'buat_akun_login' => 'nullable|boolean',
            'password_akun' => 'nullable|string|min:6|max:100',
        ]);

        $buatAkunLogin = $request->boolean('buat_akun_login');

        if (!empty($validated['email'])) {
            $validated['email'] = mb_strtolower(trim((string) $validated['email']));
        }

        if ($buatAkunLogin) {
            if (empty($validated['email'])) {
                return back()->withErrors([
                    'email' => 'Email wajib diisi jika ingin membuat akun login siswa.',
                ])->withInput();
            }

            $emailDipakai = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $validated['email'])])->exists();
            if ($emailDipakai) {
                return back()->withErrors([
                    'email' => 'Email sudah digunakan akun lain. Gunakan email lain untuk akun login siswa.',
                ])->withInput();
            }
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['buat_akun_login'], $validated['password_akun']);

        $siswa = Siswa::create($validated);

        $credentialMessage = null;
        if ($buatAkunLogin) {
            $passwordAwal = $request->filled('password_akun')
                ? (string) $request->input('password_akun')
                : (string) $siswa->nis;

            $user = User::create([
                'name' => $siswa->nama,
                'email' => (string) $siswa->email,
                'password' => Hash::make($passwordAwal),
                'role' => 'Siswa',
            ]);

            Role::findOrCreate('Siswa');
            $user->assignRole('Siswa');

            $siswa->update(['user_id' => $user->id]);
            $credentialMessage = ' Akun login siswa dibuat. Email: ' . $user->email . ' | Password awal: ' . $passwordAwal;
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.' . ($credentialMessage ?? ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id);
        return view('students.show', compact('siswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('students.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|string|max:50|unique:siswa,nis,' . $id,
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nama_ayah' => 'nullable|string|max:200',
            'telepon_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:200',
            'telepon_ibu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'buat_akun_login' => 'nullable|boolean',
            'password_akun' => 'nullable|string|min:6|max:100',
        ]);

        $buatAkunLogin = $request->boolean('buat_akun_login');

        if (!empty($validated['email'])) {
            $validated['email'] = mb_strtolower(trim((string) $validated['email']));
        }

        if ($buatAkunLogin) {
            if (empty($validated['email'])) {
                return back()->withErrors([
                    'email' => 'Email wajib diisi jika ingin membuat akun login siswa.',
                ])->withInput();
            }

            $emailBaru = mb_strtolower((string) $validated['email']);

            // Cari user yang terhubung ke siswa ini (via user_id atau via email lama)
            $excludeUserId = $siswa->user_id;
            if (!$excludeUserId && $siswa->email) {
                // Fallback: cari user berdasarkan email siswa yang tersimpan di DB
                $existingUser = User::query()
                    ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $siswa->email)])
                    ->first();
                $excludeUserId = $existingUser?->id;
            }

            $emailDipakai = User::query()
                ->whereRaw('LOWER(email) = ?', [$emailBaru])
                ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
                ->exists();

            if ($emailDipakai) {
                return back()->withErrors([
                    'email' => 'Email sudah digunakan akun lain. Gunakan email lain untuk akun login siswa.',
                ])->withInput();
            }
        }

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', $siswa->is_active);
        unset($validated['buat_akun_login'], $validated['password_akun']);

        $siswa->update($validated);

        $credentialMessage = null;
        if ($buatAkunLogin) {
            $user = $siswa->user_id ? User::query()->find($siswa->user_id) : null;
            $isUserBaru = false;

            if (!$user) {
                $user = new User();
                $isUserBaru = true;
            }

            $user->name = $siswa->nama;
            $user->email = (string) $siswa->email;
            $user->role = 'Siswa';

            if ($isUserBaru) {
                $passwordAwal = $request->filled('password_akun')
                    ? (string) $request->input('password_akun')
                    : (string) $siswa->nis;
                $user->password = Hash::make($passwordAwal);
                $credentialMessage = ' Akun login siswa dibuat. Email: ' . $user->email . ' | Password awal: ' . $passwordAwal;
            } elseif ($request->filled('password_akun')) {
                $user->password = Hash::make((string) $request->input('password_akun'));
                $credentialMessage = ' Password akun siswa berhasil diperbarui.';
            }

            $user->save();

            Role::findOrCreate('Siswa');
            if (!$user->hasRole('Siswa')) {
                $user->assignRole('Siswa');
            }

            if ((int) $siswa->user_id !== (int) $user->id) {
                $siswa->update(['user_id' => $user->id]);
            }
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diupdate.' . ($credentialMessage ?? ''));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Delete photo if exists
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
