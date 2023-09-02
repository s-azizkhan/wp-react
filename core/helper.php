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