<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeClass extends Model
{
    use HasFactory;
    protected $fillable = ['degree_class_name', 'order_level'];
}