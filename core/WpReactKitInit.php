<?php

namespace SAzizKhan\WpReactKit;

use SAzizKhan\WpReactKit\Features\CoreAdminMenu;
use SAzizKhan\WpReactKit\Features\PageTemplater;
use SAzizKhan\WpReactKit\Features\ReactBuildManager;
use SAzizKhan\WpReactKit\Features\ReactInjectIdFeature;
use SAzizKhan\WpReactKit\Features\ReactPageSetup;

defined('ABSPATH') || exit;

final class WpReactKitInit
{
    public function run()
    {
        try {
            (new ReactInjectIdFeature())->run();
            (new ReactPageSetup())->run();
            (new ReactBuildManager())->run();

            (new CoreAdminMenu())->load();
            (new PageTemplater())->load();
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            //throw $th;
        }
        return true;
    }
}
