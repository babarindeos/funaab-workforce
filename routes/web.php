<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;


use App\Http\Controllers\Identity\Identity_AuthController;
use App\Http\Controllers\Identity\Identity_DashboardController;
use App\Http\Controllers\Identity\Identity_StaffController;

use App\Http\Controllers\Admin\Admin_AuthController;
use App\Http\Controllers\Admin\Admin_DashboardController;
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


use App\Http\Controllers\Staff\Staff_AuthController;
use App\Http\Controllers\Staff\Staff_DashboardController;
use App\Http\Controllers\Staff\Staff_DocumentController;
use App\Http\Controllers\Staff\Staff_WorkflowController;
use App\Http\Controllers\Staff\Staff_GeneralMessageController;
use App\Http\Controllers\Staff\Staff_PrivateMessageController;

use App\Http\Controllers\Staff\Staff_ProfileController;

use App\Http\Controllers\Staff\Staff_CircleController;
use App\Http\Controllers\Staff\Staff_CircleGeneralRoomController;
use App\Http\Controllers\Staff\Staff_CircleTeamController;
use App\Http\Controllers\Staff\Staff_CircleAnnouncementController;


use App\Http\Controllers\StaffUpdateController;

use App\Http\Controllers\Staff\Staff_CategoryController;

use App\Http\Controllers\MailTestController;


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
    
    Route::get('staff_updates/completed', [StaffUpdateController::class, 'completed'])->name('guests.staff_updates.completed');

});









