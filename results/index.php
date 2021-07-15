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

    $x = 0;

    foreach ($exchangeItems as $itemID => $item) {
        $x++;

        if ($x > 1) continue;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://universalis.app/api/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        echo '<br>' . $itemID . '<br>';
        echo json_encode($item) . '<br>';

        echo curl_exec($curl);

        curl_close($curl);
    }
}

//Error out on nonexistant world
if (empty($desiredWorld) || !$worldExists)
    $results = "Sorry, an error has occurred (world not found or empty).";

?>

<?php echo $_GET['world']; ?>

<?php require('../assets/php/footer.php'); ?>