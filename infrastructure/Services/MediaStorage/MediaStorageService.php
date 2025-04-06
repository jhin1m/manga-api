<?php

namespace Infrastructure\Services\MediaStorage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service for handling media storage
 */
class MediaStorageService
{
    /**
     * Store an uploaded file
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return array
     */
    public function store(UploadedFile $file, string $directory, ?string $filename = null): array
    {
        // Generate a unique filename if not provided
        if (!$filename) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
        }

        // Store the file
        $path = $file->storeAs($directory, $filename, 'public');
        
        // Generate thumbnail for images
        $thumbnailPath = null;
        if ($this->isImage($file)) {
            $thumbnailPath = $this->createThumbnail($file, $directory, $filename);
        }

        return [
            'filename' => $filename,
            'path' => $path,
            'url' => Storage::url($path),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'thumbnail_path' => $thumbnailPath,
            'thumbnail_url' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
        ];
    }

    /**
     * Delete a file
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        // Delete the file
        $result = Storage::disk('public')->delete($path);
        
        // Delete thumbnail if exists
        $this->deleteThumbnail($path);
        
        return $result;
    }

    /**
     * Create a thumbnail for an image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @return string|null
     */
    private function createThumbnail(UploadedFile $file, string $directory, string $filename): ?string
    {
        try {
            // Create thumbnail directory if it doesn't exist
            $thumbnailDir = $directory . '/thumbnails';
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir);
            }

            // Generate thumbnail path
            $thumbnailPath = $thumbnailDir . '/' . $filename;
            
            // Create thumbnail using Intervention Image
            $image = \Intervention\Image\Facades\Image::make($file);
            $image->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Save thumbnail
            Storage::disk('public')->put($thumbnailPath, (string) $image->encode());
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            // Log error
            \Illuminate\Support\Facades\Log::error('Failed to create thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a thumbnail
     *
     * @param string $path
     * @return bool
     */
    private function deleteThumbnail(string $path): bool
    {
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->delete($thumbnailPath);
        }
        
        return false;
    }

    /**
     * Check if a file is an image
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return Str::startsWith($mimeType, 'image/');
    }
}
