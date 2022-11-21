<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return TCG\Voyager\Facades\Voyager::setting($key, $default);
    }
}

if (!function_exists('menu')) {
    function menu($menuName, $type = null, array $options = [])
    {
        return TCG\Voyager\Facades\Voyager::model('Menu')->display($menuName, $type, $options);
    }
}

if (!function_exists('voyager_asset')) {
    function voyager_asset($path, $secure = null)
    {
        if (config('app.env') !== 'local' && strpos($path, '.js') !== false && file_exists(public_path('admin/' . $path))) {
            return asset('admin/' . $path . '?v=2.3.3');
        }

        return route('voyager.voyager_assets').'?path='.urlencode($path);
    }
}

if (!function_exists('get_file_name')) {
    function get_file_name($name)
    {
        preg_match('/(_)([0-9])+$/', $name, $matches);
        if (count($matches) == 3) {
            return Illuminate\Support\Str::replaceLast($matches[0], '', $name).'_'.(intval($matches[2]) + 1);
        } else {
            return $name.'_1';
        }
    }
}

if (! function_exists('q')) {
    function q($string)
    {
        return app('db')->getPdo()->quote($string);
    }
}

if (! function_exists('traverseTree')) {
    function traverseTree($categories, $callback, $prefix = '-')
    {
        foreach ($categories as $category) {
            $callback($category, $prefix);
            traverseTree($category->children, $callback, $prefix . $prefix);
        }
    }
}

if (! function_exists('voyager_rrmdir')) {
    function voyager_rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object)) {
                        voyager_rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                    } else {
                        unlink($dir. DIRECTORY_SEPARATOR .$object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
