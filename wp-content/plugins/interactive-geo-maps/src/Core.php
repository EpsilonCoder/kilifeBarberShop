<?php

namespace Saltus\WP\Plugin\Saltus\InteractiveMaps;

/**
 * The core class, where logic is defined.
 */
class Core
{
    /**
     * Unique identifier (slug)
     *
     * @var string
     */
    public  $name ;
    /**
     * Current version.
     *
     * @var string
     */
    public  $version ;
    /**
     * Plugin file path
     *
     * @var string
     */
    public  $file_path ;
    /**
     * Saltus framework instance
     *
     * @var object
     */
    public  $framework ;
    /**
     * Arrays that will store data to be localized for javascript files
     *
     * @var array
     */
    public  $script_localize_data ;
    public  $script_localize_options ;
    public  $script_localize_async_srcs ;
    /**
     * Content to output in footer with script tags
     *
     * @var string
     */
    public  $footer_extra ;
    /**
     * Content to output in footer
     *
     * @var string
     */
    public  $footer_content ;
    /**
     * Instance of Actions class
     *
     * @var object
     */
    public  $actions ;
    /**
     * Setup the class variables
     *
     * @param string $name      Plugin name.
     * @param string $version   Plugin version. Use semver.
     * @param string $file_path Plugin file path
     * @param string $saltus    Saltus Framework
     */
    public function __construct(
        string $name,
        string $version,
        string $file_path,
        $framework
    )
    {
        $this->name = $name;
        $this->version = $version;
        $this->file_path = $file_path;
        $this->framework = $framework;
    }
    
    /**
     * Get the identifier, also used for i18n domain.
     *
     * @return string The unique identifier (slug)
     */
    public function get_name()
    {
        return $this->name;
    }
    
    /**
     * Get the current version.
     *
     * @return string The current version.
     */
    public function get_version()
    {
        return $this->version;
    }
    
    /**
     * Start the logic for this plugins.
     *
     * Runs on 'plugins_loaded' which is pre- 'init' filter
     */
    public function init()
    {
        $this->set_locale();
        $this->set_assets();
        $this->register_shortcode();
        $this->set_localize();
        $this->set_footer_content();
        $this->set_actions();
        $this->prepare_edit_screen();
        $this->register_util_shortcodes();
        $this->register_blocks();
        $this->prepare_meta_sanitize();
        $this->add_integrations();
        $this->prepare_assets_src();
    }
    
    /**
     * Add filters to check if assets src is correct (fix bitnami issue for csf assets)
     *
     * @return void
     */
    public function prepare_assets_src()
    {
        add_filter( 'script_loader_src', array( $this, 'check_admin_assets_src' ) );
        add_filter( 'style_loader_src', array( $this, 'check_admin_assets_src' ) );
    }
    
    /**
     * Function to remove unwanted parts from src url - fix opts/bitnami issue
     *
     * @param [type] $url
     * @return void
     */
    public function check_admin_assets_src( $url )
    {
        global  $current_screen ;
        if ( !is_admin() || !isset( $current_screen ) || 'igmap' !== $current_screen->post_type ) {
            return $url;
        }
        return str_replace( '/plugins/opt/interactive-geo-maps', '/plugins/interactive-geo-maps', $url );
    }
    
    /**
     * Add css class to body in admin to control the show/hide addons menu
     *
     * @return void
     */
    public function add_admin_body_class()
    {
        add_filter( 'admin_body_class', function ( $classes ) {
            return $classes . ' igm-pro';
        } );
    }
    
