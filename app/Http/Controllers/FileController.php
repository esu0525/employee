<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function showEmployeeAvatar($id)
    {
        $employee = Employee::findOrFail($id);
        if (!$employee->profile_picture) {
            return abort(404);
        }

        $path = public_path($employee->profile_picture);
        if (!file_exists($path)) {
            return abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    public function showUserAvatar($id)
    {
        $user = User::findOrFail($id);
        if (!$user->profile_picture) {
            return abort(404);
        }

        $path = public_path($user->profile_picture);
        if (!file_exists($path)) {
            return abort(404);
        }

        return response()->file($path, [
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    public function showDocument($id)
    {
        $document = EmployeeDocument::findOrFail($id);
        if (!$document->file_path) {
            return abort(404);
        }

        $path = public_path($document->file_path);
        if (!file_exists($path)) {
            return abort(404);
        }

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $document->document_name . '"',
        ]);
    }

    public function showRequestFile($id)
    {
        $request = \App\Models\EmployeeRequest::findOrFail($id);
        if (!$request->requirements_file) {
            return abort(404);
        }

        $path = public_path($request->requirements_file);
        if (!file_exists($path)) {
            return abort(404);
        }

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . basename($request->requirements_file) . '"',
        ]);
    }
}
