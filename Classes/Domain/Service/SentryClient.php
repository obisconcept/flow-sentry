<?php

namespace ObisConcept\FlowSentry\Domain\Service;

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
     * @Flow\InjectConfiguration(package="ObisConcept.FlowSentry")
     * @var array
     */
    protected $settings;

    /**
     * @return void
     */
    public function initializeObject()
    {
        $host = $this->settings['host'];
        $key = $this->settings['project']['key'];
        $id = $this->settings['project']['id'];

        $this->client = new \Raven_Client("https://$key@$host/$id");
    }

    /**
     * Gets a property from the client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.get
     * @param string $name The name of the property to get
     * @return mixed The property's value
     */
    public function __get($name)
    {
        return $this->client->$name;
    }

    /**
     * Sets a property on the client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.set
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @return void
     */
    public function __set($name, $value)
    {
        $this->client->$name = $value;
    }

    /**
     * Checks if a property exists on the client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.isset
     * @param string $name The name of the property to check
     * @return bool If the property exists
     */
    public function __isset($name)
    {
        return isset($this->client->$name);
    }

    /**
     * Unsets a property from the client.
     *
     * @see http://php.net/manual/de/language.oop5.overloading.php#object.unset
     * @param string $name The name of the property to unset
     * @return void
     */
    public function __unset($name)
    {
        unset($this->client->$name);
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
}
