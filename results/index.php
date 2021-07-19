<?php
require('../assets/php/header.php');

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
    'gray-900',
    'gray-800',
    'yellow-900'
];
$time = time();
$minutes = 60;
$hours = 60*60;
$days = 60*60*24;
$countVelocityWithinHighThreshold = 0;
$countVelocityWithinGoodThreshold = 0;
$countUploadedWithinNowThreshold = 0;
$countUploadedWithinRecentThreshold = 0;
$countEfficiencyWithinGoodThreshold = 0;
$countEfficiencyWithinHighThreshold = 0;


///////////////////////////////////////////////////////////////////////////
//Config
///////////////////////////////////////////////////////////////////////////

//Pruning/Rating thresholds
$thresholdEfficiencyGood = 0.5;
$thresholdEfficiencyHigh = 1.0;

$thresholdSaleVelocityOne = 5;
$thresholdSaleVelocityTwo = 10;
$thresholdSaleVelocityThree = 5;
$thresholdSaleVelocityFour = 10;
$thresholdSaleVelocityFive = 10;
$thresholdSaleVelocitySix = 20;

$thresholdSaleVelocityGood = 2;
$thresholdSaleVelocityHigh = 3;

//Time-ago thresholds
$thresholdUploadNow = $time - 5*$minutes;
$thresholdUploadRecent = $time - 30*$minutes;
$thresholdsalesWithinNowThreshold = $time - 3*$hours;
$thresholdSalesRecent = $time - 1*$days;
$thresholdsalesWithinNearThreshold = $time - 2*$days;

///////////////////////////////////////////////////////////////////////////
//Setup
///////////////////////////////////////////////////////////////////////////

//Include list of items you can buy for seals
require('../assets/php/exchangeItems.php');

//Use ffxiv server time
date_default_timezone_set('Europe/London');

//The styling for each item
$itemFormat = <<<FRM
<div class="mx-auto place-items-center justify-center bg-#COLOR rounded-lg mt-5
    py-2 px-5 box-border flex flex-wrap text-gray-300">

    <div class="w-3/6 text-left"
        title="The name of the item you might want to buy for seals and sell on
            the market. Last uploaded: #LAST_UPLOAD (server time)">
        #ITEM_NAME
    </div>

    <div class="w-2/6 text-justify font-bold"
        title="The price you should list this on the market for.">
        #PRICEgil
    </div>

    <div class="w-1/6 text-right text-xs"
        title="The efficiency of the Seals to Gil conversion.">
        #EFFICIENCY&eta;
    </div>

    <div class="w-3/6 text-left text-gray-400 text-sm"
        title="Where you can find the item for purchase in the GC Seal
            exchange window.">
        #ITEM_INFO
    </div>

    <div class="w-3/6 text-right text-sm"
        title="An arbitrary scale for how often this item is selling.
        Specifically: #SOLD sold in the last 2 days">
        #SPEED
    </div>

</div>
FRM;

//Load world list
$worldList = file_get_contents('../assets/js/worldList.js');
$worldList = str_replace('let serverList = ', '', $worldList);
$worldList = str_replace(';', '', $worldList);
$worldList = json_decode($worldList);

//Check selected world exists
if (!empty($desiredWorld)) {
    foreach ($worldList as $world) {
        if ($worldExists) continue;
        if ($desiredWorld == $world->world) {
            $worldExists = true;
            $worldName = $world->world;
        }
    }
}


///////////////////////////////////////////////////////////////////////////
//Loading data set
///////////////////////////////////////////////////////////////////////////

