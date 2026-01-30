<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Services\PrescriptionNotifier;
use App\Services\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->has('orderBy')) {
            request()->merge([
                'orderBy' => 'next_dispense_at',
                'desc' => config('const.default.direction')
            ]);
        }

        $query = Prescription::query()->with('patient');

        $query->when($request->filled('patient_search'), function ($q) use ($request) {
            $q->whereHas('patient', function ($subQuery) use ($request) {
                $search = $request->patient_search;

                $subQuery->where(function ($inner) use ($search) {
                    $inner->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('ssn', 'like', "%{$search}%");
                });
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

        $query->when($request->filled('status')
            && in_array($request->status, ['to_prepare', 'to_deliver', 'closed']), function ($q) use ($request) {
                $q->where('status', '=', $request->status);
            });

        $prescriptions = $query
            ->orderByRaw('next_dispense_at IS NULL ASC')
            ->orderBy(request('orderBy'), request('desc') ? 'desc' : 'asc')
            ->paginate(20)
            ->appends($request->query());

        return view('prescriptions.index', compact('prescriptions'));
    }

    private function getPatientData(array $data)
    {
        return [
            'last_name' => $data['patient_last_name'],
            'first_name' => $data['patient_first_name'],
            'ssn' => $data['patient_ssn'],
            'contact_method' => $data['patient_contact_method'],
            'contact_value' => $data['patient_contact_value'],
        ];
    }

    public function store(StorePrescriptionRequest $request)
    {
        $validated = $request->validated();

        $patient = new Patient($this->getPatientData($validated));
        $patient->user_id = auth()->id();
        $patient->save();

        $prescription = new Prescription($validated);
        $prescription->user_id = auth()->id();
        $prescription->patient_id = $patient->id;
        $prescription->save();

        return back()
            ->with('success', 'Ordonnance créée avec succès.');
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription)
    {
        $validated = $request->validated();

        $prescription->patient->update($this->getPatientData($validated));
        $prescription->update($validated);

        return back()
            ->with('success', 'Ordonnance mise à jour.');
    }

    public function destroy(Prescription $prescription, PrescriptionNotifier $notifier)
    {
        $prescription->delete();
        if ($prescription->hasRenewableLeft() && auth()->user()->can('notify', $prescription->patient->contact_method)) {
            $notifier->send($prescription, Subject::Deleted);
        }
        return back()
            ->with('success', 'Ordonnance supprimée.');
    }

    public function prepare(Request $request, Prescription $prescription, PrescriptionNotifier $notifier)
    {
        try {
            $prescription->update(['status' => 'to_deliver']);

            if ($request->notify === "on" && auth()->user()->can('notify', $prescription->patient->contact_method)) {
                $notifier->send($prescription, Subject::Prepared);
            }

            return back()
                ->with('success', 'Ordonnance préparée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Une erreur est survenue lors de la mise à jour du statut."]);
        }
    }

    public function deliver(Prescription $prescription)
    {
        try {
            $dispensedCount = $prescription->dispensed_count + 1;
            $status = $dispensedCount < $prescription->renewable_count
                ? 'to_prepare'
                : 'closed';

            $attr = [
                'status' => $status,
                'last_dispensed_at' => Carbon::now(),
                'dispensed_count' => $dispensedCount,
                'notes' => $prescription->notes . "\n" . "[délivrée le " . Carbon::today()->format('d/m/Y') . "]"
            ];

            $prescription->update($attr);
            return back()
                ->with('success', 'Ordonnance délivrée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Une erreur est survenue lors de la mise à jour du statut."]);
        }
    }

    public function cancel(Prescription $prescription, PrescriptionNotifier $notifier)
    {
        try {
            $dispensedCount = $prescription->dispensed_count + 1;
            $status = $dispensedCount < $prescription->renewable_count
                ? 'to_prepare'
                : 'closed';

            $attr = [
                'status' => $status,
                'dispensed_count' => $dispensedCount,
                'last_dispensed_at' => Carbon::now(),
                'notes' => $prescription->notes . "\n" . "[annulée le " . Carbon::today()->format('d/m/Y') . "]"
            ];

            $prescription->update($attr);
            if (auth()->user()->can('notify', $prescription->patient->contact_method)) {
                $notifier->send($prescription, Subject::Cancelled);
            }
            return back()
                ->with('success', 'Renouvellement annulé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Une erreur est survenue lors de la mise à jour du statut."]);
        }
    }
}
