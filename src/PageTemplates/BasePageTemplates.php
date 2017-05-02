<?php


namespace Gotrecillo\PageManager\PageTemplates;

use Illuminate\Support\Collection;

class BasePageTemplates
{
    private $fields;

    public function __construct()
    {
        $this->fields = new Collection();
    }

    public function basic()
    {
        $this->addMetas();
        $this->addContent();
        return $this->fields->toArray();
    }

    private function addMetas()
    {
        $this->fields->push([
            'name' => 'metas_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>' . trans('gotrecillo::page.fields.meta.label') . '</h2><hr>',
        ]);

        $this->fields->push([
            'name' => 'meta_title',
            'label' => trans('gotrecillo::page.fields.meta.title'),
            'fake' => true,
            'store_in' => 'extras',
            'fake_translated' => true,
        ]);

        $this->fields->push([
            'name' => 'meta_description',
            'label' => trans('gotrecillo::page.fields.meta.description'),
            'fake' => true,
            'store_in' => 'extras',
            'fake_translated' => true,
        ]);

        $this->fields->push([
            'name' => 'meta_keywords',
            'label' => trans('gotrecillo::page.fields.meta.keywords'),
            'fake' => true,
            'store_in' => 'extras',
            'fake_translated' => true,
        ]);
    }

    private function addContent()
    {
        $this->fields->push([
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>' . trans('gotrecillo::page.fields.content') . '</h2><hr>',
        ]);

        $this->fields->push([
            'name' => 'content',
            'label' => trans('gotrecillo::page.fields.content'),
            'type' => 'summernote',
        ]);
    }
}
