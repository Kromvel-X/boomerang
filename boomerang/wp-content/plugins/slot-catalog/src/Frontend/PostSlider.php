<?php
namespace SlotCatalog\Frontend;

use SlotCatalog\Query\Query;

class PostSlider 
{
    /**
     * Отображаем каталог постов
     * @return bool|string  - каталог постов
     */
    public static function render(): bool|string 
    {
        $query = new Query();
        $postsIds = $query->getAllPost([], false);
        if(empty($postsIds)) {
            return false;
        }
        $postsData = $query->formatPostResults($postsIds);
        if(empty($postsData)) {
            return false;
        }
        ob_start(); // Включаем буферизацию вывода
        include plugin_dir_path(__FILE__) . '../../templates/posts-slider.php';
        return ob_get_clean(); // Получаем содержимое буфера и очищаем его
    }
}