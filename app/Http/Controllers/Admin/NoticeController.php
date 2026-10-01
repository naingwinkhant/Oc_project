<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoticeRequest;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        return view('admin.notices.index', [
            'notices' => Notice::query()->with('author:id,name')->latest('id')->paginate(15),
            'live' => Notice::query()->live()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.notices.create');
    }

    public function store(NoticeRequest $request): RedirectResponse
    {
        $notice = Notice::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.notices.index')
            ->with('success', $notice->title.' was published.');
    }

    public function edit(Notice $notice): View
    {
        return view('admin.notices.edit', compact('notice'));
    }

    public function update(NoticeRequest $request, Notice $notice): RedirectResponse
    {
        $notice->update($request->validated());

        return redirect()
            ->route('admin.notices.index')
            ->with('success', $notice->title.' was updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $title = $notice->title;
        $notice->delete();

        return redirect()
            ->route('admin.notices.index')
            ->with('success', $title.' was removed.');
    }
}
