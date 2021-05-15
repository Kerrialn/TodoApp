<?php

namespace App\Service\Api;

use App\Service\Api\ApiInterface;

class GpsApi implements ApiInterface
{

    public function __construct(
        private string $url = '',
        private string $key = '',
        private string $path = '',
    ) {
        $this->url = 'api.gpsapi.com';
        $this->key = '83420y8hfesf0732reds';
    }

    public function configure($options)
    {
       // $this->path =  $this->url . "q=" . $options['city'] . "&appid=" . $this->key;
    }

    public function connect($options)
    {
        $this->configure($options);
        $client = new \GuzzleHttp\Client();
        return $client->request('GET',  $this->path);
    }
}
