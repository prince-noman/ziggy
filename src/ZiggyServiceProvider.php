<?php

namespace Tightenco\Ziggy;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class ZiggyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ziggy.php', 'ziggy');
    }

    public function boot()
    {
        if ($this->app->resolved('blade.compiler')) {
            $this->registerDirective($this->app['blade.compiler']);
        } else {
            $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
                $this->registerDirective($bladeCompiler);
            });
        }

        if ($this->app->runningInConsole()) {
            $this->commands(CommandRouteGenerator::class);
        }

        $this->publishes([__DIR__ . '/../config/ziggy.php' => config_path('ziggy.php')], 'ziggy');
    }

    protected function registerDirective(BladeCompiler $bladeCompiler)
    {
        $bladeCompiler->directive('routes', function ($args) {
            return "<?php echo app('" . BladeRouteGenerator::class . "')->generate({$args}); ?>";
        });
    }
}
