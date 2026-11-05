<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\Staff_CircleController;
use App\Http\Controllers\Staff\Staff_DocumentController;
use App\Http\Controllers\Admin\Admin_DocumentSetupController;
use App\Http\Controllers\Admin\Admin_StaffDocumentController;
use App\Http\Controllers\Admin\Admin_AperOverviewController;
use App\Http\Controllers\Admin\Admin_StaffQualificationController;
use App\Http\Controllers\Admin\Admin_StaffRecordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Middleware\AdminAuth;

use App\Http\Controllers\Identity\Identity_AuthController;
use App\Http\Controllers\Identity\Identity_DashboardController;
use App\Http\Controllers\Identity\Identity_StaffController;

use App\Http\Controllers\Admin\Admin_AuthController;
use App\Http\Controllers\Admin\Admin_DashboardController;
use App\Http\Controllers\Admin\Admin_LeaveTypeController;
use App\Http\Controllers\Admin\Admin_LeaveController;
use App\Http\Controllers\Admin\Admin_LeaveApplicationController;
use App\Http\Controllers\Admin\Admin_StaffController;
use App\Http\Controllers\Admin\Admin_SettingsController;
use App\Http\Controllers\Admin\Admin_ProgressionController;
use App\Http\Controllers\Admin\Admin_DivisionTypeController;
use App\Http\Controllers\Admin\Admin_IdentityController;
use App\Http\Controllers\Admin\Admin_DivisionController;
use App\Http\Controllers\Admin\Admin_DepartmentController;
use App\Http\Controllers\Admin\Admin_ContactTypeController;
use App\Http\Controllers\Admin\Admin_GenderController;
use App\Http\Controllers\Admin\Admin_MaritalStatusController;
use App\Http\Controllers\Admin\Admin_GeoPolZoneController;

use App\Http\Controllers\Admin\Admin_StateController;
use App\Http\Controllers\Admin\Admin_LGAController;
use App\Http\Controllers\Admin\Admin_EmploymentTypeController;
use App\Http\Controllers\Admin\Admin_StaffCategoryController;

use App\Http\Controllers\Admin\Admin_StaffTitleController;
use App\Http\Controllers\Admin\Admin_StaffStatusController;

use App\Http\Controllers\Admin\Admin_DegreeController;
use App\Http\Controllers\Admin\Admin_ClassOfDegreeController;

use App\Http\Controllers\Admin\Admin_DeploymentPostController;
use App\Http\Controllers\Admin\Admin_DeploymentController;

use App\Http\Controllers\Admin\Admin_AperFormController;

use App\Http\Controllers\Staff\Staff_AuthController;
use App\Http\Controllers\Staff\Staff_DashboardController;

use App\Http\Controllers\Staff\Staff_ProfileController;

use App\Http\Controllers\StaffUpdateController;

use App\Http\Controllers\Staff\Staff_AperController;

use App\Http\Controllers\Admin\Admin_AperAssessorController;
use App\Http\Controllers\Staff\Staff_AperSeniorController;

use App\Http\Controllers\Staff\Staff_SupervisorController;

use App\Http\Controllers\Staff\Staff_HodController;
use App\Http\Controllers\Staff\Staff_ChairmanController;

use App\Http\Controllers\Admin\Admin_SenatorialDistrictController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/re-signin', [Admin_AuthController::class, 'resignin'])->name('admin.auth.re-signin');

Route::middleware('guest')->group(function(){
    Route::get('/', [Staff_AuthController::class, 'index'])->name('welcome');

    Route::post('/', [Staff_AuthController::class, 'login'])->name('staff.auth.login');

    Route::get('/sign-up', [Staff_AuthController::class, 'create_signup'])->name('staff.auth.create_signup');
    Route::post('/sign-up', [Staff_AuthController::class, 'store_signup'])->name('staff.auth.store_signup');

    Route::get('/admin', [Admin_AuthController::class, 'index'])->name('admin.auth.index');
    Route::post('/admin', [Admin_AuthController::class, 'login'])->name('admin.auth.login');

    Route::get('/identity', [Identity_AuthController::class, 'index'])->name('identity.auth.index');
    Route::post('/identity', [Identity_AuthController::class, 'login'])->name('identity.auth.login');

    // Staff Update Login
    Route::get('staff_updates/login', [StaffUpdateController::class, 'login'])->name('guest.staff_updates.login');
    Route::post('staff_updates/login', [StaffUpdateController::class, 'login_auth'])->name('guest.staff_updates.login_auth');

    Route::get('staff_updates/auth_credentials', [StaffUpdateController::class, 'create_auth_credentials'])->name('guests.staff_updates.create_auth_credentials');
    Route::post('staff_updates/auth_credentials', [StaffUpdateController::class, 'store_auth_credentials'])->name('guests.staff_updates.store_auth_credentials');

    Route::get('staff_updates/upload_photograph', [StaffUpdateController::class, 'upload_photograph'])->name('guests.staff_updates.upload_photograph');
    Route::post('staff_updates/upload_photograph', [StaffUpdateController::class, 'store_photograph'])->name('guests.staff_updates.store_photograph');

    Route::get('staff_updates/staff_update_form', [StaffUpdateController::class, 'create_update_form'])->name('guests.staff_updates.create_update_form');
    Route::post('staff_updates/staff_update_form', [StaffUpdateController::class, 'store_update_form'])->name('guests.staff_updates.store_update_form');

    // Forgot / reset password (all portals)
    Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.store');
    Route::get('set-password/{token}', [PasswordResetController::class, 'invite'])->name('password.invite');

    Route::get('staff_updates/completed', [StaffUpdateController::class, 'completed'])->name('guests.staff_updates.completed');

});

