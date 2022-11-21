<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use TCG\Voyager\Traits\Seedable;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\VoyagerServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use TCG\Voyager\Providers\VoyagerDummyServiceProvider;

class InstallCommand extends Command
{
    use Seedable;

    /**
     * @var string
     */
    protected $seedersPath = __DIR__.'/../../publishable/database/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the FME Voyager Admin package';

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null]
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * @return void
     */
    protected function deleteOldSeeders()
    {
        $seeders = [
            'DataTypesTableSeeder',
            'DataRowsTableSeeder',
            'MenusTableSeeder',
            'MenuItemsTableSeeder',
            'RolesTableSeeder',
            'PermissionsTableSeeder',
            'PermissionRoleTableSeeder',
            'SettingsTableSeeder',
            'VoyagerBreadSeeder',
            'VoyagerDummyDatabaseSeeder',
            'VoyagerDatabaseSeeder',
            'TranslationsTableSeeder',
            'FME_MenuItemsTableSeeder',

            'breads/AvailabilitiesTableSeeder',
            'breads/BrandsBreadRowAdded',
            'breads/BrandsBreadTypeAdded',
            'breads/CategoriesBreadRowAdded',
            'breads/CategoriesBreadTypeAdded',
            'breads/OrdersBreadRowAdded',
            'breads/OrdersBreadTypeAdded',
            'breads/ProductsBreadRowAdded',
            'breads/ProductsBreadTypeAdded',
        ];

        foreach ($seeders as $index => $seeder) {
            if (! file_exists(database_path("seeds/{$seeder}.php"))) {
                unset($seeders[$index]);
            }
        }

        $this->info('total old seeders files found: ' . count($seeders));

        if (count($seeders) > 0 && $this->confirm('Do you wish to delete those files?')) {
            foreach ($seeders as $index => $seeder) {
                unlink(database_path("seeds/{$seeder}.php"));
            }
        }

        if (count($seeders) === 0) {
            $this->info('Good to continue...');
        }
    }

    /**
     * @return void
     */
    protected function deleteOldMigrations()
    {
        $migrations = [
            '2016_01_01_000000_add_voyager_user_fields',
            '2016_01_01_000000_create_data_types_table',
            '2016_05_19_173453_create_menu_table',
            '2016_10_21_190000_create_roles_table',
            '2016_10_21_190000_create_settings_table',
            '2016_11_30_135954_create_permission_table',
            '2016_11_30_141208_create_permission_role_table',
            '2016_12_26_201236_data_types__add__server_side',
            '2017_01_13_000000_add_route_to_menu_items_table',
            '2017_01_14_005015_create_translations_table',
            '2017_01_15_000000_make_table_name_nullable_in_permissions_table',
            '2017_03_06_000000_add_controller_to_data_types_table',
            '2017_04_21_000000_add_order_to_data_rows_table',
            '2017_07_05_210000_add_policyname_to_data_types_table',
            '2017_08_05_000000_add_group_to_settings_table',
            '2017_11_26_013050_add_user_role_relationship',
            '2017_11_26_015000_create_user_roles_table',
            '2018_03_11_000000_add_user_settings',
            '2018_03_14_000000_add_details_to_data_types_table',
            '2018_03_16_000000_make_settings_value_nullable',
            '2020_04_13_075317_add_nested_controller_to_data_types',
        ];

        foreach ($migrations as $index => $migartion) {
            if (! file_exists(database_path("migrations/{$migartion}.php"))) {
                unset($migrations[$index]);
            }
        }

        if (count($migrations) > 0 && $this->confirm('Do you wish to delete those files?')) {
            foreach ($migrations as $index => $migartion) {
                unlink(database_path("seeds/{$migartion}.php"));
            }
        }

        if (count($migrations) === 0) {
            $this->info('Old migartions cleared...');
        }
    }

    protected function deleteOldResources()
    {
        if (! $this->confirm('Do you want to delete old views? ' . resource_path('views/vendor/voyager'))) {
            return true;
        }

        voyager_rrmdir(resource_path('views/vendor/voyager'));
        $this->info('All old views deleted.');
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Looking for old voyager files...');
        $this->deleteOldSeeders();
        $this->deleteOldMigrations();
        $this->deleteOldResources();

        $this->info('Publishing the Voyager assets, database, and config files');

        $this->call('vendor:publish', [
            '--provider' => VoyagerServiceProvider::class, '--tag' => ['seeds'], '--force'
        ]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Attempting to set Voyager User model as parent to App\User');
        if (file_exists(app_path('User.php'))) {
            $str = file_get_contents(app_path('User.php'));

            if ($str !== false) {
                $str = str_replace('extends Authenticatable', "extends \TCG\Voyager\Models\User", $str);

                file_put_contents(app_path('User.php'), $str);
            }
        } else {
            $this->warn('Unable to locate "app/User.php".  Did you move this file?');
            $this->warn('You will need to update this manually.  Change "extends Authenticatable" to "extends \TCG\Voyager\Models\User" in your User model');
        }

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();

        $process = new Process([$composer.' dump-autoload']);
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

        $this->info('Adding Voyager routes to routes/web.php');

        $routes_contents = $filesystem->get(base_path('routes/web.php'));

        if (false === strpos($routes_contents, 'Voyager::routes()')) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\n\nRoute::group(['prefix' => 'admin'], function () {\n    Voyager::routes();\n});\n"
            );
        }

        $this->info('Seeding data into the database');
        $this->seed('VoyagerDatabaseSeeder');

        $this->call('vendor:publish', [
            '--provider' => VoyagerServiceProvider::class, '--tag' => ['config', 'voyager_avatar', 'voyager_webfonts']
        ]);

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy');
    }
}
