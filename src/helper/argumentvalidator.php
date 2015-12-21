<?php

namespace Xiaoju\Beatles\Utils;

/**
 * Class to validate arguments to functions
 */
class ArgumentValidator
{
    const CONSTRAINT_NAME_TYPE = 'type';
    const CONSTRAINT_NAME_REQUIRED = 'required';
    const CONSTRAINT_NAME_NOT_ZERO = 'notZero';
    const CONSTRAINT_NAME_LOWER_BOUND = 'lowerBound';
    const CONSTRAINT_NAME_UPPER_BOUND = 'upperBound';
    const CONSTRAINT_NAME_NOT_BLANK = 'notBlank';
    const CONSTRAINT_NAME_REGEX = 'regex';
    const CONSTRAINT_NAME_MIN_LENGTH = 'minLength';
    const CONSTRAINT_NAME_MAX_LENGTH = 'maxLength';
    const CONSTRAINT_NAME_PATTERN = 'pattern';
    static $supportedNames = array(
        self::CONSTRAINT_NAME_TYPE,
        self::CONSTRAINT_NAME_REQUIRED,
        self::CONSTRAINT_NAME_NOT_ZERO,
        self::CONSTRAINT_NAME_LOWER_BOUND,
        self::CONSTRAINT_NAME_UPPER_BOUND,
        self::CONSTRAINT_NAME_NOT_BLANK,
        self::CONSTRAINT_NAME_REGEX,
        self::CONSTRAINT_NAME_MIN_LENGTH,
        self::CONSTRAINT_NAME_MAX_LENGTH,
        //TBD: self::CONSTRAINT_NAME_PATTERN,
    );

    const CONSTRAINT_TYPE_INT = 'int';
    const CONSTRAINT_TYPE_DOUBLE = 'double';
    const CONSTRAINT_TYPE_STRING = 'string';
    static $supportedTypes = array(
        self::CONSTRAINT_TYPE_INT,
        self::CONSTRAINT_TYPE_DOUBLE,
        self::CONSTRAINT_TYPE_STRING
    );

    const CONSTRAINT_SOURCE_POST = 'post';
    const CONSTRAINT_SOURCE_GET = 'get';
    static $supportedSource = array(
        self::CONSTRAINT_SOURCE_POST,
        self::CONSTRAINT_SOURCE_GET,
    );


