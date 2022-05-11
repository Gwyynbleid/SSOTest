<?php

class AuthSSO
{

    private $config = false;
    private $technology = false;
    private $currentLink = '';

    //Construct function, automatically gets which technology to use and loads its config
    function __construct()
    {
        $technology = $this->getTechnology();
        switch (strtolower($technology)):
            case 'openidconnect':
                $this->config = $this->loadOpenIDConnectConfig();
                $this->technology = strtolower($technology);
                break;

            default:
                throw new Exception(htmlentities($technology) . ' not yet implemented.');

        endswitch;
    }

    //Makes sure that user is authorized, returns boolean

    private function getTechnology()
    {
        $cfg = new Config();
        return $cfg->getTechnology();
    }

    //Verifies response from authentication service depending on technologies, returns boolean

    /**
     * @return mixed
     * @throws Exception
     */
    private function loadOpenIDConnectConfig()
    {
        $cfg = new Config();
        $authData = $cfg->getConfig('openidconnect');
        if (!$this->verifyOpenIDConnectConfig($authData)) {
            throw new Exception('OpenIDConnect data not correct. Please check if everything is filled out in OpenIDConnect configuration.');
        }
        return $authData;

    }

    /********* OPEN ID CONNECT RELATED FUNCTIONS *********/

    //Verifies openID response
    private function verifyOpenIDConnectConfig($config)
    {
        return isset($config, $config['authorize_url'], $config['token_url'], $config['introspect_url'], $config['client_id'], $config['client_secret'], $config['redirect_url'])
            && !empty($config)
            && !empty($config['authorize_url'])
            && !empty($config['token_url'])
            && !empty('introspect_url')
            && !empty($config['client_id'])
            && !empty($config['client_secret'])
            && !empty($config['redirect_url']);
    }

    //processes openid data and sets session
    //returns boolean

    public function ensureAuthorized()
    {
        if (isset($_SESSION['app']['uid'], $_SESSION['app']['exp']) && ($_SESSION['app']['exp'] - 300) > time()) {
            return true;
        }

        if ($this->technology === 'openidconnect') {
            $this->authenticateOpenIDConnect();
        }
        return false;
    }

    //Gets technology to use for authenticating, uses Config. Returns string

    private function authenticateOpenIDConnect()
    {
        header('Location: ' . $this->generateOpenIDConnectAuthorizeURL());
        exit();
    }

    private function generateOpenIDConnectAuthorizeURL()
    {
        if ($this->currentLink != '') {
            $current_link = $this->currentLink;
        } else {
            $current_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        return $this->config['authorize_url'] . '?scope=openid&response_type=code&client_id=' . $this->config['client_id'] . '&redirect_uri=' . $this->config['redirect_url'] . '&state=' . urlencode($current_link);
    }

    //Starts authentication process and redirects user to service for authorizing

    public function verifyResponse($response)
    {
        if ($this->technology === 'openidconnect') {
            return $this->verifyCodeOpenIDConnect($response['code']);
        }
        return false;
    }

    //Generates correct openidconnect authorize URL, returns string


    private function verifyCodeOpenIDConnect($code)
    {

        $url = $this->config['token_url'];

        $fields = [
            'code' => $code,
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $this->config['redirect_url'],
            'grant_type' => 'authorization_code'
        ];

        $postvars = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        if ($this->config['use_proxy']) {
            curl_setopt($ch, CURLOPT_PROXY, $this->config['proxy_server']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }


        $result = curl_exec($ch);

        curl_close($ch);

        return $this->processOpenIDConnectCallback($result);
    }

    //Loads openidconnect. Uses Config, returns stdClass

    private function processOpenIDConnectCallback($data)
    {

        $token_response = json_decode($data);

        if ($token_response) {

            if (isset($token_response->error)) {
                throw new Exception('Error happened while authenticating. Please, try again later. [' . $token_response->error . ']');
            }

            if (!isset($token_response->id_token)) {
                return false;
            }

            $jwt_arr = explode('.', $token_response->id_token);
            $encoded = $jwt_arr[1];
            $decoded = '';
            $encoded = strtr($encoded, '-_', '+/');

            $end = ceil(strlen($encoded) / 4);
            for ($i = 0; $i < $end; $i++) {
                $decoded .= base64_decode(substr($encoded, $i * 4, 4));
                $userData = json_decode($decoded, true);
            }


            //Code used in w3ID
            if (isset($userData, $userData['exp'], $userData['uid']) && !empty($userData) && !empty($userData['exp']) && !empty($userData['uid'])) {

                //DEBUG: Use this for returned values from w3id/IBM ID service if you got to else in the condition below
                $_SESSION['app']['exp'] = $userData['exp'];
                $_SESSION['app']['uid'] = $userData['uid'];
                $_SESSION['app']['emailAddress'] = $userData['emailAddress'];
                $_SESSION['app']['lastName'] = urldecode($userData['lastName']);
                $_SESSION['app']['firstName'] = urldecode($userData['firstName']);
                return true;

            }
        }

        return false;

    }

    //Verifies if all openidconnect config data are filled out correctly, returns boolean

    public function setCurrentLink($link)
    {
        $this->currentLink = preg_replace('/^http:/i', 'https:', $link);
    }
}
