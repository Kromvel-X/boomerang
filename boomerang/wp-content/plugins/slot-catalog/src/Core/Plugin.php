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
     */
    public static function init() {
        add_action('init', [SlotCatalogShortcode::class, 'register']);
        add_action('init', [SlotSliderShortcode::class, 'register']);
        SearchAjax::register();
        FilterCategoriesAjax::register();
        self::initAPI();
       
    }

    public static function initAPI() {
        SlotCatalogAPIendpoint::register();
    }
}