<?php
require('../assets/php/header.php');
require('../assets/php/exchangeItems.php');

//Set up variables
$desiredWorld = $_GET['world'];
$worldExists = false;
$worldName = '';
$resultData = [];
$results = '';
$salesVelocityRanking = [
    'flowing',
    'perfect',
    'great',
    'good',
    'okay',
    'meh',
    '?'
];

//Load data
$worldList = file_get_contents('../assets/js/worldList.js');
$worldList = str_replace('let serverList = ', '', $worldList);
$worldList = str_replace(';', '', $worldList);
$worldList = json_decode($worldList);


//Check world exists
if (!empty($desiredWorld)) {
    foreach ($worldList as $world) {
        if ($worldExists) continue;
        if ($desiredWorld == $world->world) {
            $worldExists = true;
            $worldName = $world->world;
        }
    }

    //Loop through all items you can get from exchanging seals
    foreach ($exchangeItems as $itemID => $item) {

        //Request the item market information from Universalis
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://universalis.app/api/' . $worldName . '/' . $itemID);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        //Set up variables
        $salesLastThreeHour = 0;
        $salesLastDay = 0;
        $salesLastTwoDay = 0;
        $time = time();
        $threeHoursAgo = $time - (60*60*3);
        $twoDaysAgo = $time - (60*60*24*2);
        $oneDayAgo = $time - (60*60*24);
        $price = 0;
        $lastSoldPrice = 0;
        $efficiency = 0;

        //Determine the price to use
        $price = (int)$output->listings[0]->pricePerUnit;
        $price--;
        $lastSoldPrice = (int)$output->recentHistory[0]->pricePerUnit;

        //Determine the efficiency
        $efficiency = $price / $item[0];
        $efficiency = round($efficiency, 2);

        //Determine the recent sales
        foreach ($output->recentHistory as $sale) {
            $timestamp = $sale->timestamp;
            if ($timestamp > $twoDaysAgo) $salesLastTwoDay++;
            if ($timestamp > $oneDayAgo) $salesLastDay++;
            if ($timestamp > $threeHoursAgo) $salesLastThreeHour++;
        }

        //Rate the sale speed
        $salesVelocity = '?';
        if ($salesLastTwoDay > 10)
            $salesVelocity = '<p class="text-red-500">meh</p>';
        if ($salesLastTwoDay > 20)
            $salesVelocity = '<p class="text-gray-300">okay</p>';
        if ($salesLastDay > 10)
            $salesVelocity = '<p class="text-yellow-300">good</p>';
        if ($salesLastDay > 25)
            $salesVelocity = '<p class="text-green-300">great</p>';
        if ($salesLastThreeHour > 20)
            $salesVelocity = '<p class="text-pink-400">perfect</p>';
        if ($salesLastThreeHour > 30)
            $salesVelocity = '<p class="text-purple-400">flowing!</p>';

        //Make sure to close out the API request
        curl_close($curl);

        //Append raw data (mostly)
        $resultData[] = [
            'itemID' => $itemID,
            'itemName' => $item[1],
            'itemRankTab' => $item[2],
            'itemTab' => $item[3],
            'price' => $price,
            'lastSoldFor' => $lastSoldPrice,
            'efficiency' => $efficiency,
            'speed' => $salesVelocity, //formatted
            'sales' => [
                'threeHours' => $salesLastThreeHour,
                'oneDay' => $salesLastDay,
                'twoDays' => $salesLastTwoDay
            ]
        ];
    }

    var_dump($resultData);
}

//Error out on nonexistant world
if (empty($desiredWorld) || !$worldExists)
    $results = "Sorry, an error has occurred (world not found or empty).";

?>

<br><br>

<p>
    These are the results for the most efficient items to buy with seals and sell for gil on the market on <u><?php echo $worldName; ?></u> - excluding furniture items.
    <br>
    The top result is the most efficient item that is selling the quickest you can just nab and start selling now.
    <br><br>
    If you're intent on getting more information, you can scroll through the list and see the NAME of the item (and in parentheses, the rank tab that the item is in, and which material tab it is in), PRICE to sell at per item (1 gil under the lowest listing; in parentheses next to the price is the exact price the item last sold for), EFFICIENCY to see gil per seal, SPEED to get a rating of how much has been selling (worst to best: red, gray, yellow, green, pink, purple), and SALES (per time period).
</p>

<div class="mx-auto place-items-center justify-center">
    <?php echo $results; ?>
</div>

<?php require('../assets/php/footer.php'); ?>