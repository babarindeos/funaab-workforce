<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Stores staff documents on the PRIVATE "local" disk (storage/app), so a
 * file can only be reached through the app's own permission checks — never
 * by a direct link.
 */
class StaffDocumentService
{
    public const DISK      = 'local';
    public const MAX_KB    = 5120;                 // 5 MB
    public const MIMES     = 'pdf,jpg,jpeg,png';

    public static function fileRules(): array
    {
        return ['required', 'file', 'mimes:' . self::MIMES, 'max:' . self::MAX_KB];
    }

    public function store(UploadedFile $file, Staff $staff, array $attributes): StaffDocument
    {
        $ext  = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $name = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
        $path = $file->storeAs("staff_documents/{$staff->uuid}", $name, self::DISK);

        return StaffDocument::create($attributes + [
            'staff_id'      => $staff->id,
            'file_path'     => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'mime_type'     => $file->getMimeType() ?? 'application/octet-stream',
            'file_size'     => $file->getSize(),
        ]);
    }

    /** inline = open in the browser (PDF/images); otherwise download. */
    public function respond(StaffDocument $doc, bool $inline): StreamedResponse
    {
        abort_unless(Storage::disk(self::DISK)->exists($doc->file_path), 404, 'The file could not be found.');

        $ext      = pathinfo($doc->file_path, PATHINFO_EXTENSION);
        $niceName = Str::slug(($doc->staff?->staff_no ?? 'staff') . ' ' . $doc->display_title) . '.' . $ext;

        return Storage::disk(self::DISK)->response($doc->file_path, $niceName, [
            'Content-Type'           => $doc->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-store',
        ], $inline ? 'inline' : 'attachment');
    }

    public function delete(StaffDocument $doc): void
    {
        Storage::disk(self::DISK)->delete($doc->file_path);
        $doc->delete();
    }
}
