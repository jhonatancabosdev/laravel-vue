<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use ZipArchive;

class ExportStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fme:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip and export your storage files.';

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
        $public_dir = public_path();
        $zipFileName = 'artisanUploadsExport-' . date('m-d-Y_hia') . '.zip';
        $zip = new ZipArchive;
        
        
        if ($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {

            $path = storage_path('app/public');

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $name => $file)
            {
                // We're skipping all subfolders
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
    
                    // extracting filename with substr/strlen
                    $relativePath = substr($filePath, strlen($path) + 1);
    
                    $zip->addFile($filePath, $relativePath);
                }
            }    

            // Set Header
            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );
            // Close ZipArchive     
            $zip->close();
        }


    }
}
