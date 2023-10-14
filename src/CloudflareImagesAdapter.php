<?php

declare(strict_types=1);

namespace Foodticket\FilesystemCloudflareImages;

use Foodticket\FilesystemCloudflareImages\DataTransferObjects\ImageData;
use Foodticket\FilesystemCloudflareImages\Exceptions\MethodNotAvailable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;

class CloudflareImagesAdapter implements FilesystemAdapter
{
    private const BASE_URL = 'https://api.cloudflare.com/client/v4/accounts/';

    private PendingRequest $client;

    public function __construct($config)
    {
        $this->client = $this->client(Arr::get($config, 'account_id'), Arr::get($config, 'api_token'));
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
        $this->client->asMultipart()
            ->attach('file', $contents, $path)
            ->post('images/v1');
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        throw MethodNotAvailable::throw('writeStream');
    }

    public function read(string $path): string
    {
        $response = $this->client->get("/images/v1/{$path}/blob");

        return $response->body();
    }

    public function readStream(string $path)
    {
        throw MethodNotAvailable::throw('readStream');
    }

    public function delete(string $path): void
    {
        $this->client->delete("/images/v1/{$path}");
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

        return new FileAttributes(
            path: $path,
            fileSize: $fileSize,
            lastModified: $file->uploaded->timestamp,
            mimeType: $mimeType,
            extraMetadata: (array) $file->meta,
        );
    }

    private function createTempFile(string $path): bool|string
    {
        $contents = $this->read($path);

        $temp = tempnam(sys_get_temp_dir(), 'TMP_');
        file_put_contents($temp, $contents);

        return $temp;
    }

    private function getDetails(string $path): ImageData
    {
        $response = $this->client->get("/images/v1/{$path}");

        return ImageData::create($response->object()->result);
    }

    private function client(
        string $accountId,
        string $apiToken,
    ): PendingRequest {
        return Http::baseUrl(self::BASE_URL.$accountId)
            ->withToken($apiToken);
    }
}
