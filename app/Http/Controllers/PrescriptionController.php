<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Prescription;
use App\Services\PrescriptionNotifier;
use App\Services\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::query();

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

        $query->when($request->filled('status')
            && in_array($request->status, ['to_prepare', 'to_deliver', 'closed']), function ($q) use ($request) {
                $q->where('status', '=', $request->status);
            });

        $prescriptions = $query
            ->orderByRaw('next_dispense_at IS NULL ASC')
            ->orderBy('next_dispense_at', 'asc')
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

    public function destroy(Prescription $prescription, PrescriptionNotifier $notifier)
    {
        $prescription->delete();
        if ($prescription->hasRenewableLeft() && auth()->user()->can('notify', $prescription->patient_contact_method)) {
            $notifier->send($prescription, Subject::Deleted);
        }
        return redirect()->route('prescriptions.index')
            ->with('success', 'Ordonnance supprimée.');
    }

    public function prepare(Request $request, Prescription $prescription, PrescriptionNotifier $notifier)
    {
        try {
            $prescription->update(['status' => 'to_deliver']);

            if ($request->notify === "on" && auth()->user()->can('notify', $prescription->patient_contact_method)) {
                $notifier->send($prescription, Subject::Prepared);
            }

            return redirect()->route('prescriptions.index')
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
            return redirect()->route('prescriptions.index')
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
            if (auth()->user()->can('notify', $prescription->patient_contact_method)) {
                $notifier->send($prescription, Subject::Cancelled);
            }
            return redirect()->route('prescriptions.index')
                ->with('success', 'Renouvellement annulé.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Une erreur est survenue lors de la mise à jour du statut."]);
        }
    }

    public function close(Prescription $prescription)
    {
        try {
            $attr = [
                'status' => 'closed',
                'notes' => $prescription->notes . "\n" . "[clôturée le " . Carbon::today()->format('d/m/Y') . "]"
            ];

            $prescription->update($attr);

            return redirect()->route('prescriptions.index')
                ->with('success', 'Ordonnance clôturée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Une erreur est survenue lors de la mise à jour du statut."]);
        }
    }
}
