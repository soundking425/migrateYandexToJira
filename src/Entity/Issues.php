<?php

namespace App\Entity;

use App\Repository\IssuesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IssuesRepository::class)]
class Issues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $key = null;

    #[ORM\Column(length: 255)]
    private ?string $keyYandex = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keyJira = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idYandex = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idJira = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $parent = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $aliases = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?array $type = null;

    #[ORM\Column(nullable: true)]
    private ?array $priority = null;

    #[ORM\Column(nullable: true)]
    private ?array $queue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $boards = null;

    #[ORM\Column(nullable: true)]
    private ?int $loadJira = null;

    #[ORM\Column(nullable: true)]
    private ?array $attachments = null;

    #[ORM\Column(nullable: true)]
    private ?array $fileLoad = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionHtml = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $epic = null;

    #[ORM\Column(nullable: true)]
    private ?array $components = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getKeyYandex(): ?string
    {
        return $this->keyYandex;
    }

    public function setKeyYandex(string $keyYandex): static
    {
        $this->keyYandex = $keyYandex;

        return $this;
    }

    public function getKeyJira(): ?string
    {
        return $this->keyJira;
    }

    public function setKeyJira(?string $keyJira): static
    {
        $this->keyJira = $keyJira;

        return $this;
    }

    public function getIdYandex(): ?string
    {
        return $this->idYandex;
    }

    public function setIdYandex(?string $idYandex): static
    {
        $this->idYandex = $idYandex;

        return $this;
    }

    public function getIdJira(): ?string
    {
        return $this->idJira;
    }

    public function setIdJira(?string $idJira): static
    {
        $this->idJira = $idJira;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(?string $parent): ?static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getAliases(): ?array
    {
        return $this->aliases;
    }

    public function setAliases(?array $aliases): static
    {
        $this->aliases = $aliases;

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

    public function getType(): ?array
    {
        return $this->type;
    }

    public function setType(?array $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPriority(): ?array
    {
        return $this->priority;
    }

    public function setPriority(?array $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getQueue(): ?array
    {
        return $this->queue;
    }

    public function setQueue(?array $queue): static
    {
        $this->queue = $queue;

        return $this;
    }

    public function getBoards(): ?string
    {
        return $this->boards;
    }

    public function setBoards(?string $boards): static
    {
        $this->boards = $boards;

        return $this;
    }

    public function getLoadJira(): ?int
    {
        return $this->loadJira;
    }

    public function setLoadJira(?int $loadJira): static
    {
        $this->loadJira = $loadJira;

        return $this;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): static
    {
        $this->attachments = $attachments;

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

    public function getDescriptionHtml(): ?string
    {
        return $this->descriptionHtml;
    }

    public function setDescriptionHtml(?string $descriptionHtml): static
    {
        $this->descriptionHtml = $descriptionHtml;

        return $this;
    }

    public function getEpic(): ?string
    {
        return $this->epic;
    }

    public function setEpic(?string $epic): static
    {
        $this->epic = $epic;

        return $this;
    }

    public function getComponents(): ?array
    {
        return $this->components;
    }

    public function setComponents(?array $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(?string $project): static
    {
        $this->project = $project;

        return $this;
    }
}
