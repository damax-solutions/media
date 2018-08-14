<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Exception\InvalidFile;
use DateTimeImmutable;
use DateTimeInterface;

class Media
{
    private const STATUS_PENDING = 'pending';
    private const STATUS_UPLOADED = 'uploaded';

    private $id;
    private $status = self::STATUS_PENDING;
    private $type;
    private $name;
    private $mimeType;
    private $fileSize;
    private $fileKey;
    private $storage;
    private $metadata = [];
    private $createdAt;
    private $updatedAt;
    private $createdById;
    private $updatedById;

    public function __construct(MediaId $id, string $type, string $name, FileInfo $info, UserId $userId = null)
    {
        $this->id = (string) $id;
        $this->type = $type;
        $this->name = $name;
        $this->mimeType = $info->mimeType();
        $this->fileSize = $info->fileSize();
        $this->createdAt = $this->updatedAt = new DateTimeImmutable();
        $this->createdById = $this->updatedById = $userId ? (string) $userId : null;
    }

    public function id(): MediaId
    {
        return MediaId::fromString((string) $this->id);
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

    public function info(): FileInfo
    {
        return new FileInfo($this->mimeType, $this->fileSize);
    }

    public function matchesInfo(FileInfo $info): bool
    {
        return $this->info()->sameAs($info);
    }

    public function uploaded(): bool
    {
        return $this->fileKey && $this->storage;
    }

    /**
     * @throws InvalidFile
     */
    public function file(): File
    {
        if (!$this->uploaded()) {
            throw InvalidFile::notUploaded();
        }

        return new File($this->fileKey, $this->storage, $this->info());
    }

    public function metadata(): Metadata
    {
        return Metadata::fromArray($this->metadata);
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function createdById(): ?UserId
    {
        return $this->createdById ? UserId::fromString((string) $this->createdById) : null;
    }

    public function updatedById(): ?UserId
    {
        return $this->updatedById ? UserId::fromString((string) $this->updatedById) : null;
    }

    /**
     * @throws InvalidFile
     */
    public function upload(File $file, Metadata $metadata, UserId $uploaderId = null)
    {
        if (!$this->matchesInfo($file->info())) {
            throw InvalidFile::unmatchedInfo();
        }

        $this->status = self::STATUS_UPLOADED;
        $this->fileKey = $file->key();
        $this->storage = $file->storage();
        $this->metadata = $metadata->all();
        $this->updatedAt = new DateTimeImmutable();
        $this->updatedById = $uploaderId ? (string) $uploaderId : $this->updatedById;
    }
}
