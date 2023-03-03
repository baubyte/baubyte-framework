<?php

namespace Baubyte\Storage;

/**
 * File helper.
 */
class File {
    /**
     * Instantiate new file.
     *
     * @param string $path
     * @param mixed $content
     * @param string $type
     */
    public function __construct(
        private mixed $content,
        private string $type,
        private string $originalName,
    ) {
        $this->content = $content;
        $this->type = $type;
        $this->originalName = $originalName;
    }

    /**
     * Check if the current file is an image.
     *
     * @return boolean
     */
    public function isImage(): bool {
        return str_starts_with($this->type, "image");
    }

    /**
     * Type of the image.
     *
     * @return string|null
     */
    public function extension(): ?string {
        return match ($this->type) {
            "image/jpeg"                => ".jpeg",
            "image/jpg"                 => ".jpg",
            "image/png"                 => ".png",
            "image/gif"                 => ".gif",
            "image/tiff"                => ".tiff",
            "image/bmp"                 => ".tiff",
            "application/pdf"           => ".pdf",
            "text/vnd.ms-word"          => ".docx",
            "application/vnd.ms-excel"  => ".xlsx",
            "text/html"                 => ".html",
            "text/plain"                => ".txt",
            "audio/wav"                 => ".wav",
            "video/mpeg"                => ".mpeg",
            "video/mpg"                 => ".mpg",
            "font/ttf"                  => ".ttf",
            default => null,
        };
    }

    /**
     * Store the file.
     *
     * @return string URL.
     */
    public function store(?string $directory = null): string {
        $file = uniqid().$this->extension();
        $path = is_null($directory) ? $file : "{$directory}".DIRECTORY_SEPARATOR."{$file}";
        return Storage::put($path, $this->content);
    }
}
