<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;

class EnumController extends Controller
{
    public function index(): JsonResponse
    {
        $enumsDir = app_path("Enums");
        if (!File::exists($enumsDir)) {
            return response()->json([]);
        }
        
        $files = File::allFiles($enumsDir);
        $result = [];

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $className = "\\App\\Enums\\" . $filename;

            if (enum_exists($className)) {
                $cases = $className::cases();
                
                $baseName = str_replace("Enum", "", $filename);
                $key = Str::plural(Str::snake($baseName));
                
                $result[$key] = array_map(function ($case) {
                    return [
                        "name" => $case->name,
                        "value" => $case->value
                    ];
                }, $cases);
            }
        }

        return response()->json($result);
    }
}

