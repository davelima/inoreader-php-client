<?php

namespace Davelima\Inoreader;

/**
 * Subscription manager for API
 *
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
class Subscription extends Client
{
    /**
     * Add a new subscription
     * 
     * @param string $source
     *      Feed URL
     * @return string OK if success. Null otherwise
     */
    public function quickSubscribe($source)
    {
        $endpoint = 'subscription/quickadd';
        
        return $this->request($endpoint, [
            'quickadd' => $source
        ]);
    }
    
    /**
     * Rename a previously added subscription
     * 
     * @param string $source
     *      Feed URL
     * @param string $newTitle
     *      New title for the subscription
     * @return mixed
     */
    public function rename($source, $newTitle)
    {
        $endpoint = 'subscription/edit';
        
        $postData = [
            'ac' => 'edit',
            's' => $source,
            't' => $newTitle
        ];
        
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Add a new subscription with more informations
     * 
     * @param array $sourceData
     *      Feed information
     * @example $this->subscribe([
     *     'url' => 'http://feed.com/rss',
     *     't' => 'Feed title',
     *     'a' => 'user/-/label/FolderName'
     * ])
     * @return mixed
     */
    public function subscribe(array $sourceData)
    {
        $endpoint = 'subscription/edit';
        
        $postData = [
            'ac' => 'subscribe',
            's' => $sourceData['url'],
            't' => isset($sourceData['title']) ? $sourceData['title'] : null,
            'a' => isset($sourceData['folder']) ? $sourceData['folder'] : null,
        ];
        
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Remove a subscription
     * 
     * @param string $source
     *      Feed URL
     * @return mixed
     */
    public function unsubscribe($source)
    {
        $endpoint = 'subscription/edit';
        
        $postData = [
            'ac' => 'unsubscribe',
            's' => $source
        ];
        
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Return a collection with all subscriptions
     * for the authenticated user
     * 
     * @return stdClass
     */
    public function getAll()
    {
        $endpoint = 'subscription/list';
        
        return $this->request($endpoint);
    }
    
    /**
     * Return a collection with all
     * subscriptions with unread items
     * and the count of each one
     * 
     * @return mixed
     */
    public function unreadCounts()
    {
        $endpoint = 'unread-count';
        
        return $this->request($endpoint);
    }
    
    /**
     * Return the total of unread items
     * 
     * @return int
     */
    public function getUnread()
    {
        $unread = $this->unreadCounts();
        return $unread->unreadcounts[0]->count;
    }
}
