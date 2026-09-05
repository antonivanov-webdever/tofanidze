<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SkillCategory;
use App\Support\Site;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    public function show(): View
    {
        return view('pages.resume', $this->data());
    }

    public function download(Site $site): Response
    {
        $pdf = Pdf::loadView('pdf.resume', $this->data())
            ->setPaper('a4')
            ->setOption(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);

        $filename = Str::slug($site->name().' resume '.now()->format('Y-m')).'.pdf';

        return $pdf->download($filename);
    }

    protected function data(): array
    {
        return [
            'experiences' => Experience::ordered()->get(),
            'skillCategories' => SkillCategory::ordered()->with('skills')->get(),
            'projects' => Project::published()->featured()->ordered()->with('technologies')->take(4)->get(),
        ];
    }
}
