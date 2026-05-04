<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guru::with('user');

        // Filter by search (NIP atau Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%');
            });
        }

        // Filter by Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by Pendidikan Terakhir
        if ($request->filled('pendidikan_terakhir')) {
            $query->where('pendidikan_terakhir', 'like', '%' . $request->pendidikan_terakhir . '%');
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $guru = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        return view('teachers.index', compact('guru'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:guru',
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
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
                    'email' => 'Email wajib diisi jika ingin membuat akun login guru.',
                ])->withInput();
            }

            $emailDipakai = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $validated['email'])])->exists();
            if ($emailDipakai) {
                return back()->withErrors([
                    'email' => 'Email sudah digunakan akun lain. Gunakan email lain untuk akun login guru.',
                ])->withInput();
            }
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('guru', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['buat_akun_login'], $validated['password_akun']);

        $guru = Guru::create($validated);

        $credentialMessage = null;
        if ($buatAkunLogin) {
            $passwordAwal = $request->filled('password_akun')
                ? (string) $request->input('password_akun')
                : (string) $guru->nip;

            $user = User::create([
                'name' => $guru->nama,
                'email' => (string) $guru->email,
                'password' => Hash::make($passwordAwal),
                'role' => 'Guru',
            ]);

            Role::findOrCreate('Guru');
            $user->assignRole('Guru');

            $guru->update(['user_id' => $user->id]);
            $credentialMessage = ' Akun login guru dibuat. Email: ' . $user->email . ' | Password awal: ' . $passwordAwal;
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.' . ($credentialMessage ?? ''));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        return view('teachers.show', compact('guru'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $guru = Guru::findOrFail($id);
        return view('teachers.edit', compact('guru'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:guru,nip,' . $id,
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
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
                    'email' => 'Email wajib diisi jika ingin membuat akun login guru.',
                ])->withInput();
            }

            $emailDipakai = User::query()
                ->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $validated['email'])])
                ->when($guru->user_id, fn ($q) => $q->where('id', '!=', $guru->user_id))
                ->exists();

            if ($emailDipakai) {
                return back()->withErrors([
                    'email' => 'Email sudah digunakan akun lain. Gunakan email lain untuk akun login guru.',
                ])->withInput();
            }
        }

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($guru->foto) {
                Storage::disk('public')->delete($guru->foto);
            }
            $validated['foto'] = $request->file('foto')->store('guru', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', $guru->is_active);
        unset($validated['buat_akun_login'], $validated['password_akun']);

        $guru->update($validated);

        $credentialMessage = null;
        if ($buatAkunLogin) {
            $user = $guru->user_id ? User::query()->find($guru->user_id) : null;
            $isUserBaru = false;

            if (!$user) {
                $user = new User();
                $isUserBaru = true;
            }

            $user->name = $guru->nama;
            $user->email = (string) $guru->email;
            $user->role = 'Guru';

            if ($isUserBaru) {
                $passwordAwal = $request->filled('password_akun')
                    ? (string) $request->input('password_akun')
                    : (string) $guru->nip;
                $user->password = Hash::make($passwordAwal);
                $credentialMessage = ' Akun login guru dibuat. Email: ' . $user->email . ' | Password awal: ' . $passwordAwal;
            } elseif ($request->filled('password_akun')) {
                $user->password = Hash::make((string) $request->input('password_akun'));
                $credentialMessage = ' Password akun guru berhasil diperbarui.';
            }

            $user->save();

            Role::findOrCreate('Guru');
            if (!$user->hasRole('Guru')) {
                $user->assignRole('Guru');
            }

            if ((int) $guru->user_id !== (int) $user->id) {
                $guru->update(['user_id' => $user->id]);
            }
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diupdate.' . ($credentialMessage ?? ''));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guru = Guru::findOrFail($id);
        
        // Delete photo if exists
        if ($guru->foto) {
            Storage::disk('public')->delete($guru->foto);
        }
        
        $guru->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }
}
