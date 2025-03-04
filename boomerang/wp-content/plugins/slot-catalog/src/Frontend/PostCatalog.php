<?php
namespace SlotCatalog\Frontend;

use SlotCatalog\Query\Query;

class PostCatalog 
{
    /**
     * Отображаем каталог постов
     * @return string - каталог постов
     */
    public static function render(): bool|string {
        $query = new Query();
        $postsIds = $query->getAllPost([], true);
        if(empty($postsIds)) {
            return false;
        }
        $postsData = $query->formatPostResults($postsIds);
        if(empty($postsData)) {
            return false;
        }
        ob_start(); // Включаем буферизацию вывода
        include plugin_dir_path(__FILE__) . '../../templates/posts-catalog.php';
        return ob_get_clean(); // Получаем содержимое буфера и очищаем его
    }
}