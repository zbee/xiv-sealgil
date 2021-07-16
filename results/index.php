<?php
require('../assets/php/header.php');
require('../assets/php/exchangeItems.php');

date_default_timezone_set('Europe/London');

//The styling for each item
$itemFormat = <<<FRM
<div class="mx-auto place-items-center justify-center bg-#COLOR rounded-lg mt-5 py-2 px-5 box-border flex flex-wrap text-gray-300">

    <div class="w-3/6 text-left" title="The name of the item you might want to buy for seals and sell on the market. Last uploaded: #LAST_UPLOAD (server time)">
        #ITEM_NAME
    </div>

    <div class="w-2/6 text-justify font-bold" title="The price you should list this on the market for.">
        #PRICEgil
    </div>

    <div class="w-1/6 text-right text-xs" title="The efficiency of the Seals to Gil conversion.">
        #EFFICIENCY&eta;
    </div>

    <div class="w-3/6 text-left text-gray-400 text-sm" title="Where you can find the item for purchase in the GC Seal exchange window.">
        #ITEM_INFO
    </div>

    <div class="w-3/6 text-right text-sm" title="An arbitrary scale for how often this item is selling. Specifically: #SOLD sold in the last 2 days">
        #SPEED
    </div>

</div>
FRM;

//Set up variables
$desiredWorld = $_GET['world'];
$worldExists = false;
$worldName = '';
$resultData = [];
$resultSelection = [];
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
$dateColoring = [
    'gray-800',
    'gray-900',
    'yellow-900'
];
$time = time();
$fiveMinutesAgo = $time - (60*5);
$thirtyMinutesAgo = $time - (60*5);
$threeHoursAgo = $time - (60*60*3);
$twoDaysAgo = $time - (60*60*24*2);
$oneDayAgo = $time - (60*60*24);
$uploadedWithinFive = 0;
$uploadedWithinThirty = 0;
$highVelocityItems = 0;
$goodVelocityItems = 0;

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
        $price = 0;
        $lastSoldPrice = 0;
        $efficiency = 0;
        $withinFive = false;
        $withinThirty = false;
        $coloring = $dateColoring[2];

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

        if ($salesVelocity > 3) $highVelocityItems++;
        if ($salesVelocity > 2) $goodVelocityItems++;

        //Check upload date
        if ($output->lastUploadTime > $fiveMinutesAgo) {
            $uploadedWithinFive++;
            $withinFive = true;
        }
        if ($output->lastUploadTime > $thirtyMinutesAgo) {
            $uploadedWithinThirty++;
            $withinThirty = true;
        }

        //Determine age coloring
        if ($output->lastUploadTime > $twoDaysAgo) $coloring = $dateColoring[1];
        if ($output->lastUploadTime > $threeHoursAgo) $coloring = $dateColoring[0];

        //Make sure to close out the API request
        curl_close($curl);

        //Calculate the sort value
        $sort = $efficiency * $salesVelocity;

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
            ],
            'lastUpload' => $output->lastUploadTime,
            'withinFive' => $withinFive,
            'withinThirty' =>$withinThirty,
            'coloring' => $coloring
        ];
    }
    //Item Loop Over

    //Prune non-selling items
    //If there are a reasonable amount of good velocity items, prune the lowest
    if ($goodVelocityItems > 20) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 1)
                unset($resultData[$key]);
    }
    //If it's all good velocity, prune the two lowest velocities
    if ($goodVelocityItems > 50) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 2)
                unset($resultData[$key]);
    }
    //If it's mostly high velocity, prune the three lowest velocities
    if ($highVelocityItems > 30) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 3)
                unset($resultData[$key]);
    }

    //Determining the age of the data set
    $recentUpload = 'older than 30 minutes.';
    //Prune if the data set has recent information, but only if it's not mostly recent
    if ($uploadedWithinThirty > 10 && $uploadedWithinThirty < 50 && $uploadedWithinFive < 30) {
        $recentUpload = 'displayed are within last 30 minutes.';
        //As done below, sort by the upload date and choose only the top 10 items
        $pruned = [];
        $prune_keys = array_column($resultData, 'lastUpload');
        array_multisort($prune_keys, SORT_DESC, $resultData);
        for ($x = 0; $x < 10; $x++)
            $pruned[] = $resultData[$x];
        //Replace the data with the pruned data
        $resultData = $pruned;
    }
    //If it's mostly pretty recent, prune the oldest
    if ($uploadedWithinThirty > 50) {
        foreach ($resultData as $key => $item)
            if ($item['lastUpload'] < $thirtyMinutesAgo)
                unset($resultData[$key]);
    }
    //If it's mostly very recent, prune the oldest
    if ($uploadedWithinFive > 50) {
        foreach ($resultData as $key => $item)
            if ($item['lastUpload'] < $fiveMinutesAgo)
                unset($resultData[$key]);
    }
    if ($uploadedWithinThirty > 30)  $recentUpload = 'most are within last 30 minutes.';
    if ($uploadedWithinThirty > 50)  $recentUpload = 'all are within last 30 minutes.';
    if ($uploadedWithinFive > 10)  $recentUpload = 'displayed are within last 5 minutes.';
    if ($uploadedWithinFive > 30)  $recentUpload = 'most are within last 5 minutes.';
    if ($uploadedWithinFive > 50)  $recentUpload = 'all are within last 5 minutes.';

    //Choose top items to display, sorting by a key and then choosing the top two items three times
    $speed_keys = array_column($resultData, 'speed');
    array_multisort($speed_keys, SORT_DESC, $resultData);
    $resultSelection[] = $resultData[0];
    $resultSelection[] = $resultData[1];

    $sort_keys = array_column($resultData, 'sort');
    array_multisort($sort_keys, SORT_DESC, $resultData);
    $resultSelection[] = $resultData[0];
    $resultSelection[] = $resultData[1];

    $efficiency_keys = array_column($resultData, 'efficiency');
    array_multisort($efficiency_keys, SORT_DESC, $resultData);
    $resultSelection[] = $resultData[0];
    $resultSelection[] = $resultData[1];

    //Prune variables
    unset($resultData);

    //Format top items for display
    foreach ($resultSelection as $key => $result) {
        //Header for sections of two items
        if ($key == 0) $results .= '<b>Highest selling items</b><br>';
        if ($key == 2) $results .= '<br><b>Best-bet items</b><br>';
        if ($key == 4) $results .= '<br><b>Highest efficiency items</b><br>';

        //Format item
        $results .= str_replace(
            [
                '#COLORING',
                '#ITEM_NAME',
                '#LAST_UPLOAD',
                '#PRICE',
                '#EFFICIENCY',
                '#ITEM_INFO',
                '#SOLD',
                '#SPEED',
            ],
            [
                $result['coloring'],
                $result['itemName'],
                date("m-d H:i", $result['lastUpload']),
                $result['price'],
                $result['efficiency'],
                $result['itemRankTab'] . ', ' . $result['itemTab'],
                $result['sales']['twoDays'],
                $salesVelocityRanking[$result['speed']],
            ],
            $itemFormat
        );
    }
}

//Error out on nonexistant world
if (empty($desiredWorld) || !$worldExists) {
    $results = 'Sorry, an error has occurred (world not found or empty).';
    $recentUpload = '<b>None.</b>';
}
?>

<br><br>

<p>
    These are the results for the most efficient items to buy with seals and sell for gil on the market on <u><?php echo $worldName; ?></u> - excluding furniture items.
    <br>
    Data age available now: <u><?php echo $recentUpload; ?></u>
    <br>
    Hover over any field for more information; the title also has the data upload date, and the colored sales text includes recent sales.
</p>

<br><hr class="border-gray-600"><br>

<div class="mx-auto place-items-center justify-center">
    <?php echo $results; ?>
</div>

<?php require('../assets/php/footer.php'); ?>