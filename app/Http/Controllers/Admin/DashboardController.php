<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly WorkshopService $workshopService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'workshops' => $this->workshopService->getAll(),
        ]);
    }
}
