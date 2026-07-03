<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AcademicPeriod;

class AcademicPeriodController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $gestiones = AcademicPeriod::allGestiones();
        $idGestion = (int) ($_GET['gestion'] ?? ($gestiones[0]['id_gestion'] ?? 0));

        if ($idGestion <= 0 || !AcademicPeriod::gestionExists($idGestion)) {
            $idGestion = (int) ($gestiones[0]['id_gestion'] ?? 0);
        }

        $this->view('academic_periods/index', [
            'title' => 'Control de Trimestres',
            'gestiones' => $gestiones,
            'idGestion' => $idGestion,
            'trimestres' => $idGestion > 0 ? AcademicPeriod::trimestresByGestion($idGestion) : [],
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function update(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $idGestion = (int) ($_POST['id_gestion'] ?? 0);
        if ($idGestion <= 0 || !AcademicPeriod::gestionExists($idGestion)) {
            flash('error', 'Gestion no encontrada.');
            $this->redirect('/trimestres');
        }

        $data = $this->validatedDates($_POST['trimestres'] ?? []);
        if (is_string($data)) {
            flash('error', $data);
            $this->redirect('/trimestres?gestion=' . $idGestion);
        }

        AcademicPeriod::updateDates($idGestion, $data);
        flash('success', 'Fechas de trimestres actualizadas.');
        $this->redirect('/trimestres?gestion=' . $idGestion);
    }

    public function activate(): void
    {
        require_any_role(['Administrador', 'Secretaria']);
        verify_csrf();

        $idTrimestre = (int) ($_POST['id_trimestre'] ?? 0);
        $idGestion = AcademicPeriod::trimestreGestion($idTrimestre);
        if ($idGestion === null) {
            flash('error', 'Trimestre no encontrado.');
            $this->redirect('/trimestres');
        }

        $active = (int) ($_POST['esta_activo'] ?? 0) === 1;
        AcademicPeriod::setActiveStatus($idTrimestre, $active);
        flash('success', $active ? 'Trimestre habilitado para carga.' : 'Trimestre deshabilitado para carga.');
        $this->redirect('/trimestres?gestion=' . $idGestion);
    }

    private function validatedDates(array $input): array|string
    {
        $result = [];

        foreach ($input as $idTrimestre => $row) {
            $inicio = trim($row['fecha_inicio'] ?? '');
            $fin = trim($row['fecha_fin'] ?? '');

            if (!$this->validDateOrEmpty($inicio) || !$this->validDateOrEmpty($fin)) {
                return 'Ingrese fechas validas para los trimestres.';
            }

            if ($inicio !== '' && $fin !== '' && $inicio > $fin) {
                return 'La fecha de inicio no puede ser posterior a la fecha de fin.';
            }

            $result[(int) $idTrimestre] = [
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
            ];
        }

        return $result;
    }

    private function validDateOrEmpty(string $value): bool
    {
        if ($value === '') {
            return true;
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
