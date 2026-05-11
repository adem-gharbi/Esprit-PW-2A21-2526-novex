<?php

class AiRecommendationTool {
    private array $topicMap = [
        'adventure' => ['adventure', 'adventurous', 'trek', 'hike', 'hiking', 'climb', 'climbing', 'offroad', 'outdoor', 'explore', 'safari', 'wild', 'drift', 'race', 'rally', 'motor', 'driving', 'roadtrip'],
        'culture' => ['culture', 'cultural', 'history', 'historic', 'heritage', 'museum', 'art', 'arts', 'traditional', 'local', 'city', 'urban'],
        'car' => ['car', 'cars', 'drive', 'drift', 'rally', 'roadtrip', 'automobile', 'auto', 'motor', 'driving', 'speed'],
        'beach' => ['beach', 'coast', 'ocean', 'surf', 'island', 'sea', 'shore', 'swim', 'sunset', 'waterfront'],
        'family' => ['family', 'kids', 'children', 'child', 'family-friendly', 'family friendly', 'play', 'fun', 'kids-friendly'],
        'luxury' => ['luxury', 'premium', 'exclusive', 'private', 'five-star', '5-star', 'VIP', 'deluxe', 'comfort', 'vip'],
        'nature' => ['nature', 'eco', 'wildlife', 'forest', 'mountain', 'river', 'park', 'green', 'natural', 'scenic', 'hike'],
        'food' => ['food', 'culinary', 'gastronomy', 'taste', 'tasting', 'market', 'cuisine', 'dining', 'restaurant'],
        'relax' => ['relax', 'spa', 'calm', 'tranquil', 'wellness', 'chill', 'rest', 'slow', 'serene']
    ];

    private array $stopWords = [
        'the','and','for','with','that','this','from','via','into','using','through','across','in','on','at','by','of','to','a','an','is','are','was','were','be','been','it','its','as','or','but','so','if','then'
    ];

    public function extractTopics(string $text): array {
        $text = $this->normalizeText($text);
        $found = [];

        foreach ($this->topicMap as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $found[] = $topic;
                    break;
                }
            }
        }

        return array_values(array_unique($found));
    }

    public function topicMatchScore(array $topicsA, array $topicsB): int {
        if (empty($topicsA) || empty($topicsB)) {
            return 0;
        }

        $common = array_intersect($topicsA, $topicsB);
        return min(count($common), 3) * 5;
    }

    public function semanticSimilarityScore(string $textA, string $textB): float {
        $vecA = $this->vectorizeText($textA);
        $vecB = $this->vectorizeText($textB);

        if (empty($vecA) || empty($vecB)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $term => $weightA) {
            $normA += $weightA * $weightA;
            if (isset($vecB[$term])) {
                $dot += $weightA * $vecB[$term];
            }
        }

        foreach ($vecB as $weightB) {
            $normB += $weightB * $weightB;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return round($dot / (sqrt($normA) * sqrt($normB)), 3);
    }

    public function normalizeTextForSearch(string $text): string {
        return $this->normalizeText($text);
    }

    private function vectorizeText(string $text): array {
        $tokens = $this->tokenize($text);
        $vector = [];

        foreach ($tokens as $token) {
            if ($this->isStopWord($token) || mb_strlen($token, 'UTF-8') < 2) {
                continue;
            }
            $vector[$token] = ($vector[$token] ?? 0) + 1;
        }

        return $vector;
    }

    private function tokenize(string $text): array {
        $text = $this->normalizeText($text);
        return array_filter(explode(' ', $text));
    }

    private function isStopWord(string $token): bool {
        return in_array($token, $this->stopWords, true);
    }

    private function normalizeText(string $text): string {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s-]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
