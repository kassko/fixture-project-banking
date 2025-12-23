<?php

declare(strict_types=1);

namespace App\DTO\Response;

class NotificationTemplate
{
    public function __construct(
        private string $templateId,
        private string $name,
        private string $description,
        private string $channel,
        private string $subject,
        private string $body,
        private array $variables,
        private string $category,
        private string $createdAt,
        private ?string $updatedAt = null
    ) {
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function toArray(): array
    {
        $result = [
            'template_id' => $this->templateId,
            'name' => $this->name,
            'description' => $this->description,
            'channel' => $this->channel,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'category' => $this->category,
            'created_at' => $this->createdAt,
        ];

        if ($this->updatedAt !== null) {
            $result['updated_at'] = $this->updatedAt;
        }

        return $result;
    }
}
