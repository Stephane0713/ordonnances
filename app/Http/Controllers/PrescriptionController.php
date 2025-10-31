<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::where('next_dispense_at', '<=', Carbon::now()->addDays(7))
            ->orderBy('next_dispense_at')
            ->paginate(20);

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function store(StorePrescriptionRequest $request)
    {
        $prescription = new Prescription($request->validated());
        $prescription->user_id = auth()->id();
        $prescription->save();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription créée avec succès.');
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription)
    {
        $prescription->update($request->validated());
        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription mise à jour.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription supprimée.');
    }
}
