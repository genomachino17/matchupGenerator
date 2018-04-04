<?php
error_reporting(E_ERROR);

// For decoding the matchups later on
$codex = [
    0 => 'Pat',
    1 => 'Dragons',
    2 => 'Kevin',
    3 => 'Gene',
    4 => 'Sean',
    5 => 'Angels',
    6 => 'Dave'
];

function debug($text) {
    global $argv;
    if ($argv[1] == '--debug') {
        echo "$text\n";
    }
}

// Go until we are satisfied with the outcome (see below)
while (true) {

    $restart = false;

    $players = [0, 1, 2, 3, 4, 5, 6];
    $totalGames = 28;
    $playersPerGame = 3;
    $maxPerPerson = $totalGames * $playersPerGame / count($players);

    // Desired result = Everyone is scheduled for N games
    $desiredResult = array_fill(0, 7, $maxPerPerson);

    // Declarations
    $matchupSchedule = [];
    $games = [];
    $gamesPlayedPerPerson = [];
    $randomThree = [];
    $i = 0;


    // Generate the matchups
    while ($i < $totalGames) {

        // Assume we haven't seen this matchup generated yet
        $alreadySaw = 0;

        while (true) {

            // If there aren't enough players to pick from, start over
            if (count($players) < $playersPerGame) {
                $restart = true;
                break;
            }

            // Pick 3 players at random
            $randomThree = array_rand($players, $playersPerGame);
            sort($randomThree);

            // See if this matchup already happened
            foreach ($matchupSchedule as $matchup) {
                debug("comparing " . implode(',', $randomThree) . " and " . implode(',', $matchup));
                if ($matchup === $randomThree) {
                    $alreadySaw++;
                }
            }

            if ($alreadySaw == 0) {
                debug(implode(',', $randomThree) . " is a new matchup");
                break;
            } else if ($alreadySaw == 1) {
                debug("already saw this. generate new");
                continue;
            } else if ($alreadySaw > 1) {
                debug("infinite loop. restarting");
                $restart = true;
                break;
            }
        }

        // If this is a net new matchup, add to the schedule of matchups
        $matchupSchedule[] = $randomThree;


        // Add the three participants to the countable array of games played per person
        foreach ($randomThree as $number) {
            $games[] = $number;
        }


        // If anyone already has 9 games played, remove them from future random number picks
        $gamesPlayedPerPerson = array_count_values($games);
        ksort($gamesPlayedPerPerson);
        foreach ($gamesPlayedPerPerson as $person => $gp) {
            if ($gp === $maxPerPerson) {
                if (isset($players[$person])) {     // No need to unset more than once per player
                    unset($players[$person]);
                }
            }
        }

        // Increment one successful match generation
        $i++;

        if ($restart) {
            break;
        }


//        echo implode(',', $players)  . "\n";
//        print_r($gamesPlayedPerPerson);
//        echo "press any key...\n";
//        $handle = fopen ("php://stdin","r");
//        $line = fgets($handle);
//        if(trim($line) != 'fdafdsafsa'){
//
//        }
//        fclose($handle);
//        system('clear');
    }

    // Check if we got the results we wanted
    $gamesPlayedPerPerson = array_count_values($games);
    ksort($gamesPlayedPerPerson);
    if ($desiredResult == $gamesPlayedPerPerson) {

        // Output things
        debug("Total matches: $totalGames");
        debug("Players per match: $playersPerGame");
        debug("Matches per person: $maxPerPerson");
        debug(print_r($gamesPlayedPerPerson, true));

        // Randomly shuffle the codex for more randomness when deciding matchups
        shuffle($codex);

        // Print the matchups
        foreach ($matchupSchedule as $matchup) {
            foreach ($matchup as $participant) {
                echo $codex[$participant] . ",";
            }
            echo "\n";
        }
        exit(0);

    } else {

        // If desired result not obtained, then try the whole program again...
        debug("Desired result failed. Starting over...");
        continue;
    }
}
