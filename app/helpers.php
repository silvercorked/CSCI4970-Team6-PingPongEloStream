<?php

if (!function_exists('elo_probability')) {
    // Probability team w/ elo 2
    function elo_probability (float $elo1, float $elo2): float {
        return 1.0 * 1.0 / (1 + 1.0 *
            pow(10, 1.0 * ($elo1 - $elo2) / 400));
    }
}

if (!function_exists('elo_rating_update')) {
    // update elo ratings of team 1 and team 2
    function elo_rating_update(float $elo1, float $elo2, bool $team1Won = true, int $K = 30) {
        $pB = elo_probability($elo1, $elo2);
        $pA = elo_probability($elo2, $elo1);
        if ($team1Won) {
            $elo1 += $K * (1 - $pA);
            $elo2 += $K * (0 - $pB);
        }
        else { // team 2 won
            $elo1 += $K * (0 - $pA);
            $elo2 += $K * (1 - $pB);
        }
        return [$elo1, $elo2];
    }
}
