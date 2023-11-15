<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditFileRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    private function getFilePath(string $name): string
    {
        return Storage::disk('local')->path('uploads/' . $name);
    }
    private function getFileName(mixed $file): string
    {
        $files = File::all()->pluck('name');
        $name = $file->getClientOriginalName();

        if (!$files->contains($name)) {
            return $name;
        }

        $fileInfo = pathinfo($name);
        $i = 1;
        while ($files->contains("$fileInfo[filename] ($i).$fileInfo[extension]")) {
            $i++;
        }
        return "$fileInfo[filename] ($i).$fileInfo[extension]";
    }

    public function store(StoreFileRequest $request): Collection
    {
        $files = $request->file('files');
        if (!is_array($files)){
            $files = array($files);
        }

        return collect($files)->map(function($file){
            $validator = Validator::make(['file' => $file],
                ['file' => ['mimes:doc,pdf,docx,zip,jpeg,jpg,png', 'max:2048']]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $file->getClientOriginalName()
                ];
            }

            $file_id = bin2hex(random_bytes(10));
            $file_name = $this->getFileName($file);

            $file->storeAs('uploads', $file_name);
            $created_file = File::create([
                'name' => $file_name,
                'user_id' => Auth::id(),
                'file_id' => $file_id
            ]);
            Permission::createAuthor(Auth::id(), $created_file->id);

            return [
                'success' => true,
                'code' => 200,
                'message' => 'Success',
            ] + (new FileResource($created_file))->jsonSerialize();
        });
    }

    public function edit(EditFileRequest $request, File $file): JsonResponse
    {
        $validated = $request->safe()->only(['name']);
        rename($this->getFilePath($file->name), $this->getFilePath($validated['name']));
        $file->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Renamed',
            'code' => 200
        ]);
    }

    public function delete(Request $request, File $file): JsonResponse
    {
        if (Storage::exists('uploads/' . $file->name)) {
            Storage::delete('uploads/' . $file->name);
        }
        $file->delete();
        return response()->json([
            'success' => true,
            'message' => 'File deleted',
            'code' => 200
        ]);
    }

    public function download(Request $request, File $file): BinaryFileResponse
    {
        return response()->download($this->getFilePath($file->name));
    }
}
