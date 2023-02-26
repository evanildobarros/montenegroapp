<?php
declare(strict_types=1);

namespace Winsite\View\Helper;

use Cake\View\Helper as Helper;

class AnalyticsHelper extends Helper
{
    private $service_account_email;
    private $key_file_location;
    private $profile_id;
    private $token;
    private $status;

    /**
     * @return mixed
     * phpcs:disable
     */
    public function getProfile_id()
    {
        return $this->profile_id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $config
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->service_account_email = 'analytics@site-pmu.iam.gserviceaccount.com';
        $this->profile_id = '206663517';
        $this->key_file_location = 'site-pmu-3a6134bda225.p12';

        try {
            $client = new \Google_Client();

            $client->setClassConfig('Google_Cache_File', ['directory' => TMP . '/cache/google']);

            $key = null;
            if (file_exists($this->key_file_location)) {
                $key = file_get_contents($this->key_file_location);
            }

            $cred = new \Google_Auth_AssertionCredentials($this->service_account_email, [\Google_Service_Analytics::ANALYTICS_READONLY], $key);
            $client->setAssertionCredentials($cred);
            if ($client->getAuth()->isAccessTokenExpired()) {
                $client->getAuth()->refreshTokenWithAssertion($cred);
            }
            $token = json_decode($client->getAccessToken());
            $this->token = $token->access_token;
            $this->status = true;
        } catch (\Google_Auth_Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->status = false;
        }
    }
}
