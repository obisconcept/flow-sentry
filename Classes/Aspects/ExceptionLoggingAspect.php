<?php

namespace ObisConcept\FlowSentry\Aspects;

/**
 * Copyright (C) 2018  obis|CONCEPT GmbH & Co. KG
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Neos\Flow\Annotations as Flow;

use Neos\Flow\Aop\JoinPointInterface;

use ObisConcept\FlowSentry\Domain\Service\SentryClient;
use Neos\Neos\Domain\Service\UserService;
use Neos\Neos\Domain\Model\User;

/**
 * An aspect for sending exception information to a Sentry tracker.
 *
 * @Flow\Aspect
 */
class ExceptionLoggingAspect
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
     * Sends all caught exceptions to Sentry.
     *
     * @Flow\Before("class(Neos\Flow\Error\.*ExceptionHandler->handleException())")
     * @param JoinPointInterface $joinPoint
     * @return void
     */
    public function logException(JoinPointInterface $joinPoint)
    {
        /** @var \Throwable $e */
        $e = $joinPoint->getMethodArgument('exception');

        $context = [
            'extra' => [
                'Request Type' => FLOW_SAPITYPE,
                'Flow Context' => FLOW_CONTEXT,
                'Project Root' => FLOW_PATH_ROOT,
                'Document Root' => FLOW_PATH_WEB,
                'Flow Version' => FLOW_VERSION_BRANCH
            ],
            'fingerprint' => ['{{ default }}', get_class($e)],
            'level' => 'error',
            'logger' => [self::class]
        ];

        /** @var User|null $user */
        $user = $this->userService->getCurrentUser();

        if ($user !== null) {
            $context['user'] = [
                'id' => $user->getName() . '(' . $user->getLabel() . ')',
                'email' => $user->getPrimaryElectronicAddress()
            ];
        }

        $this->sentryClient->captureException($e, $context);
    }
}
