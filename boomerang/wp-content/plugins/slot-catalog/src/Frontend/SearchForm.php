<?php 
namespace SlotCatalog\Frontend;

use SlotCatalog\Query\Query;

class SearchForm 
{
    /**
     * Отображаем форму поиска
     * @return string - форма поиска
     */
    public static function render(): bool|string {
        $query = new Query();
        $providers = $query->getCategories('12');
        $games = $query->getCategories('11');

        if(empty($games) || empty($providers)){
            return false;
        }

        ob_start(); // Включаем буферизацию вывода
        include plugin_dir_path(__FILE__) . '../../templates/search-form.php';
        return ob_get_clean(); // Получаем содержимое буфера и очищаем его
    }
}