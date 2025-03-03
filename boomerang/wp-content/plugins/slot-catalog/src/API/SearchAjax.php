<?php
namespace SlotCatalog\API;

use SlotCatalog\Query\Query;

class SearchAjax{

    /**
     * Регистрация ajax обработчика
     */
    public static function register(): void
    {
        add_action('wp_ajax_searchPostsByTitle', [self::class, 'searchPostsByTitle']); 
        add_action('wp_ajax_nopriv_searchPostsByTitle', [self::class, 'searchPostsByTitle']);
    }

    /**
     * Поиск слотов по названию (с кешированием)
     */
    public static function searchPostsByTitle(): void
    {
        check_ajax_referer('search_nonce', 'nonce');
        $searchTerm = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

        // Создаём ключ кеша
        $cache_key = "search_results_" . md5($searchTerm);
        $cached_result = wp_cache_get($cache_key, 'slot_catalog');

        if ($cached_result !== false) {
            wp_send_json($cached_result);
        }

        // Выполняем запрос в БД, если кеш пуст
        $query = new Query();
        $postsIds = $query->getPostsByTitle($searchTerm);

        if (empty($postsIds)) {
            wp_cache_set($cache_key, [], 'slot_catalog', 300); // Кешируем пустой результат
            wp_send_json([]);
        }

        $result = $query->formatPostResults($postsIds);

        // Сохраняем результат в Redis на 5 минут (300 секунд)
        wp_cache_set($cache_key, $result, 'slot_catalog', 300);

        wp_send_json($result);
    }
}