<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idYandex = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(nullable: true)]
    private ?array $file = null;

    #[ORM\Column(nullable: true)]
    private ?array $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $issues = null;

    #[ORM\Column]
    private ?int $issueId = null;

    #[ORM\Column(nullable: true)]
    private ?array $fileLoad = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdYandex(): ?string
    {
        return $this->idYandex;
    }

    public function setIdYandex(string $idYandex): static
    {
        $this->idYandex = $idYandex;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getFile(): ?array
    {
        return $this->file;
    }

    public function setFile(?array $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getCreatedBy(): ?array
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?array $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIssues(): ?string
    {
        return $this->issues;
    }

    public function setIssues(string $issues): static
    {
        $this->issues = $issues;

        return $this;
    }

    public function getIssueId(): ?int
    {
        return $this->issueId;
    }

    public function setIssueId(int $issueId): static
    {
        $this->issueId = $issueId;

        return $this;
    }

    public function getFileLoad(): ?array
    {
        return $this->fileLoad;
    }

    public function setFileLoad(?array $fileLoad): static
    {
        $this->fileLoad = $fileLoad;

        return $this;
    }
}
