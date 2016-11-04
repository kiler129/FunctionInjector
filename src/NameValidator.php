<?php


namespace noFlash\FunctionsManipulator;

class NameValidator
{
    const RESULT_REGEX_FAILED     = -2;
    const RESULT_RESERVED_KEYWORD = -1;
    const RESULT_OK               = 0;

    const RESERVED_KEYWORDS = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callback',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'use',
        'var',
        'while',
        'xor',
        'yield'
    ];

    const LABEL_REGEX = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    /**
     * Translates numeric codes defined by self::RESULT_* constants into human-readable
     * message.
     *
     * @param int $code
     *
     * @return string
     */
    public static function getErrorFromCode($code)
    {
        if (!is_integer($code)) {
            throw new \InvalidArgumentException(
                sprintf('Code should be integer, got ', gettype($code))
            );
        }

        switch ($code) {
            case self::RESULT_OK:
                return 'no error';

            case self::RESULT_RESERVED_KEYWORD:
                return 'reserved keyword used';

            case self::RESULT_REGEX_FAILED:
                return 'regex failed';

            default:
                return sprintf('unknown error #%d', $code);
        }
    }

    /**
     * Verifies if namespace provided could be used in PHP.
     *
     * @param string $ns Namespace to check. Trailing/leading slashes are ignored.
     *
     * @return int Code defined by self::RESULT_* constants
     */
    public function validateNamespace($ns)
    {
        $ns = trim($ns, '\\');
        if (empty($ns)) {
            return self::RESULT_OK;
        }

        foreach (explode('\\', $ns) as $part) {
            if (in_array($part, self::RESERVED_KEYWORDS)) {
                return self::RESULT_RESERVED_KEYWORD;
            }

            if (!preg_match(self::LABEL_REGEX, $part)) {
                return self::RESULT_REGEX_FAILED;
            }
        }

        return self::RESULT_OK;
    }

    /**
     * Verifies if function name provided could be used in PHP.
     *
     * @param string $name Function name to check.
     *
     * @return int Code defined by self::RESULT_* constants
     */
    public function validateFunctionName($name)
    {
        if (in_array($name, self::RESERVED_KEYWORDS)) {
            return self::RESULT_RESERVED_KEYWORD;
        }

        if (!preg_match(self::LABEL_REGEX, $name)) {
            return self::RESULT_REGEX_FAILED;
        }

        return self::RESULT_OK;
    }
}
