<?php

// app/Http/Controllers/Management/People/PersonDocumentController.php

namespace App\Http\Controllers\Management\People;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\PersonDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonDocumentController extends Controller
{
    /**
     * Stream a private person document inline.
     */
    public function inline(Person $person, PersonDocument $document): StreamedResponse
    {
        $this->assertDocumentBelongsToPerson($person, $document);

        Gate::authorize('download', $document);

        $this->assertDocumentFileExists($document);

        return Storage::disk($document->file_disk)->response(
            $document->file_path,
            $document->original_filename ?: basename((string) $document->file_path),
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
            ],
        );
    }

    /**
     * Download a private person document.
     */
    public function download(Person $person, PersonDocument $document): StreamedResponse
    {
        $this->assertDocumentBelongsToPerson($person, $document);

        Gate::authorize('download', $document);

        $this->assertDocumentFileExists($document);

        return Storage::disk($document->file_disk)->download(
            $document->file_path,
            $document->original_filename ?: basename((string) $document->file_path),
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
            ],
        );
    }

    private function assertDocumentBelongsToPerson(Person $person, PersonDocument $document): void
    {
        abort_unless((int) $document->person_id === (int) $person->id, 404);
    }

    private function assertDocumentFileExists(PersonDocument $document): void
    {
        abort_if(blank($document->file_disk), 404);
        abort_if(blank($document->file_path), 404);

        abort_unless(
            Storage::disk($document->file_disk)->exists($document->file_path),
            404,
        );
    }
}