Route::prefix('staff')->middleware(['auth', 'staff'])->group(function(){
    Route::get('/dashboard', [Staff_DashboardController::class, 'index'])->name('staff.dashboard.index');
    Route::post('/logout', [Staff_AuthController::class, 'logout'])->name('staff.auth.logout');


    // Circle
    Route::get('/circles', [Staff_CircleController::class, 'index'])->name('staff.circles.index');
    Route::get('/circles/{circle}/general_room', [Staff_CircleGeneralRoomController::class, 'general_room'])->name('staff.circles.general_room');
    Route::post('/circles/{cell}/general_room', [Staff_CircleGeneralRoomController::class, 'store'])->name('staff.circles.general_room.store');
    
    Route::get('/circles/{circle}/team', [Staff_CircleTeamController::class, 'team'])->name('staff.circles.team');

    Route::get('/circles/{circle}/announcements', [Staff_CircleAnnouncementController::class, 'announcements']
    )->name('staff.circles.announcements');

    Route::get('/circles/{circle}/create_announcement', [Staff_CircleAnnouncementController::class, 'create_announcement']
    )->name('staff.circles.create_announcement');

    Route::post('/circles/{circle}/store_announcement', [Staff_CircleAnnouncementController::class, 'store_announcement']
    )->name('staff.circles.store_announcement');

    Route::get('/circles/{circle}/announcement/{announcement}/show_announcement', [Staff_CircleAnnouncementController::class, 'show_announcement'])->name('staff.circles.show_announcement');
    Route::post('/circles/{circle}/announcement/{announcement}/store_announcement', [Staff_CircleAnnouncementController::class, 'store_announcement_comment'])->name('staff.circles.store_announcement_comment');

    // Documents
    Route::get('/documents', [Staff_DocumentController::class, 'index'])->name('staff.document.index');
    Route::get('/documents/create', [Staff_DocumentController::class, 'create'])->name('staff.documents.create');
    Route::post('/documents/store', [Staff_DocumentController::class, 'store'])->name('staff.documents.store');
    
    Route::get('/documents/{document}/show', [Staff_DocumentController::class, 'show'])->name('staff.documents.show');
    Route::get('/documents/mydocuments', [Staff_DocumentController::class, 'mydocuments'])->name('staff.documents.mydocuments');
    
    

    // Workflow
    Route::get('/workflows/{document}/flow', [Staff_WorkflowController::class, 'flow'])->name('staff.workflows.flow');
    Route::get('/workflows/{document}/add_contributor',[Staff_WorkflowController::class, 'add_contributor'])->name('staff.workflows.add_contributor');
    Route::post('/workflows/{document}/post_add_contributor', [Staff_WorkflowController::class, 'post_add_contributor'])->name('staff.workflows.post_add_contributor');

    Route::post('/workflows/{document}/search_staff', [Staff_WorkflowController::class, 'search_staff'])->name('staff.workflows.search_staff');
    Route::post('/workflows/{document}/forward_document', [Staff_WorkflowController::class, 'forward_document'])->name('staff.workflows.forward_document');

    Route::get('/workflows/{workflow}/notification_update', [Staff_WorkflowController::class, 'notification_update'])->name('staff.workflows.notification_update');

    
    Route::get('/workflows/{document}/general_message', [Staff_GeneralMessageController::class, 'index'])->name('staff.workflows.general_message');
    Route::post('/workflows/{document}/general_message', [Staff_GeneralMessageController::class, 'store'])->name('staff.workflows.general_message.store');

    Route::get('/workflows/{document}/private_messages/{recipient}/my_private_messages', [Staff_PrivateMessageController::class, 'my_private_messages'])->name('staff.workflows.private_messages.my_private_messages');
    Route::get('/workflows/{document}/private_message/{recipient}', [Staff_PrivateMessageController::class, 'index'])->name('staff.workflows.private_message.index');
    Route::get('/workflows/{document}/private_message/{sender}/{recipient}/{chat_uuid}/chat', [Staff_PrivateMessageController::class, 'chat'])->name('staff.workflows.private_message.chat');

    Route::post('/workflows/{document}/private_message/{sender}/{recipient}/{chat_uuid}/chat', [Staff_PrivateMessageController::class, 'store'])->name('staff.workflows.private_message.store');





    // Profile
    Route::get('/profile/create', [Staff_ProfileController::class, 'create'])->name('staff.profile.create');
    Route::post('/profile/create', [Staff_ProfileController::class, 'store'])->name('staff.profile.store');
    Route::post('/profile/upload_avatar', [Staff_ProfileController::class, 'upload_avatar'])->name('staff.profile.upload_avatar');

    Route::get('/profile/myprofile', [Staff_ProfileController::class, 'myprofile'])->name('staff.profile.myprofile');
    
    Route::get('/profile/myprofile/edit', [Staff_ProfileController::class, 'edit'])->name('staff.profile.myprofile.edit');
    Route::post('/profile/myprofile/update', [Staff_ProfileController::class, 'update'])->name('staff.profile.myprofile.update');

    Route::post('/profile/myprofile/update_avatar', [Staff_ProfileController::class, 'update_avatar'])->name('staff.profile.myprofile.update_avatar');
    
    Route::get('/profile/user/{fileno}', [Staff_ProfileController::class, 'user_profile'])->name('staff.profile.user_profile');
    Route::get('/profile/user/{email}/user_profile', [Staff_ProfileController::class, 'email_user_profile'])->name('staff.profile.email_user_profile');

    Route::get('/profile/change_password', [Staff_ProfileController::class, 'change_password'])->name('staff.profile.change_password');
    Route::post('/profile/update_password', [Staff_ProfileController::class, 'update_password'])->name('staff.profile.update_password');



    // Categories
    Route::get('/categories/create', [Staff_CategoryController::class, 'create'])->name('staff.categories.create');
    Route::post('/categories/store', [Staff_CategoryController::class, 'store'])->name('staff.categories.store');


    
    
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
    Route::post('identity/{identity}/update', [Admin_IdentityController::class, 'update'])->name('admin.identity.update');


    // Divisions
    Route::get('divisions', [Admin_DivisionController::class, 'index'])->name('admin.divisions.index');
    Route::get('divisions/create', [Admin_DivisionController::class, 'create'])->name('admin.divisions.create');
    Route::post('divisions/store', [Admin_DivisionController::class, 'store'])->name('admin.divisions.store');
    Route::get('divisions/{division}/show', [Admin_DivisionController::class, 'show'])->name('admin.divisions.show');
    Route::get('divisions/{division}/edit', [Admin_DivisionController::class, 'edit'])->name('admin.divisions.edit');
    Route::post('divisions/{division}/update', [Admin_DivisionController::class, 'update'])->name('admin.divisions.update');
    Route::get('divisions/{division}/confirm_delete', [Admin_DivisionController::class, 'confirm_delete'])->name('admin.divisions.confirm_delete');
    Route::delete('divisions/{division}/delete', [Admin_DivisionController::class, 'destroy'])->name('admin.divisions.delete');
    

    // Departments
    Route::get('departments', [Admin_DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::get('departments/create', [Admin_DepartmentController::class, 'create'])->name('admin.departments.create');
    Route::post('departments/store', [Admin_DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::get('departments/{department}/show', [Admin_DepartmentController::class, 'show'])->name('admin.departments.show');
    Route::get('departments/{department}/edit', [Admin_DepartmentController::class, 'edit'])->name('admin.departments.edit');
    Route::post('departments/{department}/update', [Admin_DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::get('departments/{department}/confirm_delete', [Admin_DepartmentController::class, 'confirm_delete'])->name('admin.departments.confirm_delete');
    Route::delete('departments/{department}/delete', [Admin_DepartmentController::class, 'destroy'])->name('admin.departments.delete');


    // Contact Types
    Route::get('contact_types', [Admin_ContactTypeController::class, 'index'])->name('admin.contact_types.index');
    Route::get('contact_types/create', [Admin_ContactTypeController::class, 'create'])->name('admin.contact_types.create');
    Route::post('contact_types/store', [Admin_ContactTypeController::class, 'store'])->name('admin.contact_types.store');
    Route::get('contact_types/{contact_type}/show', [Admin_ContactTypeController::class, 'show'])->name('admin.contact_types.show');
    Route::get('contact_types/{contact_type}/edit', [Admin_ContactTypeController::class, 'edit'])->name('admin.contact_types.edit');
    Route::post('contact_types/{contact_type}/update', [Admin_ContactTypeController::class, 'update'])->name('admin.contact_types.update');
    Route::get('contact_types/{contact_type}/confirm_delete', [Admin_ContactTypeController::class, 'confirm_delete'])->name('admin.contact_types.confirm_delete');
    Route::delete('comtact_types/{contact_type}/delete', [Admin_ContactTypeController::class, 'destroy'])->name('admin.contact_type.delete');


    // Gender
    Route::get('gender', [Admin_GenderController::class, 'index'])->name('admin.gender.index');
    Route::get('gender/{gender}/show', [Admin_GenderController::class, 'show'])->name('admin.gender.show');


    // Marital Status
    Route::get('marital_statuses', [Admin_MaritalStatusController::class, 'index'])->name('admin.marital_statuses.index');
    Route::get('marital_statuses/create',[Admin_MaritalStatusController::class, 'create'])->name('admin.marital_statuses.create');
    Route::post('marital_statuses/store', [Admin_MaritalStatusController::class, 'store'])->name('admin.marital_statuses.store');
    Route::get('marital_statuses/{marital_status}/show', [Admin_MaritalStatusController::class, 'show'])->name('admin.marital_statuses.show');
    Route::get('marital_statuses/{marital_status}/edit', [Admin_MaritalStatusController::class, 'edit'])->name('admin.marital_statuses.edit');
    Route::post('marital_statuses/{marital_status}/update', [Admin_MaritalStatusController::class, 'update'])->name('admin.marital_statuses.update');
    Route::get('marital_statuses/{marital_status}/confirm_delete', [Admin_MaritalStatusController::class, 'confirm_delete'])->name('admin.marital_statuses.confirm_delete');
    Route::delete('martial_statuses/{marital_status}/delete', [Admin_MaritalStatusController::class, 'destroy'])->name('admin.marital_status.delete');



    // GeoPolZone
    Route::get('geo_pol_zones', [Admin_GeoPolZoneController::class, 'index'])->name('admin.geo_pol_zones.index');
    Route::get('geo_pol_zones/create', [Admin_GeoPolZoneController::class, 'create'])->name('admin.geo_pol_zones.create');
    Route::post('geo_pol_zones/store', [Admin_GeoPolZoneController::class, 'store'])->name('admin.geo_pol_zones.store');
    Route::get('geo_pol_zones/{geo_pol_zone}/show', [Admin_GeoPolZoneController::class, 'show'])->name('admin.geo_pol_zones.show');
    Route::get('geo_pol_zones/{geo_pol_zone}/edit', [Admin_GeoPolZoneController::class, 'edit'])->name('admin.geo_pol_zones.edit');
    Route::post('geo_pol_zones/{geo_pol_zone}/update', [Admin_GeoPolZoneController::class, 'update'])->name('admin.geo_pol_zones.update');
    Route::get('geo_pol_zones/{geo_pol_zone}/confirm_delete', [Admin_GeoPolZoneController::class, 'confirm_delete'])->name('admin.geo_pol_zones.confirm_delete');
    Route::delete('geo_pol_zones/{geo_pol_zone}/delete', [Admin_GeoPolZoneController::class, 'destroy'])->name('admin.geo_pol_zones.delete');


    // State
    Route::get('states', [Admin_StateController::class, 'index'])->name('admin.states.index');
    Route::get('states/{state}/show', [Admin_StateController::class, 'show'])->name('admin.states.show');


    // LGA
    Route::get('lgas', [Admin_LGAController::class, 'index'])->name('admin.lgas.index');
    Route::get('lgas/{lga}/show', [Admin_LGAController::class, 'show'])->name('admin.lgas.show');


    // Employment Types
    Route::get('employment_types', [Admin_EmploymentTypeController::class, 'index'])->name('admin.employment_types.index');
    Route::get('employment_types/create', [Admin_EmploymentTypeController::class, 'create'])->name('admin.employment_types.create');
    Route::post('employment_types/store', [Admin_EmploymentTypeController::class, 'store'])->name('admin.employment_types.store');
    Route::get('employment_types/{employment_type}/show', [Admin_EmploymentTypeController::class, 'show'])->name('admin.employment_types.show');
    Route::get('employment_types/{employment_type}/edit' , [Admin_EmploymentTypeController::class, 'edit'])->name('admin.employment_types.edit');
    Route::post('employment_types/{employment_type}/update', [Admin_EmploymentTypeController::class, 'update'])->name('admin.employment_types.update');
    Route::get('employment_types/{employment_type}/confirm_delete', [Admin_EmploymentTypeController::class, 'confirm_delete'])->name('admin.employment_types.confirm_delete');
    Route::delete('employment_types/{employment_type}/delete', [Admin_EmploymentTypeController::class. 'destroy'])->name('admin.employment_types.delete');


    // Staff Categories
    Route::get('staff_categories', [Admin_StaffCategoryController::class, 'index'])->name('admin.staff_categories.index');
    Route::get('staff_categories/create', [Admin_StaffCategoryController::class, 'create'])->name('admin.staff_categories.create');
    Route::post('staff_categories/store', [Admin_StaffCategoryController::class, 'store'])->name('admin.staff_categories.store');
    Route::get('staff_categories/{staff_category}/show', [Admin_StaffCategoryController::class, 'show'])->name('admin.staff_categories.show');
    Route::get('staff_categories/{staff_category}/edit' , [Admin_StaffCategoryController::class, 'edit'])->name('admin.staff_categories.edit');
    Route::post('staff_categories/{staff_category}/update', [Admin_StaffCategoryController::class, 'update'])->name('admin.staff_categories.update');
    Route::get('staff_categories/{staff_category}/confirm_delete', [Admin_StaffCategoryController::class, 'confirm_delete'])->name('admin.staff_categories.confirm_delete');
    Route::delete('staff_categories/{staff_category}/delete', [Admin_StaffCategoryController::class. 'destroy'])->name('admin.staff_categories.delete');


    // Staff Titles - Admin_StaffTitleController
    Route::get('staff_titles', [Admin_StaffTitleController::class, 'index'])->name('admin.staff_titles.index');
    Route::get('staff_titles/create', [Admin_StaffTitleController::class, 'create'])->name('admin.staff_titles.create');
    Route::post('staff_titles/store', [Admin_StaffTitleController::class, 'store'])->name('admin.staff_titles.store');
    Route::get('staff_titles/{staff_title}/show', [Admin_StaffTitleController::class, 'show'])->name('admin.staff_titles.show');
    Route::get('staff_titles/{staff_title}/edit' , [Admin_StaffTitleController::class, 'edit'])->name('admin.staff_titles.edit');
    Route::post('staff_titles/{staff_title}/update', [Admin_StaffTitleController::class, 'update'])->name('admin.staff_titles.update');
    Route::get('staff_titles/{staff_title}/confirm_delete', [Admin_StaffTitleController::class, 'confirm_delete'])->name('admin.staff_titles.confirm_delete');
    Route::delete('staff_titles/{staff_title}/delete', [Admin_StaffTitleController::class. 'destroy'])->name('admin.staff_titles.delete');


    // Staff Status - Admin_StaffStatusController
    Route::get('staff_statuses', [Admin_StaffStatusController::class, 'index'])->name('admin.staff_statuses.index');
    Route::get('staff_statuses/create', [Admin_StaffStatusController::class, 'create'])->name('admin.staff_statuses.create');
    Route::post('staff_statuses/store', [Admin_StaffStatusController::class, 'store'])->name('admin.staff_statuses.store');
    Route::get('staff_statuses/{staff_status}/show', [Admin_StaffStatusController::class, 'show'])->name('admin.staff_statuses.show');
    Route::get('staff_statuses/{staff_status}/edit' , [Admin_StaffStatusController::class, 'edit'])->name('admin.staff_statuses.edit');
    Route::post('staff_statuses/{staff_status}/update', [Admin_StaffStatusController::class, 'update'])->name('admin.staff_statuses.update');
    Route::get('staff_statuses/{staff_status}/confirm_delete', [Admin_StaffStatusController::class, 'confirm_delete'])->name('admin.staff_statuses.confirm_delete');
    Route::delete('staff_statuses/{staff_status}/delete', [Admin_StaffStatusController::class. 'destroy'])->name('admin.staff_statuses.delete');


    // Degree - Admin_DegreeController
    Route::get('degrees', [Admin_DegreeController::class, 'index'])->name('admin.degrees.index');
    Route::get('degrees/create', [Admin_DegreeController::class, 'create'])->name('admin.degrees.create');
    Route::post('degrees/store', [Admin_DegreeController::class, 'store'])->name('admin.degrees.store');
    Route::get('degrees/{degree}/show', [Admin_DegreeController::class, 'show'])->name('admin.degrees.show');
    Route::get('degrees/{degree}/edit' , [Admin_DegreeController::class, 'edit'])->name('admin.degrees.edit');
    Route::post('degrees/{degree}/update', [Admin_DegreeController::class, 'update'])->name('admin.degrees.update');
    Route::get('degrees/{degree}/confirm_delete', [Admin_DegreeController::class, 'confirm_delete'])->name('admin.degrees.confirm_delete');
    Route::delete('degrees/{degree}/delete', [Admin_DegreeController::class. 'destroy'])->name('admin.degrees.delete');


    // Class of Degree - Admin_ClassOfDegreeController
    Route::get('classes_of_degrees', [Admin_ClassOfDegreeController::class, 'index'])->name('admin.classes_of_degrees.index');
    Route::get('classes_of_degrees/create', [Admin_ClassOfDegreeController::class, 'create'])->name('admin.classes_of_degrees.create');
    Route::post('classes_of_degrees/store', [Admin_ClassOfDegreeController::class, 'store'])->name('admin.classes_of_degrees.store');
    Route::get('classes_of_degrees/{class_of_degree}/show', [Admin_ClassOfDegreeController::class, 'show'])->name('admin.classes_of_degrees.show');
    Route::get('classes_of_degrees/{class_of_degree}/edit' , [Admin_ClassOfDegreeController::class, 'edit'])->name('admin.classes_of_degrees.edit');
    Route::post('classes_of_degrees/{class_of_degree}/update', [Admin_ClassOfDegreeController::class, 'update'])->name('admin.classes_of_degrees.update');
    Route::get('classes_of_degrees/{class_of_degree}/confirm_delete', [Admin_ClassOfDegreeController::class, 'confirm_delete'])->name('admin.classes_of_degrees.confirm_delete');
    Route::delete('classes_of_degrees/{class_of_degree}/delete', [Admin_ClassOfDegreeController::class. 'destroy'])->name('admin.classes_of_degrees.delete');


    // Deployment of Post - Admin_DeploymentPostController 
    Route::get('deployment_posts', [Admin_DeploymentPostController::class, 'index'])->name('admin.deployment_posts.index');
    Route::get('deployment_posts/create', [Admin_DeploymentPostController::class, 'create'])->name('admin.deployment_posts.create');
    Route::post('deployment_posts/store', [Admin_DeploymentPostController::class, 'store'])->name('admin.deployment_posts.store');
    Route::get('deployment_posts/{deployment_post}/show', [Admin_DeploymentPostController::class, 'show'])->name('admin.deployment_posts.show');
    Route::get('deployment_posts/{deployment_post}/edit' , [Admin_DeploymentPostController::class, 'edit'])->name('admin.deployment_posts.edit');
    Route::post('deployment_posts/{deployment_post}/update', [Admin_DeploymentPostController::class, 'update'])->name('admin.deployment_posts.update');
    Route::get('deployment_posts/{deployment_post}/confirm_delete', [Admin_DeploymentPostController::class, 'confirm_delete'])->name('admin.deployment_posts.confirm_delete');
    Route::delete('deployment_posts/{deployment_post}/delete', [Admin_DeploymentPostController::class. 'destroy'])->name('admin.deployment_posts.delete');


    // Deployment - Admin_DeploymentsController 
    Route::get('deployments', [Admin_DeploymentController::class, 'index'])->name('admin.deployments.index');
    Route::get('deployments/create', [Admin_DeploymentController::class, 'create'])->name('admin.deployments.create');
    Route::post('deployments/store', [Admin_DeploymentController::class, 'store'])->name('admin.deployments.store');
    Route::get('deployments/{deployment}/show', [Admin_DeploymentController::class, 'show'])->name('admin.deployments.show');
    Route::get('deployments/{deployment}/edit' , [Admin_DeploymentController::class, 'edit'])->name('admin.deployments.edit');
    Route::post('deployments/{deployment}/update', [Admin_DeploymentController::class, 'update'])->name('admin.deployments.update');
    Route::get('deployments/{deployment}/confirm_delete', [Admin_DeploymentController::class, 'confirm_delete'])->name('admin.deployments.confirm_delete');
    Route::delete('deployments/{deployment}/delete', [Admin_DeploymentController::class. 'destroy'])->name('admin.deployments.delete');







});



/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


