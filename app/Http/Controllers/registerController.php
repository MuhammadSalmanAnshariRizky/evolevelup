<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class registerController extends Controller
{
    public function showForm()
    {
        return view('home.registrasi');
    }

    public function register(Request $request)
    {
        // 1. Validasi semua input sekaligus (termasuk validasi kode kelas)
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'password' => ['required', 'min:6'],
                'role' => ['required', Rule::in(['student', 'teacher', 'murid', 'guru'])],
                'kodeKelas' => [
                    'nullable',
                    'string',
                    'max:50',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && strtolower(trim($value)) !== 'null') {
                            // Pastikan nama kolom 'token' di tabel 'classes' sesuai dengan database Anda
                            $exists = Classes::whereRaw('LOWER(token) = ?', [strtolower(trim($value))])->exists();
                            if (!$exists) {
                                $fail('Kode kelas / token yang dimasukkan tidak terdaftar.');
                            }
                        }
                    }
                ],
                'type_id_other' => ['nullable', Rule::in(['NISN', 'NIM', 'NIP', 'NIDN', 'NUPTK', 'id_lainnya'])],
                'id_other' => ['nullable', 'string', 'max:255', Rule::unique('users', 'id_other')],
            ],
            [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.unique' => 'Nama lengkap sudah terdaftar. Silakan gunakan nama lain.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
                'id_other.unique' => 'Nomor ID ini (NISN/NIM/NIP) sudah terdaftar pada akun lain.',
                'password.required' => 'Kata sandi wajib diisi.',
                'password.min' => 'Kata sandi minimal 6 karakter.',
            ]
        );

        // 2. Transaksi Database
        try {
            $roleMap = [
                'murid' => 'student',
                'student' => 'student',
                'guru' => 'teacher',
                'teacher' => 'teacher'
            ];
            $role = $roleMap[strtolower($validated['role'])];

            $kodeKelas = null;
            if (!empty($validated['kodeKelas']) && strtolower(trim($validated['kodeKelas'])) !== 'null') {
                $kodeKelas = trim($validated['kodeKelas']);
            }

            $typeIdOther = $validated['type_id_other'] ?? null;
            $idOther = !empty($validated['id_other']) && trim($validated['id_other']) !== '' ? trim($validated['id_other']) : null;

            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $role,
                'type_id_other' => $typeIdOther,
                'id_other' => $idOther,
            ]);

            // Jika ada kode kelas, hubungkan user dengan kelas tersebut
            if ($kodeKelas) {
                $kelas = Classes::whereRaw('LOWER(token) = ?', [strtolower($kodeKelas)])->first();

                if ($kelas) {
                    if ($role === 'student') {
                        $exists = DB::table('student_classes')->where('id_student', $user->id)->where('id_class', $kelas->id)->exists();
                        if (!$exists) {
                            DB::table('student_classes')->insert(['id_student' => $user->id, 'id_class' => $kelas->id]);
                        }
                    } else {
                        $exists = DB::table('teacher_classes')->where('id_teacher', $user->id)->where('id_class', $kelas->id)->exists();
                        if (!$exists) {
                            DB::table('teacher_classes')->insert(['id_teacher' => $user->id, 'id_class' => $kelas->id]);
                        }
                    }
                }
            }

            DB::commit();

            Auth::login($user);

            $redirectUrl = match ($user->role) {
                'student' => route('dashboard.siswa'),
                'teacher' => route('dashboardGuru'),
                default => route('login'),
            };

            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil.',
                    'redirect' => $redirectUrl,
                    'role' => $user->role,
                ], 201);
            }

            return redirect($redirectUrl)->with('success', 'Registrasi berhasil.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Register QueryError: ' . $e->getMessage());

            if ($e->errorInfo[1] == 1062) {
                $msg = $e->getMessage();
                $errors = [];

                if (str_contains($msg, 'name')) {
                    $errors['name'] = ['Nama lengkap sudah terdaftar.'];
                }
                if (str_contains($msg, 'email')) {
                    $errors['email'] = ['Email sudah terdaftar.'];
                }
                if (str_contains($msg, 'id_other')) {
                    $errors['id_other'] = ['Nomor ID sudah terdaftar.'];
                }
                if (empty($errors)) {
                    $errors['email'] = ['Data sudah terdaftar pada sistem.'];
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Data sudah pernah terdaftar.',
                    'errors' => $errors
                ], 422);
            }

            return $this->errorResponse($request, 'Terjadi kesalahan pada sistem database.', 500);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Register error: ' . $e->getMessage());

            return $this->errorResponse($request, 'Terjadi kesalahan saat registrasi. Silakan coba lagi.', 500);
        }
    }

    protected function errorResponse(Request $request, string $message, int $status = 422)
    {
        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }
        return back()->withInput($request->except('password'))->withErrors(['error' => $message]);
    }
}