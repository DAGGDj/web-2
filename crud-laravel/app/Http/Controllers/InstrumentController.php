<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instrument;

class InstrumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instruments = Instrument::all();
        return view('instruments.index', compact('instruments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('instruments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Instrument::create($request->all());
        return redirect()->route('instruments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $instrument = Instrument::findOrFail($id);
        return view('instruments.show', compact('instrument'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $instrument = Instrument::findOrFail($id);
        return view('instruments.edit', compact('instrument'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $instrument = Instrument::findOrFail($id);
        $instrument->update($request->all());

        return redirect()->route('instruments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $instrument = Instrument::findOrFail($id);
        $instrument->delete();
        return redirect()->route('instruments.index');
    }
}
