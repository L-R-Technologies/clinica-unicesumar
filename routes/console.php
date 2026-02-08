<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('samples:check-freezer')->dailyAt('09:00');
