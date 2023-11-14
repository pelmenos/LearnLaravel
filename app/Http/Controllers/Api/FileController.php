<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileController extends Controller
{
    private function getFilePath(string $name): string
    {
        return public_path('uploads') . '/' . $name;
    }
    private function getFileName(mixed $file): string
    {
        $count = 1;
        $name = $file->getClientOriginalName();
        $originalName = $name;
        $dir = scandir(public_path('uploads'));

        while (in_array($name, $dir)){
            $name = explode('.', $originalName)[0] . '(' . $count . ').' . $file->extension();
            $count++;
        }

        return $name;
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'required|mimes:doc,pdf,docx,zip,jpeg,jpg,png|max:2048',
        ]);

        $response = [];
        $files = $request->file('files');
        if (!is_array($files)){
            $files = array($files);
        }
        foreach($files as $key => $file)
        {
            try {
                $file_name = $this->getFileName($file);;
                $file_id = bin2hex(random_bytes(10));
                $file->move(public_path('uploads'), $file_name);
                $created_file = File::create([
                    'name' => $file_name,
                    'user_id' => Auth::id(),
                    'file_id' => $file_id
                ]);
                Permission::createAuthor(Auth::id(), $created_file->id);
                $response[] = [
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                    'name' => $file_name,
                    'file_id' => $file_id,
                    'url' => $_SERVER['HTTP_HOST'] . '/files/' . $file_id
                ];
            } catch (FileException $e) {
                $response[] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        return response()->json($response);
    }

    public function edit(Request $request, File $file) {
        $request->validate([
            'name' => 'required|unique:files',
        ]);

        rename($this->getFilePath($file->name), 'uploads/'. $request->name);
        $file->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Renamed',
            'code' => 200
        ]);
    }

    public function delete(Request $request, File $file) {
        unlink($this->getFilePath($file->name));
        $file->delete();
        return response()->json([
            'success' => true,
            'message' => 'File deleted',
            'code' => 200
        ]);
    }

    public function download(Request $request, File $file)
    {
        return response()->download($this->getFilePath($file->name));
    }
}
