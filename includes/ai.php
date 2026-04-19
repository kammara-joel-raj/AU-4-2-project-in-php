<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/store.php';

if (!function_exists('size_chart_for_category')) {
    function size_chart_for_category($category)
    {
        return match (strtolower((string) $category)) {
            'accessories' => [
                'ONE SIZE' => ['chest' => [0, 999], 'waist' => [0, 999]],
            ],
            default => [
                'S' => ['chest' => [34, 37], 'waist' => [28, 31]],
                'M' => ['chest' => [38, 40], 'waist' => [32, 34]],
                'L' => ['chest' => [41, 43], 'waist' => [35, 37]],
                'XL' => ['chest' => [44, 47], 'waist' => [38, 41]],
            ],
        };
    }
}

if (!function_exists('recommend_size_for_product')) {
    function recommend_size_for_product(array $product, array $measurements)
    {
        $sizes = product_sizes($product);
        if (count($sizes) === 1 && $sizes[0] === 'ONE SIZE') {
            return ['size' => 'ONE SIZE', 'confidence' => 1.0];
        }

        $chest = (float) ($measurements['chest'] ?? 0);
        $waist = (float) ($measurements['waist'] ?? 0);
        $chart = size_chart_for_category($product['category'] ?? 'apparel');

        if ($chest <= 0 && $waist <= 0) {
            return ['size' => $sizes[0] ?? 'M', 'confidence' => 0.4];
        }

        foreach ($sizes as $size) {
            if (!isset($chart[$size])) {
                continue;
            }

            $target = $chart[$size];
            $chestMatch = $chest <= 0 || ($chest >= $target['chest'][0] && $chest <= $target['chest'][1]);
            $waistMatch = $waist <= 0 || ($waist >= $target['waist'][0] && $waist <= $target['waist'][1]);

            if ($chestMatch && $waistMatch) {
                return ['size' => $size, 'confidence' => 0.85];
            }
        }

        return ['size' => end($sizes) ?: 'XL', 'confidence' => 0.55];
    }
}

if (!function_exists('ai_enabled')) {
    function ai_enabled()
    {
        return AI_PROVIDER === 'replicate' && AI_API_TOKEN !== '' && AI_MODEL_VERSION !== '';
    }
}

if (!function_exists('file_to_data_uri')) {
    function file_to_data_uri($path)
    {
        if (!is_file($path)) {
            throw new RuntimeException('Missing file for AI processing.');
        }

        $size = filesize($path);
        if ($size === false || $size > AI_MAX_IMAGE_BYTES) {
            throw new RuntimeException('The uploaded image is too large for AI processing. Please try a smaller photo.');
        }

        $mime = mime_content_type($path) ?: 'image/jpeg';
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Unable to read uploaded image.');
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}

if (!function_exists('download_remote_file')) {
    function download_remote_file($url, $destination)
    {
        $ch = curl_init($url);
        $fp = fopen($destination, 'wb');
        if (!$fp) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $success = curl_exec($ch) !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) < 400;
        curl_close($ch);
        fclose($fp);

        return $success;
    }
}

if (!function_exists('cleanup_upload_directory')) {
    function cleanup_upload_directory($directory, $limit = null)
    {
        $limit = $limit !== null ? (int) $limit : APP_UPLOAD_CLEANUP_LIMIT;
        if ($limit < 1 || !is_dir($directory)) {
            return;
        }

        $files = array_filter(glob(rtrim($directory, '/\\') . '/*') ?: [], 'is_file');
        if (count($files) <= $limit) {
            return;
        }

        usort($files, static function ($a, $b) {
            return filemtime($a) <=> filemtime($b);
        });

        $staleFiles = array_slice($files, 0, max(0, count($files) - $limit));
        foreach ($staleFiles as $file) {
            @unlink($file);
        }
    }
}

if (!function_exists('replicate_request')) {
    function replicate_request($method, $endpoint, array $payload = null, array $headers = [])
    {
        $ch = curl_init();
        $requestHeaders = array_merge([
            'Authorization: Bearer ' . AI_API_TOKEN,
            'Content-Type: application/json',
            'Prefer: wait=60',
        ], $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.replicate.com/v1/' . ltrim($endpoint, '/'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $requestHeaders,
            CURLOPT_TIMEOUT => 90,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('AI request failed: ' . $error);
        }

        curl_close($ch);
        $decoded = json_decode($response, true);
        if ($status >= 400) {
            throw new RuntimeException($decoded['detail'] ?? $decoded['error'] ?? 'AI provider error.');
        }

        return $decoded;
    }
}

if (!function_exists('run_ai_tryon')) {
    function run_ai_tryon(array $product, $userImagePath)
    {
        if (!ai_enabled()) {
            return [
                'success' => false,
                'mode' => 'fallback',
                'message' => 'AI try-on is not configured yet. The local fit lab is ready instead.',
            ];
        }

        $category = product_ai_category($product);
        if ($category === null) {
            return [
                'success' => false,
                'mode' => 'fallback',
                'message' => 'AI try-on currently supports apparel and premium garments only.',
            ];
        }

        $productImagePath = dirname(__DIR__) . '/' . ltrim((string) $product['image'], '/');
        if (!is_file($productImagePath)) {
            return [
                'success' => false,
                'mode' => 'fallback',
                'message' => 'The product image is not available for AI try-on.',
            ];
        }

        $response = replicate_request('POST', 'predictions', [
            'version' => AI_MODEL_VERSION,
            'input' => [
                'garm_img' => file_to_data_uri($productImagePath),
                'garment_des' => trim(($product['name'] ?? '') . ' ' . ($product['description'] ?? '')),
                'human_img' => file_to_data_uri($userImagePath),
                'category' => $category,
                'crop' => true,
                'steps' => 30,
            ],
        ]);

        $status = $response['status'] ?? '';
        $pollUrl = $response['urls']['get'] ?? null;

        $attempts = 0;
        while ($pollUrl && !in_array($status, ['succeeded', 'failed', 'canceled'], true) && $attempts < 10) {
            usleep(2000000);
            $attempts++;
            $poll = replicate_request('GET', str_replace('https://api.replicate.com/v1/', '', $pollUrl), null, ['Prefer:']);
            $response = $poll;
            $status = $response['status'] ?? '';
        }

        if (($response['status'] ?? '') !== 'succeeded' || empty($response['output'])) {
            return [
                'success' => false,
                'mode' => 'fallback',
                'message' => $response['error'] ?? 'AI try-on is temporarily unavailable. Opening the local fit lab instead.',
            ];
        }

        $outputUrl = is_array($response['output']) ? ($response['output'][0] ?? null) : $response['output'];
        if (!$outputUrl) {
            return [
                'success' => false,
                'mode' => 'fallback',
                'message' => 'AI try-on did not return an image. Opening the local fit lab instead.',
            ];
        }

        $filename = 'vton_' . time() . '_' . (int) $product['id'] . '.png';
        $relativePath = 'uploads/generated/' . $filename;
        $fullPath = dirname(__DIR__) . '/' . $relativePath;

        if (!download_remote_file($outputUrl, $fullPath)) {
            return [
                'success' => true,
                'mode' => 'ai',
                'image_url' => $outputUrl,
            ];
        }

        cleanup_upload_directory(dirname($fullPath));

        return [
            'success' => true,
            'mode' => 'ai',
            'image_url' => $relativePath,
        ];
    }
}
