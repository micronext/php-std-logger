<?php
/**
 * MicroNext
 *
 * PHP version 7.1
 *
 * @category  Library
 * @package   MicroNext\ConsoleLogger
 * @copyright Copyright (c) 2018 MicroNext
 * @license   http://mit-license.org/ MIT License
 * @link      https://medium.com/@onegin_online
 */
/*# declare(strict_types=1); */

namespace MicroNext\Console;

use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Logger
 * ---
 * Outputs logs to `std::out` or `std::err`
 *
 * @package MicroNext\Console\Logger
 * @author  Yevhenii Ivanets <evgeniyivanets@gmail.com>
 * @see     Psr\Log\AbstractLogger
 * @see     Psr\Log\LoggerInterface
 * @see     Psr\Log\LogLevel
 * @version 1.0.2
 * @since   1.0.1 added
 * @since   1.0.2 rename repository and update fields
 *                moved to structure that allows to add
 *                dynamic libs in same root scope
 */
class Logger extends AbstractLogger implements LoggerInterface
{
    public const DEFAULT_OUTPUT_TEMPLATE = "~~ --------\n|> {date}\t| [{level}]: {message}{lf}|> Context:\t\t| [{level}]: {context}{lf}~~ --------{lf}";
    /**
     * Input:
     * ```
     * ~~ --------\n|> {date}\t| [{level}]: {message}{lf}|> Context:\t\t| [{level}]: {context}{lf}~~ --------{lf}
     * ```
     *
     * Render:
     * ```
     * ~~ --------
     * |> 2018-11-01 12:17:11  | [debug]: Test message
     * |> Context:             | [debug]: test
     * ~~ --------
     * ```
     * @api
     * @access public
     * @var string $outputFormat
     */
    public $outputFormat = Logger::DEFAULT_OUTPUT_TEMPLATE;

    /**
     * @api
     * @access public
	 * @var string $dateFormat
	 */
	public $dateFormat = DateTime::RFC2822;
	/**
	 * Текущая дата
	 *
	 * @return string
	 */
	protected function getDate()
	{
		return (new DateTime())->format($this->dateFormat);
	}

    /**
     * Sets `$format` string for substitutional formatting, that can consists of parts:
     * - `{date}`
     * - `{level}`
     * - `{message}`
     * - `{context}`
     * - `{lf}`
     * ### Example: `"==> {date}\t| [{level}]: {message}{lf}    Context:\t\t{context}{lf}<== -------"`
     * ### Result:
     * ```
     * ==> 2018-11-01 12:17:11   | [debug]: Test message
     *     Context: "test"
     * <== -------
     * echo -e "|> 2018-11-01 12:17:11\t| [debug]: Test message\n|> Context:\t\t| [debug]: test\n|  --------"
     * && echo -e "[warning]\t| 2018-11-01 12:22:23\t| Warning message" && echo -e "[warning]\t| Context:\t\t| test" && echo -e "--------";
     * echo -e "•• --------\n|> 2018-11-01 12:17:11\t| [debug]: Test message\n|> Context:\t\t| [debug]: test\n•• --------"
     * ```
     * Result:
     * ```
     * echo -e "~~ --------\n|> 2018-11-01 12:17:11\t| [debug]: Test message\n|> Context:\t\t| [debug]: test\n~~ --------"
     * ```
     * @param  string $format output log format.
     * @param  int $permissions filesystem permissions
     * @access public
     * @since  2.1.0 changed first param
     * @api
     */
    public function __construct($outputFormat = Logger::DEFAULT_OUTPUT_TEMPLATE, $dateFormat = DateTime::RSS) {
        $this->outputFormat = $outputFormat;
        $this->dateFormat = $dateFormat;
    }

    /**
     * ### Array that defines associations, where we gonna write `$message`, `std::out` or `std::err`
     * ```
     *   std::out << WARNING | NOTICE | INFO | DEBUG
     *   std::err << EMERGENCY | CRITICAL | CRITICAL | ERROR
     * ```
     * #### IMPORTANT!
     * It's `private` for reason.
     * - User DO NOT need to know split logic realization
     * - According to `Psr\Log\LoggerInterface` and `Psr\Log\AbstractLogger`,
     * we MUST to implement a `log` method to DESCRIBE behaviour, but NOT change user interaction
     * - Since PHP 7.1.0 visibility modifiers are allowed for class constants.
     *
     * @property array(Psr\Log\LogLevel=>std::void) OUT
     *
     * @since 7.1.0
     * @see http://php.net/manual/en/language.oop5.constants.php#language.oop5.basic.class.this
     */
    private const OUT = [
        // Error logs
        LogLevel::EMERGENCY => STDERR,
        LogLevel::CRITICAL => STDERR,
        LogLevel::CRITICAL => STDERR,
        LogLevel::ERROR => STDERR,
        // Logs
        LogLevel::WARNING => STDOUT,
        LogLevel::NOTICE => STDOUT,
        LogLevel::INFO => STDOUT,
        LogLevel::DEBUG => STDOUT,
    ];

    /**
     * ## Logs with an arbitrary level.
     * Writes `$message` to corresponding output depends on `$logLevel`
     * If it's described at `StdLogger::OUT` outputs there, else into std:error.
     *
     * @api
     *
     * @param mixed  $level if empty, will use `LogLevel::DEBUG` fallback
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $level = $level ?? LogLevel::DEBUG;
        $output = (array_key_exists($level, self::OUT)) ? self::OUT[$level] : STDERR;
        $data = '';
        if (!empty($context)) {
            try {
                $json = json_encode($context, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
                $data .= $json;
            } catch (\Exception $e) {
                $data = '[X] Error occured when tried to convert additional context to JSON.'.PHP_EOL.'\t'.$e->getMessage();
            }
        }
        fwrite($output, $this->format($level, $message, $data));
    }

    private function format(string $level, string $message, string $context): string
    {
        return trim(strtr($this->outputFormat, [
            '{date}' => $this->getDate(),
            '{level}' => $level,
            '{message}' => $message,
            '{context}' => $context,
            '{lf}' => PHP_EOL,
        ]));
    }
}
