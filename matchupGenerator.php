<?php
function readUserLine() {
    $fh = fopen('php://stdin', 'r');
    return trim(fgets($fh, 1024));
}

function factorial($number) {
    if ($number <= 1) {
        return 1;
    } else {
        return $number * factorial($number - 1);
    }
}

function occurrencesPerCombination($n, $k) {
    return nChooseK($n, $k) * ($k / $n);
}

/**
 * Returns the value from the equation:
 *
 *       n!
 *   ----------
 *    k!(n-k)!
 *
 * @param int $n
 * @param int $k
 * @return int
 */
function nChooseK($n, $k) {
    if ($k > $n) {
        return 0;
    } else {
        return factorial($n) / (factorial($k) * factorial($n - $k));
    }
}

/**
 * @param $count
 * @return array
 */
function initializePointers($count) {
    $pointers = [];

    for($i=0; $i<$count; $i++) {
        $pointers[] = $i;
    }

    return $pointers;
}

/**
 * @param $pointers
 * @param $arrLength
 * @return bool
 */
function incrementPointers(&$pointers, &$arrLength) {
    for($i=0; $i<count($pointers); $i++) {
        $currentPointerIndex = count($pointers) - $i - 1;
        $currentPointer = $pointers[$currentPointerIndex];

        if($currentPointer < $arrLength - $i - 1) {
            ++$pointers[$currentPointerIndex];

            for($j=1; ($currentPointerIndex+$j)<count($pointers); $j++) {
                $pointers[$currentPointerIndex+$j] = $pointers[$currentPointerIndex]+$j;
            }

            return true;
        }
    }

    return false;
}

/**
 * @param $arr
 * @param $pointers
 * @return array
 */
function getDataByPointers(&$arr, &$pointers) {
    $data = [];

    for($i=0; $i<count($pointers); $i++) {
        $data[] = $arr[$pointers[$i]];
    }

    return $data;
}

/**
 * This, and the functions called within it, were taken from this stackOverflow answer: https://stackoverflow.com/a/44036562
 * @param $list
 * @param $size
 * @return array
 */
function getCombinations($list, $size)
{
    $len = count($list);
    $result = [];
    $pointers = initializePointers($size);

    do {
        $result[] = getDataByPointers($list, $pointers);
    } while(incrementPointers($pointers, count($list)));

    return $result;
}


/**
 * Main
 */
try {



    // Ask number of participants in the league (the variable 'n' in a k-combination)
    echo "How many players in the tournament?\n";
    $participantCount = readUserLine();

    // Right now, only 3-player games are supported, so this value is hardcoded as 3 (the variable 'k' in a k-combination)
    $PLAYERS_PER_GAME = 3;

    // Create an array with N elements
    $range = range(0, $participantCount - 1);

    //TODO: add support for CLI arg with a list of names

    // Prompt the user for the names of each participant
    $players = [];
    for ($i = 1; $i <= $participantCount; $i++) {
        echo "Type a name for player [$i] then press Enter:\n";
        $players[] = readUserLine();
    }
    echo "Here are the players in the tournament:\n";
    print_r($players);

    // Find longest name in the list
    $longest = 0;
    foreach ($players as $player) {
        $len = strlen($player);
        if ($len > $longest) {
            $longest = $len;
        }
    }
    $longest += 2;  // Buffer 2 spaces

    // Ask user to approve the optimal number of games per player
    $optimalGamesPerPlayer = occurrencesPerCombination($participantCount, $PLAYERS_PER_GAME);
    echo "With $participantCount participants, the optimal number of games per player is $optimalGamesPerPlayer. Is this OK? (y/n)\n";
    while (true) {
        $response = readUserLine();
        if ($response == 'y') {
            echo "OK! Generating matchups now...\n";
            break;

        } else if ($response == 'n') {
            //TODO: develop functionality to tune games
            echo "OK, but functionality for generating more or fewer games isn't implemented yet! Exiting...\n";
            exit(1);

        } else {
            echo "Please respond with [y] or [n].\n";
        }
    }

    // Generate all possible matchup combinations
    shuffle($players);      // randomize the player name vs. seed
    $combinations = getCombinations($range, $PLAYERS_PER_GAME);

    foreach ($combinations as $combination) {
        foreach ($combination as $identifier) {
            printf("%-{$longest}s", $players[$identifier]);
        }
        echo "\n";
    }

} catch (Exception $e) {
    fwrite(STDERR, "$e\n");
    exit(2);
}