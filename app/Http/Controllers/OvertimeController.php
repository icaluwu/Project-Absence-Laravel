<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOvertimeRequest;
use App\Http\Requests\UpdateOvertimeStatusRequest;
use App\Models\OvertimeRequest as Overtime;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Admin','HR'])) {
            $items = Overtime::latest()->paginate(10);
        } else {
            $items = Overtime::where('user_id', $user->id)->latest()->paginate(10);
        }
        return view('overtime.index', compact('items'));
    }

    public function create()
    {
        return view('overtime.create');
    }

    public function store(StoreOvertimeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        Overtime::create($data);
        return redirect()->route('overtime.index')->with('status','Pengajuan lembur terkirim');
    }

    public function show(Overtime $overtime)
    {
        $this->authorize('view', $overtime);
        return view('overtime.show', compact('overtime'));
    }

    public function update(UpdateOvertimeStatusRequest $request, Overtime $overtime)
    {
        $this->authorize('update', $overtime);
        $overtime->status = $request->validated()['status'];
        $overtime->approved_by = Auth::id();
        $overtime->save();
        return back()->with('status','Status lembur diperbarui');
    }

    public function exportCsv()
    {
        $user = Auth::user();
        $filename = 'overtime_'.now()->format('Ymd_His').'.csv';
        $callback = function () use ($user) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal','Jam','Alasan','Status','Disetujui Oleh','Karyawan']);
            $query = Overtime::with(['user','approver'])->orderByDesc('date');
            if ($user->hasRole('Karyawan')) {
                $query->where('user_id',$user->id);
            }
            foreach ($query->cursor() as $it) {
                fputcsv($out, [
                    optional($it->date)->format('Y-m-d'),
                    number_format($it->hours,2),
                    $it->reason,
                    $it->status,
                    optional($it->approver)->name,
                    optional($it->user)->name,
                ]);
            }
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
