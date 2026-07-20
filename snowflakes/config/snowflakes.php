<?php

return [
    'size' => (int) env('SNOWFLAKES_SIZE', 0.8),
    'speed' => (int) env('SNOWFLAKES_SPEED', 1),
    'opacity' => (int) env('SNOWFLAKES_OPACITY', 0.5),
    'density' => (int) env('SNOWFLAKES_DENSITY', 1),
    'quality' => (int) env('SNOWFLAKES_QUALITY', 1),
];
