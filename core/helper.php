<?php
defined('ABSPATH') || exit;

/**
 * Debugs the given data by printing it in a pre-formatted way.
 *
 * @param mixed $data The data to be debugged.
 * @return void
 */
function azdebugg($data)
{
    error_log(json_encode($data));
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

/**
 * Display the contents of a variable in a formatted way.
 *
 * @param mixed $data The variable to be displayed.
 * @return void
 */
function azdebug($data)
{
    error_log(json_encode($data));
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}
// Recursive function to list files in a directory and its subdirectories
function list_files_recursively($dir, $allowed_extensions = array(), $explode_path = null)
{
    $files = array();

    if (is_dir($dir)) {
        $contents = scandir($dir);
        foreach ($contents as $item) {
            if ($item != '.' && $item != '..') {
                $itemPath = $dir . '/' . $item;
                if (is_dir($itemPath)) {
                    // Recursively list files in subdirectories
                    $subdirFiles = list_files_recursively($itemPath, $allowed_extensions, $explode_path);
                    $files = array_merge($files, $subdirFiles);
                } else {
                    // Check if the file has an allowed extension
                    $file_extension = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION));
                    if (in_array($file_extension, $allowed_extensions)) {
                        $itemPathOnly = $explode_path ? explode($explode_path, $itemPath)[1] : $itemPath;
                        $files[] = str_replace('//', '/', $itemPathOnly);
                    }
                }
            }
        }
    }

    return $files;
}

function removeFolder($folderPath)
{
    // Remove all files and subdirectories within the folder
    $files = glob($folderPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } elseif (is_dir($file)) {
            removeFolder($file);
        }
    }

    // Remove the folder itself
    rmdir($folderPath);
}
