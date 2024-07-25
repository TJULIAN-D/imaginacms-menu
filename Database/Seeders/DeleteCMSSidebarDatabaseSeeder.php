<?php

namespace Modules\Menu\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Menu\Entities\Menu;

class DeleteCMSSidebarDatabaseSeeder extends Seeder
{
  public function run()
  {
    Model::unguard();
    Menu::whereIn('name', ['cms_admin', 'cms_panel'])->forceDelete();
  }
}
