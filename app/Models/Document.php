<?php

namespace App\Models;

use App\Http\Traits\SyncDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SyncDocuments;

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

}
