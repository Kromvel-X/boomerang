"use strict";
(() => {
    window.addEventListener("load", function() {
        document.addEventListener("scroll", initEvent, { once: true });
        document.addEventListener("mousemove", initEvent, { once: true });
        document.addEventListener("touchstart", initEvent, { once: true });
    });

    /*
    * Инициализируем функции при скролле, наведении и таче.
    *
    */
    function initEvent(){
        // проверяем были ли инициализированы
        if (window.initEvent) {
            return false;
        }
    
        window.initEvent = true;
    
        // Запускаем функции
        requestAnimationFrame(() => {
            lazyloadImg();
            toggleDropdown();
        });

        requestAnimationFrame(() => {
            deleteClassOptimizeRender();
        });

    }

    /**
     * Добавляем обработчик клика по кнопке открывающей дропдауна.
     * При клике на кнопку, открывается/закрывается дропдаун.
     * При клике вне дропдауна, он закрывается.
     */
    function toggleDropdown(){
        const dropdowns = document.querySelectorAll(".dropdown-btn");
        if (!dropdowns) return;

        dropdowns.forEach(dropdown => {
            // Обработчик клика по кнопке дропдоуна
            dropdown.addEventListener("click", function(event) {
                event.stopPropagation();
                const dropdownContent = document.getElementById(this.dataset.dropdown);
                if (!dropdownContent) return;
    
                document.querySelectorAll(".dropdown-content.active").forEach(el => {
                    if (el !== dropdownContent) el.classList.remove("active");
                });

                dropdown.classList.toggle("active");
                dropdownContent.classList.toggle("active");

                if (dropdown.classList.contains('select-dropdown')){
                    selectItem(dropdown, dropdownContent);
                }

                if (dropdown.classList.contains('select-dropdown-bonus')){
                    selectBonus(dropdown, dropdownContent);
                }
                
                // Закрываем дропдаун при клике вне его
                closeDropdownOnClickOutside();
            });
        });
    };

    /**
     * Закрываем дропдаун при клике вне его.
    **/
    function closeDropdownOnClickOutside(){
        document.addEventListener("click", function(event) {
            event.stopPropagation();
            document.querySelectorAll(".dropdown-content.active").forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove("active");
                    const dropdownBtn = document.querySelector(`[data-dropdown="${dropdown.id}"]`);
                    if (dropdownBtn) dropdownBtn.classList.remove("active");
                }
            });
        }, { once: true });
    }


    /**
     * Обработчик выбора элемента из дропдауна
     * @param {HTMLElement} dropdownBtn - Кнопка дропдауна
     * @param {HTMLElement} dropdownMenu - Контейнер с элементами списка
     */
    function selectItem(dropdownBtn, dropdownMenu) {
        dropdownMenu.addEventListener('click', function (event) {
            const item = event.target.closest('.dropdown-item');
            if (!item) return;

            const selected = dropdownMenu.querySelector('.dropdown-item.selected');
            if (selected) selected.classList.remove('selected');

            item.classList.add('selected');

            const btnText = dropdownBtn.querySelector('.dropdown-btn__text');
            if (btnText) btnText.innerText = item.innerText;

            var hiddenInput = document.getElementById('currency-value');
            hiddenInput.value = item.innerText;
            requestAnimationFrame(() => {
                dropdownMenu.classList.remove('active');
                dropdownBtn.classList.remove('active');
            });
        }, { once: true });
    }

    /**
     * Обработчик выбора бонуса из дропдауна
     * @param {HTMLElement} dropdownBtn - Кнопка дропдауна
     * @param {HTMLElement} dropdownMenu - Контейнер с элементами списка
     */
    function selectBonus(dropdownBtn, dropdownMenu){
        dropdownMenu.addEventListener('click', function (event) {
            const item = event.target.closest('.dropdown-item');
            if (!item) return;
            
            let oldBonusName = dropdownBtn.querySelector('.bonus-list__name');
            let oldBonusOffer = dropdownBtn.querySelector('.bonus-list__offer');
            let oldBonusIcon = dropdownBtn.dataset.icon;
            
            const oldBonusNameText = oldBonusName.innerText;
            const oldBonusOfferText = oldBonusOffer.innerText;

            let newBonusName = item.querySelector('.bonus-list__name');
            let newBonusOffer = item.querySelector('.bonus-list__offer');
            let newBonusIcon = item.dataset.icon;

            const newBonusNameText = newBonusName.innerText;
            const newBonusOfferText = newBonusOffer.innerText;

            oldBonusName.innerText = newBonusNameText;
            oldBonusOffer.innerText = newBonusOfferText;
            dropdownBtn.dataset.icon = newBonusIcon;

            newBonusName.innerText = oldBonusNameText;
            newBonusOffer.innerText = oldBonusOfferText
            item.dataset.icon = oldBonusIcon;
            
            requestAnimationFrame(() => {
                dropdownMenu.classList.remove('active');
                dropdownBtn.classList.remove('active');
            });
        }, { once: true });
    }

    /**
     * Инициализируем ленивую загрузку изображений.
     * Изображения загружаются только при прокрутке страницы.
     */
    function lazyloadImg(){
        //lazy Load img and background images
        var lazyImages = [].slice.call(document.querySelectorAll("img.lazy_load"));
      
        if ("IntersectionObserver" in window) {
          let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting) {
                let lazyImage = entry.target;
                lazyImage.srcset = lazyImage.dataset.srcset;
                lazyImage.removeAttribute("data-srcset");
                lazyImage.classList.remove("lazy_load");
                lazyImageObserver.unobserve(lazyImage);
              }
            });
          }, {
            root: null,
            rootMargin: '250px',
          });
      
          lazyImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
          });
        }
    
        //lazy Load img and background images
        var lazyBackgrounds = [].slice.call(document.querySelectorAll(".lazy_image_bc"));
      
        if ("IntersectionObserver" in window) {
          let lazyBackgroundObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
              if (entry.isIntersecting) {
                entry.target.classList.remove("lazy_image_bc");
                lazyBackgroundObserver.unobserve(entry.target);
              }
            });
          }, {
            root: null,
            rootMargin: '100px',
          });
      
          lazyBackgrounds.forEach(function(lazyBackground) {
            lazyBackgroundObserver.observe(lazyBackground);
          });
        }
    };

    /**
     * Удаляем класс cnt-vis у элементов.
     * .cnt-vis{
     *    content-visibility: auto;
     *    contain-intrinsic-size: auto 559px;
     *  }
     */
    function deleteClassOptimizeRender(){
      const elements = document.querySelectorAll('.cnt-vis');
      if (elements.length > 0){
          elements.forEach(el => {
              el.classList.remove('cnt-vis');
          });
      }
    }
})();