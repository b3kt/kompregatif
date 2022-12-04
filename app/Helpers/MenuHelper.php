<?php

namespace App\Helpers;

use TCG\Voyager\Models\Menu;
use TCG\Voyager\Events\MenuDisplay;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MenuHelper{

    public static function display($menuName)
    {
        $menu = Menu::where('name', '=', $menuName)->with(['parent_items.children' => function ($q) {
                $q->orderBy('order');
            }])->first();

        // Check for Menu Existence
        if (!isset($menu)) {
            return false;
        }

        event(new MenuDisplay($menu));

        $items = $menu->parent_items->sortBy('order');

        if ($menuName == 'simanis') {
            $items = static::processItems($items);
        }

        return $items;

        // if ($type == 'admin') {
        //     $type = 'voyager::menu.'.$type;
        // } else {
        //     if (is_null($type)) {
        //         $type = 'voyager::menu.default';
        //     } elseif ($type == 'bootstrap' && !view()->exists($type)) {
        //         $type = 'voyager::menu.bootstrap';
        //     }
        // }

        // if (!isset($options->locale)) {
        //     $options->locale = app()->getLocale();
        // }

        // if ($type === '_json') {
        //     return $items;
        // }

        // return new \Illuminate\Support\HtmlString(
        //     \Illuminate\Support\Facades\View::make($type, ['items' => $items, 'options' => $options])->render()
        // );
    }


    protected static function processItems($items)
    {
        $user = Auth::user();
        $permissions = \App\Models\PermissionMenu::select('menu_item_id')->where('role_id',$user->role->id)->get();

        $permissionMenus = [];
        foreach($permissions as $key=>$value){
            array_push($permissionMenus, $value->menu_item_id);
        }

        // Eagerload Translations
        if (config('voyager.multilingual.enabled')) {
            $items->load('translations');
        }

        $items = $items->transform(function ($item) {
            // Translate title
            $item->title = $item->getTranslatedAttribute('title');
            // Resolve URL/Route
            $item->href = $item->link(true);

            if ($item->href == url()->current() && $item->href != '') {
                // The current URL is exactly the URL of the menu-item
                $item->active = true;
            } elseif (Str::startsWith(url()->current(), Str::finish($item->href, '/'))) {
                // The current URL is "below" the menu-item URL. For example "admin/posts/1/edit" => "admin/posts"
                $item->active = true;
            }
            if (($item->href == url('') || $item->href == route('voyager.dashboard')) && $item->children->count() > 0) {
                // Exclude sub-menus
                $item->active = false;
            } elseif ($item->href == route('voyager.dashboard') && url()->current() != route('voyager.dashboard')) {
                // Exclude dashboard
                $item->active = false;
            }

            if ($item->children->count() > 0) {
                $item->setRelation('children', static::processItems($item->children));

                if (!$item->children->where('active', true)->isEmpty()) {
                    $item->active = true;
                }
            }

            return $item;
        });


        // Filter items by permission
        $items = $items->filter(function ($item) use (&$permissionMenus) {
            return (!$item->children->isEmpty() || Auth::user()->can('browse', $item)) && in_array($item->id, $permissionMenus);
        })->filter(function ($item) {
            // Filter out empty menu-items
            if ($item->url == '' && $item->route == '' && $item->children->count() == 0) {
                return false;
            }

            return true;
        });

        return $items->values();
    }
}
