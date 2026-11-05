<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * - Nominal roll import: a record of each uploaded roll, the designation text
 *   from the roll, and optional marital status / LGA (non-Nigerian staff have
 *   no LGA; a few roll rows have no marital status).
 * - Login mail: when each user was last sent their login mail, by whom, and
 *   when they last signed in, plus a log of every mail sent.
 * - DICTS staff who were onboarded one by one are marked as already sent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'login_mail_sent_at')) $t->timestamp('login_mail_sent_at')->nullable();
            if (!Schema::hasColumn('users', 'login_mail_sent_by')) $t->unsignedBigInteger('login_mail_sent_by')->nullable();
            if (!Schema::hasColumn('users', 'login_mail_count'))   $t->unsignedInteger('login_mail_count')->default(0);
            if (!Schema::hasColumn('users', 'last_login_at'))      $t->timestamp('last_login_at')->nullable();
        });

        if (!Schema::hasTable('login_mail_logs')) {
            Schema::create('login_mail_logs', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('staff_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('email')->nullable();
                $t->unsignedBigInteger('sent_by')->nullable();
                $t->string('status', 20);           // sent | failed | recorded
                $t->text('note')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('nominal_roll_imports')) {
            Schema::create('nominal_roll_imports', function (Blueprint $t) {
                $t->id();
                $t->uuid('uuid')->unique();
                $t->string('file_name');
                $t->string('stored_path');
                $t->unsignedBigInteger('uploaded_by')->nullable();
                $t->string('status', 20)->default('previewed');   // previewed | applied
                $t->json('summary')->nullable();
                $t->timestamp('applied_at')->nullable();
                $t->unsignedBigInteger('applied_by')->nullable();
                $t->timestamps();
            });
        }

        Schema::table('employments', function (Blueprint $t) {
            if (!Schema::hasColumn('employments', 'designation_title')) $t->string('designation_title')->nullable()->after('designation_id');
        });

        DB::statement('ALTER TABLE staff MODIFY marital_status_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE staff MODIFY lga_id BIGINT UNSIGNED NULL');

        $hasUnique = collect(DB::select("SHOW INDEX FROM staff WHERE Column_name = 'staff_no' AND Non_unique = 0"))->isNotEmpty();
        $dupes     = DB::table('staff')->select('staff_no')->groupBy('staff_no')->havingRaw('COUNT(*) > 1')->exists();
        if (!$hasUnique && !$dupes) {
            Schema::table('staff', fn (Blueprint $t) => $t->unique('staff_no'));
        }

        // DICTS staff were sent their login one by one when they were onboarded.
        $dictsDivisions   = DB::table('divisions')->where('short_name', 'DICTS')->pluck('id');
        $dictsDepartments = DB::table('departments')->where('short_name', 'DICTS')->pluck('id');
        $dictsStaffNos = DB::table('staff')->join('employments', 'employments.staff_id', '=', 'staff.id')
            ->where(fn ($q) => $q->whereIn('employments.division_id', $dictsDivisions)->orWhereIn('employments.department_id', $dictsDepartments))
            ->pluck('staff.staff_no');

        $users = DB::table('users')->where('role', 'staff')->whereIn('fileno', $dictsStaffNos)->whereNull('login_mail_sent_at')->get(['id', 'email', 'created_at']);
        foreach ($users as $u) {
            DB::table('users')->where('id', $u->id)->update(['login_mail_sent_at' => $u->created_at ?? now(), 'login_mail_count' => 1]);
            DB::table('login_mail_logs')->insert([
                'user_id' => $u->id, 'staff_id' => DB::table('staff')->where('staff_no', DB::table('users')->where('id', $u->id)->value('fileno'))->value('id'),
                'email' => $u->email, 'status' => 'recorded', 'note' => 'Onboarded (DICTS) before login mail tracking',
                'created_at' => $u->created_at ?? now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('login_mail_logs');
        Schema::dropIfExists('nominal_roll_imports');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['login_mail_sent_at', 'login_mail_sent_by', 'login_mail_count', 'last_login_at']));
        Schema::table('employments', fn (Blueprint $t) => $t->dropColumn('designation_title'));
    }
};
