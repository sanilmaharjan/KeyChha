<?php
class MarkovChain implements WordGenerator
{
    private $transitions = [];

    public function __construct()
    {
        $words = file("words.txt", FILE_IGNORE_NEW_LINES);
        foreach ($words as $w) {
            $chars = str_split($w);
            for ($i = 0; $i < count($chars) - 1; $i++) {
                $this->transitions[$chars[$i]][$chars[$i + 1]] =
                    ($this->transitions[$chars[$i]][$chars[$i + 1]] ?? 0) + 1;
            }
        }
        // normalize probabilities
        foreach ($this->transitions as $ch => $nexts) {
            $sum = array_sum($nexts);
            foreach ($nexts as $n => $c) {
                $this->transitions[$ch][$n] = $c / $sum;
            }
        }
    }

    public function generateWords($userId, $count = 10)
    {
        $words = [];
        for ($i = 0; $i < $count; $i++) {
            $words[] = $this->generateWord();
        }
        return $words;
    }

    private function generateWord($length = 6)
    {
        $keys = array_keys($this->transitions);
        $current = $keys[array_rand($keys)];
        $word = $current;
        for ($i = 1; $i < $length; $i++) {
            if (!isset($this->transitions[$current]))
                break;
            $rand = mt_rand() / mt_getrandmax();
            $cumulative = 0;
            foreach ($this->transitions[$current] as $next => $prob) {
                $cumulative += $prob;
                if ($rand <= $cumulative) {
                    $word .= $next;
                    $current = $next;
                    break;
                }
            }
        }
        return $word;
    }
}
?>