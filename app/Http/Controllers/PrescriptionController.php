<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::query();

        if ($request->get('display') !== 'all') {
            $query->where('next_dispense_at', '<=', Carbon::now()->addDays(7));
        }

        $query->when($request->filled('patient_search'), function ($q) use ($request) {
            $q->where(function ($subQuery) use ($request) {
                $subQuery->where('patient_last_name', 'like', '%' . $request->patient_search . '%')
                    ->orWhere('patient_first_name', 'like', '%' . $request->patient_search . '%')
                    ->orWhere('patient_ssn', 'like', '%' . $request->patient_search . '%');
            });
        });

        $query->when($request->filled('doctor_search'), function ($q) use ($request) {
            $q->where(function ($subQuery) use ($request) {
                $subQuery->where('doctor_last_name', 'like', '%' . $request->doctor_search . '%')
                    ->orWhere('doctor_first_name', 'like', '%' . $request->doctor_search . '%');
            });
        });

        $query->when($request->filled('prescribed_from'), function ($q) use ($request) {
            $q->whereDate('prescribed_at', '>=', $request->prescribed_from);
        });

        $query->when($request->filled('prescribed_to'), function ($q) use ($request) {
            $q->whereDate('prescribed_at', '<=', $request->prescribed_to);
        });

        $prescriptions = $query
            ->orderBy('next_dispense_at')
            ->paginate(20)
            ->appends($request->query());

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function store(StorePrescriptionRequest $request)
    {
        $prescription = new Prescription($request->validated());
        $prescription->user_id = auth()->id();
        $prescription->save();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance créée avec succès.');
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription)
    {
        $prescription->update($request->validated());
        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance mise à jour.');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance supprimée.');
    }

    public function prepare(Prescription $prescription)
    {
        $prescription->update(['status' => 'to_deliver']);
        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance préparée.');
    }

    public function deliver(Prescription $prescription)
    {
        $dispensedCount = $prescription->dispensed_count + 1;
        $status = $dispensedCount < $prescription->renewable_count
            ? 'to_prepare'
            : 'closed';

        $attr = [
            'status' => $status,
            'last_dispensed_at' => Carbon::now(),
            'dispensed_count' => $dispensedCount
        ];

        $prescription->update($attr);
        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance délivrée.');
    }
}
