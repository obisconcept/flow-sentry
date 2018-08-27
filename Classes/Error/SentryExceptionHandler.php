<?php

namespace ObisConcept\NeosSentry\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\ProductionExceptionHandler;
use Neos\Neos\Domain\Model\User;
use Neos\Neos\Domain\Service\UserService;
use ObisConcept\FlowSentry\Domain\Service\SentryClient;

class SentryExceptionHandler extends ProductionExceptionHandler
{
    /**
     * @Flow\Inject
     * @var SentryClient
     */
    protected $sentryClient;

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

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
                'Request Type' => FLOW_SAPITYPE,
                'Flow Context' => FLOW_CONTEXT,
                'Project Root' => FLOW_PATH_ROOT,
                'Document Root' => FLOW_PATH_WEB,
                'Flow Version' => FLOW_VERSION_BRANCH,
            ],
            'fingerprint' => ['{{ default }}', get_class($e)],
            'level' => 'error',
            'logger' => [self::class],
        ];

        /** @var User|null $user */
        $user = $this->userService->getCurrentUser();

        if ($user !== null) {
            $context['user'] = [
                'id' => $user->getName() . '(' . $user->getLabel() . ')',
                'email' => $user->getPrimaryElectronicAddress(),
            ];
        }

        $this->sentryClient->captureException($exception, $context);

        parent::handleException($exception);
    }
}
