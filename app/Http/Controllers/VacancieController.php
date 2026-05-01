<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVacancieRequest;
use App\Http\Requests\UpdateVacancieRequest;
use App\Http\Resources\VacancieResource;
use App\Models\Vacancie;
use Illuminate\Http\Request;

class VacancieController extends Controller
{
    public function index(Request $request)
    {
        $vacancies = Vacancie::where('user_id', $request->user()->id)->get();

        return VacancieResource::collection($vacancies);
    }

    public function store(StoreVacancieRequest $request)
    {
        $data = $request->validated();
        Vacancie::create($data);

        return redirect()->back();
    }

    public function show(Vacancie $vacancie)
    {
        return new VacancieResource($vacancie);
    }

    public function update(UpdateVacancieRequest $request, Vacancie $vacancie)
    {
        $data = $request->validated();
        $vacancie->update($data);

        return redirect()->back();
    }

    public function destroy(Vacancie $vacancie)
    {
        $vacancie->delete();

        return redirect()->back();
    }
}
