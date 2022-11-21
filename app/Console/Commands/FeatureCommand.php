<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FeatureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fme:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add the necessary code and run the necessary migrations to integrate a feature into your store.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * Checks the arguments on the command.
         * if an argument is missing, list all possible arguments.
         * Search for each feature flagged
         */
    }

    /**
     * List all features available with each that is already installed highlighted
     * If there is a specific feature requested in the command, then display the feature name, its description, and if it is already installed
     */
    private function list($feature)
    {
        // If specific feature is requested, try to find that feature.
        // If not found, list all features
            // ask if details are desired in one of the features
            // $feature = $this->ask('If you want details on a given feature, type its name:');
        // If found, display the feature name, its description, and whether or not it is active
        // If no feature is requested, get all features
        // Loop through features list and display each with installed features highlighted
    }
    
    /**
     * Install the feature requested
     */
    private function install($feature)
    {
        // checks for specific feature requested
        // If feature is requested, try to find that feature.
        // If not found, list all features
            // ask if details are desired in one of the features
            // $feature = $this->ask('If you want details on a given feature, type its name:');
        // If found, find the method associated with that feature.
        // 
    }
}
