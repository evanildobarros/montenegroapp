<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://cakephp.org
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Localized\Validation;

/**
 * ValidationInterface defining some common base validation methods.
 */
interface ValidationInterface
{
    /**
     * Checks a phone number.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function phone(string $string): bool;

    /**
     * Checks a postal code.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function postal(string $string): bool;

    /**
     * Checks a country specific identification number.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function personId(string $string): bool;

    /**
     * Checks a date string, from language specific format.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function date(string $string): bool;

    /**
     * Checks a date and time string, from language specific format.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function dateTime(string $string): bool;

    /**
     * Checks a decimal number, from language specific format.
     *
     * @param string $string The value to check.
     * @return bool Success.
     */
    public static function decimal(string $string): bool;
}
