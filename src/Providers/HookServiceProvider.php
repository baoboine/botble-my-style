<?php

namespace Botble\MyStyle\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use MetaBox;
use Assets;
use Theme;
use File;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addMyStyleField'], 9020, 3);
        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, [$this, 'saveFieldsInFormScreen'], 75, 3);
        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, [$this, 'saveFieldsInFormScreen'], 75, 3);

        // embed your css to article
        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, [$this, 'embedMyStyles'], 1001, 2);
    }

    /**
     * @param $context
     * @param $object
     */
    public function addMyStyleField($context, $object)
    {
        if (my_style_supported($object) && Auth::user()->hasPermission('my-style.root')) {
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
     * @param $article
     * @return string
     */
    public function renderCustomCssField($article): string
    {
        $slug = $article->slug;
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

            if ($slug) {
                $file = $this->file($slug);

                if (File::exists($file)) {
                    $css = get_file_data($file, false);
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
            my_style_supported($object) &&
            Auth::user()->hasPermission('my-style.root') &&
            $request->has('has-my-style')
        ) {
            $slug   = $request->input('slug');
            $css    = strip_tags($request->input('my_custom_css', ''));
            $file   = $this->file($slug);

            if (empty($css)) {
                File::delete($file);
            } else {
                save_file_data($file, $css, false);
            }
        }
    }

    public function embedMyStyles($screen, $object)
    {
        if (my_style_supported($object)) {
            $slug = $object->slug;
            $file = $this->file($slug);

            if (File::exists($file)) {
                Theme::asset()
                    ->container('after_header')
                    ->usePath()
                    ->add($slug . '-my-style', 'css/'. $slug .'.css', [], [], filectime($file));
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
        return !empty($slug) ?  public_path($path .'/'. $slug .'.css') : $path;
    }
}