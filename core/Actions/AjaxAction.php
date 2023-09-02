<?php

namespace SAzizKhan\WpReactKit\Actions;

defined('ABSPATH') || exit;


/**
 * Class AjaxAction
 *
 * @link https://github.com/s-azizkhan/wp-react-kit
 * @since 1.0.0
 * @author Aziz Khan <sakatazizkhan1@gmail.com>
 */
abstract class AjaxAction
{
    /**
     * @since 1.0.0
     * @access protected
     * @var string $id
     */
    protected $id = '';

    /**
     * @since 1.0.0
     * @access protected
     * @var string $prefix
     */
    protected $prefix = WP_REACT_KIT_SHORTNAME . '_';

    /**
     * @since 1.0.0
     * @access protected
     * @var string $authProtected
     */
    protected $authProtected = false;

    /**
     * @since 1.0.0
     * @access protected
     * @var string $emptyExistingAction
     */
    protected $emptyExistingAction = true;

    /**
     * Action constructor.
     *
     * @param $id
     * @since 1.0.0
     * @access public
     */
    public function __construct(string $id, bool $protected = false, bool $emptyExistingAction = true)
    {
        $this->id = $this->prefix . $id;
        $this->authProtected = $protected;
        $this->emptyExistingAction = $emptyExistingAction;
    }

    /**
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_id(): string
    {
        return $this->id;
    }

    /**
     * checks the authentication required or not
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function is_protected(): bool
    {
        return $this->authProtected;
    }

    /**
     * Empty the all existing actions or not
     * @since 1.0.0
     * @access public
     * @return boolean
     */
    public function empty_all_actions(): bool
    {
        return $this->authProtected;
    }

    /**
     * @since 1.0.0
     * @access public
     */
    public function load(): void
    {
        if (!class_exists('woocommerce')) {
            if ($this->empty_all_actions()) {
                remove_all_actions("wc_ajax_{$this->get_id()}");
            }
            add_action("wc_ajax_{$this->get_id()}", array($this, 'execute'));
        }
        /**
         * These legacy handlers are here because Woo adds them and 3rd party plugins
         * sometimes expect them. This is particularly important for WooCommerce Memberships
         * which uses these handlers to detect valid WC ajax requests when the home page is
         * restricted
         */
        if ($this->empty_all_actions()) {
            remove_all_actions("wp_ajax_{$this->get_id()}");
        }
        add_action("wp_ajax_{$this->get_id()}", array($this, 'execute'));

        if (!$this->is_protected()) {
            if ($this->empty_all_actions()) {
                remove_all_actions("wp_ajax_nopriv_{$this->get_id()}");
            }
            add_action("wp_ajax_nopriv_{$this->get_id()}", array($this, 'execute'));
        }
    }

    abstract function action();

    public function execute()
    {
        /**
         * PHP Warning / Notice Suppression
         */
        if (!defined('WP_DEV_MODE')) {
            ini_set('display_errors', 'Off');
        }

        return $this->action();
    }

    /**
     * @param $out
     * @since 1.0.0
     * @access protected
     */
    protected static function out($out, int $status_code = null): void
    {
        ini_set('display_errors', 'Off');

        // TODO: Execute and out (in Action) should be final and not overrideable. Action needs to NOT force JSON as an object. Could use a parameter to flip JSON to object
        if (!defined('CFW_ACTION_NO_ERROR_SUPPRESSION_BUFFER')) {
            @ob_end_clean(); // @phpcs:ignore
        }

        wp_send_json($out, $status_code);
    }
}
