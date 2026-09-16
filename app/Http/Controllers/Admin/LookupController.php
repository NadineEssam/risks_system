<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\LookupRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * متحكم عام لإدارة كل البيانات المرجعية أحادية العمود (Lookups)، لتفادي
 * تكرار نفس منطق CRUD البسيط لعشرة جداول متشابهة البنية.
 */
class LookupController extends Controller
{
    public function index(string $type): View
    {
        $definition = $this->resolve($type);
        $model = $definition['model'];

        $query = $model::query();
        $query = $definition['has_sort_order'] ?? false ? $query->orderBy('sort_order') : $query->orderBy($definition['field']);

        $items = $query->get();

        return view('admin.lookups.index', [
            'type' => $type,
            'definition' => $definition,
            'items' => $items,
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $definition = $this->resolve($type);

        $rules = [$definition['field'] => ['required', 'string', 'max:255']];
        if ($definition['has_sort_order'] ?? false) {
            $rules['sort_order'] = ['nullable', 'integer'];
        }

        $data = $request->validate($rules);

        $definition['model']::create($data);

        return back()->with('success', 'تمت الإضافة بنجاح.');
    }

    public function update(Request $request, string $type, string $id): RedirectResponse
    {
        $definition = $this->resolve($type);
        $item = $definition['model']::findOrFail($id);

        $rules = [$definition['field'] => ['required', 'string', 'max:255']];
        if ($definition['has_sort_order'] ?? false) {
            $rules['sort_order'] = ['nullable', 'integer'];
        }

        $data = $request->validate($rules);
        $item->update($data);

        return back()->with('success', 'تم تحديث البيانات بنجاح.');
    }

    public function toggle(string $type, string $id): RedirectResponse
    {
        $definition = $this->resolve($type);
        $item = $definition['model']::findOrFail($id);
        $item->update(['validity' => ! $item->validity]);

        return back()->with('success', 'تم تحديث حالة التفعيل بنجاح.');
    }

    private function resolve(string $type): array
    {
        $definition = LookupRegistry::find($type);

        if (! $definition) {
            throw new NotFoundHttpException("Unknown lookup type: {$type}");
        }

        return $definition;
    }
}
