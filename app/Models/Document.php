<?php

namespace App\Models;

use App\Http\Traits\SyncDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\DocumentObserver;

#[ObservedBy([DocumentObserver::class])]
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



}
