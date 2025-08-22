<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $items = Announcement::latest()->paginate(10);
        return view('announcements.index', compact('items'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:150'],
            'content' => ['required','string'],
            'visible_from' => ['nullable','date'],
            'visible_to' => ['nullable','date','after_or_equal:visible_from'],
        ]);
        $data['posted_by'] = Auth::id();
        Announcement::create($data);
        return redirect()->route('announcements.index')->with('status','Pengumuman dibuat');
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => ['required','string','max:150'],
            'content' => ['required','string'],
            'visible_from' => ['nullable','date'],
            'visible_to' => ['nullable','date','after_or_equal:visible_from'],
        ]);
        $announcement->update($data);
        return back()->with('status','Pengumuman diperbarui');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('status','Pengumuman dihapus');
    }
}
