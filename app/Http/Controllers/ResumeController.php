<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeResource;
use App\Jobs\ProcessResumes;
use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        $resumes = Resume::where('user_id', $request->user()->id)->get();

        return ResumeResource::collection($resumes);
    }

    public function store(StoreResumeRequest $request)
    {
        $data = $request->validated();
        $user_id = $request->user()->id;

        if ($request->hasFile('file')) {
            $job = new ProcessResumes($data, $user_id, $request->user()->gemini_key);
            $resumeData = $job->handle();
        } else {
            $skills = $data['skills'] ?? '';
            $resumeData = array_merge($data, [
                'user_id' => $user_id,
                'score' => 0,
                'technical_score' => 0,
                'match_score' => 0,
                'skills' => is_string($skills) ? array_map('trim', explode(',', $skills)) : $skills,
            ]);
        }

        $resume = Resume::create($resumeData);

        return redirect()->back();
    }

    public function show(Resume $resume)
    {
        return new ResumeResource($resume);
    }

    public function update(UpdateResumeRequest $request, Resume $resume)
    {
        $data = $request->validated();
        $resume->update($data);

        return redirect()->back();
    }

    public function destroy(Resume $resume)
    {
        $resume->delete();

        return redirect()->back();
    }
}
