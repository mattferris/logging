<?php

namespace MattFerris\Logging\Helpers;

use MattFerris\Logging\MessageInterface;

class InterpolationHelper implements HelperInterface
{
    public function help(MessageInterface $message)
    {
        $repl = [];
        foreach ($message->getContext() as $k => $v) {
            if (!is_string($v)) {
                $v = $this->_convertTypeToString($v);
            }
            $repl['{'.$k.'}'] = $v;
        }

        $msgstr = strtr($message->getMessage(), $repl);

        return $message->withMessage($msgstr);
    }

    /**
     * Convert a non-string type to a string
     *
     * @param mixed $value The value to convert
     * @param bool $quoted If true, quote the resulting string
     * @return string
     */
    public function _convertTypeToString($value, $quoted = false)
    {
        if (is_object($value)) {
            $value = $this->_convertObjectToString($value, $quoted);
        } elseif (is_array($value)) {
            $value = $this->_convertArrayToString($value);
        } elseif (is_null($value)) {
            $value = 'null';
        } elseif (is_resource($value)) {
            $value = '('.(string)$value.')';
        } elseif (is_numeric($value)) {
            $value = (string)$value;
        } else {
            $value = '<unknown type>';
        }
        return $value;
    }

    /**
     * Convert an object to a string
     *
     * @param object $obj Object to convert to string
     * @param bool $quoted If true, quote the resulting string
     * @return string
     */
    public function _convertObjectToString($obj, $quoted = false)
    {
        if (!is_object($obj)) {
            throw new InvalidArgumentException('$obj expects object');
        }

        $str = '';
        if (method_exists($obj, '__toString')) {
            $str = (string)$obj;
            if ($quoted === true) {
                $str = '"'.$str.'"';
            }
        } else {
            $str = '('.get_class($obj).')';
        }

        return $str;
    }

    /**
     * Convert an array to a string
     *
     * @param array $arr Array to convert to string
     * @return string
     */
    public function _convertArrayToString(array $arr)
    {
        $str = '[';
        foreach ($arr as $k => $v) {
            $str .= $k.'=>';
            if (is_string($v)) {
                $str .= '"'.$v.'"';
            } else {
                $str .= $this->_convertTypeToString($v, true);
            }
        }
        $str .= ']';
        return $str;
    }
}
