<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LookupDataTable;
use App\Http\Controllers\Controller;
use App\Support\LookupRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * CRUD عام لكل البيانات المرجعية + تصنيف بازل (من LookupRegistry):
 * قائمة DataTable — إضافة/تعديل/عرض — حذف لو مش مستخدم (زي الشكاوى).
 */
class LookupController extends Controller
{
    public function index(string $type)
    {
        $definition = $this->resolve($type);

        return app(LookupDataTable::class)
            ->forType($type, $definition)
            ->render('admin.lookups.index', compact('type', 'definition'));
    }

    public function create(string $type): View
    {
        $definition = $this->resolve($type);

        return view('admin.lookups.create_edit', [
            'type'       => $type,
            'definition' => $definition,
            'item'       => null,
            'options'    => $this->options($definition),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $definition = $this->resolve($type);
        $data = $request->validate($this->rules($definition), [], $this->attributes($definition));
        $data['validity'] = $request->boolean('validity', true);

        $definition['model']::create($data);

        return redirect()->route("admin.{$type}.index")->with('success', 'تمت الإضافة بنجاح.');
    }

    public function show(string $type, string $id): View
    {
        $definition = $this->resolve($type);
        $item = $this->findItem($definition, $id);

        return view('admin.lookups.show', [
            'type'       => $type,
            'definition' => $definition,
            'item'       => $item,
            'usage'      => $this->usageCounts($definition, $item->getKey()),
        ]);
    }

    public function edit(string $type, string $id): View
    {
        $definition = $this->resolve($type);

        return view('admin.lookups.create_edit', [
            'type'       => $type,
            'definition' => $definition,
            'item'       => $this->findItem($definition, $id),
            'options'    => $this->options($definition),
        ]);
    }

    public function update(Request $request, string $type, string $id): RedirectResponse
    {
        $definition = $this->resolve($type);
        $item = $this->findItem($definition, $id);

        $data = $request->validate($this->rules($definition), [], $this->attributes($definition));
        $data['validity'] = $request->boolean('validity');

        $item->update($data);

        return redirect()->route("admin.{$type}.index")->with('success', 'تم التعديل بنجاح.');
    }

    // AJAX من زر 🗑 — الحذف ممنوع لو القيمة مستخدمة في أي جدول
    public function destroy(Request $request, string $type, string $id)
    {
        $definition = $this->resolve($type);
        $item = $this->findItem($definition, $id);

        $used = collect($this->usageCounts($definition, $item->getKey()))->filter(fn ($u) => $u['count'] > 0);

        if ($used->isNotEmpty()) {
            $message = 'لا يمكن الحذف لأنه مستخدم في: '
                .$used->map(fn ($u) => $u['count'].' من '.$u['label'])->implode('، ')
                .'. يمكنك إلغاء تفعيله بدلاً من الحذف.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $item->delete();
        $message = 'تم الحذف بنجاح.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->route("admin.{$type}.index")->with('success', $message);
    }

    /** قواعد التحقق من تعريف الحقول */
    private function rules(array $definition): array
    {
        $rules = [];

        foreach ($definition['fields'] as $field) {
            $rules[$field['name']] = array_merge(
                [$field['required'] ? 'required' : 'nullable'],
                match ($field['type']) {
                    'number'   => ['integer'],
                    'select'   => ['integer', 'exists:'.(new $field['options'])->getTable().',id'],
                    'textarea' => ['string'],
                    default    => ['string', 'max:255'],
                }
            );
        }

        $rules['validity'] = ['nullable', 'boolean'];

        return $rules;
    }

    /** أسماء الحقول بالعربي في رسائل الخطأ */
    private function attributes(array $definition): array
    {
        return collect($definition['fields'])
            ->mapWithKeys(fn ($f) => [$f['name'] => $f['label']])
            ->put('validity', 'الحالة')
            ->all();
    }

    /** قوائم الـ select (المستوى الأعلى في بازل) */
    private function options(array $definition): array
    {
        $options = [];

        foreach ($definition['fields'] as $field) {
            if ($field['type'] === 'select') {
                $options[$field['name']] = $field['options']::query()->orderBy($field['display'])->get();
            }
        }

        return $options;
    }

    /** عدد الاستخدامات في كل جدول */
    private function usageCounts(array $definition, $id): array
    {
        return collect($definition['usage'] ?? [])->map(fn ($u) => [
            'label' => $u[2],
            'count' => DB::table($u[0])->where($u[1], $id)->count(),
        ])->all();
    }

    private function findItem(array $definition, string $id)
    {
        return $definition['model']::with($definition['with'] ?? [])->findOrFail($id);
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