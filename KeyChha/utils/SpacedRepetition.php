<?php
class SpacedRepetition implements WordGenerator
{
    public function generateWords($userId, $count = 10)
    {
        $words = $this->getUserHistory($userId);
        // pick words that are "due" for review
        $due = array_filter($words, fn($w) => $w['next_due'] <= time());
        return array_column(
            array_slice($due, 0, $count),
            'word'
        );
    }
    private function getUserHistory($userId)
    { // Example: return [['word'=>'apple','next_due'=>timestamp], ...]
    }
}
?>