<?php

namespace App\Service\Api;

use App\Service\Api\ApiInterface;

class WeatherApi implements ApiInterface
{

    public function __construct(
        private string $url = '',
        private string $key = '',
        private string $path = '',
    ) {
        $this->url = 'api.openweathermap.org/data/2.5/weather?';
        $this->key = '497c6d4e6f1f57d30203e4253a84869d';
    }

    public function configure($options)
    {
        $this->path =  $this->url . "q=" . $options['city'] . "&appid=" . $this->key;
    }

    public function connect($options)
    {
        $this->configure($options);
        $client = new \GuzzleHttp\Client();
        return $client->request('GET',  $this->path);
    }
}
