<?php
namespace SlotCatalog\Query;

use WP_Query;
use WP_Term_Query;

class Query
{
/**
     * @var array<string, int|string>
     */
    private array $defaultArgs = [
        'post_type'      => 'post',      // string
        'posts_per_page' => -1,          // int
        'post_status'    => 'publish',   // string
        'fields'         => 'ids',       // string
    ];
    
    /**
     * Получаем все посты
     * @param array<int|string, mixed> $customArgs - массив с дополнительными параметрами запроса
     * @param bool $filterQueryArgs - фильтровать параметры запроса или нет
     * @return int[]|\WP_Post[] Возвращает массив ID записей или массив объектов WP_Post - массив с постами
     */
    public function getAllPost(array $customArgs = [], bool $filterQueryArgs = false): array
    {
        // Получаем параметры запроса
        $args = $customArgs ? array_merge($this->defaultArgs, $customArgs) : $this->defaultArgs;
        // Фильтруем параметры запроса если нужно
        if ($filterQueryArgs) {
            $args = $this->filterQueryArgs($args);
        }
        // Получаем посты
        $queryPosts = new WP_Query($args);
        $postsList = $queryPosts->posts;
        return $postsList;
    }
    
    /**
     * Фильтруем параметры запроса
     * @param array<int|string, mixed> $args - массив с параметрами запроса
     * @return array<int|string, mixed> - массив с отфильтрованными параметрами запроса
     */
    public function filterQueryArgs(array $args): array
    {
        // Получаем количество постов
        $args['posts_per_page'] = $this->getCountPosts();
        // Получаем отсортированные посты, если есть параметр сортировки в GET параметрах
        $args = $this->getSortingArgs($args);

        $title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $title = sanitize_text_field((string) ($title !== false ? $title : ''));
        if (!empty($title)){
            $args['s'] = $title;
        }
        
        return $args;
    }

    /**
     * Получаем количество постов
     * @return int - количество постов
     */
    public function getCountPosts():int
    {
        // Получаем параметр count и фильтруем его, преобразуем в число
        return filter_input(INPUT_GET, 'count', FILTER_VALIDATE_INT) ?: -1;
    }

    /**
     * Получаем параметры сортировки
     * @param array<int|string, mixed> $args - массив с параметрами запроса
     * @return array<int|string, mixed> - массив с параметрами запроса с учетом сортировки
     */
    public function getSortingArgs($args): array 
    {
        // Получаем параметр сортировки и фильтруем
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $sort = sanitize_text_field((string) ($sort !== false ? $sort : 'asc'));
        // Допустимые значения сортировки
        $sorting_options = [
            'asc'    => ['orderby' => 'title', 'order' => 'ASC'],
            'desc'   => ['orderby' => 'title', 'order' => 'DESC'],
            'random' => ['orderby' => 'rand']
        ];
        // Применяем сортировку к параметрам запроса
        return array_merge($args, $sorting_options[$sort] ?? []);
    }

    /**
     * Получаем посты по имени категории
     * @param string $categoryName - название категории
     * @return int[]|\WP_Post[] - массив с постами
     */
    public function getPostsByCategory(string $categoryName = ''): array
    {
        if(empty($categoryName)) {
            return [];
        }
        // Получаем категорию по имени
        $category = get_term_by('name', $categoryName, 'category');
        if (empty($category) || is_wp_error($category)) {
            return [];
        }
        $args['category__in'] = [$category->term_id]; 
        return $this->getAllPost($args);
    }

    /**
     * Получаем посты по заголовку
     * @param string $title - название поста
     * @return int[]|\WP_Post[] - массив с постами
     */
    public function getPostsByTitle(string $title = ''): array
    {
        if(empty($title)) {
            return $this->getAllPost();
        }
        $args['s'] = $title;
        return $this->getAllPost($args);
    }

    /**
     * Форматируем результаты запроса
     * @param array<int|string, mixed> $postsIds - массив с ID постов
     * @return array<int, array<string, string>> - массив с отформатированными результатами
     */
    public function formatPostResults(array $postsIds): array
    {
        if (empty($postsIds)) {
            return [];
        }
        $results = [];
        foreach ($postsIds as $postID) {
            if(!is_int($postID)) {
                continue;
            }
            $postTitle = esc_html(get_the_title($postID));
            $postLink = esc_url((string) get_permalink($postID));
            $postContent = get_post_field('post_content', $postID);
            $postContent = wp_strip_all_tags($postContent);
            $postContent = wp_trim_words($postContent, 5, '...');
            $imageID = (int) get_post_thumbnail_id($postID);
            $tags = get_the_tags($postID);
            // Проверяем, что это массив и в нём есть хотя бы один элемент
            if (!empty($tags) && !is_wp_error($tags) && isset($tags[0])) {
                $tagName = esc_html($tags[0]->name);
            } else {
                $tagName = ''; // Если тегов нет или произошла ошибка
            }
            $image = wp_get_attachment_image($imageID, 'full', false, ['class' => 'slot-card__image lazy_load']) ?: '';
            $results[] = [
                'title' => $postTitle,
                'link' => $postLink,
                'content' => $postContent,
                'tag' => $tagName,
                'image' => $image,
            ];
        }
        return $results;
    }

    /**
     * Получаем массив категорий
     * @return array<int|string, mixed> - массив с категориями
     */
    public function getCategories(int $parentCatID = 0): array 
    {
        // Создаём ключ кеша
        $cache_key = "category_results_" . md5('cat' . $parentCatID);
        $cached_result = wp_cache_get($cache_key, 'slot_catalog');
      
        if ($cached_result !== false && is_array($cached_result)) {
            return $cached_result;
        }

        $uncategorized_id = get_category_by_slug('uncategorized')->term_id ?? 0;
        $term_query = new WP_Term_Query([
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
            'exclude'    => [$uncategorized_id],
            'parent'     => $parentCatID,
        ]);

        $tems = $term_query->terms;
        // Сохраняем результат в Redis на 5 минут (300 секунд)
        wp_cache_set($cache_key, $tems, 'slot_catalog', 300);
        return $tems;
    }
}