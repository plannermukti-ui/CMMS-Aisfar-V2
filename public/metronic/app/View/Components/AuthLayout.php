<?php

namespace App\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthLayout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Init layout file
        app(config('settings.KT_THEME_BOOTSTRAP.auth'))->init();
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return Application|Factory|View
     */
    public function render()
    {
        return view(config('settings.KT_THEME_LAYOUT_DIR').'._auth');
    }
}
