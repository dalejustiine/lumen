<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Student;
use App\Cy;
use App\Preference;

class StudentController extends BaseController
{
    public function getAllStudents() {
        $students = Student::all();

        if($students == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Students not found!");
        }

        return $this->response->array($students->toArray());
    }

    public function getStudent($student_number) {
        if(!($this->validateStudentNumber($student_number))) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Invalid student number format!");
        }

        $student = Student::with(['info', 'record'])->where('student_number', $student_number)->first();

        if($student == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Student not found!");
        }

        return $this->response->array($student->toArray());
    }

    public function validateStudentNumber($student_number) {
        $regex = '/\b([\d]{2}-[\d]+)\b/';
        if(preg_match($regex, $student_number)) {
            return true;
        }
        return false;
    }

    public function getAllCy() {
        $cy = Cy::all();

        if($cy == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Cys not found!");
        }

        return $this->response->array($cy->toArray());
    }

    public function getCy($student_number) {
        if(!($this->validateStudentNumber($student_number))) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Invalid student number format!");
        }

        $cy = DB::connection()
            ->table('sresu')
            ->join('student_records', 'sresu.id', '=', 'student_records.student_id')
            ->join('enlisted', 'student_records.id', '=', 'enlisted.student_rec_id')
            ->join('preferences', 'enlisted.pref_id', '=', 'preferences.id')
            ->join('cys', 'preferences.cy_id', '=', 'cys.id')
            ->select(['preferences.cy_id', 'cys.cy'])
            ->distinct()
            ->where('sresu.student_number', '=', $student_number)
            ->get();

        if($cy == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Cy not found!");
        }

        return $this->response->array($cy->toArray());
    }

    public function getAllPreferences() {
        $pref = Preference::all();

        if($pref == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Preferences not found!");
        }

        return $this->response->array($pref->toArray());
    }

    public function getPreference($preference_id) {
        if(!($this->validatePreferenceId($preference_id))) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Invalid preference id!");
        }

        $pref = Preference::where('cy_id', '=', $preference_id)->get();

        if($pref == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Preferences not found!");
        }

        return $this->response->array($pref->toArray());
    }

    public function validatePreferenceId($preference_id) {
        $regex = '/^\d+$/';
        if(preg_match($regex, $preference_id)) {
            return true;
        }
        return false;
    }

    public function getSchedules($student_number, $preference_id) {
        if(!($this->validateStudentNumber($student_number))) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Invalid student number format!");
        }

        $sched = DB::connection()
            ->table('sresu')
            ->join('student_records', 'sresu.id', '=', 'student_records.student_id')
            ->join('enrollments', 'student_records.id', '=', 'enrollments.student_rec_id')
            ->join('enrollment_details', 'enrollments.id', '=', 'enrollment_details.enrollment_id')
            ->join('schedules', 'enrollment_details.sched_id', '=', 'schedules.id')
            ->join('courses', 'schedules.course_id', '=', 'courses.id')
            ->select('courses.code', 'courses.title', 'schedules.section', 'schedules.time', 'schedules.day', 'schedules.room', 'schedules.bldg')
            ->where('sresu.student_number', '=', $student_number)
            ->where('enrollments.pref_id', '=', $preference_id)
            ->get();

        if($sched == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Schedules not found!");
        }

        return $this->response->array($sched->toArray());
    }

    public function getGrades($student_number, $preference_id) {
        if(!($this->validateStudentNumber($student_number))) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Invalid student number format!");
        }

        $ed = DB::connection()
            ->table('sresu')
            ->join('student_records', 'sresu.id', '=', 'student_records.student_id')
            ->join('enrollments', 'student_records.id', '=', 'enrollments.student_rec_id')
            ->join('enrollment_details', 'enrollments.id', '=', 'enrollment_details.enrollment_id')
            ->select('enrollment_details.id')
            ->where('sresu.student_number', '=', $student_number)
            ->where('enrollments.pref_id', '=', $preference_id)
            ->get();

        $id_list = collect([]);
        foreach ($ed as $key => $value) {
            $id_list->push($value->id);
        }

        $grades = DB::connection()
                ->table('gradesheets')
                ->join('gradesheet_details', 'gradesheets.id', '=', 'gradesheet_details.gradesheet_id')
                ->join('courses', 'gradesheets.course_id', '=', 'courses.id')
                ->select('courses.code', 'courses.title', 'gradesheet_details.grade')
                ->whereIn('gradesheet_details.enrollment_detail_id', $id_list->all())
                ->where('gradesheets.status', '=', '3')
                ->get();

        if($grades == null) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Grades not found!");
        }

        return $this->response->array($grades->toArray());
    }
}
