<?php

declare(strict_types=1);

namespace Foodticket\FilesystemCloudflareImages\DataTransferObjects;

use Carbon\Carbon;

class ImageData
{
    private function __construct(public string $id, public string $filename, public object $meta, public Carbon $uploaded, public array $variants)
    {
        //
    }

    public static function create(object $image): self
    {
        return new self($image->id, $image->filename, $image->meta, Carbon::parse($image->uploaded), $image->variants);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function meta(): array
    {
        return $this->meta;
    }

    public function uploaded(): Carbon
    {
        return $this->uploaded;
    }

    public function variants(): array
    {
        return $this->variants;
    }
}
