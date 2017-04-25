#!/usr/bin/env php
<?php

require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$settings = json_decode(file_get_contents('./settings.json'), TRUE);

$connection = new TwitterOAuth(
  $settings['consumer_key'], 
  $settings['consumer_secret'], 
  $settings['oauth_access_token'],
  $settings['oauth_access_token_secret']
);


// Run random search query as RSS, grab random object from results
$search_string = $settings['search_string'] . '&solr_profile=rss';
$rss = simplexml_load_string(file_get_contents($search_string));
$count = $rss->channel->item->count();
$rando = rand(0, $count - 1);
$object = $rss->channel->item[$rando];
$title = rtrim($object->title, '.');
$pid = $object->guid;
$link = $object->link;

$tn = $connection->upload('media/upload', ['media' => "$link/datastream/TN/view.jpg"]);
$statuses = $connection->post("statuses/update", array(
  "status" => "$title $link",
  'media_ids' => implode(',', [$tn->media_id_string])
));
