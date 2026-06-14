<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use App\Models\StaffTitle;
use App\Models\StaffCategory;
use App\Models\StaffStatus;
use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\ContactType;
use App\Models\GeoPolZone;
use App\Models\State;
use App\Models\Lga;
use App\Models\SenatorialDistrict;
use App\Models\Degree;
use App\Models\DegreeClass;
use App\Models\DivisionType;
use App\Models\Division;
use App\Models\Department;
use App\Models\DeploymentPost;

class Admin_SettingsController extends Controller
{
    public function index()
    {
        $counts = [
            'employment_types'  => EmploymentType::count(),
            'staff_titles'      => StaffTitle::count(),
            'staff_categories'  => StaffCategory::count(),
            'staff_statuses'    => StaffStatus::count(),
            'genders'           => Gender::count(),
            'marital_statuses'  => MaritalStatus::count(),
            'contact_types'     => ContactType::count(),
            'geo_pol_zones'     => GeoPolZone::count(),
            'states'            => State::count(),
            'lgas'              => Lga::count(),
            'senatorial_districts' => SenatorialDistrict::count(),
            'degrees'           => Degree::count(),
            'degree_classes'    => DegreeClass::count(),
            'division_types'    => DivisionType::count(),
            'divisions'         => Division::count(),
            'departments'       => Department::count(),
            'deployment_posts'  => DeploymentPost::count(),
        ];

        return view('admin.settings.index', compact('counts'));
    }
}