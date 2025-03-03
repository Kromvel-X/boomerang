"use strict";
(() => {
    window.addEventListener("load", function() {
        validInputNumber();
        handlePseudoFormSubmit();
    });


    /**
     * Получаем данные из псевдоформы
     * @returns {Object} - Объект с данными формы
    */
    function getPseudoFormData(){
        const form = document.querySelector('.bonus-pseudo-form');
        if (!form) return;
        const bonusName = form.querySelector('.bonus-list__name').innerText;
        const bonnusAmount = document.getElementById('deposit-amount').value;
        const bonnusCurrency = document.getElementById('currency-value').value;
        const data = {
            name: bonusName,
            amount: bonnusAmount,
            currency: bonnusCurrency
        }
        return data;
    }

    /**
     * Обработчик отправки псевдоформы
     * При клике на кнопку формы, получаем данные формы и выводим их в алерте
    */
    function handlePseudoFormSubmit(){
        const pseudoFormButton = document.querySelector('.bonus-pseudo-form__button');
        if (!pseudoFormButton) return;
           
        pseudoFormButton.addEventListener('click', function(event){
            event.preventDefault();

            const form = event.target.closest('.bonus-pseudo-form');
            if (!form) return;
            
            if (form.querySelector('.error-message')){
                pseudoFormButton.classList.add('error');
                return;
            }

            const data = getPseudoFormData();
            alert(
                `✅ Данные успешно получены! 🎉\n\n` +
                `📌 Bonus: ${data.name}\n` +
                `💰 Amount: ${data.amount}\n` +
                `💳 Currency: ${data.currency}\n\n` +
                `🔄 Готово к дальнейшей обработке!`
            );
        });
    };

    /**
     * Валидация ввода числа
    */
    function validInputNumber() {
        const input = document.getElementById('deposit-amount');
        if (!input) return;
    
        input.addEventListener('input', function(event) {
            const numValue = normalizeNumber(event.target.value);
            event.target.value = numValue;
            // Проверяем, является ли введённое значение отрицательным
            if (numValue <= 0 || isNaN(numValue)) {
                event.target.value = 0; // Сбрасываем значение на 0
                // Создаём или находим сообщение об ошибке
                let errorMess = document.querySelector('.error-message');
                if (errorMess) {
                   return;
                }
                
                errorMess = document.createElement('div');
                errorMess.classList.add('error-message');
                errorMess.innerText = 'Только положительное!';
                event.target.after(errorMess);
                event.target.classList.add('error');
            } else {
                event.target.classList.remove('error');
                // Удаляем сообщение об ошибке, если оно есть
                const errorMess = document.querySelector('.error-message');
                if (errorMess) errorMess.remove();

                const form = event.target.closest('.bonus-pseudo-form');
                if (!form) return;
                const error = form.querySelector('.bonus-pseudo-form__button.error');
                if (error) error.classList.remove('error');
            }
        });
    }

    /**
     * Нормализация числа
     * @param {string|number} value - Число
     * @returns {number} - Нормализованное число
    */
    function normalizeNumber(value) {
        if (typeof value !== 'string') value = String(value);

        // Заменяем запятую на точку
        value = value.replace(',', '.');

        // Блокируем экспоненциальную запись (например, 6e44)
        if (/e/i.test(value)) return NaN;

        // Удаляем все символы, кроме цифр и точки
        value = value.replace(/[^\d.]/g, '');

        // Проверяем корректность десятичной точки (чтобы не было "1..2")
        if ((value.match(/\./g) || []).length > 1) return NaN; // Более одной точки → ошибка

        // Удаляем ведущие нули только если число НЕ дробное
        if (!value.includes('.')) {
            value = value.replace(/^0+/, '') || '0'; // Если всё было нулями, оставляем "0"
        }

        let numValue = Number(value);
        
        return Number.isFinite(numValue) ? numValue : NaN; // Возвращаем NaN, если число невалидное
    }
})();