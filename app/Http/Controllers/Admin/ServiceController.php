<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\ListInput;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', ['service' => new Service(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $service = Service::create($this->validated($request));

        return redirect()
            ->route('admin.services.index')
            ->with('status', "Service “{$service->title}” created.");
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->validated($request));

        return redirect()
            ->route('admin.services.index')
            ->with('status', "Service “{$service->title}” updated.");
    }

    public function destroy(Service $service): RedirectResponse
    {
        $title = $service->title;
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('status', "Service “{$title}” deleted.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'subtitle' => ['nullable', 'string', 'max:190'],
            'description' => ['required', 'string'],
            'icon' => ['required', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'bullets_text' => ['nullable', 'string'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] ??= 0;
        $data['bullets'] = ListInput::lines($request->input('bullets_text'));

        unset($data['bullets_text']);

        return $data;
    }
}
