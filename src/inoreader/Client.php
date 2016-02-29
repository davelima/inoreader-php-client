<?php

namespace Davelima\Inoreader;

/**
 * PHP Client for the Inoreader API 
 * 
 * @author David Lima
 * @copyright 2016, David Lima
 * @version 1.0
 * @see http://www.inoreader.com/developers/
 */
abstract class Client
{
    /**
     * AppID generated on Inoreader Developers
     * 
     * @var string
     */
    private $appId;
    
    /**
     * AppKey generated on Inoreader Developers
     * 
     * @var string
     */
    private $appKey;
    
    /**
     * Username of Inoreader account
     * 
     * @var string
     */
    private $email;
    
    /**
     * Password of Inoreader account
     * 
     * @var string
     */
    private $password;
    
    /**
     * Authorization token generated automatically
     * This token is valid for 30 days and will be
     * regenerated when the cookie expires
     * 
     * @var string
     */
    private $authToken;
    
    /**
     * @var string
     */
    const BASE_ENDPOINT = 'https://www.inoreader.com/reader/api/0/';
    
    /**
     * Inoreader API has a different endpoint for users authentication
     * 
     * @var string
     */
    const LOGIN_ENDPOINT = 'https://www.inoreader.com/accounts/ClientLogin';
    
    /**
     * @var string
     */
    const USERAGENT = 'Inoreader PHP Client/1.0';
    
    /**
     * Name of cookie to be saved with the authorization token
     * 
     * @var string
     */
    const COOKIE_KEY = 'InoreaderPhpClientToken';
    
    /**
     * Base constructor
     * 
     * @param string $appId AppID generated on Inoreader Developers
     * @param string $appKey AppKey generated on Inoreader Developers
     * @param string $email Username of Inoreader account
     * @param string $password Password of Inoreader account
     */
    public function __construct($appId, $appKey, $email, $password)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->email = $email;
        $this->password = $password;
        $this->getAuthToken();
    }
    
    /**
     * Send a POST via CURL to the defined $endpoint 
     * 
     * @param string $endpoint API endpoint to request
     * @param array $data Data to send via POST
     * @throws \Exception
     */
    protected function request($endpoint, array $data = [])
    {
        $url = self::BASE_ENDPOINT . $endpoint;
        $ch = curl_init($url);
        
        $appData = [
            'AppId' => $this->appId,
            'AppKey' => $this->appKey
        ];
        
        $postData = array_merge($appData, $data);
        
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, \CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, \CURLOPT_POST, true);
        curl_setopt($ch, \CURLOPT_HTTPHEADER, ["Authorization: GoogleLogin " . $this->authToken]);
        curl_setopt($ch, \CURLOPT_POSTFIELDS, $postData);
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        switch ($info['http_code']) {
            case 200:
                return json_decode($data);
                break;
            case 401:
            default:
                throw new \Exception("Request error: " . $data);
                break;
        }
    }
    
    /**
     * Generate the authToken and stores for 30 days
     * Cookie name: self::COOKIE_KEY
     * 
     * @throws \Exception
     */
    private function getAuthToken()
    {
        if (isset($_COOKIE[self::COOKIE_KEY])) {
            $this->authToken = $_COOKIE[self::COOKIE_KEY];
            return $this->authToken;
        }
        $ch = curl_init(self::LOGIN_ENDPOINT);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, \CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, \CURLOPT_POST, true);
        curl_setopt($ch, \CURLOPT_POSTFIELDS, [
            'Email' => $this->email,
            'Passwd' => $this->password
        ]);
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
            switch ($info['http_code']) {
            case 200:
                $data = explode(PHP_EOL, trim($data));
                $this->authToken = $data[2];
                setcookie(self::COOKIE_KEY, $this->authToken, (time() + 60 * 60 * 24 * 30));
                return $this->authToken;
                break;
            case 401:
            default:
                throw new \Exception("Cannot authenticate: Invalid Email/Password");
                break;
        }
    }
}
