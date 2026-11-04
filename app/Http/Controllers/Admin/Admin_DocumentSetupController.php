<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Registry maintains the document categories (e.g. "Appointment Records")
 * and the document types under each (e.g. "Letter of Confirmation").
 * Anything already in use cannot be deleted.
 */
class Admin_DocumentSetupController extends Controller
{
    public function index(): View
    {
        $categories = DocumentCategory::with(['types' => fn ($q) => $q->withCount('documents')])
            ->withCount('types')
            ->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.documents.setup', compact('categories'));
    }

    // ── Categories ──────────────────────────────────────────

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:document_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        DocumentCategory::create($data + ['sort_order' => $data['sort_order'] ?? (DocumentCategory::max('sort_order') + 1)]);

        return back()->with('success', 'Category added.');
    }

    public function updateCategory(Request $request, DocumentCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('document_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        $category->update($data + ['sort_order' => $data['sort_order'] ?? $category->sort_order]);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(DocumentCategory $category): RedirectResponse
    {
        if ($category->types()->exists()) {
            return back()->with('error', "\"{$category->name}\" still has document types. Remove or move them first.");
        }
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    // ── Types ───────────────────────────────────────────────

    public function storeType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'name'                 => ['required', 'string', 'max:255',
                Rule::unique('document_types', 'name')->where('document_category_id', $request->document_category_id)],
            'description'          => ['nullable', 'string', 'max:255'],
        ], ['name.unique' => 'This category already has a document type with that name.']);

        DocumentType::create($data + [
            'sort_order' => DocumentType::where('document_category_id', $data['document_category_id'])->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Document type added.');
    }

    public function updateType(Request $request, DocumentType $type): RedirectResponse
    {
        $data = $request->validate([
            'document_category_id' => ['required', 'exists:document_categories,id'],
            'name'                 => ['required', 'string', 'max:255',
                Rule::unique('document_types', 'name')->where('document_category_id', $request->document_category_id)->ignore($type->id)],
            'description'          => ['nullable', 'string', 'max:255'],
        ], ['name.unique' => 'This category already has a document type with that name.']);

        $type->update($data);

        return back()->with('success', 'Document type updated.');
    }

    public function destroyType(DocumentType $type): RedirectResponse
    {
        if ($type->documents()->exists()) {
            return back()->with('error', "\"{$type->name}\" is used by staff documents and cannot be deleted. You can rename it instead.");
        }
        $type->delete();

        return back()->with('success', 'Document type deleted.');
    }
}
