<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Staff_ProfileController extends Controller
{
    // ── Show profile ──────────────────────────────────────────
    public function myprofile()
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        if (!$profile) {
            return redirect()->route('staff.profile.create')
                             ->with('error', 'Please complete your profile first.');
        }

        return view('staff.profile.myprofile', compact('profile'));
    }

    // ── Create profile form ───────────────────────────────────
    public function create()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        return view('staff.profile.create', compact('profile'));
    }

    // ── Store profile (first-time) ────────────────────────────
    public function store(Request $request)
    {
       
        $request->validate([
            'avatar'      => 'required|image|mimes:png,jpeg,jpg|max:1024',
            'phone'       => 'required|string|max:20',
            'home_address' => 'required|string|max:200'
        ]);

        $userId  = Auth::id();
        $avatar  = '';

        if ($request->hasFile('avatar')) {
            $avatar = $this->saveAvatar($request->file('avatar'), $userId);
        }

        Profile::updateOrCreate(
            ['user_id' => $userId],
            array_merge($request->except(['_token', 'avatar']), [
                'user_id' => $userId,
                'designation' => Auth::user()->staff->employment->designation->cadreRank->cadre_rank_name,
                'avatar'  => $avatar,
                'home_address'=> $request->input('home_address')
            ])
        );

        return redirect()->route('staff.dashboard.index')
                         ->with('success', 'Profile created successfully.');
    }

    // ── Edit profile form ─────────────────────────────────────
    public function edit()
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();
        return view('staff.profile.edit', compact('profile'));
    }

    // ── Update profile ────────────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        // Staff can update personal info — NOT employment fields
        $profile->update($request->only([
            'title', 'phone', 'dob', 'gender',
            'geopolitical_zone', 'state', 'lga', 'senatorial_district',
            'nationality', 'birth_place', 'permanent_home_address',
            'nok_fullnames', 'nok_address', 'nok_phone', 'nok_email',
            'change_name', 'date_name_change', 'reason_name_change', 'original_names',
        ]));

        return redirect()->route('staff.profile.myprofile')
                         ->with('success', 'Profile updated successfully.');
    }

    // ── Update avatar only ────────────────────────────────────
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:png,jpeg,jpg|max:1024',
        ]);

        $profile = Profile::where('user_id', Auth::id())->firstOrFail();
        $avatar  = $this->saveAvatar($request->file('photo'), Auth::id());
        $profile->update(['avatar' => $avatar]);

        return back()->with('success', 'Profile photo updated.');
    }

    // ── Change password form ──────────────────────────────────
    public function changePasswordForm()
    {
        return view('staff.profile.change_password');
    }

    // ── Change password ───────────────────────────────────────
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return redirect()->route('staff.profile.myprofile')
                         ->with('success', 'Password changed successfully.');
    }

    // ── Private helper: save and compress avatar ──────────────
    private function saveAvatar($file, $userId): string
    {
        $extension   = strtolower($file->getClientOriginalExtension());
        $filename    = $userId . '.' . $extension;
        $storagePath = storage_path('app/public/avatars');

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $src = match($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($file->getRealPath()),
            'png'         => imagecreatefrompng($file->getRealPath()),
            default       => null,
        };

        if ($src) {
            $dst = imagecreatetruecolor(200, 200);
            $w   = imagesx($src);
            $h   = imagesy($src);
            $sq  = min($w, $h);
            $x   = (int)(($w - $sq) / 2);
            $y   = (int)(($h - $sq) / 2);

            imagecopyresampled($dst, $src, 0, 0, $x, $y, 200, 200, $sq, $sq);

            if ($extension === 'png') {
                imagepng($dst, $storagePath . '/' . $filename, 8);
            } else {
                imagejpeg($dst, $storagePath . '/' . $filename, 80);
            }

            imagedestroy($src);
            imagedestroy($dst);
        }

        return $filename;
    }

    public function email_user_profile(string $email)
    {
        $userprofile = User::where('email', $email)->first();
        return view('staff.profile.email_user_profile', compact('userprofile'));
    }
}
