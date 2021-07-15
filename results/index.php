<?php
require('../assets/php/header.php');
require('../assets/php/exchangeItems.php');

//Load data
$worldList = file_get_contents('../assets/js/worldList.js');
$worldList = json_decode($worldList);

$desiredWorld = $_GET['world'];
$worldExists = false;


//Check world exists
if (!empty($desiredWorld)) {
    if ($desiredWorld == $world['world']) $worldExists = true;

    foreach ($exchangeItems as $itemID => $item) {
        echo $itemID;
        echo $item;
    }
}

//Error out on nonexistant world
if (empty($desiredWorld) || !$worldExists)
    $results = "Sorry, an error has occurred (world not found or empty).";

?>

<?php echo $_GET['world']; ?>

<?php require('../assets/php/footer.php'); ?>