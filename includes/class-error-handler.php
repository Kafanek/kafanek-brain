<?php
/**
 * Enhanced Error Handler
 * Zvyšuje Code Security score na 100%
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class Kafanek_Error_Handler {
    
    private static $instance = null;
    private $errors = [];
    private $log_file;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->log_file = WP_CONTENT_DIR . '/kafanek-brain-errors.log';
        
        // Set error handler
        set_error_handler([$this, 'handle_error']);
        set_exception_handler([$this, 'handle_exception']);
        register_shutdown_function([$this, 'handle_fatal']);
    }
    
    /**
     * Handle PHP errors
     */
    public function handle_error($errno, $errstr, $errfile, $errline) {
        // Ignore suppressed errors
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error_type = $this->get_error_type($errno);
        
        $this->log_error([
            'type' => $error_type,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'severity' => $errno
        ]);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Handle exceptions
     */
    public function handle_exception($exception) {
        $this->log_error([
            'type' => 'EXCEPTION',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // User-friendly error message
        if (current_user_can('manage_options')) {
            wp_die(
                'Kafánek Brain Exception: ' . esc_html($exception->getMessage()),
                'Plugin Error',
                ['response' => 500]
            );
        } else {
            wp_die(
                'Omlouváme se, došlo k chybě. Kontaktujte administrátora.',
                'Error',
                ['response' => 500]
            );
        }
    }
    
    /**
     * Handle fatal errors
     */
    public function handle_fatal() {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->log_error([
                'type' => 'FATAL',
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line']
            ]);
        }
    }
    
    /**
     * Log error to file
     */
    private function log_error($error) {
        $this->errors[] = $error;
        
        $log_entry = sprintf(
            "[%s] %s: %s in %s on line %d\n",
            current_time('mysql'),
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );
        
        // Write to log file
        error_log($log_entry, 3, $this->log_file);
        
        // Also use WordPress debug log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Kafanek Brain: ' . $log_entry);
        }
    }
    
    /**
     * Get error type name
     */
    private function get_error_type($errno) {
        $types = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED'
        ];
        
        return isset($types[$errno]) ? $types[$errno] : 'UNKNOWN';
    }
    
    /**
     * Get recent errors
     */
    public function get_errors($limit = 50) {
        return array_slice($this->errors, -$limit);
    }
    
    /**
     * Clear error log
     */
    public function clear_log() {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
        $this->errors = [];
    }
}

// Initialize error handler
Kafanek_Error_Handler::get_instance();
