<?php



ob_implicit_flush(true);

define('HOSTNAME', 'localhost');
define('USERNAME', '***');
define('PASSWORD', '***');
define('DATABASE', 'hackernews');

$ids = file('item-ids.txt');

$k = 0;

foreach ($ids as $id) {
    
    $json = get_item($id);
    
    if ($json && insert_database($json)) $k++;
}

echo "\nNumber of inserts:", $k;

// -------------------------------------------------------------------------------------------------

// Recursive database inserts
function insert_database(stdClass $item, int $parent_id = null) {
    
    $sql = "INSERT INTO items (id,parent_id,type,username,timestamp,title,content,url,score,descendants) VALUES (?,?,?,?,?,?,?,?,?,?)";
    
    $parent_id   = get_property($item, 'parent', $parent_id);
    $title       = get_property($item, 'title');
    $text        = get_property($item, 'text');
    $url         = get_property($item, 'url');
    $score       = get_property($item, 'score');
    $descendants = get_property($item, 'descendants');
    
    $values = array(
        $item->id,
        $parent_id,
        $item->type,
        $item->by ?? 'unknown',
        $item->time,
        $title,
        $text,
        $url,
        $score,
        $descendants
    );
    
    $sth = pdo_query($sql, $values);
    
    echo '.';
    
    if ( property_exists($item, 'kids') && count($item->kids) > 0 ) {
        foreach ($item->kids as $id) {
            $json = get_item($id);
            $json && insert_database($json, $item->id);
        }
    }
    
    return ( $sth->rowCount() > 0 );
}

function pdo_connect( $hostname, $username, $password, $database ) {
    try {
        return new PDO("mysql:dbname=$database;host=$hostname", $username, $password);
    } catch (PDOException $e) {
        echo "PDO Error: {$e->getMessage()}\n\n";
        exit;
    }
}

function pdo_query($sql, $args) {
    $dbh = pdo_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);
    $stmt = $dbh->prepare($sql);
    if (!$stmt->execute($args)) {
        echo "PDO Error:\n";
        print_r($stmt->errorInfo());
        // exit;
    }
    return $stmt;
}

function get_item($id) {
    $url = 'https://hacker-news.firebaseio.com/v0/item/%d.json';
    $text = file_get_contents_utf8(sprintf($url, $id));
    if (!$text) return false;
    return json_decode($text);
}

function get_property(stdClass $item, string $property, $default_value = null) {
    return property_exists($item, $property) ? $item->{$property} : $default_value;
}

function file_get_contents_utf8($filename) {
    $content = file_get_contents($filename);
    return mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
}

?>