if ($worldExists) {
    //Loop through all items you can get from exchanging seals
    foreach ($exchangeItems as $itemID => $item) {

        //Request the item market information from Universalis
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,
            'https://universalis.app/api/' . $worldName . '/' . $itemID
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = json_decode(curl_exec($curl));

        //Set up individual variables
        $salesWithinNowThreshold = 0;
        $salesWithinRecentThreshold = 0;
        $salesWithinNearThreshold = 0;
        $price = 0;
        $lastSoldPrice = 0;
        $efficiency = 0;
        $uploadedWithinNowThreshold = false;
        $uploadedWithinRecentThreshold = false;
        $coloring = $dateColoring[2];

        //Determine the price to use, 1 gil under the lowest listed
        if ($output->listings != null) {
            $price = (int)$output->listings[0]->pricePerUnit;
            $price--;
        }
        $lastSoldPrice = (int)$output->recentHistory[0]->pricePerUnit;

        //Determine the seal->gil efficiency
        $efficiency = $price / (int)$item[0];
        $efficiency = round($efficiency, 2);

        //Running number of good efficiency items
        if ($efficiency > $thresholdEfficiencyGood)
            $countEfficiencyWithinGoodThreshold++;
        if ($efficiency > $thresholdEfficiencyHigh)
            $countEfficiencyWithinHighThreshold++;

        //Determine the recent sales
        foreach ($output->recentHistory as $sale) {
            $timestamp = $sale->timestamp;
            if ($timestamp > $thresholdsalesWithinNearThreshold)
                $salesWithinNearThreshold++;
            if ($timestamp > $thresholdSalesRecent)
                $salesWithinRecentThreshold++;
            if ($timestamp > $thresholdsalesWithinNowThreshold)
                $salesWithinNowThreshold++;
        }

        //Rate the sale speed
        $salesVelocity = 0;
        if ($salesWithinNearThreshold > $thresholdSaleVelocityOne)
            $salesVelocity = 1;
        if ($salesWithinNearThreshold > $thresholdSaleVelocityTwo)
            $salesVelocity = 2;
        if ($salesWithinRecentThreshold > $thresholdSaleVelocityThree)
            $salesVelocity = 3;
        if ($salesWithinRecentThreshold > $thresholdSaleVelocityFour)
            $salesVelocity = 4;
        if ($salesWithinNowThreshold > $thresholdSaleVelocityFive)
            $salesVelocity = 5;
        if ($salesWithinNowThreshold > $thresholdSaleVelocitySix)
            $salesVelocity = 6;

        //Running number of good velocity items
        if ($salesVelocity > $thresholdSaleVelocityGood)
            $countVelocityWithinGoodThreshold++;
        if ($salesVelocity > $thresholdSaleVelocityHigh)
            $countVelocityWithinHighThreshold++;

        //Check upload date
        $uploadTime = substr($output->lastUploadTime, 0, 10); //fix timestamp
        if ($uploadTime > $fiveMinutesAgo) {
            $countUploadedWithinNowThreshold++;
            $uploadedWithinNowThreshold = true;
        }
        if ($uploadTime > $thirtyMinutesAgo) {
            $countUploadedWithinRecentThreshold++;
            $uploadedWithinRecentThreshold = true;
        }

        //Determine age coloring
        if ($uploadTime > $thresholdSalesRecent)
            $coloring = $dateColoring[1];
        if ($uploadTime > $thresholdsalesWithinNowThreshold)
            $coloring = $dateColoring[0];

        //Calculate the sort value
        $sort = $efficiency * $salesVelocity;

        //Make sure to close out the API request
        curl_close($curl);

        //Store processed data
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
                'threeHours' => $salesWithinNowThreshold,
                'oneDay' => $salesWithinRecentThreshold,
                'twoDays' => $salesWithinNearThreshold
            ],
            'lastUpload' => $uploadTime,
            'uploadedWithinNowThreshold' => $uploadedWithinNowThreshold,
            'uploadedWithinRecentThreshold' =>$uploadedWithinRecentThreshold,
            'coloring' => $coloring
        ];
    }


    ///////////////////////////////////////////////////////////////////////////
    //Pruning, determining dataset averages
    ///////////////////////////////////////////////////////////////////////////

    //Prune low-efficiency items
    //If it's all good efficiency, prune all below the good efficiency threshold
    if ($countEfficiencyWithinGoodThreshold > 50) {
        foreach ($resultData as $key => $item)
            if ($item['efficiency'] < $thresholdEfficiencyGood)
                unset($resultData[$key]);
    }
    //If it's mostly high efficiency, prune all below
    if ($countEfficiencyWithinHighThreshold > 30) {
        foreach ($resultData as $key => $item)
            if ($item['efficiency'] < $thresholdEfficiencyHigh)
                unset($resultData[$key]);
    }

    //Prune non-selling items
    //If there are a reasonable amount of good velocity items, prune the lowest
    if ($countVelocityWithinGoodThreshold > 20) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 1)
                unset($resultData[$key]);
    }
    //If it's all good velocity, prune the two lowest velocities
    if ($countVelocityWithinGoodThreshold > 50) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 2)
                unset($resultData[$key]);
    }
    //If it's mostly high velocity, prune the three lowest velocities
    if ($countVelocityWithinHighThreshold > 30) {
        foreach ($resultData as $key => $item)
            if ($item['speed'] < 3)
                unset($resultData[$key]);
    }

    //Determining the age of the data set
    $recentUpload = 'older than 30 minutes';
    $uploadedFormat = '#w are within last #t minutes';

    //Prune if the data set has recent information,
    // but only if it's not mostly recent
    // (these numbers are static due to being a percentage of the dataset size)
    if (
        $countUploadedWithinRecentThreshold > 10
        && $countUploadedWithinRecentThreshold < 50
        && $countUploadedWithinNowThreshold < 30
    ) {
        $recentUpload = str_replace(
            ['#w','#t'],
            ['displayed', $thresholdUploadRecent],
            $uploadedFormat
        );
        //As below, sort by the upload date and choose only the top 10 items
        $pruned = [];
        $prune_keys = array_column($resultData, 'lastUpload');
        array_multisort($prune_keys, SORT_DESC, $resultData);
        for ($x = 0; $x < 10; $x++)
            $pruned[] = $resultData[$x];
        //Replace the data with the pruned data
        $resultData = $pruned;
    }
    //If it's mostly pretty recent, prune the oldest
    if ($countUploadedWithinRecentThreshold > 50) {
        foreach ($resultData as $key => $item)
            if ($item['lastUpload'] < $thresholdUploadRecent)
                unset($resultData[$key]);
    }
    //If it's mostly very recent, prune the oldest
    if ($countUploadedWithinNowThreshold > 50) {
        foreach ($resultData as $key => $item)
            if ($item['lastUpload'] < $thresholdUploadNow)
                unset($resultData[$key]);
    }
    if ($countUploadedWithinRecentThreshold > 30)
        $recentUpload = str_replace(
            ['#w','#t'],
            ['most', $thresholdUploadRecent],
            $uploadedFormat
        );
    if ($countUploadedWithinRecentThreshold > 55)
        $recentUpload = str_replace(
            ['#w','#t'],
            ['all', $thresholdUploadRecent],
            $uploadedFormat
        );
    if ($countUploadedWithinNowThreshold > 10)
        $recentUpload = str_replace(
            ['#w','#t'],
            ['displayed', $thresholdUploadNow],
            $uploadedFormat
        );
    if ($countUploadedWithinNowThreshold > 30)
    $recentUpload = str_replace(
        ['#w','#t'],
        ['most', $thresholdUploadNow],
        $uploadedFormat
    );
    if ($countUploadedWithinNowThreshold > 55)
    $recentUpload = str_replace(
        ['#w','#t'],
        ['all', $thresholdUploadNow],
        $uploadedFormat
    );


    ///////////////////////////////////////////////////////////////////////////
    //Displaying
    ///////////////////////////////////////////////////////////////////////////

    //Choose top items to display
    // sorting by a key and then choosing the top two items three times
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
                '#COLOR',
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
                date("M j H:i", $result['lastUpload']),
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

///////////////////////////////////////////////////////////////////////////
//Base page
///////////////////////////////////////////////////////////////////////////
?>

<div class="pt-4 text-md text-gray-300">

<br><br>

<p>
    These are the most efficient items to convert from seals to gil on <u><?php echo $worldName; ?></u> - excluding furniture.
    <br>
    Data age available now: <u><?php echo $recentUpload; ?></u>.
    <br>
    (lighter items are older, yellow is quite old, please refresh that data)
    <br>
    <span class="text-xs">Hover over any field for more information; the title also has the data upload date, and the colored sales text includes recent sales.</span>
</p>

<br><hr class="border-gray-600"><br>

<div class="mx-auto place-items-center justify-center">
    <?php echo $results; ?>
</div>

<?php require('../assets/php/footer.php'); ?>