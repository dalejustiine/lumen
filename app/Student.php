<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'sresu';

    public function info() {
        return $this->hasOne('App\StudentInfo', 'student_number', 'student_number');
    }

    public function record() {
        return $this->hasOne('App\StudentRecord', 'student_id');
    }
}
