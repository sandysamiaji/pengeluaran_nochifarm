<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ExpenseUserPermission;
use App\Services\ExpensePermissionService;

class PermissionController extends Controller
{
    /**
     * Tampilkan Halaman Manajemen Hak Akses Pengguna
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Halaman Hak Akses hanya dapat dikelola oleh Administrator.');
        }

        $users = User::orderByRaw("FIELD(role, 'admin', 'user')")
            ->orderBy('name', 'asc')
            ->get();

        $selectedUserId = $request->query('user_id');
        if (!$selectedUserId) {
            $defaultNonAdmin = $users->firstWhere('role', '!=', 'admin');
            $selectedUser = $defaultNonAdmin ?: $users->first();
        } else {
            $selectedUser = User::find($selectedUserId) ?: $users->first();
        }

        $allPermissions = ExpensePermissionService::getAllPermissions();
        $userPermissionsMap = [];

        if ($selectedUser) {
            $existingPerms = ExpenseUserPermission::where('user_id', $selectedUser->id)
                ->pluck('is_enabled', 'permission_key')
                ->toArray();

            foreach ($allPermissions as $catKey => $cat) {
                foreach ($cat['items'] as $itemKey => $item) {
                    if (isset($existingPerms[$itemKey])) {
                        $userPermissionsMap[$itemKey] = (bool) $existingPerms[$itemKey];
                    } else {
                        // Admin default true, non-admin default dari catalog
                        $userPermissionsMap[$itemKey] = $selectedUser->role === 'admin' ? true : (bool) $item['default'];
                    }
                }
            }
        }

        return view('master.permissions.index', compact('users', 'selectedUser', 'allPermissions', 'userPermissionsMap'));
    }

    /**
     * AJAX Toggle Switch Hak Akses Fitur Per User
     */
    public function toggle(Request $request, $userId)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya Administrator yang berhak mengubah hak akses.'], 403);
        }

        $user = User::findOrFail($userId);
        $permissionKey = $request->input('permission_key');
        $isEnabled = $request->boolean('is_enabled');

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Administrator memiliki akses penuh dan tidak dapat dibatasi.',
                'is_admin' => true,
            ], 422);
        }

        ExpenseUserPermission::updateOrCreate(
            ['user_id' => $user->id, 'permission_key' => $permissionKey],
            ['is_enabled' => $isEnabled]
        );

        $permissionLabel = $permissionKey;
        foreach (ExpensePermissionService::getAllPermissions() as $category) {
            if (isset($category['items'][$permissionKey])) {
                $permissionLabel = $category['items'][$permissionKey]['label'];
                break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Izin '{$permissionLabel}' berhasil " . ($isEnabled ? 'diaktifkan' : 'dinonaktifkan') . " untuk {$user->name}.",
            'permission_key' => $permissionKey,
            'is_enabled' => $isEnabled,
        ]);
    }

    /**
     * Bulk Action Hak Akses (Aktifkan Semua / Nonaktifkan Semua / Reset Default)
     */
    public function bulk(Request $request, $userId)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Administrator yang berhak mengubah hak akses.');
        }

        $user = User::findOrFail($userId);
        $action = $request->input('action');

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Akun Administrator selalu memiliki akses penuh tanpa batas.');
        }

        $allPermissions = ExpensePermissionService::getAllPermissions();

        if ($action === 'grant_all') {
            foreach ($allPermissions as $cat) {
                foreach ($cat['items'] as $key => $item) {
                    ExpenseUserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => true]
                    );
                }
            }
            return redirect()->route('master.permissions', ['user_id' => $user->id])
                ->with('success', "Seluruh hak akses & izin login berhasil diberikan kepada {$user->name}.");
        } elseif ($action === 'revoke_all') {
            foreach ($allPermissions as $cat) {
                foreach ($cat['items'] as $key => $item) {
                    ExpenseUserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => false]
                    );
                }
            }
            return redirect()->route('master.permissions', ['user_id' => $user->id])
                ->with('success', "Seluruh hak akses & izin login berhasil dicabut dari {$user->name}. Pengguna ini tidak dapat login.");
        } elseif ($action === 'reset_default') {
            // Hapus rekaman spesifik agar kembali ke nilai bawaan
            ExpenseUserPermission::where('user_id', $user->id)->delete();
            return redirect()->route('master.permissions', ['user_id' => $user->id])
                ->with('success', "Hak akses {$user->name} berhasil direset ke pengaturan default (nonaktif).");
        } elseif ($action === 'grant_category' || $action === 'revoke_category') {
            $categoryKey = $request->input('category_key');
            $setVal = ($action === 'grant_category');

            if (isset($allPermissions[$categoryKey])) {
                foreach ($allPermissions[$categoryKey]['items'] as $key => $item) {
                    ExpenseUserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => $setVal]
                    );
                }
                $catLabel = $allPermissions[$categoryKey]['label'];
                $msg = $setVal ? "Seluruh izin kategori '{$catLabel}' berhasil diaktifkan." : "Seluruh izin kategori '{$catLabel}' berhasil dinonaktifkan.";
                return redirect()->route('master.permissions', ['user_id' => $user->id])->with('success', $msg);
            }
        }

        return redirect()->route('master.permissions', ['user_id' => $user->id]);
    }

    /**
     * Tambah Pengguna Baru oleh Admin
     */
    public function storeUser(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Administrator yang dapat menambah pengguna.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ], [
            'username.unique' => 'Username ini sudah digunakan di database farm.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan! Silakan atur hak aksesnya.");
    }

    /**
     * Update Pengguna oleh Admin
     */
    public function updateUser(Request $request, $id)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Administrator yang dapat mengubah data pengguna.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name = $validated['name'];
        $user->username = strtolower(trim($validated['username']));
        $user->email = strtolower(trim($validated['email']));
        $user->role = $validated['role'];
        $user->phone = $validated['phone'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle Status Aktif Pengguna
     */
    public function toggleActiveUser(Request $request, $id)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Administrator yang dapat mengubah status pengguna.');
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif digunakan.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Akun pengguna {$user->name} berhasil {$statusText}.");
    }
}
