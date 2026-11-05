<?php

namespace App\Services;

use App\Mail\StaffLoginMail;
use App\Models\LoginMailLog;
use App\Models\Staff;
use App\Models\StaffStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

/**
 * Login mails are only ever sent when an Admin / Registry officer asks for it
 * (one staff member, or in bulk to staff never sent one). The mail carries a
 * "set your password" link — never a password.
 */
class LoginMailService
{
    public const STATUSES = [
        'not_sent'  => 'Not sent',
        'sent'      => 'Sent',
        'signed_in' => 'Signed in',
        'no_email'  => 'No e-mail',
    ];

    public function emailFor(Staff $staff): ?string
    {
        if ($staff->user?->email) return $staff->user->email;
        $typeId = $this->emailTypeId();
        $contacts = $staff->relationLoaded('contacts') ? $staff->contacts : $staff->contacts()->get();
        foreach ($contacts->where('contact_type_id', $typeId)->sortBy('id') as $c) {
            $e = strtolower(trim($c->contact_value));
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) return $e;
        }
        return null;
    }

    public function status(Staff $staff): string
    {
        $u = $staff->user;
        if ($u?->last_login_at) return 'signed_in';
        if ($u?->login_mail_sent_at) return 'sent';
        return $this->emailFor($staff) ? 'not_sent' : 'no_email';
    }

    /** Serving staff, with the filters used on the Login Mails page. */
    public function query(array $f = []): Builder
    {
        $inactive = StaffStatus::whereIn('status', config('workforce.inactive_staff_statuses', []))->pluck('id');
        $emailType = $this->emailTypeId();

        return Staff::query()
            ->when($inactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff_status_id')->orWhereNotIn('staff_status_id', $inactive)))
            ->when(!empty($f['division']),   fn ($q) => $q->whereHas('employment', fn ($e) => $e->where('division_id', $f['division'])))
            ->when(!empty($f['department']), fn ($q) => $q->whereHas('employment', fn ($e) => $e->where('department_id', $f['department'])))
            ->when(!empty($f['q']), fn ($q) => $q->search($f['q']))
            ->when(($f['status'] ?? '') !== '', function ($q) use ($f, $emailType) {
                $hasEmail = fn ($w) => $w->whereHas('user')->orWhereHas('contacts', fn ($c) => $c->where('contact_type_id', $emailType));
                match ($f['status']) {
                    'not_sent'  => $q->whereDoesntHave('user', fn ($u) => $u->whereNotNull('login_mail_sent_at')->orWhereNotNull('last_login_at'))->where($hasEmail),
                    'sent'      => $q->whereHas('user', fn ($u) => $u->whereNotNull('login_mail_sent_at')->whereNull('last_login_at')),
                    'signed_in' => $q->whereHas('user', fn ($u) => $u->whereNotNull('last_login_at')),
                    'no_email'  => $q->whereDoesntHave('user')->whereDoesntHave('contacts', fn ($c) => $c->where('contact_type_id', $emailType)),
                    default     => null,
                };
            });
    }

    /** Staff who have an e-mail address and have never been sent a login mail. */
    public function neverSent(array $f = []): Builder
    {
        return $this->query(['status' => 'not_sent'] + $f);
    }

    public function counts(array $f = []): array
    {
        $f = array_diff_key($f, ['status' => 1]);
        $out = ['all' => $this->query($f)->count()];
        foreach (array_keys(self::STATUSES) as $s) $out[$s] = $this->query($f + ['status' => $s])->count();
        return $out;
    }

    /** @return array{0: bool, 1: string} */
    public function send(Staff $staff, ?User $by = null): array
    {
        $email = $this->emailFor($staff);
        if (!$email) return [false, 'No e-mail address on record'];

        try {
            $user = $staff->user ?? $this->createAccount($staff, $email);
            if (is_string($user)) return $this->fail($staff, null, $email, $by, $user);

            $token = Password::broker('invites')->createToken($user);
            $link  = route('password.invite', ['token' => $token, 'email' => $user->email]);

            Mail::to($user->email)->send(new StaffLoginMail($staff, $user, $link, (int) env('LOGIN_MAIL_LINK_DAYS', 7)));

            $user->forceFill([
                'login_mail_sent_at' => now(),
                'login_mail_sent_by' => $by?->id,
                'login_mail_count'   => (int) $user->login_mail_count + 1,
            ])->saveQuietly();
            LoginMailLog::create(['staff_id' => $staff->id, 'user_id' => $user->id, 'email' => $user->email, 'sent_by' => $by?->id, 'status' => 'sent']);
            return [true, "Login mail sent to {$user->email}"];
        } catch (Throwable $e) {
            report($e);
            return $this->fail($staff, $staff->user, $email, $by, 'Mail server: ' . Str::limit($e->getMessage(), 180));
        }
    }

    private function createAccount(Staff $staff, string $email): User|string
    {
        $other = User::where('email', $email)->first();
        if ($other) return "E-mail {$email} is already used by another account ({$other->fileno})";
        if (User::where('fileno', $staff->staff_no)->exists()) return 'An account with this staff number already exists';

        $user = User::forceCreate([
            'fileno' => $staff->staff_no, 'surname' => $staff->surname, 'firstname' => $staff->firstname,
            'middlename' => $staff->othernames, 'email' => $email, 'role' => 'staff',
            'password' => Hash::make(Str::random(48)),       // unusable until they set their own
        ]);
        $staff->setRelation('user', $user);
        return $user;
    }

    private function fail(Staff $staff, ?User $user, string $email, ?User $by, string $why): array
    {
        LoginMailLog::create(['staff_id' => $staff->id, 'user_id' => $user?->id, 'email' => $email, 'sent_by' => $by?->id, 'status' => 'failed', 'note' => $why]);
        return [false, $why];
    }

    private ?int $emailType = null;

    private function emailTypeId(): ?int
    {
        return $this->emailType ??= DB::table('contact_types')->where('contact_type_name', 'Email Address')->value('id');
    }
}
