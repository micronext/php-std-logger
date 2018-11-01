<?php

namespace MicroNext\Console;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class `StdOut\Logger`.
 *
 * @api
 *
 * @author Yevhenii Ivanets <evgeniyivanets@gmail.com>
 * Outputs logs to `std::out` or `std::err`
 */
class Logger extends AbstractLogger implements LoggerInterface
{
    /**
     * @api
     *
     * @var string Template
     */
    public const OUTPUT_FORMAT = " {date}\t| [{level}]: {message}{lf} {date}\t| [{level}]: {context}{lf}---------------------{lf}";

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
                $json = json_encode($context, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
                $data .= $json;
            } catch (\Exception $e) {
                $data = '[X] Error occured when tried to convert additional context to JSON.'.PHP_EOL.'\t'.$e->getMessage();
            }
        }
        fwrite($output, $this->format($level, $message, $data));
    }

    private function format(string $level, string $message, string $context): string
    {
        return trim(strtr(self::OUTPUT_FORMAT, [
            '{date}' => $this->getDate(),
            '{level}' => $level,
            '{message}' => $message,
            '{context}' => $context,
            '{lf}' => PHP_EOL,
        ]));
    }
}
