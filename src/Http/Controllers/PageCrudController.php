<?php

namespace Gotrecillo\PageManager\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;

use Illuminate\Support\Facades\Lang;
use Gotrecillo\PageManager\Http\Requests\PageRequest;
use Gotrecillo\PageManager\Models\Page;
use Gotrecillo\PageManager\PageTemplates\BasePageTemplates;

class PageCrudController extends CrudController
{

    public function setUp()
    {
        $this->crud->setModel(Page::class);
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/page');
        $this->crud->setEntityNameStrings(
            trans('gotrecillo::page.page_singular'),
            trans('gotrecillo::page.page_plural')
        );

        $this->crud->addColumn([
            'name' => 'name',
            'label' => trans('gotrecillo::page.name'),
        ]);
        $this->crud->addColumn([
            'name' => 'slug',
            'label' => trans('gotrecillo::page.url'),
        ]);

        $this->crud->addColumn([
            'name' => 'template',
            'label' => trans('gotrecillo::page.template'),
            'type' => 'model_function',
            'function_name' => 'getTemplateName',
        ]);
    }

    public function create($template = false)
    {
        $this->addCrudFields($template);
        return parent::create();
    }

    public function store(PageRequest $request)
    {
        $this->prepareRequestForSaving($request);
        return parent::storeCrud($request);
    }

    public function edit($id, $template = false)
    {
        $template = ($template == false) ? $this->figureTemplateUsed($id) : $template;
        $this->addCrudFields($template);
        return parent::edit($id);
    }

    public function update(PageRequest $request)
    {
        $this->prepareRequestForSaving($request);
        return parent::updateCrud($request);
    }

    private function addDefaultPageFields($template = false)
    {
        $this->crud->addField([
            'name' => 'template',
            'label' => trans('gotrecillo::page.template'),
            'type' => 'select_page_template',
            'options' => $this->getTemplatesOptions(),
            'value' => $template,
            'allows_null' => false,
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        $this->crud->addField([
            'name' => 'name',
            'label' => trans('gotrecillo::page.name'),
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        $this->crud->addField([
            'name' => 'title',
            'label' => trans('gotrecillo::page.title'),
            'type' => 'text',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => trans('gotrecillo::page.url'),
            'type' => 'text',
            'hint' => trans('gotrecillo::page.url_hint'),
        ]);
        $this->crud->addField([
            'name' => 'extras',
            'type' => 'hidden',
        ]);
    }

    private function useTemplate($template = false)
    {
        $templateClass = $this->getTemplatesClass();
        $templates = $this->getTemplates();

        $templateName = ($template == false) ? array_first($templates)->name : $template;

        $fields = (new $templateClass)->{$templateName}();
        $this->crud->addFields($fields);
    }

    private function getTemplatesOptions()
    {
        $templatesOptions = [];
        $templates = $this->getTemplates();

        foreach ($templates as $template) {
            $templatesOptions[$template->name] = Lang::has('gotrecillo::page.templates.' . $template->name)
                ? trans('gotrecillo::page.templates.' . $template->name)
                : $this->crud->makeLabel($template->name);
        }

        return $templatesOptions;
    }

    private function figureTemplateUsed($id)
    {
        $model = $this->crud->model;
        $this->data['entry'] = $model::findOrFail($id);
        $template = $this->data['entry']->template;

        return $template;
    }

    private function getTemplatesClass()
    {
        $namespace = config('gotrecillo.page.templates_namespace');
        $userTemplates = $namespace . 'PageTemplates';

        $templatesClassName = class_exists($userTemplates) ? $userTemplates : BasePageTemplates::class;

        return $templatesClassName;
    }

    private function getTemplates()
    {
        $templatesClassName = $this->getTemplatesClass();
        $reflector = new \ReflectionClass($templatesClassName);
        $templates = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

        $templates = array_filter($templates, function ($method) {
            return $method->name !== '__construct';
        });

        if (!count($templates)) {
            abort('403', 'No templates have been found.');
        }

        return $templates;
    }

    private function prepareRequestForSaving(PageRequest $request)
    {
        $request->request->remove('extras');
        $template = $request->get('template');
        $this->addCrudFields($template);
    }

    private function addCrudFields($template)
    {
        $this->addDefaultPageFields($template);
        $this->useTemplate($template);
    }
}
