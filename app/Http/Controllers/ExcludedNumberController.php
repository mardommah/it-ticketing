<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExcludedNumber;

class ExcludedNumberController extends Controller
{
    public function index()
    {
        $excludedNumbers = ExcludedNumber::latest()->get();
        return view('excluded_numbers.index', compact('excludedNumbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|unique:excluded_numbers,number',
            'note' => 'nullable|string|max:255',
        ]);

        ExcludedNumber::create($request->all());

        return back()->with('success', 'Number/Group excluded successfully.');
    }

    public function destroy(ExcludedNumber $excludedNumber)
    {
        $excludedNumber->delete();

        return redirect()->route('excluded-numbers.index')->with('success', 'Number removed from exclusion list.');
    }
}
