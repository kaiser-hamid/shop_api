<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait HasFileUpload
{
    /**
     * The disk to use for file storage
     */
    protected string $fileDisk = 'public';

    /**
     * Boot the trait
     */
    protected static function bootHasFileUpload()
    {
        static::saving(function ($model) {
            $model->handleFileUploads();
        });

        static::deleting(function ($model) {
            $model->deleteFiles();
        });
    }

    /**
     * Handle file uploads for all file columns
     */
    protected function handleFileUploads(): void
    {
        foreach ($this->fileColumns as $fileColumn) {
            if ($this->isDirty($fileColumn) && $this->getOriginal($fileColumn)) {
                $this->deleteFile($fileColumn);
            }

            if ($this->isDirty($fileColumn) && $this->$fileColumn instanceof UploadedFile) {
                $this->$fileColumn = $this->uploadFile($this->$fileColumn);
            }
        }
    }

    /**
     * Upload a file
     */
    protected function uploadFile(UploadedFile $file): string
    {
        $path = $this->generateFilePath($file);

        if ($this->hasThumbnail) {
            $thumbPath = $this->fileDirectory . '/thumb/' . $path;
            $manager = new ImageManager(new Driver());
            $thumbImage = $manager->read($file)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk($this->fileDisk)->put($thumbPath, $thumbImage->encode());
        }

        return $file->storeAs($this->fileDirectory, $path, $this->fileDisk);
    }

    /**
     * Generate a unique file path
     */
    protected function generateFilePath(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(16);
        $timestamp = now()->timestamp;
        
        return "{$timestamp}_{$filename}.{$extension}";
    }

    /**
     * Delete a specific file
     */
    protected function deleteFile(string $fileColumn): void
    {
        if ($this->getOriginal($fileColumn)) {
            Storage::disk($this->fileDisk)->delete($this->getOriginal($fileColumn));
        }
    }

    /**
     * Delete all files
     */
    protected function deleteFiles(): void
    {
        foreach ($this->fileColumns as $fileColumn) {
            $this->deleteFile($fileColumn);
        }
    }

    /**
     * Get the full URL for a specific file
     */
    public function getFileUrl(string $fileColumn): ?string
    {
        if (!$this->{$fileColumn}) {
            return null;
        }

        return Storage::disk($this->fileDisk)->url($this->{$fileColumn});
    }

    /**
     * Get the full URL for the default file
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->getFileUrl($this->fileColumns[0]);
    }
}