<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends Controller
{
    public function index(): void
    {
        require_auth();

        if (($_SESSION['user_role'] ?? '') === 'Docente') {
            $this->redirect('/docente/dashboard');
        }

        $this->view('dashboard/index', [
            'title' => 'Panel Principal',
            'stats' => Dashboard::stats(),
        ]);
    }

    public function inicial(): void
    {
        require_role('Administrador');

        $cursos = Dashboard::coursesByLevel('Inicial');
        $totalCursos = count($cursos);
        $totalEstudiantes = array_sum(array_column($cursos, 'total_estudiantes'));
        $totalHombres = array_sum(array_column($cursos, 'hombres'));
        $totalMujeres = array_sum(array_column($cursos, 'mujeres'));

        $this->view('dashboard/inicial', [
            'title' => 'Cursos de Nivel Inicial',
            'cursos' => $cursos,
            'totalCursos' => $totalCursos,
            'totalEstudiantes' => $totalEstudiantes,
            'totalHombres' => $totalHombres,
            'totalMujeres' => $totalMujeres,
        ]);
    }

    public function primaria(): void
    {
        require_role('Administrador');

        $cursos = Dashboard::coursesByLevel('Primaria');
        $totalCursos = count($cursos);
        $totalEstudiantes = array_sum(array_column($cursos, 'total_estudiantes'));
        $totalHombres = array_sum(array_column($cursos, 'hombres'));
        $totalMujeres = array_sum(array_column($cursos, 'mujeres'));

        $this->view('dashboard/primaria', [
            'title' => 'Cursos de Nivel Primaria',
            'cursos' => $cursos,
            'totalCursos' => $totalCursos,
            'totalEstudiantes' => $totalEstudiantes,
            'totalHombres' => $totalHombres,
            'totalMujeres' => $totalMujeres,
        ]);
    }

    public function secundaria(): void
    {
        require_role('Administrador');

        $cursos = Dashboard::coursesByLevel('Secundaria');
        $totalCursos = count($cursos);
        $totalEstudiantes = array_sum(array_column($cursos, 'total_estudiantes'));
        $totalHombres = array_sum(array_column($cursos, 'hombres'));
        $totalMujeres = array_sum(array_column($cursos, 'mujeres'));

        $this->view('dashboard/secundaria', [
            'title' => 'Cursos de Nivel Secundaria',
            'cursos' => $cursos,
            'totalCursos' => $totalCursos,
            'totalEstudiantes' => $totalEstudiantes,
            'totalHombres' => $totalHombres,
            'totalMujeres' => $totalMujeres,
        ]);
    }
}
