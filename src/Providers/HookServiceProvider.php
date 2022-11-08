<?php

namespace Botble\MyStyle\Providers;

use Assets;
use BaseHelper;
use Botble\Base\Models\BaseModel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use MetaBox;
use MyStyleHelper;
use Theme;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addMyStyleField'], 50, 2);
        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, [$this, 'saveFieldsInFormScreen'], 75, 3);
        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, [$this, 'saveFieldsInFormScreen'], 75, 3);
        add_action(BASE_ACTION_AFTER_DELETE_CONTENT, [$this, 'deleteFields'], 75, 3);

        // embed your css to article
        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, [$this, 'embedMyStyles'], 1001, 2);
    }

    /**
     * @param $screen
     * @param Request $request
     * @param BaseModel $object
     */
    public function deleteFields($screen, $request, $object)
    {
        if (MyStyleHelper::isSupportedModel(get_class($object)) &&
            Auth::user()->hasPermission('my-style.root')) {
            $fileName = $this->fileName($object);
            $file = $this->file($fileName);

            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }

    /**
     * @param $context
     * @param $object
     */
    public function addMyStyleField($context, $object)
    {
        if (MyStyleHelper::isSupportedModel(get_class($object)) &&
            Auth::user()->hasPermission('my-style.root')) {
            MetaBox::addMetaBox(
                'my_style',
                __('My CSS'),
                [$this, 'renderCustomCssField'],
                get_class($object),
                'advanced',
                'low'
            );
        }
    }

    /**
     * @param BaseModel $object
     * @return string
     */
    public function renderCustomCssField($object): string
    {
        $fileName = $this->fileName($object);
        $path = $this->file();
        $isWriteable = File::isWritable($path);
        $css = '';

        if ($isWriteable) {
            Assets::addStylesDirectly([
                'vendor/core/core/base/libraries/codemirror/lib/codemirror.css',
                'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.css',
                'vendor/core/packages/theme/css/custom-css.css',
            ])
                ->addScriptsDirectly([
                    'vendor/core/core/base/libraries/codemirror/lib/codemirror.js',
                    'vendor/core/core/base/libraries/codemirror/lib/css.js',
                    'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.js',
                    'vendor/core/core/base/libraries/codemirror/addon/hint/anyword-hint.js',
                    'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
                    'vendor/core/packages/theme/js/custom-css.js',
                ]);

            if ($fileName) {
                $file = $this->file($fileName);

                if (File::exists($file)) {
                    $css = BaseHelper::getFileData($file, false);
                }
            }
        }

        return view('plugins/my-style::css-editor', compact('css', 'isWriteable', 'path'))->render();
    }

    /**
     * @param $type
     * @param Request $request
     * @param $object
     */
    public function saveFieldsInFormScreen($type, Request $request, $object)
    {
        if (
            MyStyleHelper::isSupportedModel(get_class($object)) &&
            Auth::user()->hasPermission('my-style.root') &&
            $request->has('has-my-style')
        ) {
            $fileName = $this->fileName($object);
            $css = strip_tags($request->input('my_custom_css', ''));
            $file = $this->file($fileName);

            if (empty($css)) {
                File::delete($file);
            } else {
                BaseHelper::saveFileData($file, $css, false);
            }
        }
    }

    /**
     * @param string $screen
     * @param $object
     */
    public function embedMyStyles($screen, $object)
    {
        if (MyStyleHelper::isSupportedModel(get_class($object))) {
            $fileName = $this->fileName($object);
            $file = $this->file($fileName);

            if (File::exists($file)) {
                Theme::asset()
                    ->container('after_header')
                    ->usePath()
                    ->add($fileName . '-my-style', 'css/' . $fileName . '.css', [], [], filectime($file));
            }
        }
    }

    /**
     * @param string $slug
     * @return string
     */
    protected function file(string $slug = ''): string
    {
        $path = Theme::path() . '/css';

        return !empty($slug) ? public_path($path . '/' . $slug . '.css') : $path;
    }

    /**
     * @param BaseModel $object
     * @return string
     */
    protected function fileName(BaseModel $object): string
    {
        return md5(get_class($object) . '-' . $object->id);
    }
}
