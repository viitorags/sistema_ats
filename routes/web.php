<?php

use App\Http\Controllers\InterviewController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\VacancieController;
use App\Models\Interview;
use App\Models\Resume;
use App\Models\Vacancie;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard', [
            'stats' => [
                'activeVacancies' => Vacancie::where('active', true)->count(),
                'totalCandidates' => Resume::count(),
                'interviewsThisWeek' => Interview::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ],
            'recentApplications' => Resume::latest()->take(5)->get(),
        ]);
    })->name('dashboard');

    Route::get('vacancies', function () {
        return Inertia::render('Vacancies', [
            'vacancies' => Vacancie::latest()->get(),
        ]);
    })->name('vacancies');

    Route::get('talents', function () {
        return Inertia::render('Talents', [
            'talents' => Resume::latest()->get(),
        ]);
    })->name('talents');

    Route::get('analyse', function () {
        return Inertia::render('Analyse', [
            'recentResumes' => Resume::latest()->take(5)->get(),
        ]);
    })->name('analyse');

    Route::get('interviews', function () {
        return Inertia::render('Interviews', [
            'interviews' => Interview::latest()->get(),
        ]);
    })->name('interviews');

    // Action Routes pointing to existing controllers
    Route::post('vacancies', [VacancieController::class, 'store'])->name('vacancies_store');
    Route::put('vacancies/{vacancie}', [VacancieController::class, 'update'])->name('vacancies_update');
    Route::delete('vacancies/{vacancie}', [VacancieController::class, 'destroy'])->name('vacancies_destroy');

    Route::post('talents', [ResumeController::class, 'store'])->name('talents_store');
    Route::delete('talents/{resume}', [ResumeController::class, 'destroy'])->name('talents_destroy');

    Route::post('interviews', [InterviewController::class, 'store'])->name('interviews_store');
    Route::put('interviews/{interview}', [InterviewController::class, 'update'])->name('interviews_update');
    Route::delete('interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews_destroy');
});

require __DIR__.'/settings.php';
