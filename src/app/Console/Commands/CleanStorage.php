<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanStorage extends Command
{
    protected $signature = 'storage:clean';

    protected $description = 'Remove all uploaded storage files (item images, profile images, temp files).';

    public function handle()
    {
        $targets = [
            'item_image',
            'profile_image',
            'tmp',
        ];

        foreach ($targets as $dir) {
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
                $this->info("Deleted: storage/app/public/{$dir}");
            } else {
                $this->info("No directory: {$dir}");
            }
        }
    }
}
