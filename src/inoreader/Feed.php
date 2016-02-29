<?php

namespace Davelima\Inoreader;

/**
 * Feed wrapper for API
 * This class manage all feed entries
 *
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
class Feed extends Client
{
    /**
     * Revert the feed results?
     * 
     * @var boolean
     */
    public $reverseOrdering = false;
    
    /**
     * Source feed subscription
     * '/user/-/label/Google' means that we'll fetching feeds 
     * from all subscriptions of the current authenticated user
     * 
     * @var unknown
     */
    public $source = '/user/-/label/Google';
    
    /**
     * If false, results will contain only data from
     * manually added tags
     * 
     * @var boolean
     */
    public $includeAllDirectStreamIds = true;
    
    /**
     * UNIX timestamp indicating when the
     * results must start
     *  
     * @var int
     */
    public $startTime = null;
    
    /**
     * Return a collection with a limited number of feed entries 
     * 
     * @param number $limit
     *      Limit of entries to fetch (max. 1000)
     * @param string $continuation
     *      'Continuation' token from API. This can be used to fetch the next page of results.
     *      This token is included in all results from this method.
     * @param string $onlyUnread
     *      Hide results that already be ready?
     * @param string $include
     *      Include liked/starred results?
     * @throws \InvalidArgumentException
     * @return stdClass
     */
    public function get($limit = 20, $continuation = null, $onlyUnread = false, $include = null)
    {
        if ($limit > 1000) {
            throw new \InvalidArgumentException("Limit must not be greater than 1000. {$limit} provided.");
        }
        
        $stream = urlencode($this->source ? $this->source : '');
        $endpoint = "stream/contents/$stream";
        
        switch ($include) {
            case 'liked':
                $include = 'user/-/state/com.google/like';
                break;
            case 'starred':
                $include = 'user/-/state/com.google/starred';
                break;
            default:
                $include = null;
        }

        $postData = [
            'n' => $limit,
            'output' => 'json',
            'r' => $this->reverseOrdering ? 'o' : null,
            'c' => $continuation,
            'includeAllDirectStreamIds' => $this->includeAllDirectStreamIds,
            'xt' => $onlyUnread ? 'user/-/state/com.google/read' : null,
            'it' => $include
        ];
        
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Same that $this->get(), but return only entries IDs
     * 
     * @param number $limit
     *      Limit of entries to fetch (max. 1000)
     * @param string $continuation
     *      'Continuation' token from API. This can be used to fetch the next page of results.
     *      This token is included in all results from this method.
     * @param string $onlyUnread
     *      Hide results that already be ready?
     * @param string $include
     *      Include liked/starred results?
     * @throws \InvalidArgumentException|\Exception
     * @return stdClass
     */
    public function getIds($limit = 20, $continuation = null, $onlyUnread = false, $include = null)
    {
        if ($limit > 1000) {
            throw new \InvalidArgumentException("Limit must not be greater than 1000. {$limit} provided.");
        }
    
        $stream = urlencode($this->source ? $this->source : '');
        $endpoint = "stream/items/ids";
    
        switch ($include) {
            case 'liked':
                $include = 'user/-/state/com.google/starred';
                break;
            case 'starred':
                $include = 'user/-/state/com.google/like';
                break;
            default:
                $include = null;
        }
    
        $postData = [
            'n' => $limit,
            'output' => 'json',
            'r' => $this->reverseOrdering ? 'o' : null,
            'c' => $continuation,
            'includeAllDirectStreamIds' => $this->includeAllDirectStreamIds,
            'xt' => $onlyUnread ? 'user/-/state/com.google/read' : null,
            'it' => $include,
            's' => $stream
        ];
    
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Mark all feeds <= $untilTimestamp as read
     * 
     * @param string $untilTimestamp
     *      Mark only entries <= $untilTimestamp as read
     * @return string OK if success. Null otherwhise
     */
    public function readAll($untilTimestamp)
    {
        $endpoint = 'mark-all-as-read';
        
        $postData = [
            'ts' => $untilTimestamp,
            's' => $this->source
        ];
        
        return $this->request($endpoint, $postData);
    }
}
