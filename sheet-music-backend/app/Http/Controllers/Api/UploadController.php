<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle chunked file upload.
     */
    public function uploadChunk(Request $request)
    {
        $validator = $request->validate([
            'file' => 'required|file',
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer',
            'fileName' => 'required|string',
            'uniqueId' => 'required|string'
        ]);

        $file = $request->file('file');
        $chunkIndex = $request->chunkIndex;
        $totalChunks = $request->totalChunks;
        $fileName = $request->fileName;
        $uniqueId = $request->uniqueId;

        // Create temporary directory for chunks
        $tempDir = storage_path('app/temp/' . $uniqueId);
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Save chunk
        $chunkPath = $tempDir . '/' . $chunkIndex;
        $file->move($tempDir, $chunkIndex);

        // If all chunks are uploaded, combine them
        if ($this->allChunksUploaded($tempDir, $totalChunks)) {
            $finalPath = $this->combineChunks($tempDir, $fileName, $totalChunks);

            // Validate it's a PDF
            $mimeType = mime_content_type($finalPath);
            if ($mimeType !== 'application/pdf') {
                unlink($finalPath);
                $this->cleanupTempDir($tempDir);
                return response()->json(['error' => 'Invalid file type. Only PDF allowed.'], 422);
            }

            // Move to permanent storage
            $permanentPath = 'sheet-music/' . Str::uuid() . '_' . $fileName;
            Storage::disk('public')->put($permanentPath, file_get_contents($finalPath));

            // Cleanup
            unlink($finalPath);
            $this->cleanupTempDir($tempDir);

            return response()->json([
                'path' => $permanentPath,
                'fileName' => $fileName,
                'size' => Storage::disk('public')->size($permanentPath)
            ]);
        }

        return response()->json(['message' => 'Chunk uploaded successfully']);
    }

    /**
     * Check if all chunks are uploaded.
     */
    private function allChunksUploaded($tempDir, $totalChunks)
    {
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!file_exists($tempDir . '/' . $i)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Combine chunks into a single file.
     */
    private function combineChunks($tempDir, $fileName, $totalChunks)
    {
        $finalPath = $tempDir . '/' . $fileName;
        $finalFile = fopen($finalPath, 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $tempDir . '/' . $i;
            $chunkFile = fopen($chunkPath, 'rb');
            stream_copy_to_stream($chunkFile, $finalFile);
            fclose($chunkFile);
            unlink($chunkPath);
        }

        fclose($finalFile);
        return $finalPath;
    }

    /**
     * Cleanup temporary directory.
     */
    private function cleanupTempDir($tempDir)
    {
        if (file_exists($tempDir)) {
            array_map('unlink', glob($tempDir . '/*'));
            rmdir($tempDir);
        }
    }

    /**
     * Cancel upload and cleanup.
     */
    public function cancelUpload(Request $request)
    {
        $request->validate([
            'uniqueId' => 'required|string'
        ]);

        $tempDir = storage_path('app/temp/' . $request->uniqueId);
        $this->cleanupTempDir($tempDir);

        return response()->json(['message' => 'Upload cancelled']);
    }
}
