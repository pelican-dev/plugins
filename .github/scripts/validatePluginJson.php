<?php

/**
 * Recursively find all `plugin.json` files in a directory.
 *
 * @param  string  $dir  Directory to search in.
 * @return array List of file paths.
 */
function findPluginJsonFiles(string $dir): array
{
    $results = [];
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_dir($path)) {
            $results = array_merge($results, findPluginJsonFiles($path));
        } elseif ($file === 'plugin.json') {
            $results[] = $path;
        }
    }

    return $results;
}

/**
 * Validate a `plugin.json` file to ensure it does not contain a "meta" key.
 *
 * @param  string  $file  Path to the JSON file.
 * @param  string  $relativePath  Relative path to the file for better output clarity.
 * @return string|null Error message if invalid, or null if valid.
 */
function validateJsonFile(string $file, string $relativePath): ?string
{
    $content = file_get_contents($file);

    if ($content === false) {
        return "Failed to read $relativePath.";
    }

    $json = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Invalid JSON in $relativePath: " . json_last_error_msg();
    }

    if (array_key_exists('meta', $json)) {
        return "$relativePath contains a 'meta' key. Please remove it.";
    }

    return null;
}

$root = realpath(__DIR__ . '/../../');
$pluginJsonFiles = findPluginJsonFiles($root);

echo 'Found ' . count($pluginJsonFiles) . " plugin.json file(s) to validate.\n";

$errors = [];

foreach ($pluginJsonFiles as $file) {
    $relativePath = str_replace($root . DIRECTORY_SEPARATOR, '', $file);

    echo "Validating $relativePath...\n";
    $error = validateJsonFile($file, $relativePath);

    if ($error !== null) {
        $errors[] = $error;
    }
}

if (!empty($errors)) {
    echo "\nErrors found in the following plugin.json files:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    exit(1);
}

echo "All plugin.json files are valid!\n";
exit(0);
