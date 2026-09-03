<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get authenticated user profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'role' => $user->role,
            'created_at' => $user->created_at->format('Y-m-d'),
        ], 'تم جلب بيانات الملف الشخصي');
    }

    /**
     * Update user profile details
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'] ?? (($validated['first_name'] ?? $user->first_name) . ' ' . ($validated['last_name'] ?? $user->last_name)),
            'first_name' => $validated['first_name'] ?? $user->first_name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'phone' => $validated['phone'] ?? $user->phone,
            'email' => $validated['email'],
        ]);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
        ], 'تم تحديث بيانات الحساب بنجاح');
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->errorResponse('كلمة المرور الحالية غير صحيحة', 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return $this->successResponse(null, 'تم تغيير كلمة المرور بنجاح');
    }
}

