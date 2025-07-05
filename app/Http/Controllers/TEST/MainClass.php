<?php

namespace App\Http\Controllers\TEST;

class MainClass
{
    public function index() {
        $weatherStation = new WeatherStation();
        $display = new Display();

        $weatherStation->registerObserver($display);
        $weatherStation->setTemperature(26);
    }
}