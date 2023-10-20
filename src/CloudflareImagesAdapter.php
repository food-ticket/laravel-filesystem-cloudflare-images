<?php

declare(strict_types=1);

namespace Foodticket\FilesystemCloudflareImages;

use Carbon\Carbon;
use Foodticket\Cloudflare\Facades\Cloudflare;
use Foodticket\FilesystemCloudflareImages\Exceptions\MethodNotAvailable;
use Illuminate\Support\Arr;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;

class CloudflareImagesAdapter implements FilesystemAdapter
{
    private string $accountId;

    public function __construct($config)
    {
        $this->accountId = Arr::get($config, 'account_id');
    }

    public function fileExists(string $path): bool
    {
        try {
            return ! empty($this->read($path));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        throw MethodNotAvailable::throw('directoryExists');
    }

    public function write(string $path, $contents, Config $config): void
    {
        Cloudflare::images()->uploadImage($this->accountId, $path, $contents);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        throw MethodNotAvailable::throw('writeStream');
    }

    public function read(string $path): string
    {
        return Cloudflare::images()->getBaseImage($this->accountId, $path);
    }

    public function readStream(string $path)
    {
        throw MethodNotAvailable::throw('readStream');
    }

    public function delete(string $path): void
    {
        Cloudflare::images()->deleteImage($this->accountId, $path);
    }

    public function deleteDirectory(string $path): void
    {
        throw MethodNotAvailable::throw('deleteDirectory');
    }

    public function createDirectory(string $path, Config $config): void
    {
        throw MethodNotAvailable::throw('deleteDirectory');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // TODO: Implement setVisibility() method.
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->fileAttributes($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->fileAttributes($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->fileAttributes($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->fileAttributes($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        throw MethodNotAvailable::throw('listContents');
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $contents = $this->read($source);

        $this->write($destination, $contents, $config);

        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $contents = $this->read($source);

        $this->write($destination, $contents, $config);
    }

    private function fileAttributes(string $path): FileAttributes
    {
        $temporaryFile = $this->createTempFile($path);

        $fileSize = filesize($temporaryFile);
        $mimeType = mime_content_type($temporaryFile);

        $file = $this->getDetails($path);

        $uploaded = Carbon::parse($file->uploaded);

        return new FileAttributes(
            path: $path,
            fileSize: $fileSize,
            lastModified: $uploaded->timestamp,
            mimeType: $mimeType,
            extraMetadata: ! empty($file->meta) ? (array) $file->meta : [],
        );
    }

    private function createTempFile(string $path): bool|string
    {
        $contents = $this->read($path);

        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($temp, $contents);

        return $temp;
    }

    private function getDetails(string $path): object
    {
       return Cloudflare::images()->getImageDetails($this->accountId, $path);
    }
}
