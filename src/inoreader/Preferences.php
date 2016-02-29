<?php

namespace Davelima\Inoreader;

/**
 * Preference manager for API
 *
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
class Preferences extends Client
{
    /**
     * Return the preferences for all subscriptions (streams)
     * 
     * @return stdClass
     */
    public function getSubscriptionPreferences()
    {
        $endpoint = 'preference/stream/list';
        
        return $this->request($endpoint);
    }
    
    /**
     * Update subscription ordering
     * 
     * @param string $subscriptionId API's ID
     * @param string $order API's Sort ID
     * @see http://www.inoreader.com/developers/sortids
     */
    public function reorderSubscription($subscriptionId, $order)
    {
        $endpoint = 'preference/stream/set';
        
        $postData = [
            's' => $subscriptionId,
            'v' => $order,
            'k' => 'subscription-ordering'
        ];
        
        return $this->request($endpoint, $postData);
    }
}
