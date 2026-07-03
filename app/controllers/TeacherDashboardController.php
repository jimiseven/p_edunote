<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TeacherDashboard;

class TeacherDashboardController extends Controller
{
    public function index(): void
    {
        require_role('Docente');

        $this->view('teacher/dashboard', [
            'title' => 'Mis Cursos',
            'courses' => TeacherDashboard::assignedCourses((int) $_SESSION['user_id']),
        ]);
    }
}
