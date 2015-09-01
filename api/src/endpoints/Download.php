<?php
/**
 * Download
 *
 * This REST module hooks on
 * following URLs
 *
 * /download/:plugin_id
 */

use \API\Core\Tool;
use \API\Model\Plugin;
use \API\Model\PluginDownload;
use \Illuminate\Database\Capsule\Manager as DB;
use \API\OAuthServer\OAuthHelper;

$download = function($key) use($app) {
   OAuthHelper::needsScopes(['plugin:download']);

   $plugin = Plugin::where('key', '=', $key)->first();

   $plugin_download = new PluginDownload();
   $plugin_download->downloaded_at = DB::raw('NOW()');
   $plugin_download->plugin_id = $plugin->id;
   $plugin_download->save();
   $app->redirect($plugin->download_url, 301);
};


// HTTP Rest Map
$app->get('/plugin/:key/download', $download);

$app->options('/plugin/:key/download', function($key){});