    /**
     * Adds integration widgets
     *
     * @return void
     */
    public function add_integrations()
    {
        // elementor widget
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'elementor_widget' ] );
    }
    
    /**
     * Registers Elementor Widget
     *
     * @return void
     */
    public function elementor_widget()
    {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Plugin\Integrations\ElementorMapWidget() );
    }
    
    /**
     * Add filter for Codestar to sanitize properly the meta info when saving
     *
     * @return void
     */
    public function prepare_meta_sanitize()
    {
        add_filter(
            'csf_map_info_save',
            array( $this, 'sanitize_meta_save' ),
            1,
            3
        );
    }
    
    /**
     * Set initial empty array for localize data
     *
     * @return void
     */
    private function set_localize()
    {
        $this->script_localize_data = [];
        $this->script_localize_options = [];
        $this->script_localize_async_srcs = [];
    }
    
    /**
     * Set initial empty footer content
     *
     * @return void
     */
    private function set_footer_content()
    {
        $this->extra_scripts = '';
        $this->extra_styles = '';
        $this->footer_content = '';
        $this->footer_scripts = '';
    }
    
    /**
     * Add content to localize data array - data for the maps
     *
     * @param string $value
     * @return void
     */
    public function add_localize_data( $value )
    {
        array_push( $this->script_localize_data, $value );
    }
    
    /**
     * Add content to options localize data array
     *
     * @param string $value
     * @return void
     */
    public function add_localize_options( $value )
    {
        array_push( $this->script_localize_options, $value );
    }
    
    /**
     * Add content to options localize async sources array
     *
     * @param string $value
     * @return void
     */
    public function add_localize_async_srcs( $value )
    {
        array_push( $this->script_localize_async_srcs, $value );
    }
    
    /**
     * Add content to footer scripts
     *
     * @param string $value
     * @return void
     */
    public function add_extra_scripts( $value )
    {
        $this->extra_scripts .= $value;
    }
    
    /**
     * Add content styles
     *
     * @param string $value
     * @return void
     */
    public function add_extra_styles( $value )
    {
        $this->extra_styles .= $value;
    }
    
    /**
     * Add content to footer
     *
     * @param string $value
     * @return void
     */
    public function add_footer_content( $value )
    {
        $this->footer_content .= $value;
    }
    
    /**
     * Collect content for raw scripts
     *
     * @param string $value
     * @return void
     */
    public function add_footer_scripts( $value )
    {
        $this->footer_scripts .= $value;
    }
    
    /**
     * Instanciate actions class
     *
     * @return void
     */
    public function set_actions()
    {
        $this->actions = new Plugin\Actions( $this );
    }
    
    /**
     * Load translations
     */
    private function set_locale()
    {
        $i18n = new Plugin\I18n( $this->name );
        $i18n->load_plugin_textdomain( dirname( $this->file_path ) );
    }
    
    /**
     * Load assets
     */
    private function set_assets()
    {
        $assets = new Plugin\Assets( $this );
        $assets->load_assets();
    }
    
    /**
     * Register Shortcode
     */
    public function register_shortcode()
    {
        add_shortcode( 'display-map', array( $this, 'render_shortcode' ) );
    }
    
    /**
     * Register blocks
     */
    public function register_blocks()
    {
        $map_block = new Plugin\Blocks\MapBlock( $this );
    }
    
    /* Util Shortcodes */
    public function register_util_shortcodes()
    {
        $dropdown_preview = new Plugin\Utils\MapListDropdown( $this );
        $list_preview = new Plugin\Utils\MapListOutput( $this );
        $current_map_preview = new Plugin\Utils\MapListCurrent( $this );
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode( $atts )
    {
        if ( !isset( $atts['id'] ) ) {
            return;
        }
        // ID can also accept a shortcode with JSON:
        // $json = '{"shortcode":"display-number", "atts": { "number":"111111" }}';
        // usefull to populate the id field with a value coming from a meta field for example
        
        if ( !is_numeric( $atts['id'] ) && $this->isJson( $atts['id'] ) ) {
            $json = json_decode( $atts['id'], true );
            // maybe there is a way to directly find out the callback function of a given shortcode
            // instead of using the code below?
            
            if ( isset( $json['shortcode'] ) ) {
                $shortcode = '[' . $json['shortcode'] . ' ';
                if ( isset( $json['atts'] ) ) {
                    foreach ( $json['atts'] as $key => $value ) {
                        $shortcode .= sprintf( '%1$s="%2$s" ', $key, $value );
                    }
                }
                $shortcode .= ']';
                $atts['id'] = do_shortcode( $shortcode );
            }
        
        }
        
        $atts['id'] = $atts['id'];
        $map = new Plugin\Map( $this );
        $html = $map->render( $atts, $this );
        // add footer scripts
        add_action( 'wp_footer', array( $this, 'extra_styles' ) );
        add_action( 'wp_footer', array( $this, 'footer_content' ) );
        return $html;
    }
    
    /**
     * Init edit screen
     *
     * @return void
     */
    public function prepare_edit_screen()
    {
        $edit = new Plugin\EditMap( $this );
    }
    
    /**
     * Add extra styles
     *
     * @return void
     */
    public function extra_styles()
    {
        if ( '' !== $this->extra_styles ) {
            wp_add_inline_style( $this->name . '_main', $this->extra_styles );
        }
    }
    
    /**
     * Output footer content
     *
     * @return void
     */
    public function footer_content()
    {
        
        if ( '' !== $this->footer_content && !is_admin() ) {
            $html = '<div id="igm-hidden-footer-content">' . $this->footer_content . '</div>';
            // we should sanitize for security, but users want to include all kinds of content, including forms.
            /*
            	$allowed_html = wp_kses_allowed_html( 'post' );
            	$allowed_html['style'] = [
            		'type' => true,
            	];
            	echo wp_kses( $html, $allowed_html );
            */
            echo  $html ;
        }
    
    }
    
    /**
     * Check if string is valid json
     *
     * @param [type] $string
     * @return boolean
     */
    public function isJson( $string )
    {
        json_decode( $string );
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Function to sanitize meta on save
     *
     * @param array $request with meta info
     * @param int $post_id
     * @param obj $csf class
     * @return array
     */
    public function sanitize_meta_save( $request, $post_id, $csf )
    {
        if ( empty($request) || !is_array( $request ) ) {
            return $request;
        }
        // if map_info for regions or markers doesn't have useDefaults,
        // it's a free map, we need to make sure we save the useDefaults for backward compatibility
        // in case use upgrades.
        if ( isset( $request['regions'] ) && is_array( $request['regions'] ) && !empty($request['regions']) && !isset( $request['regions'][0]['useDefaults'] ) ) {
            foreach ( $request['regions'] as $key => $field ) {
                if ( !isset( $field['useDefaults'] ) ) {
                    $request['regions'][$key]['useDefaults'] = '1';
                }
            }
        }
        if ( isset( $request['roundMarkers'] ) && is_array( $request['roundMarkers'] ) && !empty($request['roundMarkers']) && !isset( $request['roundMarkers'][0]['useDefaults'] ) ) {
            foreach ( $request['roundMarkers'] as $key => $field ) {
                if ( !isset( $field['useDefaults'] ) ) {
                    $request['roundMarkers'][$key]['useDefaults'] = '1';
                }
            }
        }
        //replace line breaks on meta info to make it compatible with export
        array_walk_recursive( $request, function ( &$value ) {
            $value = str_replace( "\r\n", "\n", $value );
        } );
        return $request;
    }

}