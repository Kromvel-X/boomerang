<?php
namespace SlotCatalog\Query;

use WP_Query;
use WP_Term_Query;

class Query
{
    private array $defaultArgs = [
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
    ];
    
    /**
     * Получаем все посты
     * @param array $customArgs - массив с дополнительными параметрами запроса
     * @param bool $filterQueryArgs - фильтровать параметры запроса или нет
     * @return array - массив с постами
     */
    public function getAllPost(array $customArgs = [], bool $filterQueryArgs = false):array
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
     * @param array $args - массив с параметрами запроса
     * @return array - массив с отфильтрованными параметрами запроса
     */
    public function filterQueryArgs(array $args): array
    {
        // Получаем количество постов
        $args['posts_per_page'] = $this->getCountPosts();
        // Получаем отсортированные посты, если есть параметр сортировки в GET параметрах
        $args = $this->getSortingArgs($args);

        $title = isset($_GET['title']) ? sanitize_text_field($_GET['title']) : '';
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
        $count = filter_var($_GET['count'] ?? null, FILTER_VALIDATE_INT);
        return $count !== false ? $count : -1;
    }

    /**
     * Получаем параметры сортировки
     * @param array $args - массив с параметрами запроса
     * @return array - массив с параметрами запроса с учетом сортировки
     */
    public function getSortingArgs($args): array 
    {
        // Получаем параметр сортировки и фильтруем
        $sort = sanitize_text_field($_GET['sort'] ?? 'asc');
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
     * @return array - массив с постами
     */
    public function getPostsByCategory(string $categoryName = ''): array
    {
        if(empty($categoryName)) {
            return [];
        }
        // Получаем категорию по имени
        $category = get_term_by('name', $categoryName, 'category');
        if (empty($categoryName)) {
            return [];
        }
        $args['category__in'] = [$category->term_id]; 
        return $this->getAllPost($args);
    }

    /**
     * Получаем посты по заголовку
     * @param string $title - название поста
     * @return array - массив с постами
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
     * @param array $postsIds - массив с ID постов
     * @return array - массив с отформатированными результатами
     */
    public function formatPostResults(array $postsIds): array
    {
        if (empty($postsIds)) {
            return [];
        }
        $results = [];
        foreach ($postsIds as $postID) {
            $postTitle = esc_html(get_the_title($postID));
            $postLink = esc_url(get_permalink($postID));
            $postContent = get_post_field('post_content', $postID);
            $postContent = wp_strip_all_tags($postContent);
            $postContent = wp_trim_words($postContent, 5, '...');
            $imageID = get_post_thumbnail_id($postID);
            $tag = get_the_tags($postID);
            $tagName = (!empty($tag) && isset($tag[0])) ? esc_html($tag[0]->name) : '';
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
     * @return array - массив с категориями
     */
    public function getCategories(string $parentCatID = ''): array 
    {
        $uncategorized = get_category_by_slug('uncategorized');
        $uncategorized_id = $uncategorized ? $uncategorized->term_id : 0;
        $term_query = new WP_Term_Query([
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
            'exclude'    => [$uncategorized_id],
            'parent'     => $parentCatID,
        ]);
        return $term_query->terms;
    }
}