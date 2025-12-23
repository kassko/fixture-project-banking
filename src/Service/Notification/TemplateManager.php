<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\DTO\Response\NotificationTemplate;

class TemplateManager
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    
    private array $templates = [];

    public function __construct()
    {
        $this->initializeDefaultTemplates();
    }

    public function getTemplate(string $templateId): ?NotificationTemplate
    {
        return $this->templates[$templateId] ?? null;
    }

    public function getAllTemplates(): array
    {
        return array_values($this->templates);
    }

    public function createTemplate(
        string $name,
        string $description,
        string $channel,
        string $subject,
        string $body,
        array $variables,
        string $category
    ): NotificationTemplate {
        $templateId = uniqid('TPL-', true);
        
        $template = new NotificationTemplate(
            $templateId,
            $name,
            $description,
            $channel,
            $subject,
            $body,
            $variables,
            $category,
            date(self::DATE_FORMAT)
        );

        $this->templates[$templateId] = $template;

        return $template;
    }

    public function renderTemplate(string $templateId, array $variables): array
    {
        $template = $this->getTemplate($templateId);
        
        if (!$template) {
            throw new \RuntimeException("Template not found: {$templateId}");
        }

        $subject = $this->replaceVariables($template->getSubject(), $variables);
        $body = $this->replaceVariables($template->getBody(), $variables);

        return [
            'subject' => $subject,
            'body' => $body,
            'channel' => $template->getChannel(),
        ];
    }

    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace("{{" . $key . "}}", (string)$value, $text);
        }
        
        return $text;
    }

    private function initializeDefaultTemplates(): void
    {
        // Welcome notification
        $this->templates['welcome'] = new NotificationTemplate(
            'welcome',
            'Notification de bienvenue',
            'Message de bienvenue pour nouveaux clients',
            'EMAIL',
            'Bienvenue chez {{bank_name}}',
            'Bonjour {{customer_name}}, bienvenue dans notre banque ! Votre compte {{account_number}} est maintenant actif.',
            ['bank_name', 'customer_name', 'account_number'],
            'onboarding',
            date(self::DATE_FORMAT)
        );

        // Transaction alert
        $this->templates['transaction_alert'] = new NotificationTemplate(
            'transaction_alert',
            'Alerte de transaction',
            'Notification pour transactions importantes',
            'SMS',
            'Transaction effectuée',
            'Transaction de {{amount}} EUR effectuée sur votre compte {{account_number}}. Solde: {{balance}} EUR.',
            ['amount', 'account_number', 'balance'],
            'transaction',
            date(self::DATE_FORMAT)
        );

        // Payment reminder
        $this->templates['payment_reminder'] = new NotificationTemplate(
            'payment_reminder',
            'Rappel de paiement',
            'Rappel pour paiements à venir',
            'EMAIL',
            'Rappel: Paiement à venir',
            'Bonjour {{customer_name}}, votre paiement de {{amount}} EUR est prévu pour le {{due_date}}.',
            ['customer_name', 'amount', 'due_date'],
            'payment',
            date(self::DATE_FORMAT)
        );

        // Security alert
        $this->templates['security_alert'] = new NotificationTemplate(
            'security_alert',
            'Alerte de sécurité',
            'Notification pour événements de sécurité',
            'PUSH',
            'Alerte de sécurité',
            'Activité suspecte détectée sur votre compte. Vérifiez immédiatement.',
            [],
            'security',
            date(self::DATE_FORMAT)
        );

        // Monthly statement
        $this->templates['monthly_statement'] = new NotificationTemplate(
            'monthly_statement',
            'Relevé mensuel',
            'Notification de disponibilité du relevé mensuel',
            'IN_APP',
            'Votre relevé mensuel est disponible',
            'Bonjour {{customer_name}}, votre relevé pour {{month}} est maintenant disponible.',
            ['customer_name', 'month'],
            'statement',
            date(self::DATE_FORMAT)
        );
    }
}
