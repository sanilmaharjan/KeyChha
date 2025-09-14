<?php
class PersonalizedTypingCurve implements WordGenerator
{
    public function generateWords($userId, $count = 10)
    {
        $words = file("words.txt", FILE_IGNORE_NEW_LINES);

        // Step 1: categorize words
        $categorized = ["easy" => [], "medium" => [], "hard" => []];
        foreach ($words as $w) {
            $cat = $this->categorizeWord($w);
            $categorized[$cat][] = $w;
        }

        // Step 2: get user performance stats
        $stats = $this->getUserPerformance($userId);

        // Step 3: adjust ratio of easy/medium/hard words
        $mix = $this->decideMix($stats);

        // Step 4: pick words accordingly
        $session = [];
        foreach ($mix as $cat => $portion) {
            $subset = $this->pickRandom($categorized[$cat], round($count * $portion));
            $session = array_merge($session, $subset);
        }

        shuffle($session);
        return $session;
    }

    private function categorizeWord($word)
    {
        $len = strlen($word);
        $rareLetters = preg_match_all('/[zxq]/', $word);

        if ($len <= 4 && $rareLetters == 0)
            return "easy";
        if ($len <= 6 && $rareLetters <= 1)
            return "medium";
        return "hard";
    }

    private function getUserPerformance($userId)
    {
        // Example: fetch from DB
        // return ["easy"=>0.95, "medium"=>0.8, "hard"=>0.6];
        return [
            "easy" => 0.95,
            "medium" => 0.8,
            "hard" => 0.6
        ];
    }

    private function decideMix($stats)
    {
        $mix = ["easy" => 0.3, "medium" => 0.4, "hard" => 0.3];

        if ($stats["easy"] > 0.9) {
            $mix["medium"] += 0.1;
            $mix["easy"] -= 0.1;
        }
        if ($stats["medium"] > 0.85) {
            $mix["hard"] += 0.1;
            $mix["medium"] -= 0.1;
        }
        if ($stats["hard"] < 0.6) {
            $mix["hard"] -= 0.1;
            $mix["easy"] += 0.1;
        }

        return $mix;
    }

    private function pickRandom($arr, $n)
    {
        if ($n > count($arr))
            $n = count($arr);
        shuffle($arr);
        return array_slice($arr, 0, $n);
    }
}

?>