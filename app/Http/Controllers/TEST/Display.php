<?php

namespace App\Http\Controllers\TEST;

class Display implements Observer
{
    public function update($temp)
    {
        echo "Current Temperature: {$temp}°C\n";
    }

}