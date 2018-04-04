<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class Media
{
    private const STATUS_PENDING = 'pending';
    private const STATUS_UPLOADED = 'uploaded';

    private $id;
    private $status = self::STATUS_PENDING;
    private $type;
    private $name;
    private $info;
    private $createdAt;
    private $updatedAt;
    private $createdBy;
    private $updatedBy;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Metadata
     */
    private $metadata;

    public function __construct(UuidInterface $id, string $type, string $name, MediaInfo $info, User $user = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->info = $info;
        $this->createdAt = $this->updatedAt = new DateTimeImmutable();
        $this->createdBy = $this->updatedBy = $user;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function info(): MediaInfo
    {
        return $this->info;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function createdBy(): ?User
    {
        return $this->createdBy;
    }

    public function updatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function file(): ?File
    {
        return $this->file && $this->file->defined() ? $this->file : null;
    }

    public function metadata(): Metadata
    {
        if (is_array($this->metadata)) {
            $this->metadata = Metadata::fromArray($this->metadata);
        } elseif (is_null($this->metadata)) {
            $this->metadata = Metadata::blank();
        }

        return $this->metadata;
    }

    public function upload(File $file, Metadata $metadata = null, User $uploader = null)
    {
        $this->status = self::STATUS_UPLOADED;
        $this->file = $file;
        $this->metadata = $metadata ?? Metadata::blank();
        $this->updatedAt = new DateTimeImmutable();
        $this->updatedBy = $uploader;
    }
}
