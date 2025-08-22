<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Models\LeaveRequest as Leave;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Admin','HR'])) {
            $items = Leave::latest()->paginate(10);
        } else {
            $items = Leave::where('user_id', $user->id)->latest()->paginate(10);
        }
        return view('leave.index', compact('items'));
    }

    public function create()
    {
        return view('leave.create');
    }

    public function store(StoreLeaveRequest $request)
    {
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }
        $data = $request->validated();
        $data['attachment_path'] = $path;
        $data['user_id'] = Auth::id();
        Leave::create($data);
        return redirect()->route('leave.index')->with('status','Pengajuan izin/cuti/sakit terkirim');
    }

    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        return view('leave.show', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorize('update', $leave);
        $request->validate(['status' => 'required|in:approved,rejected']);
        $leave->status = $request->status;
        $leave->approved_by = Auth::id();
        $leave->save();
        return back()->with('status','Status pengajuan diperbarui');
    }

    public function exportCsv()
    {
        $user = Auth::user();
        $filename = 'leave_'.now()->format('Ymd_His').'.csv';
        $callback = function () use ($user) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Jenis','Mulai','Selesai','Status','Karyawan']);
            $query = Leave::with('user')->orderByDesc('start_date');
            if ($user->hasRole('Karyawan')) {
                $query->where('user_id',$user->id);
            }
            foreach ($query->cursor() as $it) {
                fputcsv($out, [
                    $it->type,
                    optional($it->start_date)->format('Y-m-d'),
                    optional($it->end_date)->format('Y-m-d'),
                    $it->status,
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
