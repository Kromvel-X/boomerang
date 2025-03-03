<?php 
namespace SlotCatalog\API;

use SlotCatalog\Query\Query;
use WP_REST_Request;
use WP_Error;
use WP_REST_Response;

class SlotCatalogAPIendpoint {
    /**
     * Регистрируем маршруты REST API
     */
    public static function register() {
        add_action('rest_api_init', [self::class, 'registerRoutes']);
    }

    /**
     * Регистрация эндпоинтов REST API
     */
    public static function registerRoutes(): void {
        register_rest_route('testtask/v1', '/slots/get', [
            'methods' => 'GET',
            'callback' => [self::class, 'handleSubmission'],
            // 'permission_callback' => function() {
            //     return current_user_can('read'); // Только пользователи с правами "read"
            // },
            'permission_callback' => '__return_true', // Для тестирования
            'args' => [
                'per_page' => [
                    'required' => false,
                    'default' => 10,
                    'sanitize_callback' => function($value) {
                        $value = absint($value);
                        return min(max($value, 1), 50); // Ограничение от 1 до 50
                    }
                ]
            ]
        ]);
    }

    /**
     * Обрабатываем запрос REST API
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error Ответ REST API
     */
    public static function handleSubmission(WP_REST_Request $request): WP_Error|WP_REST_Response {
        $per_page = $request->get_param('per_page');

        // Кэшируем запрос
        $cache_key = "slot_catalog_posts_{$per_page}";
        $result = wp_cache_get($cache_key, 'slot_catalog');

        if (!empty($result)) {
            return rest_ensure_response($result); 
        }
        
        if (!class_exists(Query::class)) {
            return new WP_Error('query_not_found', 'Ошибка: Query class не найден', ['status' => 500]);
        }

        $query = new Query();
        $postsIDS = $query->getAllPost(['posts_per_page' => $per_page]);

        // Формируем массив только с существующими постами
        $result = array_filter(array_map(function ($postID) {
            return [
                'name'  => get_the_title($postID),
                'thumb' => get_the_post_thumbnail_url($postID) ?: '',
                'slug'  => get_post_field('post_name', $postID) ?: '',
            ];
        }, $postsIDS));

        // Сохраняем результат в кэше на 5 минут
        wp_cache_set($cache_key, $result, 'slot_catalog', 300);
        // error_log('Cache set: ' . json_encode($result));
        return rest_ensure_response($result);
    }
}