    /**
     * function to validate GET args for the rest API and return an array of results. it supports validation constraints for each argument.
     * Supported Constraints:
     * int            -    must be an integer (implies 'numeric')
     * numeric        -    must be numeric
     * notzero        -    must not be zero (implies 'numeric')
     * notblank        -    string must not be blank (implies 'string')
     * string        -    must be string
     * array            -    must be array
     * func            -    provided Closure must return true.
     * lbound arg    -    must not be below arg (e.g. "lbound 2")
     * ubound arg    -    must not be above arg (e.g. "ubound 600")
     * regex arg        -    must match regex given be arg
     */
    public function validateArgs($arguments, $description)
    {
        //validate our own args
        if (!is_array($description)) {
            throw new \Exception("Arguments Description must be an array");
            return false;
        } elseif (!is_array($arguments)) {
            throw new \Exception("Arguments must be an array");
            return false;
        }

        //array to be returned
        $retArr = array();
        //loop through each argument to be validated
        foreach ($description as $argName => $constraints) {
            if (!is_array($constraints)) {
// ensure constraints are provided as an array
                throw new \Exception("Constraints must be an array");
                return false;
            }

            $argExists = array_key_exists($argName, $arguments);
            $argValue = $argExists ? $arguments[$argName] : null;
            if ((!array_key_exists(self::CONSTRAINT_NAME_REQUIRED, $constraints) ||
                strcmp($constraints[self::CONSTRAINT_NAME_REQUIRED], '1') !== 0) &&
                !$argExists
            ) {
                continue;
            }

            foreach ($constraints as $constraint => $constraintValue) {
                if (!in_array($constraint, self::$supportedNames)) {
                    throw new \Exception("Constraint name not supported");
                    return false;
                }
                switch ($constraint) {
                    case self::CONSTRAINT_NAME_TYPE:
                        self::checkType($argName, $argValue, $constraintValue);
                        break;
                    case self::CONSTRAINT_NAME_REQUIRED:
                        self::checkRequired($argName, $argValue, $constraintValue);
                        break;
                    case self::CONSTRAINT_NAME_NOT_ZERO:
                        self::checkNotZero($argValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_NOT_BLANK:
                        self::checkNotBlank($argValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_LOWER_BOUND:
                        self::checkLowerBound($argValue, $constraintValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_UPPER_BOUND:
                        self::checkUpperbound($argValue, $constraintValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_REGEX:
                        self::checkRegex($argValue, $constraintValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_MIN_LENGTH:
                        self::checkMinLength($argValue, $constraintValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_MAX_LENGTH:
                        self::checkMaxLength($argValue, $constraintValue, $argName);
                        break;
                    case self::CONSTRAINT_NAME_REGEX:
                        self::checkRegex($constraintValue, $argValue, $argName);
                        break;
                    default:
                        throw new \Exception("Constraint " . htmlentities($c["constraint"]) . " is unsupported");
                        break;

                }
            }
        }
    }

    private static function checkType($argName, $value, $type)
    {
        if (!in_array($type, self::$supportedTypes)) {
            throw new \Exception("Invalid type: ", $type);
        }
        switch ($type) {
            case self::CONSTRAINT_TYPE_INT:
                self::checkIsInt($value, $argName);
                break;
            case self::CONSTRAINT_TYPE_DOUBLE:
                self::checkIsNumeric($value, $argName);
                break;
            case self::CONSTRAINT_TYPE_STRING:
                self::checkIsString($value, $argName);
                break;
        }
    }

    private static function checkRequired($argName, $value, $requiredValue)
    {
        $required = strcmp($requiredValue, '1') == 0;
        if ($required && $value == null) {
            throw new \InvalidArgumentException("Argument is required: " . $argName);
        }
    }

    private static function checkIsInt($value, $arg)
    {
        self::checkIsNumeric($value, $arg);
        if (!is_int($value + 0)) {
            throw new \InvalidArgumentException("Argument is not an integer: " . $arg);
        }
    }

    private static function checkIsNumeric($value, $arg)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Argument is not numeric: " . $arg);
        }
        return true;
    }

    private static function checkNotZero($value, $arg)
    {
        if (!self::checkIsNumeric($value, $arg) || $value == 0) {
            throw new \InvalidArgumentException("Argument is zero: " . $arg);
            return false;
        }
        return true;

    }

    private static function checkMinLength($argValue, $constraintValue, $argName)
    {
        if (!is_numeric($constraintValue)) {
            throw new \Exception("Invalid constraint value: ", $constraintValue);
        }

        if (!self::checkIsString($argValue, $argName) || strlen($argValue) < $constraintValue) {
            throw new \InvalidArgumentException("Argument is less than min length: " . $argName);
        }
    }

    private static function checkMaxLength($argValue, $constraintValue, $argName)
    {
        if (!is_numeric($constraintValue)) {
            throw new \Exception("Invalid constraint value: ", $constraintValue);
        }

        if (!self::checkIsString($argValue, $argName) || strlen($argValue) > $constraintValue) {
            throw new \InvalidArgumentException("Argument is larger than max length: " . $argName);
        }
    }

    private static function checkNotBlank($value, $arg)
    {
        if (!self::checkIsString($value, $arg) || $value == "") {
            throw new \InvalidArgumentException("Argument is a blank string: " . $arg);
            return false;
        }
        return true;
    }

    private static function checkIsString($value, $arg)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException("Argument is not a string: " . $arg);
            return false;
        }
        return true;
    }

    private static function checkIsArray($value, $arg)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException("Argument is not an array: " . $arg);
            return false;
        }
        return true;
    }

    private static function checkLowerBound($value, $lowerBound, $arg)
    {
        $lowerBound = (float)$lowerBound;
        if (!is_numeric($lowerBound)) {
            throw new \InvalidArgumentException("Argument to lowerBound must be numeric: " . $arg);
            return false;
        } else {
            if (!self::checkIsNumeric($value, $arg) || $value < $lowerBound) {
                throw new \InvalidArgumentException("Argument is below lowerBound(" . $lowerBound . "): " . $arg);
                return false;
            }
            return true;
        }
    }

    private static function checkUpperbound($value, $upperBound, $arg)
    {
        $upperBound = (float)$upperBound;
        if (!is_numeric($upperBound)) {
            throw new \InvalidArgumentException("Argument to upperBound must be numeric: " . $arg);
            return false;
        } else {
            if (!self::checkIsNumeric($value, $arg) || $value > $upperBound) {
                throw new \InvalidArgumentException("Argument is above upperBound(" . $upperBound . "): " . $arg);
                return false;
            }
            return true;
        }
    }

    private function checkRegex($value, $regex, $arg)
    {
        if ((preg_match($regex, $value)) !== 1) {
            throw new \InvalidArgumentException("Argument is does not match regex(" . $regex . "): " . $arg);
            return false;
        }
        return true;
    }
}
