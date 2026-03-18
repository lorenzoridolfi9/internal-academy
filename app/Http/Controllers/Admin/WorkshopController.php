<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class WorkshopController extends Controller
{
    public function __construct(
        private readonly WorkshopService $workshopService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Workshops/Index', [
            'workshops' => $this->workshopService->getAll(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Workshops/Create');
    }

    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        $this->workshopService->create(auth()->user(), $request->validated());

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop creato con successo.');
    }

    public function show(Workshop $workshop): Response
    {
        return Inertia::render('Admin/Workshops/Show', [
            'workshop' => $workshop->load('confirmedRegistrations.user', 'waitlistedRegistrations.user'),
        ]);
    }

    public function edit(Workshop $workshop): Response
    {
        return Inertia::render('Admin/Workshops/Edit', [
            'workshop' => $workshop,
        ]);
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        $this->workshopService->update($workshop, $request->validated());

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop aggiornato con successo.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->workshopService->delete($workshop);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop eliminato con successo.');
    }
}
