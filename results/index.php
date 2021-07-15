<?php
require('../assets/php/header.php');
require('../assets/php/exchangeItems.php');

date_default_timezone_set('Europe/London');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//The styling for each item
$itemFormat = <<<FRM
<div class="mx-auto place-items-center justify-center bg-gray-800 rounded-lg mt-5 py-2 px-5 box-border flex text-gray-300">

    <div class="w-6/12 text-left">
        #ITEM_NAME
    </div>

    <div class="w-4/12 text-justify font-bold" alt="The price you should list this on the market for.">
        #PRICE
    </div>

    <div class="w-2/12 text-right text-xs" alt="The efficiency of the Seals to Gil conversion.">
        #EFFICIENCY
    </div>

    <br>

    <div class="w-6/12 text-left text-gray-400 text-sm" alt="Where you can find the item for purchase in the GC Seal exchange window.">
        #ITEM_INFO
    </div>

    <div class="w-6/12 text-right text-sm" alt="An arbitrary scale for how often this item is selling. Specifically: #SOLD sold in the last 2 days">
        #SPEED
    </div>

    #EXTRA

</div>
FRM;

//Set up variables
$desiredWorld = $_GET['world'];
$worldExists = false;
$worldName = '';
$resultData = [];
$results = '';
$salesVelocityRanking = [
    '<p class="text-red-600">?</p>',
    '<p class="text-red-500">meh</p>',
    '<p class="text-gray-300">okay</p>',
    '<p class="text-yellow-300">good</p>',
    '<p class="text-green-300">great</p>',
    '<p class="text-pink-400">perfect</p>',
    '<p class="text-purple-400">flowing!</p>',
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
        $output = json_decode(curl_exec($curl));

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
        if ($output->listings != null) {
            $price = (int)$output->listings[0]->pricePerUnit;
            $price--;
        }
        $lastSoldPrice = (int)$output->recentHistory[0]->pricePerUnit;

        //Determine the efficiency
        $efficiency = $price / (int)$item[0];
        $efficiency = round($efficiency, 2);

        //Determine the recent sales
        foreach ($output->recentHistory as $sale) {
            $timestamp = $sale->timestamp;
            if ($timestamp > $twoDaysAgo) $salesLastTwoDay++;
            if ($timestamp > $oneDayAgo) $salesLastDay++;
            if ($timestamp > $threeHoursAgo) $salesLastThreeHour++;
        }

        //Rate the sale speed
        $salesVelocity = 0;
        if ($salesLastTwoDay > 5) $salesVelocity = 1;
        if ($salesLastTwoDay > 10) $salesVelocity = 2;
        if ($salesLastDay > 5) $salesVelocity = 3;
        if ($salesLastDay > 10) $salesVelocity = 4;
        if ($salesLastThreeHour > 10) $salesVelocity = 5;
        if ($salesLastThreeHour > 20) $salesVelocity = 6;

        //Make sure to close out the API request
        curl_close($curl);

        //Calculate the sort value
        $specialLastSold = ($lastSoldPrice > 10000 ? 10000 : $lastSoldPrice) / 10000;
        $sort = $specialLastSold - (0.9 * (int)$item[0]);
        $sort *= $efficiency/3;
        $sort *= $salesVelocity/6;

        //Append raw data
        $resultData[] = [
            'itemID' => $itemID,
            'itemName' => $item[1],
            'sort' => $sort,
            'itemRankTab' => $item[2],
            'itemTab' => $item[3],
            'price' => $price,
            'lastSoldFor' => $lastSoldPrice,
            'efficiency' => $efficiency,
            'speed' => $salesVelocity,
            'sales' => [
                'threeHours' => $salesLastThreeHour,
                'oneDay' => $salesLastDay,
                'twoDays' => $salesLastTwoDay
            ]
        ];
    }

    $keys = array_column($resultData, 'sort');
    array_multisort($keys, SORT_DESC, $resultData);

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
    If you're intent on getting more information, you can scroll through the list and see the NAME of the item (and in parentheses, the rank tab that the item is in,
    and which material tab it is in), PRICE to sell at per item (1 gil under the lowest listing; in parentheses next to the price is the exact price the item last sold for),
    EFFICIENCY to see gil per seal, SPEED to get a rating of how much has been selling (worst to best: red, gray, yellow, green, pink, purple), and SALES (per time period).
</p>

<div class="mx-auto place-items-center justify-center">
    <?php echo $results; ?>
</div>

<?php require('../assets/php/footer.php'); ?>