Route::prefix('staff')->middleware(['auth', 'staff', 'profile.complete'])->group(function(){
    Route::get('/dashboard', [Staff_DashboardController::class, 'index'])->name('staff.dashboard.index');
    Route::post('/logout', [Staff_AuthController::class, 'logout'])->name('staff.auth.logout');

    // Circle — colleagues in my division (read-only)
    Route::get('/circle', [Staff_CircleController::class, 'index'])->name('staff.circle.index');

    // My Documents — my Registry file; my uploads wait for Registry approval
    Route::get   ('/documents',                     [Staff_DocumentController::class, 'index'])->name('staff.documents.index');
    Route::get   ('/documents/upload',              [Staff_DocumentController::class, 'create'])->name('staff.documents.create');
    Route::post  ('/documents',                     [Staff_DocumentController::class, 'store'])->name('staff.documents.store');
    Route::get   ('/documents/{document}/view',     [Staff_DocumentController::class, 'view'])->name('staff.documents.view');
    Route::get   ('/documents/{document}/download', [Staff_DocumentController::class, 'download'])->name('staff.documents.download');
    Route::delete('/documents/{document}',          [Staff_DocumentController::class, 'destroy'])->name('staff.documents.destroy');

    // Profile
    Route::get('/profile/create', [Staff_ProfileController::class, 'create'])->name('staff.profile.create');
    Route::post('/profile/create', [Staff_ProfileController::class, 'store'])->name('staff.profile.store');

    Route::get('/profile/myprofile', [Staff_ProfileController::class, 'myprofile'])->name('staff.profile.myprofile');

    Route::get('/profile/myprofile/edit', [Staff_ProfileController::class, 'edit'])->name('staff.profile.myprofile.edit');
    Route::post('/profile/myprofile/update', [Staff_ProfileController::class, 'update'])->name('staff.profile.myprofile.update');

    //Route::post('/profile/myprofile/update_avatar', [Staff_ProfileController::class, 'update_avatar'])->name('staff.profile.myprofile.update_avatar');
    Route::post('/profile/myprofile/update_avatar', [Staff_ProfileController::class, 'updateAvatar'])->name('staff.profile.myprofile.update_avatar');

    Route::get('/profile/user/{email}/user_profile', [Staff_ProfileController::class, 'email_user_profile'])->name('staff.profile.email_user_profile');

    //Route::get('/profile/change_password', [Staff_ProfileController::class, 'change_password'])->name('staff.profile.change_password');
    Route::get('/profile/change_password', [Staff_ProfileController::class, 'changePasswordForm'])->name('staff.profile.change_password');
    Route::put('/profile/update_password', [Staff_ProfileController::class, 'changePassword'])->name('staff.profile.change_password.update');

     // -------------  APER Form listing  ----------------------------------------------------
    Route::get('aper', [Staff_AperController::class, 'index'])->name('staff.aper.index');

    // Open an APER form — determines category and loads correct form
    Route::get('aper/{aperForm}',[Staff_AperController::class, 'open'])->name('staff.aper.open');

    // Final submission by the staff member (Junior and Senior)
    Route::post('aper/{aperForm}/submit', [Staff_AperController::class, 'submit'])->name('staff.aper.submit');

    // Junior Non-Teaching — Part A store/update
    Route::post('aper/{aperForm}/junior/part-a',[Staff_AperController::class, 'storeJuniorPartA'])->name('staff.aper.junior.part_a.store');

    // Junior Non-Teaching — Part A store (new) & update (existing)
    Route::put ('aper/{aperForm}/junior/part-a',[Staff_AperController::class, 'storeJuniorPartA'])->name('staff.aper.junior.part_a.update');

    // Junior Non-Teaching — Part B
    Route::get ('aper/{aperForm}/junior/part-b', [Staff_AperController::class, 'showJuniorPartB'])->name('staff.aper.junior.part_b');
    Route::post('aper/{aperForm}/junior/part-b', [Staff_AperController::class, 'storeJuniorPartB'])->name('staff.aper.junior.part_b.store');
    Route::put ('aper/{aperForm}/junior/part-b', [Staff_AperController::class, 'storeJuniorPartB'])->name('staff.aper.junior.part_b.update');

    // ── Supervisor Portal ─────────────────────────────────

    // ── Supervisor Portal ──────────────────────────────────
    Route::get ('/supervisor',                              [Staff_SupervisorController::class, 'index']          )->name('staff.supervisor.index');
    Route::get ('/supervisor/{aperForm}/staff',             [Staff_SupervisorController::class, 'staffList']       )->name('staff.supervisor.staff_list');
    Route::get ('/supervisor/{aperForm}/assess/{staff}',    [Staff_SupervisorController::class, 'assess']          )->name('staff.supervisor.assess');
    Route::post('/supervisor/{aperForm}/assess/{staff}',    [Staff_SupervisorController::class, 'storeAssessment'] )->name('staff.supervisor.store_assessment');
    Route::put ('/supervisor/{aperForm}/assess/{staff}',    [Staff_SupervisorController::class, 'storeAssessment'] )->name('staff.supervisor.update_assessment');

    Route::get('/supervisor/{aperForm}/submission/{staff}/aper_junior_part_a', [Staff_SupervisorController::class, 'submission_part_a'])->name('staff.supervisor.submission.part_a');
    Route::get('/supervisor/{aperForm}/submission/{staff}/aper_junior_part_b', [Staff_SupervisorController::class, 'submission_part_b'])->name('staff.supervisor.submission.part_b');

    // ── Junior APER — Supervisor Evaluation Tab (Staff view) ──
    Route::get ('aper/{aperForm}/junior/supervisor-evaluation',       [Staff_AperController::class, 'showSupervisorEvaluation'])->name('staff.aper.junior.supervisor_evaluation');

    // ── Junior APER — Part C ──────────────────────────────
    Route::get ('aper/{aperForm}/junior/part-c',                      [Staff_AperController::class, 'showJuniorPartC']        )->name('staff.aper.junior.part_c');
    Route::post('aper/{aperForm}/junior/part-c/comment',              [Staff_AperController::class, 'storeJuniorPartCComment'])->name('staff.aper.junior.part_c.comment');

    // ── HOD Portal ────────────────────────────────────────
    Route::get ('/hod',                                    [Staff_HodController::class, 'index']          )->name('staff.hod.index');
    Route::get ('/hod/{aperForm}/staff',                   [Staff_HodController::class, 'staffList']       )->name('staff.hod.staff_list');
    Route::get ('/hod/{aperForm}/assess/{staff}',          [Staff_HodController::class, 'assess']          )->name('staff.hod.assess');
    Route::post('/hod/{aperForm}/assess/{staff}',          [Staff_HodController::class, 'storeAssessment'] )->name('staff.hod.store_assessment');
    Route::put ('/hod/{aperForm}/assess/{staff}',          [Staff_HodController::class, 'storeAssessment'] )->name('staff.hod.update_assessment');

    Route::get('/hod/{aperForm}/submission/senior/{staff}',     [Staff_HodController::class, 'viewSeniorSubmission'])->name('staff.hod.view_senior_submission');
    Route::get('/supervisor/{aperForm}/submission/{staff}',    [Staff_SupervisorController::class, 'viewSubmission'])->name('staff.supervisor.view_submission');
    Route::get('/hod/{aperForm}/submission/junior/{staff}',     [Staff_HodController::class, 'viewJuniorSubmission'])->name('staff.hod.view_junior_submission');

    // ── Chairman DAPC Portal ──────────────────────────────
    Route::get ('/chairman',                                  [Staff_ChairmanController::class, 'index']          )->name('staff.chairman.index');
    Route::get ('/chairman/{aperForm}/staff',                 [Staff_ChairmanController::class, 'staffList']       )->name('staff.chairman.staff_list');
    Route::get ('/chairman/{aperForm}/assess/{staff}',        [Staff_ChairmanController::class, 'assess']          )->name('staff.chairman.assess');
    Route::post('/chairman/{aperForm}/assess/{staff}',        [Staff_ChairmanController::class, 'storeAssessment'] )->name('staff.chairman.store_assessment');
    Route::put ('/chairman/{aperForm}/assess/{staff}',        [Staff_ChairmanController::class, 'storeAssessment'] )->name('staff.chairman.update_assessment');

    // Senior Non-Teaching — Part A Step 1: Personal
    Route::get ('aper/{aperForm}/senior/part-a1',        [Staff_AperSeniorController::class, 'showPartA1']  )->name('staff.aper.senior.part_a1');
    Route::post('aper/{aperForm}/senior/part-a1',        [Staff_AperSeniorController::class, 'storePartA1'] )->name('staff.aper.senior.part_a1.store');
    Route::put ('aper/{aperForm}/senior/part-a1',        [Staff_AperSeniorController::class, 'storePartA1'] )->name('staff.aper.senior.part_a1.update');

    // Senior Non-Teaching — Part A Step 2: Education
    Route::get ('aper/{aperForm}/senior/part-a2',        [Staff_AperSeniorController::class, 'showPartA2']  )->name('staff.aper.senior.part_a2');
    Route::post('aper/{aperForm}/senior/part-a2',        [Staff_AperSeniorController::class, 'storePartA2'] )->name('staff.aper.senior.part_a2.store');
    Route::put ('aper/{aperForm}/senior/part-a2',        [Staff_AperSeniorController::class, 'storePartA2'] )->name('staff.aper.senior.part_a2.update');

    // Senior Non-Teaching — Part A Step 3: University Experience
    Route::get ('aper/{aperForm}/senior/part-a3',        [Staff_AperSeniorController::class, 'showPartA3']  )->name('staff.aper.senior.part_a3');
    Route::post('aper/{aperForm}/senior/part-a3',        [Staff_AperSeniorController::class, 'storePartA3'] )->name('staff.aper.senior.part_a3.store');
    Route::put ('aper/{aperForm}/senior/part-a3',        [Staff_AperSeniorController::class, 'storePartA3'] )->name('staff.aper.senior.part_a3.update');

    // ── Senior Non-Teaching — Part C ─────────────────────────────────────
    Route::get ('/aper/{aperForm}/senior/part-c',         [Staff_AperSeniorController::class, 'showPartC']        )->name('staff.aper.senior.part_c');
    Route::post('/aper/{aperForm}/senior/part-c/comment', [Staff_AperSeniorController::class, 'storePartCComment'])->name('staff.aper.senior.part_c.comment');

    // ── HOD — Senior Non-Teaching Assessment ─────────────────────────────
    Route::get ('/hod/{aperForm}/assess/senior/{staff}',  [Staff_HodController::class, 'assessSenior']           )->name('staff.hod.senior.assess');
    Route::post('/hod/{aperForm}/assess/senior/{staff}',  [Staff_HodController::class, 'storeAssessmentSenior']  )->name('staff.hod.senior.store_assessment');
    Route::put ('/hod/{aperForm}/assess/senior/{staff}',  [Staff_HodController::class, 'storeAssessmentSenior']  )->name('staff.hod.senior.update_assessment');

    // ── Chairman DAPC — Senior Non-Teaching ──────────────────────────────
    Route::get ('/chairman/{aperForm}/senior/staff',               [Staff_ChairmanController::class, 'seniorStaffList']        )->name('staff.chairman.senior.staff_list');
    Route::get ('/chairman/{aperForm}/senior/assess/{staff}',      [Staff_ChairmanController::class, 'assessSenior']           )->name('staff.chairman.senior.assess');
    Route::post('/chairman/{aperForm}/senior/assess/{staff}',      [Staff_ChairmanController::class, 'storeAssessmentSenior']  )->name('staff.chairman.senior.store_assessment');
    Route::put ('/chairman/{aperForm}/senior/assess/{staff}',      [Staff_ChairmanController::class, 'storeAssessmentSenior']  )->name('staff.chairman.senior.update_assessment');

});

