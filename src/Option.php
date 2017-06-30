<?php
/*
 * This file is part of the GetOptionKit package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace GetOptionKit;


use Exception;
use LogicException;
use InvalidArgumentException;
use GetOptionKit\Exception\InvalidOptionValueException;

class Option
{
    public $short;

    public $long;

    /**
     * @var string the description of this option
     */
    public $desc;

    /**
     * @var string The option key
     */
    public $key;  /* key to store values */

    public $value;

    public $type;

    public $valueName; /* name for the value place holder, for printing */

    public $isa;

    public $isaOption;

    public $validValues;

    public $suggestions;

    public $defaultValue;

    public $incremental = false;

    /**
     * @var Closure The filter closure of the option value.
     */
    public $filter;

    public $validator;

    public $multiple = false;

    public $optional = false;

    public $required = false;

    public $flag = false;

    /**
     * @var callable trigger callback after value is set.
     */
    protected $trigger;

    public function __construct($spec)
    {
        $this->initFromSpecString($spec);
    }

    /**
     * Build spec attributes from spec string.
     *
     * @param string $specString
     */
    protected function initFromSpecString($specString)
    {
        $pattern = '/
        (
                (?:[a-zA-Z0-9-]+)
                (?:
                    \|
                    (?:[a-zA-Z0-9-]+)
                )?
        )

        # option attribute operators
        ([:+?])?

        # value types
        (?:=(boolean|string|number|date|file|dir|url|email|ip|ipv6|ipv4))?
        /x';
        $ret = preg_match($pattern, $specString, $regs);
        if ($ret === false || $ret === 0) {
            throw new Exception('Incorrect spec string');
        }

        $orig = $regs[0];
        $name = $regs[1];
        $attributes = isset($regs[2]) ? $regs[2] : null;
        $type = isset($regs[3]) ? $regs[3] : null;

        $short = null;
        $long = null;

        // check long,short option name.
        if (strpos($name, '|') !== false) {
            list($short, $long) = explode('|', $name);
        } else if (strlen($name) === 1) {
            $short = $name;
        } else if (strlen($name) > 1) {
            $long = $name;
        }

        $this->short = $short;
        $this->long = $long;

        // option is required.
        if (strpos($attributes, ':') !== false) {
            $this->required();
        } else if (strpos($attributes, '+') !== false) {
            // option with multiple value
            $this->multiple();
        } else if (strpos($attributes, '?') !== false) {
            // option is optional.(zero or one value)
            $this->optional();
        } else {
            $this->flag();
        }
        if ($type) {
            $this->isa($type);
        }
    }

    /*
     * get the option key for result key mapping.
     */
    public function getId()
    {
        return $this->key ?: $this->long ?: $this->short;
    }

    /**
     * To make -v, -vv, -vvv works.
     */
    public function incremental()
    {
        $this->incremental = true;

        return $this;
    }

    public function required()
    {
        $this->required = true;

        return $this;
    }

    /**
     * Set default value
     *
     * @param mixed|Closure $value
     */
    public function defaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function multiple()
    {
        $this->multiple = true;
        $this->value = array();  # for value pushing
        return $this;
    }

    public function optional()
    {
        $this->optional = true;

        return $this;
    }

    public function flag()
    {
        $this->flag = true;

        return $this;
    }

    public function trigger(callable $trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    public function isIncremental()
    {
        return $this->incremental;
    }

    public function isFlag()
    {
        return $this->flag;
    }

    public function isMultiple()
    {
        return $this->multiple;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function isOptional()
    {
        return $this->optional;
    }

    public function isTypeNumber()
    {
        return $this->isa == 'number';
    }

    public function isType($type)
    {
        return $this->isa === $type;
    }

    public function getTypeClass()
    {
        $class = 'GetOptionKit\\ValueType\\'.ucfirst($this->isa).'Type';
        if (class_exists($class, true)) {
            return new $class($this->isaOption);
        }
        throw new Exception("Type class '$class' not found.");
    }

    public function testValue($value)
    {
        $type = $this->getTypeClass();
        return $type->test($value);
    }

    protected function _preprocessValue($value)
    {
        $val = $value;

        if ($isa = ucfirst($this->isa)) {
            $type = $this->getTypeClass();
            if ($type->test($value)) {
                $val = $type->parse($value);
            } else {
                if (strtolower($isa) === 'regex') {
                    $isa .= '('.$this->isaOption.')';
                }
                throw new InvalidOptionValueException("Invalid value for {$this->renderReadableSpec(false)}. Requires a type $isa.");
            }
        }

        // check pre-filter for option value
        if ($this->filter) {
            $val = call_user_func($this->filter, $val);
        }

        // check validValues
        if ($validValues = $this->getValidValues()) {
            if (!in_array($value, $validValues)) {
                throw new InvalidOptionValueException('valid values are: '.implode(', ', $validValues));
            }
        }

        if (!$this->validate($value)[0]) {
            throw new InvalidOptionValueException('option is invalid');
        }

        return $val;
    }

    protected function callTrigger()
    {
        if ($this->trigger) {
            if ($ret = call_user_func($this->trigger, $this->value)) {
                $this->value = $ret;
            }
        }
    }

    /*
     * set option value
     */
    public function setValue($value)
    {
        $this->value = $this->_preprocessValue($value);
        $this->callTrigger();
    }

    /**
     * This method is for incremental option.
     */
    public function increaseValue()
    {
        if (!$this->value) {
            $this->value = 1;
        } else {
            ++$this->value;
        }
        $this->callTrigger();
    }

    /**
     * push option value, when the option accept multiple values.
     *
     * @param mixed
     */
    public function pushValue($value)
    {
        $value = $this->_preprocessValue($value);
        $this->value[] = $value;
        $this->callTrigger();
    }

    public function desc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * valueName is for option value hinting:.
     *
     *   --name=<name>
     */
    public function valueName($name)
    {
        $this->valueName = $name;

        return $this;
    }

    public function renderValueHint()
    {
        $n = null;
        if ($this->valueName) {
            $n = $this->valueName;
        } else if ($values = $this->getValidValues()) {
            $n = '('.implode(',', $values).')';
        } else if ($values = $this->getSuggestions()) {
            $n = '['.implode(',', $values).']';
        } else if ($val = $this->getDefaultValue()) {
            // This allows for `0` and `false` values to be displayed also.
            if ((is_scalar($val) && strlen((string) $val)) || is_bool($val)) {
                if (is_bool($val)) {
                    $n = ($val ? 'true' : 'false');
                } else {
                    $n = $val;
                }
            }
        }

        if (!$n && $this->isa !== null) {
            $n = '<'.$this->isa.'>';
        }
        if ($this->isRequired()) {
            return sprintf('=%s', $n);
        } else if ($this->isOptional() || $this->defaultValue) {
            return sprintf('[=%s]', $n);
        } else if ($n) {
            return '='.$n;
        }

        return '';
    }

    public function getDefaultValue()
    {
        if (is_callable($this->defaultValue)) {
            return $this->defaultValue;
        }

        return $this->defaultValue;
    }

    public function getValue()
    {
        if (null !== $this->value) {
            if (is_callable($this->value)) {
                return call_user_func($this->value);
            }
            return $this->value;
        }

        return $this->getDefaultValue();
    }

    /**
     * get readable spec for printing.
     * 
     * @param string $renderHint render also value hint
     */
    public function renderReadableSpec($renderHint = true)
    {
        $c1 = '';
        if ($this->short && $this->long) {
            $c1 = sprintf('-%s, --%s', $this->short, $this->long);
        } else if ($this->short) {
            $c1 = sprintf('-%s', $this->short);
        } else if ($this->long) {
            $c1 = sprintf('--%s', $this->long);
        }
        if ($renderHint) {
            return $c1.$this->renderValueHint();
        }

        return $c1;
    }

    public function __toString()
    {
        $c1 = $this->renderReadableSpec();
        $return = '';
        $return .= sprintf('* key:%-8s spec:%s  desc:%s', $this->getId(), $c1, $this->desc)."\n";
        $val = $this->getValue();
        if (is_array($val)) {
            $return .= '  value => ' . join(',', array_map(function($v) { return var_export($v, true); }, $val))."\n";
        } else {
            $return .= sprintf('  value => %s', $val)."\n";
        }

        return $return;
    }

    /**
     * Value Type Setters.
     *
     * @param string $type   the value type, valid values are 'number', 'string', 
     *                       'file', 'boolean', you can also use your own value type name.
     * @param mixed  $option option(s) for value type class (optionnal)
     */
    public function isa($type, $option = null)
    {
        // "bool" was kept for backward compatibility
        if ($type === 'bool') {
            $type = 'boolean';
        }
        $this->isa = $type;
        $this->isaOption = $option;

        return $this;
    }

    /**
     * Assign validValues to member value.
     */
    public function validValues($values)
    {
        $this->validValues = $values;

        return $this;
    }

    /**
     * Assign suggestions.
     *
     * @param Closure|array
     */
    public function suggestions($suggestions)
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    /**
     * Return valud values array.
     *
     * @return string[] or nil
     */
    public function getValidValues()
    {
        if ($this->validValues) {
            if (is_callable($this->validValues)) {
                return call_user_func($this->validValues);
            }

            return $this->validValues;
        }

        return;
    }

    /**
     * Return suggestions.
     *
     * @return string[] or nil
     */
    public function getSuggestions()
    {
        if ($this->suggestions) {
            if (is_callable($this->suggestions)) {
                return call_user_func($this->suggestions);
            }

            return $this->suggestions;
        }

        return;
    }

    public function validate($value)
    {
        if ($this->validator) {
            $ret = call_user_func($this->validator, $value);
            if (is_array($ret)) {
                return $ret;
            } else if ($ret === false) {
                return array(false, "Invalid value: $value");
            } else if ($ret === true) {
                return array(true, 'Successfully validated.');
            }
            throw new InvalidArgumentException('Invalid return value from the validator.');
        }

        return array(true);
    }

    public function validator($cb)
    {
        $this->validator = $cb;

        return $this;
    }

    /**
     * Set up a filter function for the option value.
     *
     * todo: add "callable" type hint later.
     */
    public function filter($cb)
    {
        $this->filter = $cb;

        return $this;
    }
}
