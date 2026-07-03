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
}