Route::prefix('identity')->middleware(['auth', 'identity'])->group(function(){
    Route::get('/dashboard', [Identity_DashboardController::class, 'index'])->name('identity.dashboard.index');
    Route::get('/staff/{identity}/show', [Identity_StaffController::class, 'show'])->name('identity.staff.details');    

    Route::get('/my_profile', [Identity_DashboardController::class, 'my_profile'])->name('identity.dashboard.my_profile');
    Route::get('/change_password', [Identity_AuthController::class, 'change_password'])->name('identity.auth.change_password');
    Route::post('/change_password', [Identity_AuthController::class, 'update_password'])->name('identity.auth.update_password');

    Route::post('/logout', [Identity_AuthController::class, 'logout'])->name('identity.auth.logout');
});

Route::prefix('admin')->middleware(['auth','admin'])->group(function(){

    Route::get('/dashboard', [Admin_DashboardController::class, 'index'])->name('admin.dashboard.index');
    Route::post('/logout', [Admin_AuthController::class, 'logout'])->name('admin.auth.logout');   

    // ── Staff Management ─────────────────────────────────────
    Route::get('staff',                [Admin_StaffController::class, 'index'])->name('admin.staff.index');
    Route::get('staff/create',         [Admin_StaffController::class, 'create'])->name('admin.staff.create');
    Route::post('staff',               [Admin_StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('staff/{staff}',        [Admin_StaffController::class, 'show'])->name('admin.staff.show');
    Route::get('staff/{staff}/edit',   [Admin_StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('staff/{staff}',        [Admin_StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('staff/{staff}',     [Admin_StaffController::class, 'destroy'])->name('admin.staff.destroy');

    // Staff record → Qualifications tab
    Route::post  ('staff/{staff}/qualifications',                 [Admin_StaffQualificationController::class, 'store'])->name('admin.staff.qualifications.store');
    Route::put   ('staff/{staff}/qualifications/{qualification}', [Admin_StaffQualificationController::class, 'update'])->name('admin.staff.qualifications.update');
    Route::delete('staff/{staff}/qualifications/{qualification}', [Admin_StaffQualificationController::class, 'destroy'])->name('admin.staff.qualifications.destroy');

    // Staff record → Next of Kin / Contact / Deployments / Off-Employment tabs
    Route::post  ('staff/{staff}/next-of-kin',                    [Admin_StaffRecordController::class, 'storeKin'])->name('admin.staff.kin.store');
    Route::put   ('staff/{staff}/next-of-kin/{kin}',              [Admin_StaffRecordController::class, 'updateKin'])->name('admin.staff.kin.update');
    Route::patch ('staff/{staff}/next-of-kin/{kin}/current',      [Admin_StaffRecordController::class, 'makeKinCurrent'])->name('admin.staff.kin.current');
    Route::delete('staff/{staff}/next-of-kin/{kin}',              [Admin_StaffRecordController::class, 'destroyKin'])->name('admin.staff.kin.destroy');
    Route::post  ('staff/{staff}/contacts',                       [Admin_StaffRecordController::class, 'storeContact'])->name('admin.staff.contacts.store');
    Route::put   ('staff/{staff}/contacts/{contact}',             [Admin_StaffRecordController::class, 'updateContact'])->name('admin.staff.contacts.update');
    Route::delete('staff/{staff}/contacts/{contact}',             [Admin_StaffRecordController::class, 'destroyContact'])->name('admin.staff.contacts.destroy');
    Route::post  ('staff/{staff}/deployments',                    [Admin_StaffRecordController::class, 'storeDeployment'])->name('admin.staff.deployments.store');
    Route::put   ('staff/{staff}/deployments/{deployment}',       [Admin_StaffRecordController::class, 'updateDeployment'])->name('admin.staff.deployments.update');
    Route::delete('staff/{staff}/deployments/{deployment}',       [Admin_StaffRecordController::class, 'destroyDeployment'])->name('admin.staff.deployments.destroy');
    Route::post  ('staff/{staff}/off-employments',                [Admin_StaffRecordController::class, 'storeOff'])->name('admin.staff.off.store');
    Route::put   ('staff/{staff}/off-employments/{offEmployment}', [Admin_StaffRecordController::class, 'updateOff'])->name('admin.staff.off.update');
    Route::delete('staff/{staff}/off-employments/{offEmployment}', [Admin_StaffRecordController::class, 'destroyOff'])->name('admin.staff.off.destroy');

    // Division Types
    Route::get('division_types', [Admin_DivisionTypeController::class, 'index'])->name('admin.division_types.index');
    Route::get('division_types/create', [Admin_DivisionTypeController::class, 'create'])->name('admin.division_types.create');
    Route::post('division_types/store', [Admin_DivisionTypeController::class, 'store'])->name('admin.division_types.store');
    Route::get('division_types/{division_type}/show', [Admin_DivisionTypeController::class, 'show'])->name('admin.division_types.show');
    Route::get('division_types/{division_type}/edit', [Admin_DivisionTypeController::class, 'edit'])->name('admin.division_types.edit');
    Route::post('division_types/{division_type}/update', [Admin_DivisionTypeController::class, 'update'])->name('admin.division_types.update');
    Route::get('division_types/{division_type}/confirm_delete', [Admin_DivisionTypeController::class, 'confirm_delete'])->name('admin.division_types.confirm_delete');
    Route::delete('division_types/{division_type}/delete', [Admin_DivisionTypeController::class, 'destroy'])->name('admin.division_types.delete');

    // Identity
    Route::get('identity', [Admin_IdentityController::class, 'index'])->name('admin.identity.index');
    Route::get('identity/{identity}/show', [Admin_IdentityController::class, 'show'])->name('admin.identity.show');
    Route::get('identity/{identity}/edit', [Admin_IdentityController::class, 'edit'])->name('admin.identity.edit');
    Route::put('identity/{identity}', [Admin_IdentityController::class, 'update'])->name('admin.identity.update');

    // Divisions
    Route::get('divisions', [Admin_DivisionController::class, 'index'])->name('admin.divisions.index');
    Route::post('divisions/store', [Admin_DivisionController::class, 'store'])->name('admin.divisions.store');
    Route::post('divisions/{division}/update', [Admin_DivisionController::class, 'update'])->name('admin.divisions.update');
    Route::delete('divisions/{division}/delete', [Admin_DivisionController::class, 'destroy'])->name('admin.divisions.delete');

    // Departments
    Route::get('departments', [Admin_DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::post('departments/store', [Admin_DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::post('departments/{department}/update', [Admin_DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('departments/{department}/delete', [Admin_DepartmentController::class, 'destroy'])->name('admin.departments.delete');

    // Contact Types
    Route::get('contact_types', [Admin_ContactTypeController::class, 'index'])->name('admin.contact_types.index');
    Route::get('contact_types/create', [Admin_ContactTypeController::class, 'create'])->name('admin.contact_types.create');
    Route::post('contact_types/store', [Admin_ContactTypeController::class, 'store'])->name('admin.contact_types.store');
    Route::get('contact_types/{contact_type}/edit', [Admin_ContactTypeController::class, 'edit'])->name('admin.contact_types.edit');
    Route::post('contact_types/{contact_type}/update', [Admin_ContactTypeController::class, 'update'])->name('admin.contact_types.update');
    Route::delete('contact_types/{contact_type}/delete', [Admin_ContactTypeController::class, 'destroy'])->name('admin.contact_type.delete');

    // Gender
    Route::get('gender', [Admin_GenderController::class, 'index'])->name('admin.gender.index');

    // Marital Status
    Route::get('marital_statuses', [Admin_MaritalStatusController::class, 'index'])->name('admin.marital_statuses.index');
    Route::get('marital_statuses/create',[Admin_MaritalStatusController::class, 'create'])->name('admin.marital_statuses.create');
    Route::post('marital_statuses/store', [Admin_MaritalStatusController::class, 'store'])->name('admin.marital_statuses.store');
    Route::get('marital_statuses/{marital_status}/edit', [Admin_MaritalStatusController::class, 'edit'])->name('admin.marital_statuses.edit');
    Route::post('marital_statuses/{marital_status}/update', [Admin_MaritalStatusController::class, 'update'])->name('admin.marital_statuses.update');
    Route::delete('marital_statuses/{marital_status}/delete', [Admin_MaritalStatusController::class, 'destroy'])->name('admin.marital_status.delete');

    // GeoPolZone
    Route::get('geo_pol_zones', [Admin_GeoPolZoneController::class, 'index'])->name('admin.geo_pol_zones.index');
    Route::get('geo_pol_zones/create', [Admin_GeoPolZoneController::class, 'create'])->name('admin.geo_pol_zones.create');
    Route::post('geo_pol_zones/store', [Admin_GeoPolZoneController::class, 'store'])->name('admin.geo_pol_zones.store');
    Route::get('geo_pol_zones/{geo_pol_zone}/edit', [Admin_GeoPolZoneController::class, 'edit'])->name('admin.geo_pol_zones.edit');
    Route::post('geo_pol_zones/{geo_pol_zone}/update', [Admin_GeoPolZoneController::class, 'update'])->name('admin.geo_pol_zones.update');
    Route::delete('geo_pol_zones/{geo_pol_zone}/delete', [Admin_GeoPolZoneController::class, 'destroy'])->name('admin.geo_pol_zones.delete');

    // State
    Route::get('states', [Admin_StateController::class, 'index'])->name('admin.states.index');

    // LGA
    Route::get('lgas', [Admin_LGAController::class, 'index'])->name('admin.lgas.index');

    // Employment Types
    Route::get('employment_types', [Admin_EmploymentTypeController::class, 'index'])->name('admin.employment_types.index');
    Route::get('employment_types/create', [Admin_EmploymentTypeController::class, 'create'])->name('admin.employment_types.create');
    Route::post('employment_types/store', [Admin_EmploymentTypeController::class, 'store'])->name('admin.employment_types.store');
    Route::get('employment_types/{employment_type}/edit' , [Admin_EmploymentTypeController::class, 'edit'])->name('admin.employment_types.edit');
    Route::post('employment_types/{employment_type}/update', [Admin_EmploymentTypeController::class, 'update'])->name('admin.employment_types.update');
    Route::delete('employment_types/{employment_type}/delete', [Admin_EmploymentTypeController::class, 'destroy'])->name('admin.employment_types.delete');

    // Staff Categories
    Route::get('staff_categories', [Admin_StaffCategoryController::class, 'index'])->name('admin.staff_categories.index');
    Route::post('staff_categories/store', [Admin_StaffCategoryController::class, 'store'])->name('admin.staff_categories.store');
    Route::post('staff_categories/{staff_category}/update', [Admin_StaffCategoryController::class, 'update'])->name('admin.staff_categories.update');
    Route::delete('staff_categories/{staff_category}/delete', [Admin_StaffCategoryController::class, 'destroy'])->name('admin.staff_categories.delete');

    // Staff Titles - Admin_StaffTitleController
    Route::get('staff_titles', [Admin_StaffTitleController::class, 'index'])->name('admin.staff_titles.index');
    Route::get('staff_titles/create', [Admin_StaffTitleController::class, 'create'])->name('admin.staff_titles.create');
    Route::post('staff_titles/store', [Admin_StaffTitleController::class, 'store'])->name('admin.staff_titles.store');
    Route::get('staff_titles/{staff_title}/edit' , [Admin_StaffTitleController::class, 'edit'])->name('admin.staff_titles.edit');
    Route::post('staff_titles/{staff_title}/update', [Admin_StaffTitleController::class, 'update'])->name('admin.staff_titles.update');
    Route::delete('staff_titles/{staff_title}/delete', [Admin_StaffTitleController::class, 'destroy'])->name('admin.staff_titles.delete');

    // Staff Status - Admin_StaffStatusController
    Route::get('staff_statuses', [Admin_StaffStatusController::class, 'index'])->name('admin.staff_statuses.index');
    Route::get('staff_statuses/create', [Admin_StaffStatusController::class, 'create'])->name('admin.staff_statuses.create');
    Route::post('staff_statuses/store', [Admin_StaffStatusController::class, 'store'])->name('admin.staff_statuses.store');
    Route::post('staff_statuses/{staff_status}/update', [Admin_StaffStatusController::class, 'update'])->name('admin.staff_statuses.update');
    Route::delete('staff_statuses/{staff_status}/delete', [Admin_StaffStatusController::class, 'destroy'])->name('admin.staff_statuses.delete');

    // Degree - Admin_DegreeController
    Route::get('degrees', [Admin_DegreeController::class, 'index'])->name('admin.degrees.index');
    Route::get('degrees/create', [Admin_DegreeController::class, 'create'])->name('admin.degrees.create');
    Route::post('degrees/store', [Admin_DegreeController::class, 'store'])->name('admin.degrees.store');
    Route::delete('degrees/{degree}/delete', [Admin_DegreeController::class, 'destroy'])->name('admin.degrees.delete');

    // Class of Degree - Admin_ClassOfDegreeController
    Route::get('classes_of_degrees', [Admin_ClassOfDegreeController::class, 'index'])->name('admin.classes_of_degrees.index');
    Route::post('classes_of_degrees/store', [Admin_ClassOfDegreeController::class, 'store'])->name('admin.classes_of_degrees.store');
    Route::post('classes_of_degrees/{class_of_degree}/update', [Admin_ClassOfDegreeController::class, 'update'])->name('admin.classes_of_degrees.update');
    Route::delete('classes_of_degrees/{class_of_degree}/delete', [Admin_ClassOfDegreeController::class, 'destroy'])->name('admin.classes_of_degrees.delete');

    // Deployment of Post - Admin_DeploymentPostController 
    Route::get('deployment_posts', [Admin_DeploymentPostController::class, 'index'])->name('admin.deployment_posts.index');
    Route::get('deployment_posts/create', [Admin_DeploymentPostController::class, 'create'])->name('admin.deployment_posts.create');
    Route::post('deployment_posts/store', [Admin_DeploymentPostController::class, 'store'])->name('admin.deployment_posts.store');
    Route::delete('deployment_posts/{deployment_post}/delete', [Admin_DeploymentPostController::class, 'destroy'])->name('admin.deployment_posts.delete');

    // Deployment - Admin_DeploymentsController 
    Route::get('deployments', [Admin_DeploymentController::class, 'index'])->name('admin.deployments.index');
    Route::get('deployments/create', [Admin_DeploymentController::class, 'create'])->name('admin.deployments.create');
    Route::post('deployments/store', [Admin_DeploymentController::class, 'store'])->name('admin.deployments.store');
    Route::get('deployments/{deployment}/show', [Admin_DeploymentController::class, 'show'])->name('admin.deployments.show');
    Route::get('deployments/{deployment}/edit' , [Admin_DeploymentController::class, 'edit'])->name('admin.deployments.edit');
    Route::post('deployments/{deployment}/update', [Admin_DeploymentController::class, 'update'])->name('admin.deployments.update');
    Route::delete('deployments/{deployment}/delete', [Admin_DeploymentController::class, 'destroy'])->name('admin.deployments.delete');

    // ── Gender (add store/update/delete) ─────────────────────
    Route::post('gender',             [Admin_GenderController::class, 'store'])->name('admin.gender.store');
    Route::put('gender/{gender}',     [Admin_GenderController::class, 'update'])->name('admin.gender.update');
    Route::delete('gender/{gender}',  [Admin_GenderController::class, 'destroy'])->name('admin.gender.delete');

    // ── Classes of Degree (add store/update/delete) ───────────
    Route::post('classes_of_degrees/store',                      [Admin_ClassOfDegreeController::class, 'store'])->name('admin.classes_of_degrees.store');
    Route::post('classes_of_degrees/{class_of_degree}/update',   [Admin_ClassOfDegreeController::class, 'update'])->name('admin.classes_of_degrees.update');
    Route::put('classes_of_degrees/{class_of_degree}',           [Admin_ClassOfDegreeController::class, 'update'])->name('admin.classes_of_degrees.update_put');
    Route::delete('classes_of_degrees/{class_of_degree}/delete', [Admin_ClassOfDegreeController::class, 'destroy'])->name('admin.classes_of_degrees.delete');

    // ── Staff Categories (add update PUT + delete) ────────────
    Route::put('staff_categories/{staff_category}',        [Admin_StaffCategoryController::class, 'update'])->name('admin.staff_categories.update_put');
    Route::delete('staff_categories/{staff_category}',     [Admin_StaffCategoryController::class, 'destroy'])->name('admin.staff_categories.delete');

    // ── Deployments (ensure PUT + DELETE exist) ───────────────
    Route::put('deployments/{deployment}',    [Admin_DeploymentController::class, 'update'])->name('admin.deployments.update_put');
    Route::delete('deployments/{deployment}', [Admin_DeploymentController::class, 'destroy'])->name('admin.deployments.delete');

        // ── Progression: Promotions ──────────────────────────────
    Route::get('promotions',              [Admin_ProgressionController::class, 'promotions'])->name('admin.promotions.index');
    Route::post('promotions',             [Admin_ProgressionController::class, 'storePromotion'])->name('admin.promotions.store');
    Route::put('promotions/{promotion}',  [Admin_ProgressionController::class, 'updatePromotion'])->name('admin.promotions.update');
    Route::delete('promotions/{promotion}',[Admin_ProgressionController::class, 'destroyPromotion'])->name('admin.promotions.destroy');

    // ── Progression: Step Increments ─────────────────────────
    Route::get('step_increments',                  [Admin_ProgressionController::class, 'stepIncrements'])->name('admin.step_increments.index');
    Route::post('step_increments',                 [Admin_ProgressionController::class, 'storeStepIncrement'])->name('admin.step_increments.store');
    Route::put('step_increments/{stepIncrement}',  [Admin_ProgressionController::class, 'updateStepIncrement'])->name('admin.step_increments.update');
    Route::delete('step_increments/{stepIncrement}',[Admin_ProgressionController::class, 'destroyStepIncrement'])->name('admin.step_increments.destroy');

    // ── Progression: Cadres ──────────────────────────────────
    Route::get('cadres',           [Admin_ProgressionController::class, 'cadres'])->name('admin.cadres.index');
    Route::post('cadres',          [Admin_ProgressionController::class, 'storeCadre'])->name('admin.cadres.store');
    Route::put('cadres/{cadre}',   [Admin_ProgressionController::class, 'updateCadre'])->name('admin.cadres.update');
    Route::delete('cadres/{cadre}',[Admin_ProgressionController::class, 'destroyCadre'])->name('admin.cadres.destroy');

    // ── Progression: Cadre Ranks ─────────────────────────────
    Route::get('cadre_ranks',              [Admin_ProgressionController::class, 'cadreRanks'])->name('admin.cadre_ranks.index');
    Route::post('cadre_ranks',             [Admin_ProgressionController::class, 'storeCadreRank'])->name('admin.cadre_ranks.store');
    Route::put('cadre_ranks/{cadreRank}',  [Admin_ProgressionController::class, 'updateCadreRank'])->name('admin.cadre_ranks.update');
    Route::delete('cadre_ranks/{cadreRank}',[Admin_ProgressionController::class, 'destroyCadreRank'])->name('admin.cadre_ranks.destroy');

    // ── Progression: Designations ────────────────────────────
    Route::get('designations',                [Admin_ProgressionController::class, 'designations'])->name('admin.designations.index');
    Route::post('designations',               [Admin_ProgressionController::class, 'storeDesignation'])->name('admin.designations.store');
    Route::put('designations/{designation}',  [Admin_ProgressionController::class, 'updateDesignation'])->name('admin.designations.update');
    Route::delete('designations/{designation}',[Admin_ProgressionController::class, 'destroyDesignation'])->name('admin.designations.destroy');

    // ── Progression: Salary Structures ──────────────────────
    Route::get('salary_structures',                      [Admin_ProgressionController::class, 'salaryStructures'])->name('admin.salary_structures.index');
    Route::post('salary_structures',                     [Admin_ProgressionController::class, 'storeSalaryStructure'])->name('admin.salary_structures.store');
    Route::put('salary_structures/{salaryStructure}',    [Admin_ProgressionController::class, 'updateSalaryStructure'])->name('admin.salary_structures.update');
    Route::delete('salary_structures/{salaryStructure}', [Admin_ProgressionController::class, 'destroySalaryStructure'])->name('admin.salary_structures.destroy');

    // ── Progression: Salary Levels ───────────────────────────
    Route::get('salary_levels',                  [Admin_ProgressionController::class, 'salaryLevels'])->name('admin.salary_levels.index');
    Route::post('salary_levels',                 [Admin_ProgressionController::class, 'storeSalaryLevel'])->name('admin.salary_levels.store');
    Route::put('salary_levels/{salaryLevel}',    [Admin_ProgressionController::class, 'updateSalaryLevel'])->name('admin.salary_levels.update');
    Route::delete('salary_levels/{salaryLevel}', [Admin_ProgressionController::class, 'destroySalaryLevel'])->name('admin.salary_levels.destroy');

    // ── Progression: Salary Steps ────────────────────────────
    Route::get('salary_steps',               [Admin_ProgressionController::class, 'salarySteps'])->name('admin.salary_steps.index');
    Route::post('salary_steps',              [Admin_ProgressionController::class, 'storeSalaryStep'])->name('admin.salary_steps.store');
    Route::put('salary_steps/{salaryStep}',  [Admin_ProgressionController::class, 'updateSalaryStep'])->name('admin.salary_steps.update');
    Route::delete('salary_steps/{salaryStep}',[Admin_ProgressionController::class, 'destroySalaryStep'])->name('admin.salary_steps.destroy');

    // ── Settings Hub ─────────────────────────────────────────
    Route::get('settings', [Admin_SettingsController::class, 'index'])->name('admin.settings.index');

    // ── States (missing store/update/delete) ──────────────────
    Route::post('states',             [Admin_StateController::class, 'store'])->name('admin.states.store');
    Route::delete('states/{state}',   [Admin_StateController::class, 'destroy'])->name('admin.states.delete');

    // ── LGAs (missing store/update/delete) ────────────────────
    Route::post('lgas',               [Admin_LGAController::class, 'store'])->name('admin.lgas.store');
    Route::delete('lgas/{lga}',       [Admin_LGAController::class, 'destroy'])->name('admin.lgas.delete');

    // ── Degrees (missing update/delete) ───────────────────────
    Route::delete('degrees/{degree}', [Admin_DegreeController::class, 'destroy'])->name('admin.degrees.delete');

    // ── Deployment Posts (missing store/update/delete) ────────
    Route::post('deployment_posts',               [Admin_DeploymentPostController::class, 'store'])->name('admin.deployment_posts.store');
    Route::delete('deployment_posts/{deploymentPost}',[Admin_DeploymentPostController::class, 'destroy'])->name('admin.deployment_posts.delete');

    // ── Staff Titles (missing delete) ─────────────────────────
    Route::delete('staff_titles/{staff_title}', [Admin_StaffTitleController::class, 'destroy'])->name('admin.staff_titles.delete');

    // ── Staff Statuses (missing store/update/delete) ──────────
    Route::post('staff_statuses',               [Admin_StaffStatusController::class, 'store'])->name('admin.staff_statuses.store');
    Route::put('staff_statuses/{staff_status}', [Admin_StaffStatusController::class, 'update'])->name('admin.staff_statuses.update');
    Route::delete('staff_statuses/{staff_status}',[Admin_StaffStatusController::class, 'destroy'])->name('admin.staff_statuses.delete');

    // ── Employment Types (fix update route to PUT) ────────────
    Route::put('employment_types/{employment_type}', [Admin_EmploymentTypeController::class, 'update'])->name('admin.employment_types.update_put');
    Route::delete('employment_types/{employment_type}', [Admin_EmploymentTypeController::class, 'destroy'])->name('admin.employment_types.delete');

    // ── Contact Types (missing delete) ───────────────────────

    // ── Marital Statuses (missing delete) ─────────────────────

    // ── Division Types (ensure PUT exists) ───────────────────
    Route::put('division_types/{division_type}', [Admin_DivisionTypeController::class, 'update'])->name('admin.division_types.update_put');
    Route::delete('division_types/{division_type}', [Admin_DivisionTypeController::class, 'destroy'])->name('admin.division_types.delete');

    // ── Geo-Pol Zones (ensure PUT exists) ─────────────────────
    Route::put('geo_pol_zones/{geo_pol_zone}', [Admin_GeoPolZoneController::class, 'update'])->name('admin.geo_pol_zones.update_put');
    Route::delete('geo_pol_zones/{geo_pol_zone}', [Admin_GeoPolZoneController::class, 'destroy'])->name('admin.geo_pol_zones.delete');

    // ── Leave Types ──────────────────────────────────────────
    Route::get('leave-types',              [Admin_LeaveTypeController::class, 'index'])->name('admin.leave_types.index');
    Route::post('leave-types',             [Admin_LeaveTypeController::class, 'store'])->name('admin.leave_types.store');
    Route::put('leave-types/{leaveType}',  [Admin_LeaveTypeController::class, 'update'])->name('admin.leave_types.update');
    Route::delete('leave-types/{leaveType}', [Admin_LeaveTypeController::class, 'destroy'])->name('admin.leave_types.destroy');

    // ── Leave Records ────────────────────────────────────────
    Route::get('leaves',                   [Admin_LeaveController::class, 'index'])->name('admin.leaves.index');
    Route::get('leaves/create',            [Admin_LeaveController::class, 'create'])->name('admin.leaves.create');
    Route::post('leaves',                  [Admin_LeaveController::class, 'store'])->name('admin.leaves.store');
    Route::get('leaves/{leave}',           [Admin_LeaveController::class, 'show'])->name('admin.leaves.show');
    Route::get('leaves/{leave}/edit',      [Admin_LeaveController::class, 'edit'])->name('admin.leaves.edit');
    Route::put('leaves/{leave}',           [Admin_LeaveController::class, 'update'])->name('admin.leaves.update');
    Route::delete('leaves/{leave}',        [Admin_LeaveController::class, 'destroy'])->name('admin.leaves.destroy');

    // ── Leave Applications ───────────────────────────────────
    Route::get('leave-applications',                        [Admin_LeaveApplicationController::class, 'index'])->name('admin.leave_applications.index');
    Route::get('leave-applications/{leave}',                [Admin_LeaveApplicationController::class, 'show'])->name('admin.leave_applications.show');
    Route::patch('leave-applications/{leave}/approve',      [Admin_LeaveApplicationController::class, 'approve'])->name('admin.leave_applications.approve');
    Route::patch('leave-applications/{leave}/reject',       [Admin_LeaveApplicationController::class, 'reject'])->name('admin.leave_applications.reject');

     // ── APER Forms ───────────────────────────────────
    Route::get   ('aper/overview',       [Admin_AperOverviewController::class, 'index'])->name('admin.aper.overview');
    Route::get   ('aper/overview/staff', [Admin_AperOverviewController::class, 'staff'])->name('admin.aper.overview.staff');
    Route::get   ('aper',          [Admin_AperFormController::class, 'index'  ])->name('admin.aper.index');
    Route::post  ('aper',          [Admin_AperFormController::class, 'store'  ])->name('admin.aper.store');
    Route::put   ('aper/{aper}',   [Admin_AperFormController::class, 'update' ])->name('admin.aper.update');
    Route::delete('aper/{aper}',   [Admin_AperFormController::class, 'destroy'])->name('admin.aper.destroy');

    // ── APER Supervisors ─────────────────────────────────
    Route::get   ('aper/supervisors',             [Admin_AperAssessorController::class, 'supervisors']      )->name('admin.aper.supervisors.index');
    Route::post  ('aper/supervisors',             [Admin_AperAssessorController::class, 'storeSupervisor']  )->name('admin.aper.supervisors.store');
    Route::put   ('aper/supervisors/{supervisor}',[Admin_AperAssessorController::class, 'updateSupervisor'] )->name('admin.aper.supervisors.update');
    Route::delete('aper/supervisors/{supervisor}',[Admin_AperAssessorController::class, 'destroySupervisor'])->name('admin.aper.supervisors.destroy');

    // ── APER HODs ─────────────────────────────────────────
    Route::get   ('aper/hods',      [Admin_AperAssessorController::class, 'hods']        )->name('admin.aper.hods.index');
    Route::post  ('aper/hods',      [Admin_AperAssessorController::class, 'storeHod']    )->name('admin.aper.hods.store');
    Route::put   ('aper/hods/{hod}',[Admin_AperAssessorController::class, 'updateHod']   )->name('admin.aper.hods.update');
    Route::delete('aper/hods/{hod}',[Admin_AperAssessorController::class, 'destroyHod']  )->name('admin.aper.hods.destroy');

    // ── APER Chairman DAPC ────────────────────────────────
    Route::get   ('aper/chairman-dapc',                  [Admin_AperAssessorController::class, 'chairmanDapc']       )->name('admin.aper.chairman_dapc.index');
    Route::post  ('aper/chairman-dapc',                  [Admin_AperAssessorController::class, 'storeChairmanDapc']  )->name('admin.aper.chairman_dapc.store');
    Route::put   ('aper/chairman-dapc/{chairmanDapc}',   [Admin_AperAssessorController::class, 'updateChairmanDapc'] )->name('admin.aper.chairman_dapc.update');
    Route::delete('aper/chairman-dapc/{chairmanDapc}',   [Admin_AperAssessorController::class, 'destroyChairmanDapc'])->name('admin.aper.chairman_dapc.destroy');

    // =====================================================================
    // Update routes used by the in-page "Edit" modals (form action is set in JS)
    // =====================================================================
    Route::put('states/{state}',                               [Admin_StateController::class, 'update'])->name('admin.states.update');
    Route::put('lgas/{lga}',                                   [Admin_LGAController::class, 'update'])->name('admin.lgas.update');
    Route::put('degrees/{degree}',                             [Admin_DegreeController::class, 'update'])->name('admin.degrees.update');
    Route::put('deployment_posts/{deploymentPost}',            [Admin_DeploymentPostController::class, 'update'])->name('admin.deployment_posts.update');
    Route::put('contact_types/{contact_type}',                 [Admin_ContactTypeController::class, 'update'])->name('admin.contact_types.update_put');
    Route::put('staff_titles/{staff_title}',                   [Admin_StaffTitleController::class, 'update'])->name('admin.staff_titles.update_put');
    Route::put('marital_statuses/{marital_status}',            [Admin_MaritalStatusController::class, 'update'])->name('admin.marital_statuses.update_put');
    Route::put('divisions/{division}/update',                  [Admin_DivisionController::class, 'update'])->name('admin.divisions.update_put');
    Route::put('classes_of_degrees/{class_of_degree}/update',  [Admin_ClassOfDegreeController::class, 'update'])->name('admin.classes_of_degrees.update_put2');
    Route::put('staff_categories/{staff_category}/update',     [Admin_StaffCategoryController::class, 'update'])->name('admin.staff_categories.update_put2');

    // =====================================================================
    // Staff Documents (Registry file)
    // =====================================================================
    Route::get   ('documents',                         [Admin_StaffDocumentController::class, 'index'])->name('admin.staff_documents.index');
    Route::get   ('documents/upload',                  [Admin_StaffDocumentController::class, 'create'])->name('admin.staff_documents.create');
    Route::post  ('documents',                         [Admin_StaffDocumentController::class, 'store'])->name('admin.staff_documents.store');
    Route::get   ('documents/staff/{staff}',           [Admin_StaffDocumentController::class, 'staff'])->name('admin.staff_documents.staff');
    Route::get   ('documents/{document}/view',         [Admin_StaffDocumentController::class, 'view'])->name('admin.staff_documents.view');
    Route::get   ('documents/{document}/download',     [Admin_StaffDocumentController::class, 'download'])->name('admin.staff_documents.download');
    Route::patch ('documents/{document}/approve',      [Admin_StaffDocumentController::class, 'approve'])->name('admin.staff_documents.approve');
    Route::patch ('documents/{document}/reject',       [Admin_StaffDocumentController::class, 'reject'])->name('admin.staff_documents.reject');
    Route::delete('documents/{document}',              [Admin_StaffDocumentController::class, 'destroy'])->name('admin.staff_documents.destroy');

    Route::get   ('document-setup',                    [Admin_DocumentSetupController::class, 'index'])->name('admin.document_setup.index');
    Route::post  ('document-setup/categories',         [Admin_DocumentSetupController::class, 'storeCategory'])->name('admin.document_setup.categories.store');
    Route::put   ('document-setup/categories/{category}', [Admin_DocumentSetupController::class, 'updateCategory'])->name('admin.document_setup.categories.update');
    Route::delete('document-setup/categories/{category}', [Admin_DocumentSetupController::class, 'destroyCategory'])->name('admin.document_setup.categories.destroy');
    Route::post  ('document-setup/types',              [Admin_DocumentSetupController::class, 'storeType'])->name('admin.document_setup.types.store');
    Route::put   ('document-setup/types/{type}',       [Admin_DocumentSetupController::class, 'updateType'])->name('admin.document_setup.types.update');
    Route::delete('document-setup/types/{type}',       [Admin_DocumentSetupController::class, 'destroyType'])->name('admin.document_setup.types.destroy');

    // =====================================================================
    // Senatorial Districts
    // =====================================================================

    Route::get   ('senatorial-districts',                        [Admin_SenatorialDistrictController::class, 'index'  ])->name('admin.senatorial_districts.index');
    Route::post  ('senatorial-districts',                        [Admin_SenatorialDistrictController::class, 'store'  ])->name('admin.senatorial_districts.store');
    Route::put   ('senatorial-districts/{senatorialDistrict}',   [Admin_SenatorialDistrictController::class, 'update' ])->name('admin.senatorial_districts.update');
    Route::delete('senatorial-districts/{senatorialDistrict}',   [Admin_SenatorialDistrictController::class, 'destroy'])->name('admin.senatorial_districts.destroy');

});



// =====================================================================
// Nominal roll import + login mails (Admin / Registry)
// =====================================================================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get ('nominal-roll',                  [\App\Http\Controllers\Admin\Admin_NominalRollController::class, 'index'])->name('admin.nominal_roll.index');
    Route::post('nominal-roll',                  [\App\Http\Controllers\Admin\Admin_NominalRollController::class, 'store'])->name('admin.nominal_roll.store');
    Route::get ('nominal-roll/{import}',         [\App\Http\Controllers\Admin\Admin_NominalRollController::class, 'show'])->name('admin.nominal_roll.show');
    Route::post('nominal-roll/{import}/apply',   [\App\Http\Controllers\Admin\Admin_NominalRollController::class, 'apply'])->name('admin.nominal_roll.apply');

    Route::get ('login-mails',                   [\App\Http\Controllers\Admin\Admin_LoginMailController::class, 'index'])->name('admin.login_mails.index');
    Route::post('login-mails/staff/{staff}',     [\App\Http\Controllers\Admin\Admin_LoginMailController::class, 'send'])->name('admin.login_mails.send');
    Route::post('login-mails/bulk',              [\App\Http\Controllers\Admin\Admin_LoginMailController::class, 'bulk'])->name('admin.login_mails.bulk');
});
