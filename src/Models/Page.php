<?php

namespace Gotrecillo\PageManager\Models;

use Backpack\CRUD\CrudTrait;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\Sluggable;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use CrudTrait;
    use Sluggable;
    use SluggableScopeHelpers;
    use HasTranslations;


    protected $fillable = ['template', 'name', 'title', 'slug', 'extras', 'content'];
    protected $fakeColumns = ['extras'];
    protected $translatable = ['title', 'slug', 'extras', 'content'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getTemplateName()
    {
        return trans('soluadmin::page.templates.'.$this->template);
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getSlugOrTitleAttribute()
    {
        return ($this->slug != '') ? $this->slug : $this->title;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
