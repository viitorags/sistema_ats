<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Http\Resources\InterviewResource;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $interviews = Interview::where('user_id', $request->user()->id)->get();

        return InterviewResource::collection($interviews);
    }

    public function store(StoreInterviewRequest $request)
    {
        $data = $request->validated();
        Interview::create($data);

        return redirect()->back();
    }

    public function show(Interview $interview)
    {
        return new InterviewResource($interview);
    }

    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        $data = $request->validated();
        $interview->update($data);

        return redirect()->back();
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();

        return redirect()->back();
    }
}
