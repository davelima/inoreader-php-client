<?php

namespace Davelima\Inoreader;

/**
 * User manager for API
 *
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
class User extends Client
{
    /**
     * Return a collection with account info for the authenticated user
     * 
     * @return stdClass
     */
    public function getUserInfo()
    {
        $endpoint = 'user-info';
        return $this->request($endpoint);
    }
}
