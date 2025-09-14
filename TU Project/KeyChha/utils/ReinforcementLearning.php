<?php
class ReinforcementLearning implements WordGenerator
{
    private $qTable = []; // Q-values (state-action values)
    private $alpha = 0.5; // learning rate
    private $gamma = 0.9; // discount factor
    private $epsilon = 0.2; // exploration probability

    public function __construct()
    {
        // load Q-table from DB or file if you want persistence
    }

    public function generateWords($userId, $count = 10)
    {
        $words = file("words.txt", FILE_IGNORE_NEW_LINES);
        $session = [];

        for ($i = 0; $i < $count; $i++) {
            $session[] = $this->chooseAction($userId, $words);
        }

        return $session;
    }

    private function chooseAction($userId, $words)
    {
        // ε-greedy strategy: explore or exploit
        if ((mt_rand() / mt_getrandmax()) < $this->epsilon) {
            return $words[array_rand($words)]; // explore
        }

        // exploit: pick best word by Q-value
        $bestWord = $words[array_rand($words)];
        $bestValue = -INF;

        foreach ($words as $w) {
            $q = $this->getQ($userId, $w);
            if ($q > $bestValue) {
                $bestValue = $q;
                $bestWord = $w;
            }
        }
        return $bestWord;
    }

    public function updateReward($userId, $word, $reward)
    {
        // get current Q-value
        $oldQ = $this->getQ($userId, $word);

        // update using Q-learning formula
        $newQ = $oldQ + $this->alpha * ($reward + $this->gamma * $this->maxFutureQ($userId) - $oldQ);

        $this->setQ($userId, $word, $newQ);
    }

    private function getQ($userId, $word)
    {
        return $this->qTable[$userId][$word] ?? 0.0;
    }

    private function setQ($userId, $word, $value)
    {
        $this->qTable[$userId][$word] = $value;
        // TODO: save to DB or file if you want persistence
    }

    private function maxFutureQ($userId)
    {
        if (!isset($this->qTable[$userId]))
            return 0.0;
        return max($this->qTable[$userId]);
    }
}
