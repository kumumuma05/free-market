<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanStorage extends Command
{
    /**
     * artisanコマンド名
     */
    protected $signature = 'storage:clean';

    /**
     * 指定したディレクトリを削除
     */
    public function handle()
    {

        // 削除対象ディレクトリ
        $targets = [
            'item_image',
            'profile_image',
            'tmp',
        ];

        foreach ($targets as $dir) {
            // ディレクトリがある場合は削除する
            if (Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
                $this->info("Deleted: storage/app/public/{$dir}");
            } else {
                $this->info("No directory: {$dir}");
            }
        }

        $this->info('Storage cleaned Successfully');
    }
}
