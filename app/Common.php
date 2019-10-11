<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

/**
 * Returns a URL for your assets, relative to the applications `/public`
 * directory. EG - `/public/assets`
 *
 * @param  string $file
 * @param  string $path
 *
 * @return string
 */
function assets_url(string $file, string $path = 'assets')
{
    return base_url($path . '/' . $file);
}

// ----------------------------------------------------------------------------

/**
 * Shopping cart service
 *
 * @return mixed
 */
function cart()
{
    return \Config\Services::cart();
}

// ----------------------------------------------------------------------------

/**
 * A convenience/compatibility method for logging events through
 * the Log system.
 *
 * Allowed log levels are:
 *  - emergency
 *  - alert
 *  - critical
 *  - error
 *  - warning
 *  - notice
 *  - info
 *  - debug
 *
 * @param string     $level
 * @param string     $message
 * @param array|null $context
 *
 * @return mixed
 */
function log_message(string $level, string $message, array $context = [])
{
    return \Config\Services::logger()->log($level, $message, $context);
}

// ----------------------------------------------------------------------------
