<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->limit??10;
        $animals=Animal::orderBy('id','desc')->paginate($limit)->appends($request->query());
        return response($animals,Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $animal=Animal::create($request->all());
        $animal=$animal->refresh();
        return response($animal,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Animal $animal)
    {
        return response($animal,Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Animal $animal)
    {
        $animal->update($request->all());
        return response($animal,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Animal $animal)
    {
        $animal->delete();
        return response(null,204);
    }
}
