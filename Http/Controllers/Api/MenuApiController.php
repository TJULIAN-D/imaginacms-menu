<?php

namespace Modules\Menu\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Icrud\Controllers\BaseCrudController;

// Base Api
use Modules\Menu\Repositories\MenuRepository;
use Modules\Menu\Entities\Menu;

class MenuApiController extends BaseCrudController
{
    public $model;
    public $modelRepository;

    public function __construct(Menu $model, MenuRepository $modelRepository)
    {
        $this->model = $model;
        $this->modelRepository = $modelRepository;
    }
}
