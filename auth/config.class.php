<?php

//Class used to get any configuration data from cfg/ folder
class Config
{

    private $technology = false;

    //Gets config for different kinds of supported authenticate technologies and returns as stdClass()
    public function getConfig($technology)
    {
        if ($this->setAuthTechnology($technology)) {
            switch (strtolower($technology)):
                case "openidconnect":
                    return $this->getAuthConfigForOpenID();
            endswitch;
        } else {
            throw new Exception(htmlentities($technology) . ' not yet implemented.');
        }
    }

    //Gets which technology to use, from auth_technology.inc, returns string

    private function setAuthTechnology($technology)
    {
        switch (strtolower($technology)):
            case "openidconnect":
                $this->technology = $technology;
                return true;
        endswitch;

        return false;
    }

    //Sets $technology variable if valid technology supplied, returns boolean

    private function getAuthConfigForOpenID()
    {
        include realpath(__DIR__) . "/../config.php";
        return $configs['openidconnect'];
    }

    //Gets auth config for openidconnect technology

    public function getTechnology()
    {
        include realpath(__DIR__) . "/auth_technology.inc.php";
        return $auth_technology;
    }

}
