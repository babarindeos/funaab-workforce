<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\Staff;
use App\Models\StaffDocument;
use App\Services\StaffDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Registry: upload documents into any staff member's file (official at once),
 * review staff uploads (approve / reject with reason), view, download, remove.
 */
class Admin_StaffDocumentController extends Controller
{
    public function __construct(private StaffDocumentService $files) {}

    public function index(Request $request): View
    {
        $status   = $request->query('status', 'pending');
        $search   = trim((string) $request->query('q', ''));
        $category = $request->query('category');

        $documents = StaffDocument::query()
            ->with(['staff.title', 'type.category', 'uploader'])
            ->when(in_array($status, ['approved', 'pending', 'rejected'], true), fn ($q) => $q->where('status', $status))
            ->when($category, fn ($q) => $q->whereHas('type.category', fn ($c) => $c->where('uuid', $category)))
            ->when($search !== '', fn ($q) => $q->whereHas('staff', fn ($s) => $s
                ->where('staff_no', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
                ->orWhere('firstname', 'like', "%{$search}%")))
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(25)->withQueryString();

        $counts     = StaffDocument::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $categories = DocumentCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.documents.index', compact('documents', 'counts', 'categories', 'status', 'search', 'category'));
    }

    /** All documents of one staff member (linked from the staff record). */
    public function staff(Staff $staff): View
    {
        $documents = StaffDocument::where('staff_id', $staff->id)->with(['type.category', 'uploader', 'reviewer'])
            ->orderByDesc('document_date')->latest()->get();
        $grouped = $documents->groupBy(fn ($d) => $d->type?->category?->name ?? 'Other');

        return view('admin.documents.staff', compact('staff', 'documents', 'grouped'));
    }

    public function create(Request $request): View
    {
        $categories = DocumentCategory::with('types')->orderBy('sort_order')->orderBy('name')->get();
        $staffNo    = $request->query('staff_no');

        return view('admin.documents.create', compact('categories', 'staffNo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_no'         => ['required', 'string', 'exists:staff,staff_no'],
            'document_type_id' => ['required', 'exists:document_types,id'],
            'title'            => ['nullable', 'string', 'max:255'],
            'document_date'    => ['nullable', 'date'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'file'             => StaffDocumentService::fileRules(),
        ], [
            'staff_no.exists' => 'No staff member has that Staff No.',
            'file.max'        => 'The file may not be larger than 5 MB.',
            'file.mimes'      => 'Upload a PDF, JPG or PNG file.',
        ]);

        $staff = Staff::where('staff_no', strtoupper(trim($data['staff_no'])))->firstOrFail();

        $this->files->store($request->file('file'), $staff, [
            'document_type_id' => $data['document_type_id'],
            'title'            => $data['title'] ?? null,
            'document_date'    => $data['document_date'] ?? null,
            'description'      => $data['description'] ?? null,
            'source'           => StaffDocument::SOURCE_REGISTRY,
            'status'           => StaffDocument::APPROVED,
            'uploaded_by'      => Auth::id(),
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
        ]);

        return redirect()->route('admin.staff_documents.staff', $staff)
            ->with('success', "Document added to the file of {$staff->surname}, {$staff->firstname}.");
    }

    public function view(StaffDocument $document)
    {
        return $this->files->respond($document, true);
    }

    public function download(StaffDocument $document)
    {
        return $this->files->respond($document, false);
    }

    public function approve(StaffDocument $document): RedirectResponse
    {
        if (!$document->isPending()) {
            return back()->with('error', 'Only documents awaiting approval can be approved.');
        }
        $document->update([
            'status' => StaffDocument::APPROVED, 'reviewed_by' => Auth::id(),
            'reviewed_at' => now(), 'rejection_reason' => null,
        ]);

        return back()->with('success', 'Document approved and added to the official file.');
    }

    public function reject(Request $request, StaffDocument $document): RedirectResponse
    {
        if (!$document->isPending()) {
            return back()->with('error', 'Only documents awaiting approval can be rejected.');
        }
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']],
            ['rejection_reason.required' => 'Please give the reason, so the staff member can correct it.']);

        $document->update([
            'status' => StaffDocument::REJECTED, 'reviewed_by' => Auth::id(),
            'reviewed_at' => now(), 'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', 'Document rejected. The staff member can see your reason.');
    }

    public function destroy(StaffDocument $document): RedirectResponse
    {
        $this->files->delete($document);

        return back()->with('success', 'Document removed from the staff file.');
    }
}
