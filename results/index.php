<?php
require('../assets/php/header.php');
require('../assets/php/exchangeItems.php');

//Load data
$worldList = file_get_contents('../assets/js/worldList.js');
$worldList = str_replace('let serverList = ', '', $worldList);
$worldList = str_replace(';', '', $worldList);
$worldList = json_decode($worldList);

$desiredWorld = $_GET['world'];
$worldExists = false;


//Check world exists
if (!empty($desiredWorld)) {
    foreach ($worldList as $world) {
        if ($worldExists) continue;
        if ($desiredWorld == $world->world) $worldExists = true;
    }

    $x = 0;

    foreach ($exchangeItems as $itemID => $item) {
        $x++;

        if ($x > 1) continue;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://universalis.app/api/' . $desiredWorld . '/' . $itemID);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        echo '<br>' . $itemID . '<br>';
        echo json_encode($item) . '<br>';
        echo '<br>listings';

        $output = json_decode(curl_exec($curl));
        var_dump($output->listings);
        echo '<br>';
        echo '<br>recenthistory';
        var_dump($output->recentHistory);
        echo '<br>';
        echo '<br>velocity';
        var_dump($output->regularSaleVelocity);
        echo '<br>';
        echo '<br>properties';

        var_dump(array_keys((array)$output));
        echo '<br>';

        curl_close($curl);
    }
}

//Error out on nonexistant world
if (empty($desiredWorld) || !$worldExists)
    $results = "Sorry, an error has occurred (world not found or empty).";

?>

<br><br>

<?php echo $_GET['world']; ?>

<?php require('../assets/php/footer.php'); ?>