<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\FormFields\CurrencyFormField;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Voyager::addFormField(CurrencyFormField::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        //
        Blade::directive('currency', function ( $expression ) { return "<?php echo number_format($expression,2,',','.'); ?>"; });
    }
}
