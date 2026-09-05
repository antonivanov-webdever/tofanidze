{{-- Print résumé. Dompdf renders no flexbox or grid — this layout is deliberately table- and block-based. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $site->name() }} — Résumé</title>
    <style>
        @page { margin: 14mm 14mm 16mm; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            line-height: 1.45;
            color: #1b1f27;
            margin: 0;
        }

        a { color: #1c5fd6; text-decoration: none; }

        h1 { font-size: 20pt; margin: 0 0 2pt; letter-spacing: -0.4pt; }
        h2 {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 1.1pt;
            color: #1c5fd6;
            border-bottom: 0.6pt solid #ccd3df;
            padding-bottom: 3pt;
            margin: 16pt 0 9pt;
        }
        h3 { font-size: 10.5pt; margin: 0 0 1pt; }

        p { margin: 0 0 6pt; }
        ul { margin: 5pt 0 0; padding-left: 12pt; }
        li { margin-bottom: 3pt; }

        .role-title { font-size: 10pt; color: #445066; margin: 2pt 0 6pt; }
        .contact { font-size: 8.5pt; color: #445066; }
        .contact span { padding-right: 8pt; }

        .summary {
            background: #f2f5fa;
            border-left: 2.5pt solid #2f7fff;
            padding: 8pt 10pt;
            margin-top: 10pt;
            font-size: 9pt;
            color: #303845;
        }

        .entry { margin-bottom: 12pt; page-break-inside: avoid; }
        .entry-head { width: 100%; }
        .entry-head td { vertical-align: top; padding: 0; }
        .entry-meta {
            text-align: right;
            font-size: 8.5pt;
            color: #5d6879;
            white-space: nowrap;
        }
        .company { font-size: 9.5pt; color: #445066; margin: 0 0 4pt; }

        .stack {
            font-size: 8pt;
            color: #5d6879;
            margin-top: 5pt;
        }

        .skills-table { width: 100%; border-collapse: collapse; }
        .skills-table td {
            vertical-align: top;
            padding: 0 8pt 8pt 0;
            width: 50%;
        }
        .skill-group-name { font-weight: bold; font-size: 9pt; margin-bottom: 2pt; }
        .skill-list { font-size: 8.5pt; color: #445066; }

        .project { margin-bottom: 9pt; page-break-inside: avoid; }
        .project-name { font-weight: bold; font-size: 9.5pt; }
        .project-summary { font-size: 8.5pt; color: #445066; margin: 2pt 0 0; }

        .footer {
            margin-top: 14pt;
            border-top: 0.6pt solid #ccd3df;
            padding-top: 6pt;
            font-size: 7.5pt;
            color: #7b8698;
        }
    </style>
</head>
<body>
    <h1>{{ $site->name() }}</h1>
    <p class="role-title">{{ $site->get('role') }}</p>

    <p class="contact">
        <span>{{ $site->get('email') }}</span>
        @if ($site->get('phone'))<span>{{ $site->get('phone') }}</span>@endif
        @if ($site->get('location'))<span>{{ $site->get('location') }}</span>@endif
        <span>{{ str_replace(['https://', 'http://'], '', route('home')) }}</span>
    </p>

    <div class="summary">{{ $site->get('summary') }}</div>

    <h2>Experience</h2>
    @foreach ($experiences as $experience)
        <div class="entry">
            <table class="entry-head">
                <tr>
                    <td>
                        <h3>{{ $experience->position }}</h3>
                        <p class="company">
                            {{ $experience->company }}@if ($experience->location) · {{ $experience->location }}@endif
                        </p>
                    </td>
                    <td class="entry-meta">
                        {{ $experience->period() }}<br>
                        {{ $experience->durationLabel() }}
                    </td>
                </tr>
            </table>

            @if ($experience->description)
                <p>{{ $experience->description }}</p>
            @endif

            @if ($experience->highlights)
                <ul>
                    @foreach ($experience->highlights as $highlight)
                        <li>{{ $highlight }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($experience->stack)
                <p class="stack"><strong>Stack:</strong> {{ implode(' · ', $experience->stack) }}</p>
            @endif
        </div>
    @endforeach

    <h2>Technical skills</h2>
    <table class="skills-table">
        @foreach ($skillCategories->chunk(2) as $row)
            <tr>
                @foreach ($row as $category)
                    <td>
                        <div class="skill-group-name">{{ $category->name }}</div>
                        <div class="skill-list">{{ $category->skills->pluck('name')->implode(' · ') }}</div>
                    </td>
                @endforeach
                @if ($row->count() === 1)
                    <td></td>
                @endif
            </tr>
        @endforeach
    </table>

    <h2>Selected projects</h2>
    @foreach ($projects as $project)
        <div class="project">
            <div class="project-name">
                {{ $project->title }}@if ($project->year) <span style="font-weight: normal; color: #5d6879;">— {{ $project->year }}</span>@endif
            </div>
            <p class="project-summary">{{ $project->summary }}</p>
            <p class="stack">{{ $project->technologies->pluck('name')->implode(' · ') }}</p>
        </div>
    @endforeach

    <div class="footer">
        Generated {{ now()->format('d F Y') }} · Full case studies at {{ str_replace(['https://', 'http://'], '', route('projects.index')) }}
    </div>
</body>
</html>
