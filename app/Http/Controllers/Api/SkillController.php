<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SkillCategory;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = SkillCategory::ordered()->with('skills')->get()->map(fn (SkillCategory $category) => [
            'name' => $category->name,
            'description' => $category->description,
            'skills' => $category->skills->map(fn ($skill) => [
                'name' => $skill->name,
                'level' => $skill->level,
                'years' => $skill->years,
            ]),
        ]);

        return response()->json(['data' => $categories]);
    }
}
