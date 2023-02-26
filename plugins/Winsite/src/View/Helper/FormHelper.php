<?php
declare(strict_types=1);

namespace Winsite\View\Helper;

use Cake\View\Helper\FormHelper as CakeFormHelper;

/**
 * Class FormHelper
 *
 * @package Winsite\View\Helper
 */
class FormHelper extends CakeFormHelper
{
    /**
     * Default config for the helper.
     *
     * @var array
     * phpcs:disable
     */
    protected $_defaultConfig = [
        'idPrefix' => null,
        'errorClass' => 'is-invalid',
        'typeMap' => [
            'string' => 'text',
            'datetime' => 'datetime',
            'boolean' => 'checkbox',
            'timestamp' => 'datetime',
            'text' => 'textarea',
            'time' => 'time',
            'date' => 'date',
            'float' => 'number',
            'integer' => 'number',
            'decimal' => 'number',
            'binary' => 'file',
            'uuid' => 'string',
        ],
        'templates' => [
            'button' => '<button{{attrs}}>{{text}}</button>',
            'checkbox' => '<div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}} data-text-checked="{{1}}" data-text-unchecked="{{0}}"></div></div><div class="form-control"><strong class="{{textClass}}">{{text}}</strong></div></div>',
            'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
            'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
            'error' => '<div class="invalid-feedback">{{content}}</div>',
            'errorList' => '<ul>{{content}}</ul>',
            'errorItem' => '<li class="help-block">{{text}}</li>',
            'file' => '<input type="file" name="{{name}}"{{attrs}}>',
            'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
            'formStart' => '<form{{attrs}}>',
            'formEnd' => '</form>',
            'formGroup' => '{{label}}{{input}}{{after}}',
            'checkboxFormGroup' => '{{label}}{{input}}',
            'radioFormGroup' => '{{label}}<div class="form-control">{{input}}</div>',
            'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
            'text' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            'date' => '<div class="input-group" id={{id}} data-datepicker data-target-input="nearest"><input type="{{type}}" name="{{name}}" {{attrs}} data-target="#{{id}}"/><div class="input-group-append" data-target="#{{id}}" data-toggle="datetimepicker"><div class="input-group-text"><i class="fas fa-calendar"></i></div></div></div>',
            'datetime' => '<div class="input-group" id={{id}} data-datetimepicker data-target-input="nearest"><input type="{{type}}" name="{{name}}" {{attrs}} data-target="#{{id}}"/><div class="input-group-append" data-target="#{{id}}" data-toggle="datetimepicker"><div class="input-group-text"><i class="fas fa-calendar"></i></div></div></div>',
            'time' => '<div class="input-group" id={{id}} data-timepicker data-target-input="nearest"><input type="{{type}}" name="{{name}}" {{attrs}} data-target="#{{id}}"/><div class="input-group-append" data-target="#{{id}}" data-toggle="datetimepicker"><div class="input-group-text"><i class="fas fa-clock"></i></div></div></div>',
            'email' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span></span></div>',
            'document' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text"><i class="far fa-address-card"></i></span></span></div>',
            'password' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span></span></div>',
            'search' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-btn"><button class="btn btn-default" type="button"><i class="fa fa-fw fa-search"></i></button></span></div>',
            'selectButton' => '<div class="input-group"><select name="{{name}}"{{attrs}}>{{content}}</select><span class="input-group-btn">{{button}}</span></div>',
            'phone' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text"><i class="fa fa-fw fa-phone"></i></span></span></div>',
            'url' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text"><i class="fa fa-fw fa-link"></i></span></span></div>',
            'monetary' => '<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">R$</span></span><input type="{{type}}" name="{{name}}"{{attrs}}/></div>',
            'int' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            'float' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            'percentage' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><span class="input-group-append"><span class="input-group-text">%</span></span></div>',
            'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
            'inputContainer' => '<div class="form-group {{classContainer}}{{required}}">{{content}}<span class="help-block">{{help}}</span></div>',
            'inputContainerError' => '<div class="form-group {{classContainer}}{{required}}">{{content}}{{error}}</div>',
            'label' => '<label {{attrs}}>{{text}}</label>',
            'nestingLabel' => '{{hidden}}<label class="radio-inline" {{attrs}}>{{input}}{{text}}</label>',
            'legend' => '<legend>{{text}}</legend>',
            'multicheckboxTitle' => '<legend>{{text}}</legend>',
            'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
            'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
            'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
            'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
            'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
            'radioWrapper' => '{{label}}',
            'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
            'submitContainer' => '<div class="submit">{{content}}</div>',
            'croppie' => '<input type="{{type}}"{{attrs}}>',
            'confirmJs' => '{{confirm}}',
        ],
    ];

