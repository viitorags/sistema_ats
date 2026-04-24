<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeResource;
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
        $resume = Resume::create($data);
        return new ResumeResource($resume);
    }

    public function show(Resume $resume)
    {
        return new ResumeResource($resume);
    }

    public function update(UpdateResumeRequest $request, Resume $resume)
    {
        $data = $request->validated();
        $resume->update($data);
        return new ResumeResource($resume);
    }

    public function destroy(Resume $resume)
    {
        $resume->delete();
        return response()->json(['message' => 'Currículo excluído com sucesso'], 200);
    }
}
