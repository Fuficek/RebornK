<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Grav\Common\Page\Page;

/**
 * Class SimpleSendPlugin
 * @package Grav\Plugin
 */
class SimpleSendPlugin extends Plugin
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
                ['autoload', 100000],
                ['onPluginsInitialized', 0]
            ],
            'onOutputGenerated' => [
                ['addInitializationMessage', 0]
            ],
            'onFormProcessed' => [
                ['sendEmail', 0]
            ]
        ];
    }

    /**
     * Composer autoload
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(Event $event): void
    {
        // Initialize logger
        $this->logger = new Logger('simple-send');
        $this->logger->pushHandler(new StreamHandler(GRAV_ROOT . '/logs/simple-send.log', Logger::INFO));

        // Log that the plugin was initialized
        $this->logger->info('Plugin initialized');

        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }
        $this->enable([
            'onPageNotFound' => ['onPageNotFound', 0]
        ]);
        $uri = $this->grav['uri'];
        // Zkontrolujte, jestli aktuální cesta odpovídá vaší cílové URL pro odesílání e-mailu
        if ($uri->path() == '/process-email') {
            $this->sendEmail($event);

            // Nastavte vlastní stránku nebo obsah jako odpověď
            $page = new Page;
            $page->init(new \SplFileInfo(__DIR__ . '/thankyou.md'));

            $page->template('custom');
            $page->slug(basename('/process-email'));

            unset($this->grav['page']);
            $this->grav['page'] = $page;

            // Volitelně, můžete přesměrovat uživatele nebo zobrazit vlastní obsah
            // header('Location: /vlastni-stranka');
            // exit;
        }

    }
    public function onPageNotFound(Event $event)
    {
        $uri = $this->grav['uri'];
        if ($uri->path() == '/process-email') {
            // Zde implementujte logiku odesílání e-mailu
            $this->sendEmail();
            
            // Zabrání vyvolání chyby 404 a ukončí zpracování dalších událostí
            $event->stopPropagation();
            exit;
        }
    }
    /**
     * Add initialization message to the top of the page body for /kontakt route
     *
     * @param Event $event
     */
    public function addInitializationMessage(Event $event): void
    {
        $route = $this->grav['page']->route();
        if ($route === '/kontakt') {
            $output = $event['output'];
            $output .= '<div id="plugin-initialization-message">The plugin Simple-send has been initialized!</div>';

            // Add debug information
            $output .= '<div id="debug-info">';
            $output .= '<pre>';
            $output .= 'Debug Information:' . "\n";
            $output .= 'Route: ' . $route . "\n";
            $output .= '</pre>';
            $output .= '</div>';

            $event['output'] = $output;
        }
    }

    /**
     * Send email with form data when the path to action is '/process-email'
     */
    public function sendEmail(Event $event): void
    {
        $route = $this->grav['page']->route();
        if ($route === '/process-email') {
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
            $recipientEmail = $this->config->get('plugins.simple-send.recipient_email');
    
            // Log the recipient email address
            $this->logger->info('Recipient Email: ' . $recipientEmail);
    
            // Add debug information
            $output = '<div id="debug-info">';
            $output .= '<pre>';
            $output .= 'Debug Information:' . "\n";
            $output .= 'Recipient Email: ' . $recipientEmail . "\n";
            $output .= 'Waiting for 1 minute before sending email...' . "\n";
            $output .= '</pre>';
            $output .= '</div>';
    
            $this->grav['twig']->twig_vars['debug_info'] = $output;
    
            // Send email
            $this->sendMail($recipientEmail, $subject, $body);
    
            // Log when the email has been sent
            $this->logger->info('Email sent');
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

            // Add debug information
            $output = '<div id="debug-info">';
            $output .= '<pre>';
            $output .= 'Debug Information:' . "\n";
            $output .= 'Email sent successfully' . "\n";
            $output .= '</pre>';
            $output .= '</div>';

            $this->grav['twig']->twig_vars['debug_info'] .= $output;
        } else {
            // Failed to send the email, log the error
            $this->logger->error('Failed to send email');

            // Add debug information
            $output = '<div id="debug-info">';
            $output .= '<pre>';
            $output .= 'Debug Information:' . "\n";
            $output .= 'Failed to send email' . "\n";
            $output .= '</pre>';
            $output .= '</div>';

            $this->grav['twig']->twig_vars['debug_info'] .= $output;
        }
    }
}