    /**
     * Default widgets
     *
     * @var array
     */
    protected $_defaultWidgets = [
        'button' => ['Button'],
        'checkbox' => ['Winsite.Checkbox'],
        'file' => ['File'],
        'label' => ['Label'],
        'nestingLabel' => ['NestingLabel'],
        'multicheckbox' => ['MultiCheckbox', 'nestingLabel'],
        'radio' => ['Radio', 'nestingLabel'],
        'select' => ['SelectBox'],
        'selectButton' => ['Winsite.SelectButton'],
        'textarea' => ['Textarea'],
        'date' => ['Winsite.Date'],
        'datetime' => ['Winsite.DateTime'],
        'time' => ['Winsite.Time'],
        '_default' => ['Winsite.Text'],
        'email' => ['Winsite.Email'],
        'document' => ['Winsite.Document'],
        'password' => ['Winsite.Password'],
        'search' => ['Winsite.Search'],
        'phone' => ['Winsite.Phone'],
        'url' => ['Winsite.Url'],
        'monetary' => ['Winsite.Monetary'],
        'int' => ['Winsite.Int'],
        'float' => ['Winsite.Float'],
        'percentage' => ['Winsite.Percentage'],
        'croppie' => ['Winsite.Croppie'],
    ];

    public function create($model = null, array $options = []): string
    {
        $options += ['role' => 'form'];

        return parent::create($model, $options);
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * ### Options
     *
     * See each field type method for more information. Any options that are part of
     * $attributes or $options for the different **type** methods can be included in `$options` for input().
     * Additionally, any unknown keys that are not in the list below, or part of the selected type's options
     * will be treated as a regular HTML attribute for the generated input.
     *
     * - `type` - Force the type of widget you want. e.g. `type => 'select'`
     * - `label` - Either a string label, or an array of options for the label. See FormHelper::label().
     * - `options` - For widgets that take options e.g. radio, select.
     * - `error` - Control the error message that is produced. Set to `false` to disable any kind of error reporting (field
     *    error and error messages).
     * - `empty` - String or boolean to enable empty select box options.
     * - `nestedInput` - Used with checkbox and radio inputs. Set to false to render inputs outside of label
     *   elements. Can be set to true on any input to force the input inside the label. If you
     *   enable this option for radio buttons you will also need to modify the default `radioWrapper` template.
     * - `templates` - The templates you want to use for this input. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     * - `labelOptions` - Either `false` to disable label around nestedWidgets e.g. radio, multicheckbox or an array
     *   of attributes for the label tag. `selected` will be added to any classes e.g. `class => 'myclass'` where
     *   widget is checked
     *
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array $options Each type of input takes different options.
     * @return string Completed form widget.
     * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-form-inputs
     */
    public function control($fieldName, array $options = []): string
    {
        $options += [
            'type' => null,
            'label' => null,
            'error' => null,
            'required' => null,
            'options' => null,
            'templates' => [],
            'templateVars' => [],
            'labelOptions' => true,
        ];

        $options = $this->_parseOptions($fieldName, $options);
        if ($options['type'] == 'croppie') {
            $options += ['id' => 'croppie-' . $this->_domId($fieldName)];
        } else {
            $options += ['id' => $this->_domId($fieldName)];
        }

        if (empty($options['templateVars']['classContainer']) && $options['type'] != 'textarea') {
            $options['templateVars']['classContainer'] = 'col-xs-12 col-sm-6 col-md-4 col-lg-4';
        } elseif (empty($options['templateVars']['classContainer']) && $options['type'] == 'textarea') {
            $options['templateVars']['classContainer'] = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
        }

        if (!in_array($options['type'], ['checkbox', 'radio', 'croppie'])) {
            if (empty($options['class'])) {
                $options['class'] = 'form-control';
            } else {
                $options['class'] .= ' form-control';
            }
        }

        $templater = $this->templater();
        $newTemplates = $options['templates'];

        if ($newTemplates) {
            $templater->push();
            $templateMethod = is_string($options['templates']) ? 'load' : 'add';
            $templater->{$templateMethod}($options['templates']);
        }
        unset($options['templates']);

        $error = null;
        $errorSuffix = '';
        if ($options['type'] !== 'hidden' && $options['error'] !== false) {
            if (is_array($options['error'])) {
                $error = $this->error($fieldName, $options['error'], $options['error']);
            } else {
                $error = $this->error($fieldName, $options['error']);
            }
            $errorSuffix = empty($error) ? '' : 'Error';
            unset($options['error']);
        }

        $label = $options['label'];
        unset($options['label']);

        $labelOptions = $options['labelOptions'];
        unset($options['labelOptions']);

        $nestedInput = false;

        if ($nestedInput === true && $options['type'] === 'checkbox' && !array_key_exists('hiddenField', $options) && $label !== false) {
            $options['hiddenField'] = '_split';
        }

        $input = $this->_getInput($fieldName, $options + ['labelOptions' => $labelOptions]);
        if ($options['type'] === 'hidden' || $options['type'] === 'submit') {
            if ($newTemplates) {
                $templater->pop();
            }

            return $input;
        }

        $labelOptions = compact('input', 'label', 'error', 'nestedInput') + $options;

        if ($options['type'] === 'checkbox' && $labelOptions['label'] === false) {
            $label = null;
        } else {
            $label = $this->_getLabel($fieldName, $labelOptions);
        }

        if ($options['type'] == 'croppie') {
            $label .= $this->label($fieldName, '<i class="fa fa-cloud-upload-alt"></i> Selecionar arquivo...', ['class' => 'btn btn-default form-control', 'for' => 'croppie-' . $this->_domId($fieldName), 'escape' => false]);
        }

        if ($nestedInput) {
            $result = $this->_groupTemplate(compact('label', 'error', 'options'));
        } else {
            $result = $this->_groupTemplate(compact('input', 'label', 'error', 'options'));
        }

        $result = $this->_inputContainerTemplate([
            'content' => $result,
            'error' => $error,
            'errorSuffix' => $errorSuffix,
            'options' => $options,
        ]);

        if ($newTemplates) {
            $templater->pop();
        }

        return $result;
    }

    public function label($fieldName, $text = null, array $options = []): string
    {
        if (empty($options['class'])) {
            $options['class'] = 'control-label';
        }

        if (isset($options['tooltip'])) {
            $tooltip = $options['tooltip'];
            unset($options['tooltip']);
            if (!is_array($tooltip)) {
                $tooltip = [
                    'text' => $tooltip,
                ];
            }
            if (!isset($tooltip['icon'])) {
                $tooltip['icon'] = 'fas fa-question-circle';
            }
            if (!isset($tooltip['placement'])) {
                $tooltip['placement'] = 'top';
            }
            $text = $this->Html->tag(
                'span',
                $text .
                $this->Html->tag(
                    'i',
                    '',
                    [
                        'class' => 'ml-1 ' . $tooltip['icon'],
                    ]
                ),
                [
                    'data-toggle' => 'tooltip',
                    'data-placement' => $tooltip['placement'],
                    'title' => $tooltip['text'],
                ]
            );
            $options['escape'] = false;
        }

        return parent::label($fieldName, $text, $options);
    }

    public function date($fieldName, array $options = []): string
    {
        $options += [
            'empty' => true,
            'value' => null,
        ];
        $options = $this->_initInputField($fieldName, $options);

        return $this->widget('date', $options);
    }

    public function dateTime($fieldName, array $options = []): string
    {
        $options += [
            'empty' => true,
            'value' => null,
        ];
        $options = $this->_initInputField($fieldName, $options);

        return $this->widget('datetime', $options);
    }

    public function time($fieldName, array $options = []): string
    {
        $options += [
            'empty' => true,
            'value' => null,
        ];
        $options = $this->_initInputField($fieldName, $options);

        return $this->widget('time', $options);
    }
}
