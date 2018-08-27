<?php

namespace ObisConcept\NeosSentry\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\DebugExceptionHandler;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Neos\Domain\Model\User;
use Neos\Neos\Domain\Service\UserService;
use ObisConcept\NeosSentry\Domain\Service\SentryClient;

class DebugSentryExceptionHandler extends DebugExceptionHandler
{
    /**
     * @var SentryClient
     */
    protected $sentryClient;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Inject the SentryClient dependency.
     *
     * @param SentryClient $sentryClient
     * @return void
     */
    public function setSentryClient(SentryClient $sentryClient)
    {
        $this->sentryClient = $sentryClient;
    }

    /**
     * Inject the UserService dependency.
     *
     * @param UserService $sentryClient
     * @return void
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @inheritDoc
     */
    public function handleException($exception)
    {
        // Ignore if the error is suppressed by using the shut-up operator @
        if (error_reporting() === 0) {
            return;
        }

        $context = [
            'extra' => [
                'Request Type' => constant('FLOW_SAPITYPE'),
                'Flow Context' => (defined('FLOW_CONTEXT') ? constant('FLOW_CONTEXT') : 'Development'),
                'Project Root' => constant('FLOW_PATH_ROOT'),
                'Document Root' => constant('FLOW_PATH_WEB'),
                'Flow Version' => constant('FLOW_VERSION_BRANCH'),
            ],
            'fingerprint' => ['{{ default }}', get_class($exception)],
            'level' => 'error',
            'logger' => [self::class],
        ];

        /** @var User|null $user */
        $user = $this->userService->getCurrentUser();

        if ($user !== null) {
            $context['user'] = [
                'id' => $user->getName() . '(' . $user->getLabel() . ')',
                'email' => $user->getPrimaryElectronicAddress()
            ];
        }

        $this->sentryClient->captureException($exception, $context);

        parent::handleException($exception);
    }
}
