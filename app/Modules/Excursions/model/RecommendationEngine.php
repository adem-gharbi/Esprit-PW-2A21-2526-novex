<?php

require_once __DIR__ . '/AiRecommendationTool.php';

class RecommendationEngine {
    private $conn;
    private $table = "excursion";
    private $aiTool;

    public function __construct($db) {
        $this->conn = $db;
        $this->aiTool = new AiRecommendationTool();
    }

    public function getAllExcursions() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY titre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExcursionById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSimilarExcursions(array $excursion, int $limit = 4) {
        $all = $this->getAllExcursions();
        $currentText = $this->normalizeText($excursion['titre'] . ' ' . $excursion['description']);
        $currentTopics = $this->aiTool->extractTopics($currentText);
        $recommendations = [];

        foreach ($all as $item) {
            if ($item['id'] == $excursion['id']) {
                continue;
            }

            $itemText = $this->normalizeText($item['titre'] . ' ' . $item['description']);
            $itemTopics = $this->aiTool->extractTopics($itemText);
            $topicScore = $this->aiTool->topicMatchScore($currentTopics, $itemTopics);
            $semanticScore = $this->aiTool->semanticSimilarityScore($currentText, $itemText);

            if (!empty($currentTopics) && $topicScore === 0 && $semanticScore < 0.25) {
                continue;
            }

            $score = 0;
            $score += $topicScore * 2;
            $score += (int)round($semanticScore * 12);
            $score += $this->priceCategoryScore($excursion['prix'], $item['prix']) * 2;
            $score += $this->durationCategoryScore($excursion['duree'], $item['duree']);

            if ($item['circuit_id'] === $excursion['circuit_id']) {
                $score += 3;
            }
            if ($item['guide_id'] === $excursion['guide_id']) {
                $score += 1;
            }

            if ($score >= 8) {
                $recommendations[] = ['item' => $item, 'score' => $score];
            }
        }

        usort($recommendations, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice(array_map(function ($row) {
            return $row['item'];
        }, $recommendations), 0, $limit);
    }

    public function recommendForPreferences(string $preferences, int $limit = 4) {
        $preferences = trim($preferences);
        if ($preferences === '') {
            return [];
        }

        $queryText = $this->normalizeText($preferences);
        if ($queryText === '') {
            return [];
        }

        $queryTopics = $this->aiTool->extractTopics($queryText);
        $all = $this->getAllExcursions();
        $recommendations = [];

        foreach ($all as $item) {
            $itemText = $this->normalizeText($item['titre'] . ' ' . $item['description']);
            $itemTopics = $this->aiTool->extractTopics($itemText);
            $topicScore = $this->aiTool->topicMatchScore($queryTopics, $itemTopics);
            $semanticScore = $this->aiTool->semanticSimilarityScore($queryText, $itemText);

            if (!empty($queryTopics) && $topicScore === 0 && $semanticScore < 0.22) {
                continue;
            }

            $score = 0;
            $score += $topicScore * 2;
            $score += (int)round($semanticScore * 12);
            $score += $this->priceCategoryScoreFromPreferences($preferences, $item['prix']) * 2;
            $score += $this->durationCategoryScoreFromPreferences($preferences, $item['duree']);

            if ($score >= 6) {
                $recommendations[] = ['item' => $item, 'score' => $score];
            }
        }

        usort($recommendations, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice(array_map(function ($row) {
            return $row['item'];
        }, $recommendations), 0, $limit);
    }

    private function normalizeText(string $text): string {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function keywordOverlapScore(string $textA, string $textB): int {
        if ($textA === '' || $textB === '') {
            return 0;
        }

        $wordsA = array_filter(explode(' ', $textA));
        $wordsB = array_filter(explode(' ', $textB));
        $common = array_intersect($wordsA, $wordsB);
        return min(count($common), 6);
    }

    private function priceCategoryScore($priceA, $priceB): int {
        $categoryA = $this->priceCategory($priceA);
        $categoryB = $this->priceCategory($priceB);
        return $categoryA === $categoryB ? 2 : 0;
    }

    private function priceCategoryScoreFromPreferences(string $preferences, $price): int {
        $preferences = mb_strtolower($preferences, 'UTF-8');
        if (strpos($preferences, 'budget') !== false || strpos($preferences, 'cheap') !== false || strpos($preferences, 'affordable') !== false) {
            return $this->priceCategory($price) === 1 ? 3 : 0;
        }
        if (strpos($preferences, 'premium') !== false || strpos($preferences, 'luxury') !== false || strpos($preferences, 'exclusive') !== false) {
            return $this->priceCategory($price) === 3 ? 3 : 0;
        }
        return 0;
    }

    private function priceCategory($price): int {
        $price = (float) $price;
        if ($price <= 50) {
            return 1;
        }
        if ($price <= 120) {
            return 2;
        }
        return 3;
    }

    private function durationCategoryScore($durationA, $durationB): int {
        $categoryA = $this->durationCategory($durationA);
        $categoryB = $this->durationCategory($durationB);
        return $categoryA === $categoryB ? 1 : 0;
    }

    private function durationCategoryScoreFromPreferences(string $preferences, $duration): int {
        $preferences = mb_strtolower($preferences, 'UTF-8');
        if (strpos($preferences, 'short') !== false || strpos($preferences, 'half') !== false || strpos($preferences, 'half-day') !== false) {
            return $this->durationCategory($duration) === 1 ? 2 : 0;
        }
        if (strpos($preferences, 'long') !== false || strpos($preferences, 'full') !== false || strpos($preferences, 'day') !== false) {
            return $this->durationCategory($duration) === 3 ? 2 : 0;
        }
        return 0;
    }

    private function durationCategory($duration): int {
        $duration = mb_strtolower($duration, 'UTF-8');
        if (preg_match('/\d+\s*(hour|h|heures|heure|hrs)/', $duration)) {
            return 1;
        }
        if (preg_match('/(half|demi|matin|afternoon|après-midi)/', $duration)) {
            return 1;
        }
        if (preg_match('/(day|jour|journée)/', $duration)) {
            return 3;
        }
        return 2;
    }

    private function attributeMatchScore(string $preferences, array $item): int {
        $score = 0;
        $preferences = mb_strtolower($preferences, 'UTF-8');
        $combined = $this->normalizeText($item['titre'] . ' ' . $item['description']);
        if (strpos($combined, 'adventure') !== false && strpos($preferences, 'adventure') !== false) {
            $score += 2;
        }
        if (strpos($combined, 'culture') !== false && strpos($preferences, 'culture') !== false) {
            $score += 2;
        }
        if (strpos($combined, 'family') !== false && strpos($preferences, 'family') !== false) {
            $score += 2;
        }
        if (strpos($combined, 'relax') !== false && strpos($preferences, 'relax') !== false) {
            $score += 2;
        }
        return $score;
    }
}
