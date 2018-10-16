<?php

$comments = $app['controllers_factory'];

$comments->get('/', function ($item_id) use ($app) {
    
    $story = $app['db']->fetchAssoc("SELECT * FROM `items` WHERE `id` = ?", [$item_id]);
    
    // Select all comments (including comments on comments) of a certain item
    $sql = "SELECT `id`, `parent_id`, `username`, `timestamp`, `content`
            FROM
                (SELECT * FROM `items` ORDER BY `parent_id`, `id`) AS `sorted_items`, 
                (SELECT @descendants := ?) AS `initialisation`
            WHERE
                FIND_IN_SET(`parent_id`, @descendants)
            AND
                `content` IS NOT NULL
            AND
                LENGTH(@descendants := CONCAT(@descendants, ',', `id`)) > 0
            ORDER BY
                `timestamp` ASC";
    
    $items = $app['db']->fetchAll($sql, [$item_id]);
    
    // Create a sorted array of items with indentation levels
    $comments = flatten(tree($items, $item_id));
    
    return $app['twig']->render('comments.html.twig', ['story' => $story, 'comments' => $comments]);
    
})->bind('comments');

return $comments;