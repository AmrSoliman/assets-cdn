<?php


namespace AmrSoliman\AssetsCdn;


use Illuminate\Console\Command;

class AssetsCdnUpdateCommand extends Command {

    protected $name = 'assets-cdn:update';

    protected $description = "Updates the asset file unique IDs so that the CDN updates them";

    public function fire()
    {
        \Cache::forever('assets-cdn::commitID', exec('git rev-parse HEAD'));
    }

}