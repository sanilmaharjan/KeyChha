<?php
class NGram implements WordGenerator
{
    public function generateWords($userId, $count = 10)
    {
        $words = file("words.txt", FILE_IGNORE_NEW_LINES);
        $ngrams = $this->getUserNgrams($userId);

        $scored = [];
        foreach ($words as $w) {
            $score = 0;
            for ($i = 0; $i < strlen($w) - 1; $i++) {
                $ng = substr($w, $i, 2); // bigram
                if (isset($ngrams[$ng])) {
                    $mistakes = $ngrams[$ng]['mistakes'];
                    $correct = $ngrams[$ng]['correct'] ?: 1;
                    $score += $mistakes / $correct;
                }
            }
            $scored[$w] = $score + 1;
        }
        return $this->pickWeighted($scored, $count);
    }

    private function getUserNgrams($userId)
    {
        // fetch user bigram/trigram stats from DB
        return ["th" => ["correct" => 100, "mistakes" => 20]];
    }

    private function pickWeighted($weights, $count)
    {
        // same as WeightedRandom
    }
}
?>