<?php

namespace App\Http\Controllers\TEST;

interface Subject
{
    public function registerObserver(Observer $observer);
    public function removeObserver(Observer $observer);
    public function notifyObservers();

}