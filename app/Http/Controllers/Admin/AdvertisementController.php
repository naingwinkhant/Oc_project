<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdvertisementRequest;
use App\Models\Advertisement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The advertising a manager writes for the top of the catalogue.
 *
 * Kept apart from notices, which are a line in the bell: these are the slides a
 * shopper sees before they see any goods.
 */
class AdvertisementController extends Controller
{
    public function index(): View
    {
        return view('admin.advertisements.index', [
            'advertisements' => Advertisement::query()
                ->with('author:id,name')
                ->orderBy('position')
                ->orderByDesc('id')
                ->paginate(15),
            'live' => Advertisement::query()->live()->count(),
            'total' => Advertisement::query()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.advertisements.create', [
            'advertisement' => new Advertisement(['is_active' => true, 'position' => 0]),
        ]);
    }

    public function store(AdvertisementRequest $request): RedirectResponse
    {
        $advertisement = Advertisement::create([
            ...$this->uploaded($request),
            ...$request->safe()->only([
                'title', 'eyebrow', 'body', 'link', 'button_label',
                'position', 'starts_on', 'ends_on', 'is_active',
            ]),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', $advertisement->title.' was added to the carousel.');
    }

    public function edit(Advertisement $advertisement): View
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(AdvertisementRequest $request, Advertisement $advertisement): RedirectResponse
    {
        $advertisement->update([
            ...$this->uploaded($request),
            ...$request->safe()->only([
                'title', 'eyebrow', 'body', 'link', 'button_label',
                'position', 'starts_on', 'ends_on', 'is_active',
            ]),
        ]);

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', $advertisement->title.' was updated.');
    }

    public function destroy(Advertisement $advertisement): RedirectResponse
    {
        $title = $advertisement->title;

        // The files go with the row: leaving them behind would fill the disk
        // with pictures nothing points at any more.
        $this->deleteFiles($advertisement);

        $advertisement->delete();

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', $title.' was removed.');
    }

    /**
     * Move through the carousel by swapping positions with the neighbour.
     */
    public function move(Request $request, Advertisement $advertisement): RedirectResponse
    {
        $data = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $neighbour = Advertisement::query()
            ->where('position', $data['direction'] === 'up' ? '<' : '>', $advertisement->position)
            ->orderBy('position', $data['direction'] === 'up' ? 'desc' : 'asc')
            ->orderByDesc('id')
            ->first();

        if ($neighbour) {
            // Two writes, swapped. They cannot be done in one statement without
            // either a temporary value or a case expression, and neither is
            // worth it for a list a manager reorders a few times a week.
            $mine = $advertisement->position;

            $advertisement->update(['position' => $neighbour->position]);
            $neighbour->update(['position' => $mine]);
        }

        return back()->with('success', $advertisement->title.' moved.');
    }

    /**
     * Save whatever was uploaded, and remove the files a replacement has
     * superseded.
     *
     * @return array<string, string|null>
     */
    private function uploaded(AdvertisementRequest $request): array
    {
        $fields = [];

        foreach (['image', 'poster'] as $field) {
            if ($request->hasFile($field)) {
                $fields[$field] = $request->file($field)->store('advertisements', 'public');
            }
        }

        // The video is big enough that the usual limit applies, and big enough
        // that it is worth knowing rather than failing quietly.
        if ($request->hasFile('video')) {
            $fields['video'] = $request->file('video')->store('advertisements', 'public');
        }

        $advertisement = $request->route('advertisement');

        if ($advertisement instanceof Advertisement) {
            foreach ($fields as $field => $path) {
                $old = $advertisement->{$field};

                if ($old && $old !== $path) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        return $fields;
    }

    private function deleteFiles(Advertisement $advertisement): void
    {
        foreach (['image', 'video', 'poster'] as $field) {
            if ($advertisement->{$field}) {
                Storage::disk('public')->delete($advertisement->{$field});
            }
        }
    }
}
