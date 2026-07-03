<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ReportBuilder;

class ReportBuilderController extends Controller
{
    public function index(): void
    {
        require_any_role(['Administrador', 'Secretaria', 'Director']);

        $this->view('reportes/index', [
            'title' => 'Lista de Reportes',
            'reportes' => ReportBuilder::all(),
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function constructor(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $editId = (int) ($_GET['editar'] ?? 0);
        $reporte = null;
        $columnas = [];
        $filtros = [];

        if ($editId > 0) {
            $reporte = ReportBuilder::find($editId);
            if (!$reporte) {
                flash('error', 'Reporte no encontrado.');
                $this->redirect('/reportes');
            }
            $columnas = array_column($reporte['columnas'], 'campo');
            $filtros = $reporte['filtros'] ?? [];
        }

        $tipoBase = $reporte['tipo_base'] ?? ($_GET['tipo'] ?? 'info_estudiantil');
        $filterFields = ReportBuilder::getFilterableFields();
        $columnAliases = ReportBuilder::getColumnAliases();

        // If POST, generate report
        $resultados = null;
        $resultadosCount = 0;
        $mensaje = flash('mensaje_reporte');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $nombre = trim($_POST['nombre_reporte'] ?? '');
            $descripcion = trim($_POST['descripcion_reporte'] ?? '');
            $tipoBase = $_POST['tipo_base'] ?? $tipoBase;
            $filtros = $_POST['filtros'] ?? [];
            $columnas = $_POST['columnas'] ?? [];
            $columnasOrden = $_POST['columnas_orden'] ?? [];

            // Sort columns by order
            if (!empty($columnas) && !empty($columnasOrden)) {
                $colWithOrder = [];
                foreach ($columnas as $c) {
                    $colWithOrder[$c] = (int) ($columnasOrden[$c] ?? 999);
                }
                asort($colWithOrder);
                $columnas = array_keys($colWithOrder);
            }

            if ($accion === 'guardar') {
                $result = ReportBuilder::save($nombre, $tipoBase, $descripcion, $filtros, $columnas, $columnasOrden, $editId);
                if ($result['success']) {
                    flash('success', 'Reporte ' . ($editId > 0 ? 'actualizado' : 'guardado') . ' exitosamente.');
                    $this->redirect('/reportes');
                } else {
                    $mensaje = '<div class="alert alert-danger">Error: ' . e($result['error']) . '</div>';
                }
            }

            // Generate results
            if ($accion === 'generar' || $accion === 'guardar') {
                $query = ReportBuilder::buildSql($filtros, $columnas, $tipoBase);
                $resultados = ReportBuilder::executeQuery($query['sql'], $query['params']);
                $resultadosCount = count($resultados);
            }
        } elseif ($editId > 0 && empty($_POST)) {
            // On edit load, generate results from saved config
            $query = ReportBuilder::buildSql($filtros, $columnas, $tipoBase);
            $resultados = ReportBuilder::executeQuery($query['sql'], $query['params']);
            $resultadosCount = count($resultados);
        }

        $this->view('reportes/constructor', [
            'title' => $editId > 0 ? 'Editar Reporte' : 'Nuevo Reporte',
            'reporte' => $reporte,
            'editId' => $editId,
            'tipoBase' => $tipoBase,
            'filterFields' => $filterFields,
            'columnAliases' => $columnAliases,
            'filtros' => $filtros,
            'columnas' => $columnas,
            'resultados' => $resultados,
            'resultadosCount' => $resultadosCount,
            'mensaje' => $mensaje,
            'niveles' => ReportBuilder::getNiveles(),
            'cursos' => ReportBuilder::getCursos(),
            'paralelos' => ReportBuilder::getParalelos(),
        ]);
    }

    public function ver(): void
    {
        require_any_role(['Administrador', 'Secretaria', 'Director']);

        $id = (int) ($_GET['id'] ?? 0);
        $reporte = ReportBuilder::find($id);

        if (!$reporte) {
            flash('error', 'Reporte no encontrado.');
            $this->redirect('/reportes');
        }

        $columnas = array_column($reporte['columnas'], 'campo');
        $query = ReportBuilder::buildSql($reporte['filtros'], $columnas, $reporte['tipo_base']);
        $resultados = ReportBuilder::executeQuery($query['sql'], $query['params']);
        $columnAliases = ReportBuilder::getColumnAliases();

        $this->view('reportes/ver', [
            'title' => 'Ver Reporte - ' . $reporte['nombre'],
            'reporte' => $reporte,
            'resultados' => $resultados,
            'columnas' => $columnas,
            'columnAliases' => $columnAliases,
        ]);
    }

    public function delete(): void
    {
        require_any_role(['Administrador', 'Secretaria']);

        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            ReportBuilder::delete($id);
            flash('success', 'Reporte eliminado correctamente.');
        }

        $this->redirect('/reportes');
    }
}
