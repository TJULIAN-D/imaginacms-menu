<?php

namespace Modules\Menu\Transformers;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Icrud\Transformers\CrudResource;

class MenuTransformer extends CrudResource
{

    /**
     * Method to merge values with response
     *
     * @return array
     */
    public function modelAttributes($request)
    {
        return [
            'menuitems' => $this->getMenuItems(),
        ];
    }

    /*
    * Integration with tenant - menuitems
    */
    public function getMenuItems()
    {
        return Cache::store(config('cache.default'))->remember('menu_items_' . $this->id . (tenant()->id ?? ''), 60, function () {
            $params = [
                'include' => [],
                'filter' => [
                    'menu' => $this->id,
                    'order' => ['way' => 'asc'],
                ],
            ];

            $menuItems = app('Modules\Menu\Repositories\MenuItemRepository')->getItemsBy(json_decode(json_encode($params)));

            if (!empty($menuItems)) {
                return MenuitemTransformer::collection($menuItems);
            }

            return '';
        });
    }
}