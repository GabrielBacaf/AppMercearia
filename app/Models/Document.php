<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'file_path',
        'mime_type'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($document) {
            Storage::disk('public')->delete($document->file_path);
        });
    }

    public function syncDocuments(Model $model, array $data): void
    {
        $incomingIds = array_filter(Arr::pluck($data, 'id'));

        if (count($incomingIds) > 0) {
            $documentsToDelete = $model->documents()->whereNotIn('id', $incomingIds)->get();
        } else {

            $documentsToDelete = $model->documents()->get();
        }

        foreach ($documentsToDelete as $document) {
            $document->delete();
        }

        foreach ($data as $docData) {

            $hasFile = !empty($docData['file']) && $docData['file'] instanceof UploadedFile;
            $hasId = !empty($docData['id']);

            if ($hasFile) {

                $file = $docData['file'];

                $filePath = $file->store('documents', 'public');
                $label = $docData['label'] ?? $file->getClientOriginalName();

                $payload = [
                    'label'     => $label,
                    'file_path' => $filePath,
                    'mime_type' => $file->getMimeType(),
                ];

                if ($hasId) {

                    $document = $model->documents()->find($docData['id']);
                    if ($document) {

                        Storage::disk('public')->delete($document->file_path);

                        $document->update($payload);
                    }
                } else {

                    $model->documents()->create($payload);
                }
            } elseif ($hasId) {

                $document = $model->documents()->find($docData['id']);
                if ($document) {

                    $document->update(Arr::only($docData, ['label']));
                }
            }
        }
    }
}
