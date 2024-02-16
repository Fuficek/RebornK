<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Grav\Common\Grav;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class SimpleEmailPlugin
 * @package Grav\Plugin
 */
class SimpleEmailPlugin extends Plugin
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        // Initialize logger
        $this->logger = new Logger('simple-email');
        $this->logger->pushHandler(new StreamHandler(GRAV_ROOT . '/logs/simple-email.log', Logger::INFO));

        // Log that the plugin was initialized
        $this->logger->info('Plugin initialized');

        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminSave' => ['saveRecipientEmail', 0]
            ]);
            return;
        }

        // Listen for form submission event
        $this->enable([
            'onFormProcessed' => ['sendEmail', 0]
        ]);
    }

    /**
     * Send email with form data
     */
    public function sendEmail($event): void
    {
        $form = $event['form'];

        // Log that information has been gathered from the form
        $this->logger->info('Form data gathered');

        // Get form data
        $name = $form->value('name');
        $email = $form->value('email');
        $message = $form->value('message');

        // Log the information stored in the variables
        $this->logger->info('Sender Name: ' . $name);
        $this->logger->info('Sender Email: ' . $email);
        $this->logger->info('Message: ' . $message);

        // Prepare email content
        $subject = 'New message from ' . $name;
        $body = "Name: $name\nEmail: $email\nMessage: $message";

        // Get recipient email from configuration
        $recipientEmail = $this->config->get('plugins.simple-email.recipient_email');

        // Log the recipient email address
        $this->logger->info('Recipient Email: ' . $recipientEmail);

        // Send email
        $this->sendMail($recipientEmail, $subject, $body);

        // Log when the email has been sent
        $this->logger->info('Email sent');

        // Log message
        $this->grav['log']->info('Email sent successfully.');
    }

    /**
     * Save recipient email from admin panel
     */
    public function saveRecipientEmail($event): void
    {
        $data = $event['data'];

        if (isset($data['plugins']['simple-email']['recipient_email'])) {
            $this->config->set('plugins.simple-email.recipient_email', $data['plugins']['simple-email']['recipient_email']);
            $this->config->save();
        }
    }

    /**
     * Send email using mail() function
     */
    private function sendMail($to, $subject, $message): void
    {
        // Attempt to send the email
        if (mail($to, $subject, $message)) {
            // Email sent successfully, log the success
            $this->logger->info('Email sent successfully');
        } else {
            // Failed to send the email, log the error
            $this->logger->error('Failed to send email');
        }
    }
}
