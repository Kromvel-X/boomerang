<?php 
namespace SlotCatalog\Core;

use SlotCatalog\Frontend\SlotCatalogShortcode;
use SlotCatalog\API\SearchAjax;
use SlotCatalog\API\FilterCategoriesAjax;
use SlotCatalog\API\SlotCatalogAPIendpoint;
use SlotCatalog\Frontend\SlotSliderShortcode;
class Plugin
{
    /**
     * Инициализация плагина
     * @return void
     */
    public static function init(): void {
        add_action('init', [SlotCatalogShortcode::class, 'register']);
        add_action('init', [SlotSliderShortcode::class, 'register']);
        SearchAjax::register();
        FilterCategoriesAjax::register();
        self::initAPI();
       
    }

    /**
     * Инициализация API
     * @return void
     */
    public static function initAPI(): void {
        SlotCatalogAPIendpoint::register();
    }
}