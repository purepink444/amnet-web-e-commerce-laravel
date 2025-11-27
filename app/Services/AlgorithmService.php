<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AlgorithmService
{
    /**
     * Quick Sort algorithm implementation for efficient sorting
     */
    public function quickSort(array &$arr, int $low = 0, ?int $high = null): void
    {
        if ($high === null) {
            $high = count($arr) - 1;
        }

        if ($low < $high) {
            $pivotIndex = $this->partition($arr, $low, $high);
            $this->quickSort($arr, $low, $pivotIndex - 1);
            $this->quickSort($arr, $pivotIndex + 1, $high);
        }
    }

    /**
     * Partition function for Quick Sort
     */
    private function partition(array &$arr, int $low, int $high): int
    {
        $pivot = $arr[$high];
        $i = $low - 1;

        for ($j = $low; $j < $high; $j++) {
            if ($this->compare($arr[$j], $pivot) <= 0) {
                $i++;
                $this->swap($arr, $i, $j);
            }
        }

        $this->swap($arr, $i + 1, $high);
        return $i + 1;
    }

    /**
     * Merge Sort algorithm implementation
     */
    public function mergeSort(array &$arr, int $left = 0, ?int $right = null): void
    {
        if ($right === null) {
            $right = count($arr) - 1;
        }

        if ($left < $right) {
            $mid = (int)(($left + $right) / 2);

            $this->mergeSort($arr, $left, $mid);
            $this->mergeSort($arr, $mid + 1, $right);

            $this->merge($arr, $left, $mid, $right);
        }
    }

    /**
     * Merge function for Merge Sort
     */
    private function merge(array &$arr, int $left, int $mid, int $right): void
    {
        $n1 = $mid - $left + 1;
        $n2 = $right - $mid;

        $leftArr = array_slice($arr, $left, $n1);
        $rightArr = array_slice($arr, $mid + 1, $n2);

        $i = 0;
        $j = 0;
        $k = $left;

        while ($i < $n1 && $j < $n2) {
            if ($this->compare($leftArr[$i], $rightArr[$j]) <= 0) {
                $arr[$k] = $leftArr[$i];
                $i++;
            } else {
                $arr[$k] = $rightArr[$j];
                $j++;
            }
            $k++;
        }

        while ($i < $n1) {
            $arr[$k] = $leftArr[$i];
            $i++;
            $k++;
        }

        while ($j < $n2) {
            $arr[$k] = $rightArr[$j];
            $j++;
            $k++;
        }
    }

    /**
     * Binary Search algorithm for efficient lookups
     */
    public function binarySearch(array $arr, $target, callable $comparator = null): int
    {
        $left = 0;
        $right = count($arr) - 1;

        while ($left <= $right) {
            $mid = (int)(($left + $right) / 2);

            $comparison = $comparator ? $comparator($arr[$mid], $target) : $this->compare($arr[$mid], $target);

            if ($comparison === 0) {
                return $mid;
            } elseif ($comparison < 0) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return -1; // Not found
    }

    /**
     * Heap Sort algorithm implementation
     */
    public function heapSort(array &$arr): void
    {
        $n = count($arr);

        // Build max heap
        for ($i = (int)($n / 2) - 1; $i >= 0; $i--) {
            $this->heapify($arr, $n, $i);
        }

        // Extract elements from heap
        for ($i = $n - 1; $i > 0; $i--) {
            $this->swap($arr, 0, $i);
            $this->heapify($arr, $i, 0);
        }
    }

    /**
     * Heapify function for Heap Sort
     */
    private function heapify(array &$arr, int $n, int $i): void
    {
        $largest = $i;
        $left = 2 * $i + 1;
        $right = 2 * $i + 2;

        if ($left < $n && $this->compare($arr[$left], $arr[$largest]) > 0) {
            $largest = $left;
        }

        if ($right < $n && $this->compare($arr[$right], $arr[$largest]) > 0) {
            $largest = $right;
        }

        if ($largest !== $i) {
            $this->swap($arr, $i, $largest);
            $this->heapify($arr, $n, $largest);
        }
    }

    /**
     * Calculate statistical measures using efficient algorithms
     */
    public function calculateStatistics(Collection $data, string $field): array
    {
        $values = $data->pluck($field)->filter()->values()->all();

        if (empty($values)) {
            return [
                'count' => 0,
                'mean' => 0,
                'median' => 0,
                'mode' => null,
                'min' => 0,
                'max' => 0,
                'range' => 0,
                'variance' => 0,
                'std_dev' => 0,
            ];
        }

        // Sort for median and other calculations
        $sorted = $values;
        $this->quickSort($sorted);

        $count = count($values);
        $sum = array_sum($values);
        $mean = $sum / $count;

        // Median
        $mid = (int)($count / 2);
        $median = $count % 2 === 0
            ? ($sorted[$mid - 1] + $sorted[$mid]) / 2
            : $sorted[$mid];

        // Mode (most frequent value)
        $frequency = array_count_values($values);
        $mode = array_keys($frequency, max($frequency))[0];

        // Variance and Standard Deviation
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance /= $count;
        $stdDev = sqrt($variance);

        return [
            'count' => $count,
            'mean' => round($mean, 2),
            'median' => $median,
            'mode' => $mode,
            'min' => min($values),
            'max' => max($values),
            'range' => max($values) - min($values),
            'variance' => round($variance, 2),
            'std_dev' => round($stdDev, 2),
        ];
    }

    /**
     * K-Means clustering algorithm for customer segmentation
     */
    public function kMeansClustering(Collection $data, array $features, int $k, int $maxIterations = 100): array
    {
        $points = $data->map(function ($item) use ($features) {
            return array_map(fn($feature) => $item->$feature, $features);
        })->values()->all();

        if (empty($points)) {
            return [];
        }

        // Initialize centroids randomly
        $centroids = $this->initializeCentroids($points, $k);

        $clusters = [];
        $iterations = 0;

        while ($iterations < $maxIterations) {
            // Assign points to nearest centroid
            $clusters = $this->assignToClusters($points, $centroids);

            // Update centroids
            $newCentroids = $this->updateCentroids($points, $clusters, $k);

            // Check for convergence
            if ($this->centroidsConverged($centroids, $newCentroids)) {
                break;
            }

            $centroids = $newCentroids;
            $iterations++;
        }

        return [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'iterations' => $iterations,
        ];
    }

    /**
     * Initialize centroids for K-Means
     */
    private function initializeCentroids(array $points, int $k): array
    {
        $centroids = [];
        $usedIndices = [];

        for ($i = 0; $i < $k; $i++) {
            do {
                $index = rand(0, count($points) - 1);
            } while (in_array($index, $usedIndices));

            $usedIndices[] = $index;
            $centroids[] = $points[$index];
        }

        return $centroids;
    }

    /**
     * Assign points to nearest centroids
     */
    private function assignToClusters(array $points, array $centroids): array
    {
        $clusters = array_fill(0, count($centroids), []);

        foreach ($points as $point) {
            $minDistance = PHP_FLOAT_MAX;
            $closestCentroid = 0;

            foreach ($centroids as $index => $centroid) {
                $distance = $this->euclideanDistance($point, $centroid);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestCentroid = $index;
                }
            }

            $clusters[$closestCentroid][] = $point;
        }

        return $clusters;
    }

    /**
     * Update centroids based on cluster means
     */
    private function updateCentroids(array $points, array $clusters, int $k): array
    {
        $newCentroids = [];

        for ($i = 0; $i < $k; $i++) {
            if (empty($clusters[$i])) {
                // Keep old centroid if cluster is empty
                $newCentroids[] = $newCentroids[$i] ?? [0, 0];
                continue;
            }

            $clusterPoints = $clusters[$i];
            $dimensions = count($clusterPoints[0]);
            $centroid = array_fill(0, $dimensions, 0);

            // Calculate mean for each dimension
            foreach ($clusterPoints as $point) {
                for ($d = 0; $d < $dimensions; $d++) {
                    $centroid[$d] += $point[$d];
                }
            }

            for ($d = 0; $d < $dimensions; $d++) {
                $centroid[$d] /= count($clusterPoints);
            }

            $newCentroids[] = $centroid;
        }

        return $newCentroids;
    }

    /**
     * Check if centroids have converged
     */
    private function centroidsConverged(array $oldCentroids, array $newCentroids): bool
    {
        $threshold = 0.001;

        for ($i = 0; $i < count($oldCentroids); $i++) {
            $distance = $this->euclideanDistance($oldCentroids[$i], $newCentroids[$i]);
            if ($distance > $threshold) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate Euclidean distance between two points
     */
    private function euclideanDistance(array $point1, array $point2): float
    {
        $sum = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Recommendation algorithm using collaborative filtering
     */
    public function collaborativeFiltering(Collection $userPreferences, int $userId, int $limit = 5): array
    {
        // Simple user-based collaborative filtering
        $similarUsers = $this->findSimilarUsers($userPreferences, $userId);
        $recommendations = [];

        foreach ($similarUsers as $similarUserId => $similarity) {
            $userPrefs = $userPreferences->where('user_id', $similarUserId)->pluck('product_id')->toArray();
            $currentUserPrefs = $userPreferences->where('user_id', $userId)->pluck('product_id')->toArray();

            $newItems = array_diff($userPrefs, $currentUserPrefs);

            foreach ($newItems as $itemId) {
                if (!isset($recommendations[$itemId])) {
                    $recommendations[$itemId] = 0;
                }
                $recommendations[$itemId] += $similarity;
            }
        }

        // Sort by score and return top recommendations
        arsort($recommendations);
        return array_slice($recommendations, 0, $limit, true);
    }

    /**
     * Find similar users using cosine similarity
     */
    private function findSimilarUsers(Collection $userPreferences, int $userId): array
    {
        $userVectors = $this->buildUserVectors($userPreferences);
        $targetUserVector = $userVectors[$userId] ?? [];

        $similarities = [];

        foreach ($userVectors as $otherUserId => $otherVector) {
            if ($otherUserId !== $userId) {
                $similarity = $this->cosineSimilarity($targetUserVector, $otherVector);
                if ($similarity > 0) {
                    $similarities[$otherUserId] = $similarity;
                }
            }
        }

        arsort($similarities);
        return array_slice($similarities, 0, 10, true); // Top 10 similar users
    }

    /**
     * Build user-item preference vectors
     */
    private function buildUserVectors(Collection $userPreferences): array
    {
        $vectors = [];

        foreach ($userPreferences as $preference) {
            $userId = $preference->user_id;
            $productId = $preference->product_id;
            $rating = $preference->rating ?? 1; // Default rating if not provided

            if (!isset($vectors[$userId])) {
                $vectors[$userId] = [];
            }

            $vectors[$userId][$productId] = $rating;
        }

        return $vectors;
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    private function cosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;

        $allKeys = array_unique(array_merge(array_keys($vector1), array_keys($vector2)));

        foreach ($allKeys as $key) {
            $val1 = $vector1[$key] ?? 0;
            $val2 = $vector2[$key] ?? 0;

            $dotProduct += $val1 * $val2;
            $norm1 += $val1 * $val1;
            $norm2 += $val2 * $val2;
        }

        if ($norm1 === 0 || $norm2 === 0) {
            return 0;
        }

        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }

    /**
     * Generic comparison function
     */
    private function compare($a, $b): int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }

        return strcmp((string)$a, (string)$b);
    }

    /**
     * Swap two elements in array
     */
    private function swap(array &$arr, int $i, int $j): void
    {
        $temp = $arr[$i];
        $arr[$i] = $arr[$j];
        $arr[$j] = $temp;
    }
}
