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
        if (!$employee->profile_picture_content) {
            return abort(404);
        }

        return Response::make($employee->profile_picture_content, 200, [
            'Content-Type' => 'image/jpeg', // Defaulting to jpeg, browser usually handles it
            'Cache-Control' => 'max-age=86400, public',
        ]);
    }

    public function showUserAvatar($id)
    {
        $user = User::findOrFail($id);
        if (!$user->profile_picture_content) {
            return abort(404);
        }

        return Response::make($user->profile_picture_content, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'max-age=86400, public',
        ]);
    }

    public function showDocument($id)
    {
        $document = EmployeeDocument::findOrFail($id);
        if (!$document->file_content) {
            return abort(404);
        }

        // Determine content type by extension if possible
        $extension = pathinfo($document->document_name, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return Response::make($document->file_content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $document->document_name . '"',
        ]);
    }

    public function showRequestFile($id)
    {
        $request = \App\Models\EmployeeRequest::findOrFail($id);
        if (!$request->requirements_file_content) {
            return abort(404);
        }

        $extension = pathinfo($request->requirements_file, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return Response::make($request->requirements_file_content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . basename($request->requirements_file) . '"',
        ]);
    }
}
