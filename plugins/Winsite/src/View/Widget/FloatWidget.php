<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Winsite\View\Widget;

use Cake\View\Widget\BasicWidget;

/**
 * Float input class.
 *
 * This input class can be used to render float
 */
class FloatWidget extends BasicWidget
{
    /**
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, \Cake\View\Form\ContextInterface $context): string
    {
        $data += [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'templateVars' => [],
        ];
        $data['value'] = $data['val'];
        unset($data['val']);

        $data['type'] = 'text';

        $data['data-inputmask-alias'] = 'decimal';

        if (isset($data['decimals'])) {
            $data['data-inputmask-digits'] = $data['decimals'];
        }

        return $this->_templates->format('float', [
            'name' => $data['name'],
            'type' => $data['type'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type']
            ),
        ]);
    }
}
