<?php

use Vice\LaravelFractal\FractalResponseFactory;

if (!function_exists('fractalResponse')) {
    /**
     * @return FractalResponseFactory
     */
    function fractalResponse()
    {
        return app('fractalResponse');
    }
}
