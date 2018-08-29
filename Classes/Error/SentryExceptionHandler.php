<?php

namespace ObisConcept\NeosSentry\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\ProductionExceptionHandler;
use Neos\Neos\Domain\Model\User;
use Neos\Neos\Domain\Service\UserService;
use ObisConcept\NeosSentry\Domain\Service\SentryClient;
use Neos\Flow\Configuration\Exception\InvalidConfigurationException;

class SentryExceptionHandler extends ProductionExceptionHandler
{
    /**
     * @inheritDoc
     */
    public function handleException($exception)
    {
        // Ignore if the error is suppressed by using the shut-up operator @
        if (error_reporting() === 0) {
            return;
        }

        try {
            /** @var SentryClient $sentry */
            $sentry = $this->prepareSentry();

            $context = [
                'extra' => [
                    'Request Type' => constant('FLOW_SAPITYPE'),
                    'Flow Context' => (defined('FLOW_CONTEXT') ? constant('FLOW_CONTEXT') : 'Development'),
                    'Project Root' => constant('FLOW_PATH_ROOT'),
                    'Document Root' => constant('FLOW_PATH_WEB'),
                    'Flow Version' => constant('FLOW_VERSION_BRANCH'),
                ],
                'fingerprint' => ['{{ default }}', get_class($exception)],
                'level' => 'error'
            ];

            $sentry->captureException($exception, $context);
        } catch (\Exception $e) {
        } finally {
            parent::handleException($exception);
        }
    }

    /**
     * Prepares the SentryClient.
     *
     * @return SentryClient
     * @throws \RuntimeException
     */
    protected function prepareSentry()
    {
        $host = getenv('SENTRY_HOST') ?: 'sentry.io';
        $key = getenv('SENTRY_PROJECT_KEY');
        $id = getenv('SENTRY_PROJECT_ID');

        \Kint::dump($host, $key, $id);

        if ($key === false || $id === false) {
            throw new InvalidConfigurationException(
                "The Sentry client could not be initialized due to missing configuration environment variables!",
                1535539443
            );
        }

        $sentry = new SentryClient;
        $sentry->setClient(new \Raven_Client("https://$key@$host/$id"));

        return $sentry;
    }
}
