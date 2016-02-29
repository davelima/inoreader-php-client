<?php

namespace Davelima\Inoreader;

/**
 * Tags/Folders manager for API
 *
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
class Tag extends Client
{
    /**
     * Return a collection with all
     * tags registered
     * 
     * @return stdClass
     */
    public function getAll()
    {
        $endpoint = 'tag/list';
        
        return $this->request($endpoint);
    }
    
    /**
     * Rename a registered tag
     * 
     * @param string $currentName
     *      Current tag name
     *      
     * @param string $newName
     *      New tag name
     * @return mixed
     */
    public function rename($currentName, $newName)
    {
        $endpoint = 'rename-tag';
        
        $postData = [
            's' => $currentName,
            'dest' => $newName
        ];
        
        return $this->request($endpoint, $postData);
    }
    
    /**
     * Delete a registered tag
     * 
     * @param string $tagName
     *      Tag name to delete
     * @return mixed
     */
    public function delete($tagName)
    {
        $endpoint = 'disable-tag';
        
        $postData = [
            's' => $tagName
        ];
        
        return $this->request($endpoint, $postData);
    }
}
