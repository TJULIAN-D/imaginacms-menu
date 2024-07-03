<?php

use Illuminate\Routing\Router;

Route::prefix('imenu')->group(function (Router $router) {
    // menu Routes
    $router->apiCrud([
        'module' => 'menu',
        'prefix' => 'menu',
        'controller' => 'MenuApiController',
        'middleware' => [
            'create' => ['auth:api', 'auth-can:menu.menus.create'],
            'index' => ['optional-auth'], 'show' => [],
            'update' => ['auth:api', 'auth-can:menu.menus.edit'],
            'delete' => ['auth:api', 'auth-can:menu.menus.destroy'],
            // 'restore' => []
        ]
    ]);

    // menuItems Routes
    $router->apiCrud([
        'module' => 'menu',
        'prefix' => 'menuitem',
        'controller' => 'MenuItemApiController',
        'middleware' => [
            'create' => ['auth:api', 'auth-can:menu.menuitems.create'],
            'index' => ['optional-auth'], 'show' => [],
            'update' => ['auth:api', 'auth-can:menu.menuitems.edit'],
            'delete' => ['auth:api', 'auth-can:menu.menuitems.destroy'],
            // 'restore' => []
        ]
    ]);
});
