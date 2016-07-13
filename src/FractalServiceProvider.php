<?php


namespace Vice\LaravelFractal;

use Illuminate\Support\ServiceProvider;

/**
 * Class FractalServiceProvider
 * @package Vice\LaravelFractal
 * @codeCoverageIgnore
 */
class FractalServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias(FractalResponseFactory::class, 'fractalResponse');
        $this->app->alias(FractalService::class, 'fractal');

        include_once __DIR__ . "/helpers.php";
    }
}
