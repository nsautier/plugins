<?php

use \Illuminate\Database\Capsule\Manager as DB;
use \API\Core\Tool;
use \API\Model\Plugin;

use \API\OAuthServer\OAuthHelper;

$version_plugins = function($version) {
   OAuthHelper::needsScopes(['version', 'plugins']);

   $plugins = Tool::paginateCollection(Plugin::short()
                                             ->with('authors', 'versions', 'descriptions')
                                             ->withDownloads()
                                             ->withAverageNote()
                                             ->descWithLang(Tool::getRequestLang())
                                             ->withGlpiVersion($version));
   Tool::endWithJson($plugins);
};

$app->get('/version/:version/plugin', $version_plugins);
$app->options('/version/:version/plugin', function() {});