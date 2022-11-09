<?php

namespace App;

/**
 * Computes the probability that the team ranked with elo2 beats the team ranked with elo1.
 * Eb - probability of team b winning
 * Eb = 1 / (1 + 10 ^ ((Rb - Ra) / 400)), where Ra = elo1 and Rb = elo2
 * The 400 const is just a scale factor. It implies that 2 teams with an
 * elo difference of 400 would be different by a factor of 10 in "skill".
 *
 * @param float $elo1 - Elo rating of first team.
 * @param float $elo2 - Elo rating of second team.
 * @return float - Probability of team 2 beating team 1.
 */
function elo_probability (float $elo1, float $elo2): float {
    return 1.0 / (1.0 + pow(10, 1.0 * ($elo1 - $elo2) / 400));
}

/**
 * Uses the probability of winning, based solely on elo scores, to compute the change to
 * each elo score given which team wins.
 *
 * @param float $elo1 - Elo rating of first team.
 * @param float $elo2 - Elo rating of second team.
 * @param bool  $team1Won - True if team 1 won the game, false otherwise.
 * @param int $K - maximum amount of elo change possible.
 * @return Array - containing new elo1 at 0 and new elo2 at 1
 */
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

