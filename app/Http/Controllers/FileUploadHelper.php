<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadHelper
{
    public static function handleFolderUpload($files, $folderName)
    {
        $folderContents = [];
        $zipFileName = time() . '_' . $folderName . '.zip';
        $zipPath = storage_path('app/public/chat-files/' . $zipFileName);
        
        // Ensure directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $relativePath = $file->getClientOriginalName();
                    $zip->addFile($file->getPathname(), $relativePath);
                    
                    $folderContents[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'type' => $file->getClientMimeType()
                    ];
                }
            }
            $zip->close();
        }
        
        return [
            'zip_path' => 'chat-files/' . $zipFileName,
            'folder_contents' => $folderContents,
            'original_name' => $folderName
        ];
    }
    
    public static function handleZipUpload(UploadedFile $zipFile)
    {
        $fileName = time() . '_' . $zipFile->getClientOriginalName();
        $filePath = $zipFile->storeAs('chat-files', $fileName, 'public');
        
        // Extract ZIP contents info
        $zip = new ZipArchive();
        $contents = [];
        
        if ($zip->open($zipFile->getPathname()) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $contents[] = [
                    'name' => $stat['name'],
                    'size' => $stat['size'],
                    'compressed_size' => $stat['comp_size']
                ];
            }
            $zip->close();
        }
        
        return [
            'file_path' => $filePath,
            'folder_contents' => $contents,
            'original_name' => pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME)
        ];
    }
}