<?php

namespace Modules\Menu\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Core\Icrud\Controllers\BaseCrudController;

// Base Api
use Modules\Menu\Repositories\MenuItemRepository;
use Modules\Menu\Services\MenuItemUriGenerator;
use Modules\Menu\Transformers\MenuitemTransformer;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Entities\Menuitem;

class MenuItemApiController extends BaseCrudController
{
    public $model;
    public $modelRepository;
    public $menu;
    public $menuItemUriGenerator;

    public function __construct(MenuItemRepository $modelRepository, Menuitem $model,
                                MenuItemUriGenerator $menuItemUriGenerator, Menu $menu)
    {
        $this->model = $model;
        $this->modelRepository = $modelRepository;
        $this->menu = $menu;
        $this->menuItemUriGenerator = $menuItemUriGenerator;
    }


    /**
     * UPDATE ITEM
     *
     * @return mixed
     */
    public function update($criteria, Request $request)
    {
        \DB::beginTransaction(); //DB Transaction
        try {
            $data = $request->input('attributes') ?? []; //Get data
            $params = $this->getParamsRequest($request); //Get Parameters from URL.
            $menu = $this->menu->find($data['menu_id']); //Get menu
            $languages = LaravelLocalization::getSupportedLanguagesKeys();
            if (! $menu) {
                throw new \Exception('Item not found', 204);
            }//Break if no found item

            //Validate Link type
            foreach ($languages as $lang) {
                if ($data['link_type'] === 'page' && ! empty($data['page_id'])) {
                    $data[$lang]['uri'] = $this->menuItemUriGenerator->generateUri($data['page_id'], $data['parent_id'], $lang);
                }
            }

            //Validate Parent ID
            if (! isset($data['parent_id'])) {
                $data['parent_id'] = $this->modelRepository->getRootForMenu($menu->id)->id;
            }

            //Request to Repository
            $this->modelRepository->updateBy($criteria, $data, $params);
            //Response
            $response = ['data' => 'Item Updated'];
            \DB::commit(); //Commit to DataBase
        } catch (\Exception $e) {
            \DB::rollback(); //Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ['errors' => $e->getMessage()];
        }
        //Return response
        return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
    }

    public function updateItems(Request $request)
    {
        try {
            //Get Parameters from URL.
            $params = $this->getParamsRequest($request);
            $data = $request->input('attributes') ?? []; //Get data
            //Request to Repository
            $dataEntity = $this->modelRepository->getItemsBy($params);
            $crterians = $dataEntity->pluck('id');
            $dataEntity = $this->modelRepository->updateItems($crterians, $data);
            //Response
            $response = ['data' => MenuitemTransformer::collection($dataEntity)];
            //If request pagination add meta-page
            $params->page ? $response['meta'] = ['page' => $this->pageTransformer($dataEntity)] : false;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $status = $this->getStatusError($e->getCode());
            $response = ['errors' => $e->getMessage()];
        }
        //Return response
        return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
    }

    public function deleteItems(Request $request)
    {
        try {
            //Get Parameters from URL.
            $params = $this->getParamsRequest($request);
            //Request to Repository
            $dataEntity = $this->modelRepository->getItemsBy($params);
            $crterians = $dataEntity->pluck('id');
            $this->modelRepository->deleteItems($crterians);
            //Response
            $response = ['data' => 'Items deleted'];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $status = $this->getStatusError($e->getCode());
            $response = ['errors' => $e->getMessage()];
        }
        //Return response
        return response()->json($response ?? ['data' => 'Request successful'], $status ?? 200);
    }

    public function updateOrderner(Request $request)
    {
        \DB::beginTransaction();
        try {
            $params = $this->getParamsRequest($request);
            $data = $request->input('attributes');
            //Update data
            $newData = $this->modelRepository->updateOrders($data);
            //Response
            $response = ['data' => 'updated items'];
            \DB::commit(); //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback(); //Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ['errors' => $e->getMessage()];
        }

        return response()->json($response, $status ?? 200);
    }
}
