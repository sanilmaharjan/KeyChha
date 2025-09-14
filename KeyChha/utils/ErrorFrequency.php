<?php
class ErrorFrequency implements WordGenerator
{
    public function generateWords($userId, $count = 10)
    {
        $words = file("words.txt", FILE_IGNORE_NEW_LINES);
        $stats = $this->getUserStats($userId);

        $weighted = [];
        foreach ($words as $w) {
            $weight = 1;
            foreach (str_split($w) as $ch) {
                if (isset($stats[$ch])) {
                    $mistakes = $stats[$ch]['mistakes'];
                    $correct = $stats[$ch]['correct'] ?: 1;
                    $weight += $mistakes / $correct;
                }
            }
            for ($i = 0; $i < $weight; $i++) {
                $weighted[] = $w;
            }
        }
        shuffle($weighted);
        return array_slice($weighted, 0, $count);
    }

    private function getUserStats($userId)
    {
        // fetch stats from DB - this is just example data
        return [
            'a' => ['correct' => 50, 'mistakes' => 10],
            's' => ['correct' => 20, 'mistakes' => 15]
        ];
    }
}
?>