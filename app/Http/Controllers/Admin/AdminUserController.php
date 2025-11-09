<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserEnrollment;
use App\Models\JpRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use PDF;

class AdminUserController extends Controller
{
    /**
     * Membuat instance controller baru.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar pengguna dengan paginasi dan pencarian serta filter berdasarkan peran.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Fungsi pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nip', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('jabatan', 'like', "%{$search}%")
                      ->orWhere('unit_kerja', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan peran
            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            // Filter berdasarkan status validasi
            if ($request->filled('is_validated')) {
                $query->where('is_validated', $request->is_validated);
            }

            $users = $query->orderBy('created_at', 'desc')
                          ->paginate(15);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pengguna.');
        }
    }

    /**
     * Menampilkan formulir untuk membuat pengguna baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan pengguna yang baru dibuat ke dalam penyimpanan.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nip' => 'required|string|unique:users,nip|max:50',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'jabatan' => 'required|string|max:255',
                'unit_kerja' => 'required|string|max:255',
                'role' => 'required|in:admin,instructor,student',
                'is_validated' => 'boolean'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_validated'] = $request->has('is_validated');

            User::create($validated);

            Log::info('Admin created new user: ' . $validated['email']);
            return redirect()->route('admin.users.index')
                           ->with('success', 'Pengguna berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat pengguna.');
        }
    }

    /**
     * Menampilkan pengguna tertentu dengan riwayat pendaftaran dan catatan JP.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $user = User::with([
                'userEnrollments.course',
                'userProgress.subModule',
                'quizAttempts.quiz',
                'certificates.course',
                'jpRecords.course'
            ])->findOrFail($id);

            $enrollmentStats = [
                'total_enrolled' => $user->userEnrollments->count(),
                'completed_courses' => $user->userEnrollments->where('status', 'completed')->count(),
                'in_progress' => $user->userEnrollments->where('status', 'in_progress')->count(),
                'total_jp' => $user->jpRecords->sum('jp_earned')
            ];

            return view('admin.users.show', compact('user', 'enrollmentStats'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@show: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pengguna.');
        }
    }

    /**
     * Menampilkan formulir untuk mengedit pengguna tertentu.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@edit: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pengguna.');
        }
    }

    /**
     * Memperbarui pengguna tertentu dalam penyimpanan.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $rules = [
                'nip' => 'required|string|max:50|' . Rule::unique('users')->ignore($id),
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|' . Rule::unique('users')->ignore($id),
                'jabatan' => 'required|string|max:255',
                'unit_kerja' => 'required|string|max:255',
                'role' => 'required|in:admin,instructor,student',
                'is_validated' => 'boolean'
            ];

            // Only validate password if it's provided
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            // Only update password if it's provided and not empty
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_validated'] = $request->has('is_validated');

            $user->update($validated);

            Log::info('Admin updated user: ' . $user->email);
            return redirect()->route('admin.users.index')
                           ->with('success', 'Data pengguna berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data pengguna.');
        }
    }

    /**
     * Menghapus pengguna tertentu dari penyimpanan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Periksa apakah pengguna memiliki pendaftaran atau data terkait lainnya
            if ($user->userEnrollments()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus pengguna yang memiliki data terkait.');
            }

            $userEmail = $user->email;
            $user->delete();

            Log::info('Admin deleted user: ' . $userEmail);
            return redirect()->route('admin.users.index')
                           ->with('success', 'Pengguna berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus pengguna.');
        }
    }

    /**
     * Memvalidasi/menyetujui pendaftaran pengguna.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_validated' => true]);

            Log::info('Admin validated user: ' . $user->email);
            return back()->with('success', 'Pengguna berhasil divalidasi.');

        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@validateUser: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memvalidasi pengguna.');
        }
    }

    /**
     * Mengekspor daftar pengguna ke Excel.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUsers(Request $request)
    {
        try {
            $format = $request->get('format', 'xlsx');
            
            if ($format === 'pdf') {
                $users = User::all();
                $pdf = PDF::loadView('admin.users.export-pdf', compact('users'));
                return $pdf->download('users-list.pdf');
            } else {
                return Excel::download(new UsersExport, 'users-list.xlsx');
            }
        } catch (\Exception $e) {
            Log::error('Error in AdminUserController@exportUsers: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data.');
        }
    }
}
