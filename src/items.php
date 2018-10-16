<?php

$items = $app['controllers_factory'];

$items->get('/', function () use ($app) {
    
    $sql = "SELECT `id`, `username`, `type`, `timestamp`, `title`, `score`,
                IFNULL(`url`, '') AS `url`,
                IFNULL(`descendants`, 0) AS `descendants`
            FROM
                `items`
            WHERE
                `parent_id` IS NULL
            ORDER BY
                `timestamp`, `score` DESC
            LIMIT 30";
    
    $items = $app['db']->fetchAll($sql);
    
    return $app['twig']->render('items.html.twig', ['items' => $items]);
    
})->bind('items');

return $items;