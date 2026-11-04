<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\StaffDocument;
use App\Services\AperWorkflow;
use App\Services\StaffDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * "My Documents" — a staff member's own Registry file.
 * Staff see only their own documents. Their uploads wait for Registry approval.
 */
class Staff_DocumentController extends Controller
{
    public function __construct(private AperWorkflow $wf, private StaffDocumentService $files) {}

    private function own(StaffDocument $document): StaffDocument
    {
        abort_unless($document->staff_id === $this->wf->currentStaff()->id, 403, 'You can only open your own documents.');
        return $document;
    }

    public function index(): View
    {
        $me        = $this->wf->currentStaff();
        $documents = StaffDocument::where('staff_id', $me->id)
            ->with('type.category')
            ->orderByDesc('document_date')->orderByDesc('created_at')
            ->get();

        $grouped = $documents->groupBy(fn ($d) => $d->type?->category?->name ?? 'Other');
        $counts  = $documents->countBy('status');

        return view('staff.documents.index', compact('grouped', 'counts', 'documents'));
    }

    public function create(): View
    {
        $categories = DocumentCategory::with('types')->orderBy('sort_order')->orderBy('name')->get();

        return view('staff.documents.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $me = $this->wf->currentStaff();

        $data = $request->validate([
            'document_type_id' => ['required', 'exists:document_types,id'],
            'title'            => ['nullable', 'string', 'max:255'],
            'document_date'    => ['nullable', 'date', 'before_or_equal:today'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'file'             => StaffDocumentService::fileRules(),
        ], [
            'file.max'   => 'The file may not be larger than 5 MB.',
            'file.mimes' => 'Upload a PDF, JPG or PNG file.',
        ]);

        $this->files->store($request->file('file'), $me, [
            'document_type_id' => $data['document_type_id'],
            'title'            => $data['title'] ?? null,
            'document_date'    => $data['document_date'] ?? null,
            'description'      => $data['description'] ?? null,
            'source'           => StaffDocument::SOURCE_STAFF,
            'status'           => StaffDocument::PENDING,
            'uploaded_by'      => Auth::id(),
        ]);

        return redirect()->route('staff.documents.index')
            ->with('success', 'Document uploaded. Registry will review it before it becomes part of your official file.');
    }

    public function view(StaffDocument $document)
    {
        return $this->files->respond($this->own($document), true);
    }

    public function download(StaffDocument $document)
    {
        return $this->files->respond($this->own($document), false);
    }

    /** Staff may withdraw only their own uploads that are not yet official. */
    public function destroy(StaffDocument $document): RedirectResponse
    {
        $this->own($document);

        if ($document->source !== StaffDocument::SOURCE_STAFF || $document->isApproved()) {
            return back()->with('error', 'Official documents can only be removed by Registry.');
        }

        $this->files->delete($document);

        return back()->with('success', 'Your upload has been withdrawn.');
    }
}
