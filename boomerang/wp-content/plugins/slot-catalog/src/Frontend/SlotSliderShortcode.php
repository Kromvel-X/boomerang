<?php
namespace SlotCatalog\Frontend;

use SlotCatalog\Frontend\PostSlider;
use SlotCatalog\Frontend\SlotCatalogShortcode;

class SlotSliderShortcode extends SlotCatalogShortcode
{
    protected static string $shortCodeName = 'slot-slider';
    
    /**
     * Отображаем слайдер слотов
     * @return string - слайдер слотов
     * 
     */
    public static function render(): string
    {
        add_action('wp_footer', [static::class, 'connectScripts']);
        add_action('wp_footer', [static::class, 'connectStyles']);
        $html = PostSlider::render();
        return is_string($html) ? $html : '';
    }

    /**
     * Подключаем стили первого экрана
     *
     * @return void
     */
    public static function insertStyleFirst(): void
    {
        global $shortStylesFirst;
        if(!empty($shortStylesFirst['slot-catalog'])){
            return;
        }
		$shortStylesFirst['slot-catalog'][] = plugin_dir_path(__FILE__) . '../../assets/css/style-first.min.css';
    }
}
