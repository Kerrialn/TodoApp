<?php

namespace App\Service;

class Api{
    CONST WEATHER_API = 'api.openweathermap.org/data/2.5/weather?';
    CONST WEATHER_API_KEY = '497c6d4e6f1f57d30203e4253a84869d';
    
    public function weather(string $city) : string {
        return Api::WEATHER_API."q=".$city."&appid=".Api::WEATHER_API_KEY;
    }
}
