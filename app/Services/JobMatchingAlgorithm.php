<?php

namespace App\Services;

/**
 * ═════════════════════════════════════════════════════════════════════════
 * SKILLINK (PESO Magalang) — JOB MATCHING ALGORITHM & SERVICE DATA MODELS
 *
 * 100% Katumbas (1:1 Parity) ng JobMatchingAlgorithm.kt sa Mobile Application.
 * Sumusunod sa Specific Objectives 3, 4, 5, at 6 ng Thesis Manuscript.
 * ═════════════════════════════════════════════════════════════════════════
 * 
 * 🧠 WEIGHTED MULTI-FACTOR JOB MATCHING ENGINE (Objective 3)
 *
 * Formula:
 * Match Score = (Skill Match × 35%) +
 *               (Barangay Proximity × 25%) +
 *               (Rating Score × 20%) +
 *               (PESO Verification × 10%) +
 *               (Availability × 10%)
 * 
 * Sa ilalim ng Skill Match, ginagamit ang Vector Space Model (Cosine Similarity):
 * 
 *                  A • B           ∑ (A_i * B_i)
 *    Cosine(θ) = ─────────  =  ───────────────────────
 *                ||A|| ||B||    √(∑ A_i²) * √(∑ B_i²)
 * ═════════════════════════════════════════════════════════════════════════
 */
class JobMatchingAlgorithm
{
    // 27 Opisyal na Barangays ng Munisipalidad ng Magalang (Katulad sa Android)
    const MAGALANG_BARANGAYS = [
        "Camias", "Dolores", "Escaler", "La Paz", "Navaling",
        "San Agustin", "San Antonio", "San Fernando", "San Francisco",
        "San Ildefonso", "San Isidro", "San Jose", "San Miguel",
        "San Nicolas 1st", "San Nicolas 2nd", "San Pablo",
        "San Pedro 1st", "San Pedro 2nd", "San Roque", "San Vicente",
        "Santa Cruz", "Santa Lucia", "Santa Maria", "Santo Domingo",
        "Santo Niño", "Santo Rosario", "Turu"
    ];

    // Poblacion / Centro Cluster sa Magalang para sa proximity scoring
    const POBLACION_CLUSTER = [
        "San Nicolas 1st", "San Nicolas 2nd", "Santa Cruz",
        "San Pedro 1st", "San Pedro 2nd", "Santa Lucia", "Santo Rosario"
    ];

    /**
     * Compute proximity score sa pagitan ng client at worker sa Magalang
     * (Eksaktong kapareho ng calculateProximity sa Android)
     *
     * @param string $clientBarangay
     * @param string $workerBarangay
     * @return float 0.0 to 1.0
     */
    public static function calculateProximity(string $clientBarangay, string $workerBarangay): float
    {
        $clientBrgy = trim($clientBarangay);
        $workerBrgy = trim($workerBarangay);

        if (empty($clientBrgy) || empty($workerBrgy)) {
            return 0.50;
        }

        // 1. Parehong Barangay = 100% (1.0)
        if (strcasecmp($clientBrgy, $workerBrgy) === 0) {
            return 1.0;
        }

        // 2. Parehong nasa Poblacion/Centro Cluster = 80% (0.80)
        if (in_array($clientBrgy, self::POBLACION_CLUSTER) && in_array($workerBrgy, self::POBLACION_CLUSTER)) {
            return 0.80;
        }

        // 3. Ibang Barangay sa loob pa rin ng Magalang = 60% (0.60)
        foreach (self::MAGALANG_BARANGAYS as $brgy) {
            if (strcasecmp($brgy, $workerBrgy) === 0) {
                return 0.60;
            }
        }

        return 0.40;
    }

    /**
     * Compute ang Cosine Similarity sa pagitan ng hinahanap na kategorya at kasanayan ng manggagawa
     *
     * @param string $textA (Hinahanap ng Kliyente)
     * @param string $textB (Kasanayan ng Manggagawa)
     * @return float 0.0 to 1.0
     */
    public static function calculateCosineSimilarity(string $textA, string $textB): float
    {
        $tokensA = self::tokenize($textA);
        $tokensB = self::tokenize($textB);

        if (empty($tokensA) || empty($tokensB)) {
            return 0.0;
        }

        // Term Frequency (TF)
        $freqA = array_count_values($tokensA);
        $freqB = array_count_values($tokensB);

        $vocabulary = array_unique(array_merge(array_keys($freqA), array_keys($freqB)));

        $dotProduct = 0.0;
        $magnitudeA = 0.0;
        $magnitudeB = 0.0;

        foreach ($vocabulary as $term) {
            $valA = $freqA[$term] ?? 0;
            $valB = $freqB[$term] ?? 0;

            $dotProduct += ($valA * $valB);
            $magnitudeA += ($valA * $valA);
            $magnitudeB += ($valB * $valB);
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0.0 || $magnitudeB == 0.0) {
            return 0.0;
        }

        return round($dotProduct / ($magnitudeA * $magnitudeB), 4);
    }

