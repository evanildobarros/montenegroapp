<?php
declare(strict_types=1);

/**
 * Austrian Localized Validation class. Handles localized validation for Austria.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://cakephp.org
 * @since Localized Plugin v 0.1
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Localized\Validation;

use Cake\Http\Exception\NotImplementedException;

/**
 * AtValidation
 */
class AtValidation extends LocalizedValidation
{
    /**
     * Define locale to be used by that localized
     * validation set
     *
     * @var string
     */
    protected static $validationLocale = 'de_AT';

    /**
     * Checks a postal code.
     *
     * @param string $check The value to check.
     * @return bool Success.
     */
    public static function postal(string $check): bool
    {
        $pattern = '/^\d{4}$/';

        return (bool)preg_match($pattern, $check);
    }

    /**
     * Checks a phone number.
     *
     * @param string $check The value to check.
     * @throws \Cake\Http\Exception\NotImplementedException Exception
     * @return bool Success.
     */
    public static function phone(string $check): bool
    {
        throw new NotImplementedException(__d('localized', '%s Not implemented yet.'));
    }

    /**
     * Checks a country specific identification number.
     *
     * @param string $check The value to check.
     * @throws \Cake\Http\Exception\NotImplementedException Exception
     * @return bool Success.
     */
    public static function personId(string $check): bool
    {
        throw new NotImplementedException(__d('localized', '%s Not implemented yet.'));
    }
}
