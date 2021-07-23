<?php

/**
 * A PHP function that will calculate the median value
 * of a nested array
 * 
 * @param array $arr The array that you want to get the median value of.
 * @param string $key The array key containing the values
 * @return boolean|float|int
 * @throws Exception If it's not an array
 */
function multiGetMedian($arr, $key) {
    var_dump($arr);

    //Make sure it's an array.
    if (!is_array($arr))
        throw new Exception('$arr must be an array!');
    //If it's an empty array, return FALSE.
    if (empty($arr))
        throw new Exception('$arr must be a populated array!');
    //Make sure it's a nested array.
    if (!array_key_exists(0, $arr))
        throw new Exception('$arr[0] must be an array!');
    //Make sure the specified key is in the nested array.
    if (!array_key_exists($key, $arr[0]))
        throw new Exception('$arr must contain $key!');

    //Save values into their own array
    $usedArr = [];
    foreach ($arr as $val)
        $usedArr[] = $val[$key];
    $arr = $usedArr;

    //Sort the array
    sort($arr);

    //Count how many elements are in the array.
    $num = count($arr);
    //Determine the middle value of the array.
    $middleVal = floor(($num - 1) / 2);
    //If the size of the array is an odd number,
    //then the middle value is the median.
    if($num % 2) { 
        return $arr[$middleVal];
    } 
    //If the size of the array is an even number, then we
    //have to get the two middle values and get their
    //average
    else {
        //The $middleVal var will be the low
        //end of the middle
        $lowMid = $arr[$middleVal];
        $highMid = $arr[$middleVal + 1];
        //Return the average of the low and high.
        return (($lowMid + $highMid) / 2);
    }
}

function multiGetMean($arr, $key) {
    //Make sure it's an array.
    if (!is_array($arr))
        throw new Exception('$arr must be an array!');
    //If it's an empty array, return FALSE.
    if (empty($arr))
        throw new Exception('$arr must be a populated array!');
    //Make sure it's a nested array.
    if (!array_key_exists(0, $arr))
        throw new Exception('$arr[0] must be an array!');
    //Make sure the specified key is in the nested array.
    if (!array_key_exists($key, $arr[0]))
        throw new Exception('$arr must contain $key!');

    //Save values into their own array
    $usedArr = [];
    foreach ($arr as $val)
        $usedArr[] = $val[$key];
    $arr = $usedArr;

    //Return average of array
    return array_sum($arr) / count($arr);
}

function multiGetStandardDeviation ($arr, $key) {
    //Make sure it's an array.
    if (!is_array($arr))
        throw new Exception('$arr must be an array!');
    //If it's an empty array, return FALSE.
    if (empty($arr))
        throw new Exception('$arr must be a populated array!');
    //Make sure it's a nested array.
    if (!array_key_exists(0, $arr))
        throw new Exception('$arr[0] must be an array!');
    //Make sure the specified key is in the nested array.
    if (!array_key_exists($key, $arr[0]))
        throw new Exception('$arr must contain $key!');

    //Save average of array while it's still a nested array
    $average = multiGetMean($arr, $key);

    //Save values into their own array
    $usedArr = [];
    foreach ($arr as $val)
        $usedArr[] = $val[$key];
    $arr = $usedArr;

    //Determine variance from average
    foreach ($arr as $key => $val)
        $arr[$key] = ($val - $average)**2;
    
    //Calculate variance
    $variance = array_sum($arr) / count($arr);

    //Return standard deviation
    return sqrt($variance);
}

?>