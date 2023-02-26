<?php
declare(strict_types=1);

namespace Winsite\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget;

/**
 * Class CroppieWidget
 *
 * @package Winsite\View\Widget
 */
class CroppieWidget extends BasicWidget
{
    /**
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {
        $name = $data['name'];
        $id = $data['id'];
        $data += [
            'name' => false,
            'val' => null,
            'escape' => true,
            'templateVars' => [],
            'class' => 'upload d-none',
            'accept' => 'image/*',
            'onchange' => 'readFile(this)',
            'data-boundary-height' => 300,
            'data-viewport-width' => 300,
            'data-viewport-height' => 200,
        ];

        $result = $this->_templates->format('croppie', [
            'type' => 'file',
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(
                $data,
                ['name', 'type']
            ),
        ]);

        $hidden = $this->_templates->format('croppie', [
            'type' => 'hidden',
            'attrs' => $this->_templates->formatAttributes([
                'name' => $name,
                'class' => 'encodedImage form-control',
                'id' => preg_replace('/^croppie-/', '', $id),
            ]),
        ]);

        return $result . $hidden;
    }
}
