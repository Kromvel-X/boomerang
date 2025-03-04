<?php
namespace SlotCatalog\Frontend;

use SlotCatalog\Frontend\SearchForm;
use SlotCatalog\Frontend\PostCatalog;
class SlotCatalogShortcode
{
    protected static string $shortCodeName = 'slot-catalog';

    /**
     * Регистрируем шорткод
     */
    public static function register(): void {
        add_shortcode( static::$shortCodeName, [static::class, 'render']);
        static::insertStyleFirst();
    }

    /**
     * Отображаем каталог слотов
     * @return string - каталог слотов
     */
    public static function render(): string
    {
        static::connectScripts();
        add_action('wp_footer', [static::class, 'connectScripts']);
        add_action('wp_footer', [static::class, 'connectStyles']);
        $html = SearchForm::render();
        $html .= PostCatalog::render();
        return $html;
    }

    /**
     * Подключаем скрипты
     * @return void
     */
    public static function connectScripts(): void
    {
        $realpathScript = plugin_dir_path(__FILE__) . '../../assets/js/script.min.js';
        $scripts = plugin_dir_url(__FILE__) . '../../assets/js/script.min.js';

        wp_enqueue_script( 
            'slots-catalog', 
            $scripts, 
            array(), 
            (string) filemtime( $realpathScript ) ?: '1.0', 
            array( 
                'strategy'  => 'defer',
                'in_footer' => true,
            )
        );

        wp_localize_script('slots-catalog', 'slots_catalog', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('search_nonce'),
        ]);
    }

    /**
     * Подключаем стили
     * @return void
     */
    public static function connectStyles(): void
    {
        $realpathStyle = plugin_dir_path(__FILE__) . '../../assets/css/style.min.css';
        $styles = plugin_dir_url(__FILE__) . '../../assets/css/style.min.css';

        wp_enqueue_style( 
            'slots-catalog', 
            $styles, 
            array(), 
            (string) filemtime( $realpathStyle ) ?: '1.0'
        );
    }

    /**
     * Подключаем стили первого экрана
     *
     * @return void
     */
    public static function insertStyleFirst(): void
    {
        global $shortStylesFirst;
        if(!empty($shortStylesFirst[static::$shortCodeName])){
            return;
        }
		$shortStylesFirst[static::$shortCodeName][] = plugin_dir_path(__FILE__) . '../../assets/css/style-first.min.css';
    }
}