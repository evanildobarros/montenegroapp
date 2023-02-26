<?php
declare(strict_types=1);

namespace Winsite\Log;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception as CakeException;
use Cake\Http\ServerRequestFactory;
use Cake\Log\Log;

/**
 * Trait WinsiteLogTrait
 *
 * @package Winsite\Log
 */
trait WinsiteLogTrait
{
    /**
     * Log an error for the exception if applicable.
     *
     * @param \Exception $exception The exception to log a message for.
     * @param string|null $message Message to log
     * @return void
     */
    public function logException($exception, $message = null)
    {
        $request = ServerRequestFactory::fromGlobals();

        Log::error($this->getMessage($request, $exception, $message));
    }

    /**
     * Generate the error log message.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The current request.
     * @param \Exception $exception The exception to log a message for.
     * @param string|null $aditionalMessage Aditional Message
     * @return string Error message
     */
    protected function getMessage($request, $exception, $aditionalMessage = null)
    {
        $message = $this->getMessageForException($exception);

        $message .= "\nRequest URL: " . $request->getRequestTarget();
        $referer = $request->getHeaderLine('Referer');
        if ($referer) {
            $message .= "\nReferer URL: " . $referer;
        }
        if ($aditionalMessage) {
            $message .= "\nAditional Message: " . $aditionalMessage;
        }
        $message .= "\n\n";

        return $message;
    }

    /**
     * Generate the message for the exception
     *
     * @param \Exception $exception The exception to log a message for.
     * @param bool $isPrevious False for original exception, true for previous
     * @return string Error message
     */
    protected function getMessageForException($exception, $isPrevious = false)
    {
        $message = sprintf(
            '%s[%s] %s (%s:%s)',
            $isPrevious ? "\nCaused by: " : '',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        $debug = Configure::read('debug');

        if ($debug && $exception instanceof CakeException) {
            $attributes = $exception->getAttributes();
            if ($attributes) {
                $message .= "\nException Attributes: " . var_export($exception->getAttributes(), true);
            }
        }

        $message .= "\n" . $exception->getTraceAsString();

        $previous = $exception->getPrevious();
        if ($previous) {
            $message .= $this->getMessageForException($previous, true);
        }

        return $message;
    }
}
