<?php

namespace App\Models;


use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Translatable\HasTranslations;

class Role extends ModelsRole
{
    use  HasTranslations;
public $translatable = ['display_name'];

}
