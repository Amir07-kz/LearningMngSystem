<?php

namespace App\Http\Controllers\TEST;

class WeatherStation implements Subject
{
    private $observers = [];
    private $temperature;

    public function registerObserver(Observer $observer)
    {
        $this->observe[] = $observer;
    }

    public function removeObserver(Observer $observer)
    {
        $this->observers = array_filter($this->observers, function ($o) use ($observer){
            return $o !== $observer;
        });
    }

    public function notifyObservers()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this->observers);
        }
    }

    public function setTemperature($temp)
    {
        $this->temperature = $temp;
        $this->notifyObservers();
    }
}