    /**
     * Compute skill at category relevance (Eksaktong kapareho sa Android)
     *
     * @param string $targetCategory
     * @param string|null $workerSkills
     * @return float 0.0 to 1.0
     */
    public static function calculateSkillMatch(string $targetCategory, ?string $workerSkills): float
    {
        if (trim($targetCategory) === '') {
            return 0.80;
        }

        $target = strtolower(trim($targetCategory));
        $skills = strtolower(trim($workerSkills ?? ''));

        // Gamitin ang Cosine Similarity para sa mas tumpak na pagtutugma
        $cosineScore = self::calculateCosineSimilarity($target, $skills);
        if ($cosineScore > 0.0) {
            return $cosineScore;
        }

        // Fallback rule-based matching (katulad ng Android)
        if (strpos($skills, $target) !== false) {
            return 1.0;
        }

        if (strpos($skills, 'general') !== false || strpos($skills, 'all-around') !== false || strpos($skills, 'handyman') !== false) {
            return 0.75;
        }

        return 0.40;
    }

    /**
     * Pangunahing Function: Kino-compute ang kabuuang Match Percentage at nagra-rank
     * (Eksaktong katumbas ng rankWorkers sa Android)
     *
     * @param iterable $workers Listahan ng Skilled Workers mula sa Database
     * @param string $clientBarangay Barangay ng resident client
     * @param string $targetCategory Hinahanap na kategorya o trabaho
     * @return array Listahan ng workers na may matchPercentage, badgeLabel, atbp., naka-sort mula pinakamataas
     */
    public static function rankWorkers($workers, string $clientBarangay = 'San Nicolas 1st', string $targetCategory = ''): array
    {
        $results = [];

        foreach ($workers as $worker) {
            $skills = $worker->skills ?? '';
            $rating = (float)($worker->rating ?? 5.0);
            $isVerified = (bool)($worker->is_verified ?? false);
            $barangay = $worker->barangay ?? '';

            // Factor 1: Category Match (35%)
            $skillScore = self::calculateSkillMatch($targetCategory, $skills);

            // Factor 2: Barangay Proximity (25%)
            $proximityScore = self::calculateProximity($clientBarangay, $barangay);

            // Factor 3: Normalized Rating (20%) - Katulad ng Android (Rating coerceIn 1.0 to 5.0 / 5.0)
            $ratingScore = (max(1.0, min(5.0, $rating))) / 5.0;

            // Factor 4: PESO Verification Accreditation (10%)
            $verificationScore = $isVerified ? 1.0 : 0.70;

            // Factor 5: Availability (10%)
            $availabilityScore = 1.0;

            // Kabuuang Weighted Computation (0.0 to 1.0)
            $weightedScore = ($skillScore * 0.35) +
                             ($proximityScore * 0.25) +
                             ($ratingScore * 0.20) +
                             ($verificationScore * 0.10) +
                             ($availabilityScore * 0.10);

            $matchPercent = (int)round($weightedScore * 100);
            if ($matchPercent < 40) $matchPercent = 40;
            if ($matchPercent > 99) $matchPercent = 99;

            // Dynamic Badge Label (Eksaktong katulad sa Android)
            if ($proximityScore >= 1.0) {
                $badgeLabel = "{$matchPercent}% TOP MATCH • SAME BRGY";
            } elseif ($matchPercent >= 85) {
                $badgeLabel = "{$matchPercent}% HIGH MATCH • NEARBY";
            } else {
                $badgeLabel = "{$matchPercent}% COMPATIBLE";
            }

            $results[] = [
                'worker' => $worker,
                'match_percentage' => $matchPercent,
                'proximity_score' => $proximityScore,
                'skill_match_score' => $skillScore,
                'rating_score' => $ratingScore,
                'badge_label' => $badgeLabel,
            ];
        }

        // Pag-uuri mula sa may pinakamataas na Match Percentage patungo sa mababa
        usort($results, function ($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        return $results;
    }

    /**
     * Paghihiwalay ng mga salita (Tokenization)
     */
    private static function tokenize(string $text): array
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopWords = [
            'a', 'an', 'the', 'in', 'on', 'at', 'to', 'for', 'of', 'and', 'or', 'is', 'are',
            'with', 'ang', 'mga', 'ng', 'sa', 'at', 'para', 'na', 'kay', 'si', 'ni'
        ];

        return array_values(array_filter($words, function ($w) use ($stopWords) {
            return strlen($w) > 1 && !in_array($w, $stopWords);
        }));
    }
}
