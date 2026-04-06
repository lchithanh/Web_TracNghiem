<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'sometimes|in:student,teacher',
        'student_code' => 'nullable|string', // ban đầu nullable, regex kiểm tra bên dưới
    ]);

    // Kiểm tra validation cơ bản
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Nếu là student, bắt buộc student_code phải đúng định dạng DH + 8 số
    if (($request->role ?? 'student') === 'student') {
        if (!$request->student_code || !preg_match('/^DH\d{8}$/', $request->student_code)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'student_code' => ['MSSV phải bắt đầu bằng "DH" và 8 chữ số']
                ]
            ], 422);
        }
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'student',
        'student_code' => $request->student_code,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Đăng ký thành công',
        'data' => [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]
    ], 201);
}

    /**
     * Đăng nhập
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ]);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Cập nhật profile
     */
    public function updateProfile(Request $request)
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'avatar' => 'nullable|string|max:255',
        'student_code' => 'sometimes|nullable|string|unique:users,student_code,' . $user->id,
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Nếu là student, kiểm tra định dạng MSSV
    if ($user->role === 'student' && $request->has('student_code')) {
        if ($request->student_code && !preg_match('/^DH\d{8}$/', $request->student_code)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'student_code' => ['MSSV phải bắt đầu bằng "DH" và 8 chữ số']
                ]
            ], 422);
        }
    }

    $user->update($request->only(['name', 'avatar', 'student_code']));

    return response()->json([
        'success' => true,
        'message' => 'Cập nhật thông tin thành công',
        'data' => $user
    ]);
}
}