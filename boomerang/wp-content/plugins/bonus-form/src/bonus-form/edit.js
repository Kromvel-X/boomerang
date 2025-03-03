import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, MediaUpload } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useState } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
    const { title, buttonText, bonuses } = attributes;
    const [bonusList, setBonusList] = useState(bonuses);

    // Обновление заголовка и текста кнопки
    const updateField = (field, value) => {
        setAttributes({ [field]: value });
    };

    // Добавление нового бонуса
    const addBonus = () => {
        const newBonuses = [...bonusList, { name: '', description: '', imageUrl: '' }];
        setBonusList(newBonuses);
        setAttributes({ bonuses: newBonuses });
    };

    // Обновление значений бонусов
    const updateBonus = (index, field, value) => {
        const newBonuses = [...bonusList];
        newBonuses[index][field] = value;
        setBonusList(newBonuses);
        setAttributes({ bonuses: newBonuses });
    };

    // Удаление бонуса
    const removeBonus = (index) => {
        const newBonuses = bonusList.filter((_, i) => i !== index);
        setBonusList(newBonuses);
        setAttributes({ bonuses: newBonuses });
    };

    return (
        <div { ...useBlockProps() }>
            {/* Поле заголовка */}
            <RichText
                tagName="h2"
                value={ title }
                onChange={ (value) => updateField('title', value) }
                placeholder={ __('Введите заголовок...', 'boomerang') }
            />

            {/* Группа бонусов */}
            {bonusList.map((bonus, index) => (
                <div key={index} className="bonus-group">
                    <RichText
                        tagName="h3"
                        value={ bonus.name }
                        onChange={(value) => updateBonus(index, 'name', value)}
                        placeholder={ __('Имя бонуса...', 'boomerang') }
                    />
                    <RichText
                        tagName="p"
                        value={ bonus.description }
                        onChange={(value) => updateBonus(index, 'description', value)}
                        placeholder={ __('Описание бонуса...', 'boomerang') }
                    />
                    {/* <MediaUpload
                        onSelect={(media) => updateBonus(index, 'imageUrl', media.url)}
                        type="image"
                        value={ bonus.imageUrl }
                        render={({ open }) => (
                            <Button onClick={open} className="button-select-image">
                                {bonus.imageUrl ? <img src={bonus.imageUrl} alt="Бонус" style={{ maxWidth: '100px' }} /> : __('Выберите изображение', 'boomerang')}
                            </Button>
                        )}
                    /> */}
                    <Button onClick={() => removeBonus(index)} className="button-remove">
                        {__('Удалить бонус', 'boomerang')}
                    </Button>
                </div>
            ))}

            <Button onClick={addBonus} className="button-add">
                {__('Добавить бонус', 'boomerang')}
            </Button>

            {/* Поле текста кнопки */}
            <RichText
                tagName="button"
                value={ buttonText }
                onChange={ (value) => updateField('buttonText', value) }
                placeholder={ __('Текст кнопки...', 'boomerang') }
            />
        </div>
    );
}