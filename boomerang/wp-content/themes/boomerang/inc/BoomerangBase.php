<?php

class BoomerangBase
{
    /**
     * Инициализация темы Boomerang
     * @return void
     */
    public static function init(): void
    {
        add_action('after_setup_theme', [static::class, 'themeSetup']);
        add_action('wp_enqueue_scripts', [static::class, 'connectScripts'], 1);
        add_action('boomerang_head_load_first', [static::class, 'insertFirstStylesFromShortcodes'],10);
        add_action('boomerang_head_load_first', [static::class, 'insertFonts'], 1);
        add_action('boomerang_head_load_first', [static::class, 'insertStyleFirst'], 1);
        add_action('wp_footer', [static::class, 'connectStyles'], 1);
        add_action('wp_enqueue_scripts', [static::class, 'removeStyles'], 100);
        add_filter('style_loader_tag', [static::class, 'asyncLoadCss'], 10, 4);
        add_filter('wp_get_attachment_image_attributes', [static::class, 'customLazyLoadImages'], 10, 3);
        add_action('wp_head', [static::class, 'preloadFonts'], 1);
        // add_filter('wp_is_application_passwords_available', '__return_true');
    }

    /**
     * Настройка темы
     * @return void
     */
    public static function themeSetup(): void {
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
    }

    /**
     * Подключаем скрипты
     * @return void
     */
    public static function connectScripts(): void
    {
        $scripts = get_stylesheet_directory_uri() . '/js/script.min.js';
        $realpathScript = get_stylesheet_directory().'/js/script.min.js';
        wp_enqueue_script( 
            'main', 
            $scripts, 
            array(), 
            (string) filemtime( $realpathScript ) ?: '1.0',
            array( 
                'strategy'  => 'defer',
                'in_footer' => true,
            )
        );
    }

    /**
     * Всраиваем стили из шорткодов
     * @return void
     */
    public static function insertFirstStylesFromShortcodes(): void  
    {
        echo self::getFirstStyleFromShortcode();
    }

    /**
     * Получаем стили первого экрана из шорткодов
     * @return string - стили
     */
    public static function getFirstStyleFromShortcode(): string
    {
        global $shortStylesFirst;
        $ret = '';
        $content = '';

        if(is_singular())
        {
            $content = get_the_content();
        }

        if(empty($content))
        {
            return $ret;
        }

        if(is_array(value: $shortStylesFirst) && count($shortStylesFirst) > 0) {
            foreach($shortStylesFirst as $key=>$value) {
                if(is_array($value) && has_shortcode($content, $key)) {
                    $ret .= '<style>';
                    
                    foreach($value as $link){
                        $ret .= "";
                        $buffer = '';
                        $path_to_css = $link;
                        if (is_string($link)) {
                            $buffer .= file_get_contents($link);
                        }
                        if (is_string($path_to_css)) {
                            $ret .= "\n\n /* ==========================================================================" . basename($path_to_css) . "========================================================================== */ \n\n";
                        }
                        $ret .= $buffer;
                        
                    }
                    $ret .= '</style>';  
                }
            }
        }
        return $ret;
    }

    /**
     * Встраиваем шрифты
     * @return void
     */
    public static function insertFonts(): void
    { 
        $ret = "";
        $path_to_css = get_stylesheet_directory() . '/css/fonts.min.css';
        $buffer = file_get_contents( $path_to_css );
        $ret .= "\n\n /* ==========================================================================" . basename( $path_to_css )  . "========================================================================== */ \n\n";
        if ($buffer !== false) {
            $ret .= str_replace('/fonts/', get_stylesheet_directory_uri() . '/fonts/', $buffer);
        }
        echo '<style>'.$ret.'</style>';
    }

    /**
     * Встраиваем стили первого экрана
     * @return void
     */
    public static function insertStyleFirst(): void
    { 
        $ret = "";
        $path_to_css = get_stylesheet_directory() . '/css/style_first.min.css';
        $buffer = file_get_contents( $path_to_css );
        $ret .= "\n\n /* ==========================================================================" . basename( $path_to_css )  . "========================================================================== */ \n\n";
        $ret .= $buffer;
        echo '<style>'.$ret.'</style>';
    }

    /**
     * Подключаем стили
     * @return void
     */
    public static function connectStyles(): void
    {
        $realpathStyle =get_stylesheet_directory() . '/css/style.min.css';
        $styles = get_stylesheet_directory_uri() . '/css/style.min.css';
        wp_enqueue_style( 
            'boomerang-style-main', 
            $styles, 
            array(), 
            (string) filemtime( $realpathStyle ) ?: '1.0',
        );
    }

    /**
     * Удаляем стили по умолчанию
     * @return void
     */
    public static function removeStyles(): void
    {
        if (is_admin()) return;

        
        wp_dequeue_style('wp-block-library'); 
        wp_dequeue_style('wp-block-library-theme'); 
        wp_dequeue_style('wc-blocks-style');

        wp_dequeue_style('global-styles'); 

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        wp_dequeue_style('classic-theme-styles');
    }

    /**
     * Асинхронная загрузка стилей
     * @param string $html - Тег ссылки для поставленного в очередь стиля.
     * @param string $handle - Зарегистрированное имя стиля.
     * @param string $href - Исходный URL таблицы стилей.
     * @param string $media - Атрибут media таблицы стилей
     * @return string
     */
    public static function asyncLoadCss($html, $handle, $href, $media): string 
    {
        $html = '<link rel="preload" href="' . esc_url($href) . '" as="style" id="' . esc_attr($handle) . '" onload="this.onload=null;this.rel=\'stylesheet\'">'
        . '<noscript>' . $html . '</noscript>';
        
        return $html;
    }

    /**
     * Ленивая загрузка изображений
     * @param array<string|int, mixed> $attr - Атрибуты изображения
     * @param WP_Post $attachment - Прикрепленное изображение поста.
     * @param string|array<int> $size - Размер изображения
     * @return mixed
     */
    public static function customLazyLoadImages($attr, $attachment, $size): mixed 
    {
        // Получаем массив srcset
        $image_srcset = wp_get_attachment_image_srcset($attachment->ID, $size);
        // Получаем URL самого большого изображения
        $image_full = wp_get_attachment_image_src($attachment->ID, 'full');
        // Заглушка
        $placeholder = get_template_directory_uri() . '/images/preload.svg';
        // Заменяем атрибуты
        if ($image_srcset && $image_full) {
            $attr['src'] = esc_url($image_full[0]);
            $attr['srcset'] = esc_url($placeholder) . ' 150w';
            $attr['data-srcset'] = esc_attr($image_srcset);
        }
        return $attr;
    }

    /**
     * Предзагрузка шрифтов
     * @return void
     */
    public static function preloadFonts(): void
    {
        echo '<link rel="preload" href="'.get_stylesheet_directory_uri().'/fonts/subset-Montserrat-Bold.woff2" as="font" type="font/woff2" crossorigin="">';
        echo '<link rel="preload" href="'.get_stylesheet_directory_uri().'/fonts/subset-Montserrat-Regular.woff2" as="font" type="font/woff2" crossorigin="">';
    }

    // /**
    //  * Регистрация меню
    //  */
    // public static function my_theme_register_menus() {
    //     register_nav_menus(array(
    //         'header-menu' => __('Header Menu'),
    //     ));
    // }
}
BoomerangBase::init();