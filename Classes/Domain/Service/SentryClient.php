<?php

namespace ObisConcept\NeosSentry\Domain\Service;

use Neos\Flow\Annotations as Flow;

use Neos\Flow\Configuration\Exception\InvalidConfigurationException;

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

/**
 * A wrapper class for the Raven_Client from Sentry.
 *
 * @Flow\Scope("singleton")
 */
class SentryClient
{
    /**
     * @var \Raven_Client
     */
    protected $client;

    /**
     * @Flow\InjectConfiguration(package="ObisConcept.NeosSentry")
     * @var array
     */
    protected $settings;

    /**
     * @return void
     * @throws InvalidConfigurationException
     */
    public function initializeObject()
    {
        $host = $this->settings['host'];
        $key = $this->settings['project']['key'];
        $id = $this->settings['project']['id'];

        if (empty($key) || empty($id)) {
            throw new InvalidConfigurationException(
                'Missing required configuration for Sentry! You need to provide at least a project key and identifier.',
                1535539692
            );
        }

        $this->client = new \Raven_Client("https://$key@$host/$id");
    }

    /**
     * Calls a method on the underlying client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.call
     * @param string $name The name of the method to call
     * @param array $arguments The method call arguments
     * @return mixed The return value of the method
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }

    /**
     * Calls a static method on the underlying client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.callstatic
     * @param string $name The name of the method to call
     * @param array $arguments The method call arguments
     * @return mixed The return value of the method
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([\Raven_Client::class, $name], $arguments);
    }

    /**
     * Get the underlying Raven_Client instance.
     *
     * @return \Raven_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the underlying Raven_Client instance.
     *
     * @param \Raven_Client $client
     * @return self
     */
    public function setClient(\Raven_Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
