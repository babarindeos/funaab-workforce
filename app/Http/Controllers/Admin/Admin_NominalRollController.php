<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NominalRollImport;
use App\Services\NominalRoll\NominalRollImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

/**
 * Registry uploads the nominal roll workbook, sees exactly what would change,
 * then applies it. Importing never sends e-mail (see Login Mails for that).
 */
class Admin_NominalRollController extends Controller
{
    public function index()
    {
        $imports = NominalRollImport::with(['uploader', 'applier'])->latest()->limit(20)->get();
        return view('admin.nominal_roll.index', compact('imports'));
    }

    public function store(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx', 'max:20480']],
            ['file.mimes' => 'Upload the nominal roll as an Excel .xlsx workbook (in Excel: File → Save As → Excel Workbook).']);

        $uuid = (string) Str::uuid();
        $path = $request->file('file')->storeAs('imports/nominal_roll', "{$uuid}.xlsx", 'local');

        try {
            $summary = (new NominalRollImporter(storage_path("app/{$path}")))->preview();
        } catch (Throwable $e) {
            @unlink(storage_path("app/{$path}"));
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        $import = NominalRollImport::create([
            'uuid' => $uuid, 'file_name' => $request->file('file')->getClientOriginalName(), 'stored_path' => $path,
            'uploaded_by' => auth()->id(), 'status' => 'previewed', 'summary' => $summary,
        ]);
        return redirect()->route('admin.nominal_roll.show', $import);
    }

    public function show(NominalRollImport $import)
    {
        return view('admin.nominal_roll.show', ['import' => $import, 's' => $import->summary, 'labels' => NominalRollImporter::FIELD_LABELS]);
    }

    public function apply(Request $request, NominalRollImport $import)
    {
        if ($import->status === 'applied') {
            return back()->with('error', 'This upload has already been applied.');
        }
        $file = storage_path("app/{$import->stored_path}");
        if (!is_file($file)) {
            return back()->with('error', 'The uploaded file is no longer on the server. Please upload it again.');
        }

        try {
            $result = (new NominalRollImporter($file))->apply(auth()->id(), $request->boolean('update_existing'));
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Nothing was saved — the import stopped with: ' . $e->getMessage());
        }

        $import->update(['status' => 'applied', 'applied_at' => now(), 'applied_by' => auth()->id(),
            'summary' => $result + ['update_existing' => $request->boolean('update_existing')]]);

        $c = $result['counts'];
        return redirect()->route('admin.nominal_roll.show', $import)->with('applied',
            "Nominal roll applied: {$c['new']} staff added" . ($request->boolean('update_existing') ? ", {$c['update']} updated" : '')
            . ", {$c['disengaged']} disengagement(s) recorded. No login mails have been sent.");
    }
}
