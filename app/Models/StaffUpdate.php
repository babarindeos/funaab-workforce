<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffUpdate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 
                            'fileno',
                            'title', 
                            'ippis_no', 
                            'dob', 
                            'gender', 
                            'phone', 
                            'avatar', 
                            'date_first_appointment_public_service',
                            'date_first_appointment_funaab',
                            'date_confirmation',
                            'date_present_appointment',
                            'designation',
                            'salary_structure',
                            'salary_level',
                            'salary_level_step',
                            'staff_status',
                            'geopolitical_zone',
                            'state',
                            'lga',
                            'senatorial_district',
                            'remarks',
                            'cadre',
                            'change_name',
                            'date_name_change',
                            'reason_name_change',
                            'original_names',
                            'birth_place',
                            'nationality',
                            'permanent_home_address',
                            'nok_fullnames',
                            'nok_address',
                            'nok_phone',
                            'nok_email'
                            ];
}
