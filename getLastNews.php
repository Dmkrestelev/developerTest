<?php
/**
 * Created by PhpStorm.
 * User: dmitrij
 * Date: 19/09/2019
 * Time: 00:17
 */

$count = 0;
$rss_url = "https://lenta.ru/rss";
$resXml = @file_get_contents($rss_url);

if ($resXml === false)
    die('Error connect: ' . $rss_url);
$xml = new \SimpleXMLElement($resXml);

if ($xml === false)
    die('Error parse: ' . $rss_url);

foreach ($xml->channel->item as $item) {

    $count++;
    // PHP_EOL - для консоли
    echo $count . '<br>' . PHP_EOL;
    if ($item->title) {
        echo 'навзвание: ' . $item->title . '<br>' . PHP_EOL;
    }

    if ($item->link) {
        echo 'ссылка: ' . '<a href="'. $item->link .'">'. $item->link .'</a><br>' . PHP_EOL;
    }

    if ($item->description) {
        echo 'анонс: ' . $item->description . '<br>' . PHP_EOL;
    }
    echo '<hr>';

    if ($count >= 5) {
        break;
    }
}
?>