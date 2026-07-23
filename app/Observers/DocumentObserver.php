<?php

namespace App\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentObserver
{
    /**
     * Handle the Document "deleting" event.
     */
    public function deleting(Document $document): void
    {
        Storage::disk('public')->delete($document->file_path);
    }
}
