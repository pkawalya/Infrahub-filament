<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Download a document version.
     */
    public function download(DocumentVersion $version): StreamedResponse
    {
        // Verify the user has access to the project
        $document = $version->document;
        $this->authorizeAccess($document);

        // Log the download
        $document->logHistory('downloaded', 'Document downloaded via direct link', null, $version->id);

        // Stream the file download
        return response()->streamDownload(
            function () use ($version) {
                echo Storage::disk('local')->get($version->file_path);
            },
            $version->original_filename,
            [
                'Content-Type' => $version->mime_type,
                'Content-Length' => $version->file_size,
            ]
        );
    }

    /**
     * Preview a document version (for PDFs and images).
     */
    public function preview(DocumentVersion $version)
    {
        // Verify the user has access to the project
        $document = $version->document;
        $this->authorizeAccess($document);

        if (!$version->canPreview()) {
            abort(400, 'This file type cannot be previewed.');
        }

        // Log the view
        $document->logHistory('viewed', 'Document previewed', null, $version->id);

        $mimeType = $version->mime_type;
        $content = Storage::disk('local')->get($version->file_path);

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $version->original_filename . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Authorize access to the document.
     */
    protected function authorizeAccess(ProjectDocument $document): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Super admins can access all documents
        if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
            return;
        }

        // Check if user is a member of the project
        $isMember = $document->project->members()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403, 'You do not have access to this document.');
        }
    }
}
