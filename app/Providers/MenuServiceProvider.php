<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    $verticalMenuData = json_decode($verticalMenuJson);
    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);

    //  ADD THIS: Load Supervisor Menu
    $supervisorMenuJson = file_get_contents(base_path('resources/menu/supervisorMenu.json'));
    $supervisorMenuData = json_decode($supervisorMenuJson);

    // Update share to include the 3rd index for supervisors
    $this->app->make('view')->share('menuData', [$verticalMenuData, $horizontalMenuData, $supervisorMenuData]);

    // Share all menuData to all the views
    // $this->app->make('view')->share('menuData', [$verticalMenuData, $horizontalMenuData]);
  }
}
