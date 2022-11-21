# FME Extended Voyager Integration

If you already installed **voyager**, remove it from the project.

```bash
composer remove tcg/voyager
```

```bash
rm -rf vendor/tcg
rm -rf config/voyager.php
rm -rf public/admin/
```

## Integration

Make sure this package is under packages folder "/packages/FME/voyager", navigate inside voyager folder and install javascript modules

```bash
cd packages/FME/voyager
```

```bash
npm install && npm run prod
```

Then as any other local package

```bash
composer config repositories.local '{"type": "path", "url": "packages/FME/voyager"}' --file composer.json
```

```bash
composer require fme/extended-voyager
php	artisan voyager:install
```

Now just create new admin user and login to your dashboard as usual.

```bash
php	artisan voyager:admin ryan@fountainheadme.com --create
```

If you have hard time installing this package, please 
read  [this article. ](https://laravel-news.com/developing-laravel-packages-with-local-composer-dependencies)

You can refere to voyager [documentation here. ](https://voyager-docs.devdojo.com/v/1.4/)

### Debugging & edit/add features.

You can load views path using your debugbar tool. 
You must use it in all projects. [laravel debugbar. ](https://github.com/barryvdh/laravel-debugbar).

In case you updated js files or css... just remove this folder

```bash
rm -rf public/admin/
```

Then publish your new compiled files.

```bash
php artisan vendor:publish
>> select Tag: voyager_assets
```

Finally, Update resource file version on voyager_asset helper function under

```bash
/base_project_name/packages/FME/voyager/src/Helpers/helpers.php
```

This line.

```php
return asset('admin/' . $path . '?v=2.3.3');
```

### Orders management

As you know, each project is based on client needs. So if you need to update orders filter, add or remove any feature. Just navigate to orders controller under this path

```bash
/base_project_name/packages/FME/voyager/src/Http/Controllers/Bread/OrderBreadController.php
```

Or just search for **OrderBreadController** class.

### Tracking management

This project comes with tracking controller out of the box.

```bash
/base_project_name/packages/FME/voyager/src/Http/Controllers/Bread/TrackingNumbersController.php
```

Update controller code as you need. If all fields are the same, create new event + listener for **TrackingNumberCreatedEvent** to handle notification or so...
