<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CatalogFramework;
use App\Models\CatalogFrameworkField;
use App\Models\MarcField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogFrameworkAdminController extends Controller
{
    protected function allowedBookColumns(): array
    {
        $exclude = ['created_at', 'updated_at', 'availability', 'cover_image'];

        return array_values(array_diff((new Book)->getFillable(), $exclude));
    }

    public function index()
    {
        $frameworks = CatalogFramework::orderBy('name')->get();

        return view('admin.catalog_frameworks.index', compact('frameworks'));
    }

    public function edit(CatalogFramework $catalog_framework)
    {
        $catalog_framework->load([
            'fields' => static function ($q) {
                $q->orderBy('sort_order')->with('marcField');
            },
        ]);

        $attachedIds = $catalog_framework->fields->pluck('marc_field_id')->filter()->all();

        $availableMarcFields = MarcField::query()
            ->when($attachedIds !== [], fn ($q) => $q->whereNotIn('id', $attachedIds))
            ->orderBy('tag')
            ->orderBy('subfield')
            ->get();

        $bookColumns = $this->allowedBookColumns();

        return view('admin.catalog_frameworks.edit', compact('catalog_framework', 'availableMarcFields', 'bookColumns'));
    }

    public function updateFields(Request $request, CatalogFramework $catalog_framework)
    {
        $allowed = $this->allowedBookColumns();

        $request->validate([
            'fields' => ['required', 'array'],
            'fields.*.visible' => ['nullable', 'in:0,1'],
            'fields.*.required' => ['nullable', 'in:0,1'],
            'fields.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'fields.*.default_value' => ['nullable', 'string', 'max:255'],
            'fields.*.book_column' => ['nullable', 'string', 'max:64', Rule::in($allowed)],
        ]);

        foreach ($request->input('fields', []) as $id => $row) {
            $field = CatalogFrameworkField::query()
                ->where('framework_id', $catalog_framework->id)
                ->where('id', (int) $id)
                ->first();

            if (! $field) {
                continue;
            }

            $field->update([
                'visible' => ($row['visible'] ?? '0') === '1',
                'required' => ($row['required'] ?? '0') === '1',
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'default_value' => $row['default_value'] ?? null,
                'book_column' => $row['book_column'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.catalog_frameworks.edit', $catalog_framework)
            ->with('success', 'Framework fields updated.');
    }

    public function attachField(Request $request, CatalogFramework $catalog_framework)
    {
        $data = $request->validate([
            'marc_field_id' => ['required', 'integer', 'exists:marc_fields,id'],
        ]);

        if ($catalog_framework->fields()->where('marc_field_id', $data['marc_field_id'])->exists()) {
            return back()->withErrors(['marc_field_id' => 'That tag is already in this framework.']);
        }

        $next = (int) $catalog_framework->fields()->max('sort_order') + 1;

        CatalogFrameworkField::create([
            'framework_id' => $catalog_framework->id,
            'marc_field_id' => $data['marc_field_id'],
            'visible' => true,
            'required' => false,
            'sort_order' => $next,
            'book_column' => null,
            'default_value' => null,
        ]);

        return back()->with('success', 'Field added to framework.');
    }

    public function storeMarcField(Request $request, CatalogFramework $catalog_framework)
    {
        $data = $request->validate([
            'tag' => ['required', 'string', 'size:3', 'regex:/^[0-9]{3}$/'],
            'subfield' => ['nullable', 'string', 'size:1'],
            'label' => ['nullable', 'string', 'max:255'],
            'input_type' => ['required', Rule::in(['text', 'textarea', 'select', 'date'])],
            'repeatable' => ['nullable', 'in:0,1'],
            'options_lines' => ['nullable', 'string', 'max:5000'],
        ]);

        $options = null;
        if (($data['input_type'] ?? '') === 'select' && ! empty($data['options_lines'])) {
            $options = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['options_lines']))));
            if ($options === []) {
                $options = null;
            }
        }

        $subfield = isset($data['subfield']) && $data['subfield'] !== '' ? $data['subfield'] : null;

        try {
            $marc = MarcField::create([
                'tag' => $data['tag'],
                'subfield' => $subfield,
                'label' => $data['label'] ?? null,
                'repeatable' => ($data['repeatable'] ?? '0') === '1',
                'input_type' => $data['input_type'],
                'options' => $options,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['tag' => 'Could not create tag (duplicate tag + subfield?).'])->withInput();
        }

        $next = (int) $catalog_framework->fields()->max('sort_order') + 1;

        CatalogFrameworkField::create([
            'framework_id' => $catalog_framework->id,
            'marc_field_id' => $marc->id,
            'visible' => true,
            'required' => false,
            'sort_order' => $next,
            'book_column' => null,
            'default_value' => null,
        ]);

        return back()->with('success', 'New MARC tag created and added to this framework.');
    }

    public function detachField(CatalogFramework $catalog_framework, CatalogFrameworkField $field)
    {
        if ($field->framework_id !== $catalog_framework->id) {
            abort(404);
        }

        $field->delete();

        return back()->with('success', 'Field removed from framework (MARC definition is kept for reuse).');
    }
}
