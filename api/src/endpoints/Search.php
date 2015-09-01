<?php
/**
 * Search
 *
 * This REST module hooks on
 * following URLs
 *
 * /search
 */

use \API\Core\Tool;
use \Illuminate\Database\Capsule\Manager as DB;
use \API\OAuthServer\OAuthHelper;

// Minimal length of search string
$search_min_length = 2;

$search = function() use($app) {
   OAuthHelper::needsScopes(['plugins:search']);

   global $search_min_length,
         $allowed_languages;

   $body = Tool::getBody();
   if ($body == NULL ||
      !isset($body->query_string) ||
      strlen($body->query_string) < $search_min_length ) {

     return Tool::endWithJson([
        "error" => "Your search string needs to ".
                 "have at least ".$search_min_length." chars"
     ], 400);
   }
   $query_string = $body->query_string;

   $_search = Tool::paginateCollection(
                   \API\Model\Plugin::short()
                              ->with('authors', 'versions', 'descriptions')
                              ->withDownloads()
                              ->withAverageNote()
                              ->descWithLang(Tool::getRequestLang())
                        ->where('name', 'LIKE', "%$query_string%")
                        ->orWhere('plugin_description.short_description', 'LIKE', "%$query_string%")
                        ->orWhere('plugin_description.long_description', 'LIKE', "%$query_string%"));
   Tool::endWithJson($_search);
};

$app->post('/search', $search);
$app->options('/search', function(){});