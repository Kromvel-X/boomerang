"use strict";
(() => {
    window.addEventListener("load", function() {
        ajaxSearch();
        ajaxGetSlotsByCategoryProviders(); 
        ajaxGetSlotsByCategory();
        createObserverSliderInit();
    });

    /**
     * Функция для получения постов по категории
     */
    function ajaxGetSlotsByCategoryProviders() {
        const catDropdownContent = document.getElementById('providers');
        const resultsContainer = document.getElementById("slot-results");
    
        if (!catDropdownContent) return;
    
        catDropdownContent.addEventListener('click', function(event) {
            const item = event.target.closest('.provider__item');
            if (!item) return;
    
            const catName = item.innerText;
            fetchSearchResults("searchPostByCategory", catName, resultsContainer);
    
            // Закрываем дропдаун
            const dropdown = catDropdownContent.closest('.container').querySelector('.dropdown-btn');
            dropdown.classList.toggle("active");
            catDropdownContent.classList.toggle("active");
        });
    }

    /**
     * Функция для поиска постов по названию
     */
    function ajaxSearch() {
        const searchInput = document.getElementById("search");
        const resultsContainer = document.getElementById("slot-results");
        const searchForm = document.getElementById("search-form");
    
        if (!searchInput) return;
    
        // Поиск по вводу
        searchInput.addEventListener("input", function () {
            const searchVal = searchInput.value.trim();
            fetchSearchResults("searchPostsByTitle", searchVal, resultsContainer);
        });

        // Поиск по отправке формы
        searchForm.addEventListener("submit", function(event) {
                event.preventDefault();
                const searchVal = searchInput.value.trim();
                fetchSearchResults("searchPostsByTitle", searchVal, resultsContainer);
            }
        );
    }

    /**
     * Функция для получения постов по категории
     */
    function ajaxGetSlotsByCategory() {
        const catDropdownContent = document.getElementById('category');
        const resultsContainer = document.getElementById("slot-results");    
    
        if (!catDropdownContent) return;

        const categories = catDropdownContent.querySelectorAll('.category__button');
        categories.forEach(category => {
            category.addEventListener('click', function() {
                categories.forEach(cat => cat.classList.remove('active'));
                requestAnimationFrame(() => category.classList.add('active'));
                const catName = category.innerText;
                fetchSearchResults("searchPostByCategory", catName, resultsContainer);
            });
        });
    }

    /**
     * Функция для получения результатов поиска
     * @param {string} action - действие, которое нужно выполнить в PHP
     * @param {string} searchVal - значение поиска
     *  @param {HTMLElement} resultsContainer - контейнер, в который будем вставлять результаты
     */
    function fetchSearchResults(action, searchVal, resultsContainer) {
        let formData = new FormData();
        formData.append("action", action);
        formData.append("search", searchVal);
        formData.append("nonce", slots_catalog.nonce);
    
        fetch(slots_catalog.ajaxurl, {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = "";
    
            if (data.length > 0) {
                // Запускаем Observer перед изменением HTML
                const observer = observeLazyLoad(resultsContainer);
    
                data.forEach(slot => {
                    let slotTag = slot.tag ? `<div class='badge'>${slot.tag}</div>` : '';
    
                    let slotElement = `
                        <article class='col slot__col'>
                            <a href="${slot.link}" class='slot-card'>
                                ${slot.image}
                                <div class='slot-card__content'>
                                    <h3 class='slot-card__title'>${slot.title}</h3>
                                    ${slotTag}
                                    <span class='slot-card__favourites'></span>
                                </div>
                                <div class='slot-card__hover'>
                                    <span class='slot-card__icon'></span>
                                    <p class='slot-card__desc'>${slot.content}</p>
                                </div>
                            </a>
                        </article>
                    `;
    
                    resultsContainer.insertAdjacentHTML("beforeend", slotElement);
                });
    
                // После полной загрузки отключаем MutationObserver
                setTimeout(() => observer.disconnect(), 100);
            } else {
                resultsContainer.innerHTML = "<p>Ничего не найдено</p>";
            }
        })
        .catch(error => console.error("Ошибка при запросе:", error));
    }

    /**
     * Подключаем MutationObserver для отслеживания появления новых элементов.
     * При появлении новых элементов, устанавливаем srcset (lazyload)
     * @param container - контейнер, в котором будем отслеживать появление новых элемент
     * @returns 
     */
    function observeLazyLoad(container) {
        const observer = new MutationObserver((mutationsList, observer) => {
            mutationsList.forEach(mutation => {
                if (mutation.type === 'childList') {
                    let images = container.querySelectorAll('.lazy_load');
                    images.forEach(image => {
                        image.srcset = image.dataset.srcset;
                        image.removeAttribute("data-srcset");
                        image.classList.remove("lazy_load");
                    });
                }
            });
        });

        observer.observe(container, { childList: true });

        // Возвращаем observer, чтобы можно было его отключить
        return observer;
    }


     /**
     * 
     */
     function createObserverSliderInit(){
        if ("IntersectionObserver" in window) {
            var sliders = document.querySelectorAll('.slider');
            sliders.forEach(slider => {
                var slidersObserver = new IntersectionObserver(function(entries) {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            initSlider();
                            slidersObserver.disconnect();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '200px',
                    threshold: 0,
                });
                slidersObserver.observe(slider);
            });
        }
    };

    function initSlider(){
        var sliderJsUrl = '/wp-content/plugins/slot-catalog/assets/js/tiny-slider.js';
        var script = document.createElement('script');
        script.src = sliderJsUrl;
        script.defer = true;
        document.body.appendChild(script);

        var sliderCssUrl = '/wp-content/plugins/slot-catalog/assets/css/tiny-slider.css';
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = sliderCssUrl;
        document.body.appendChild(link);
        
        script.onload = function () {
            if (typeof tns === 'function') {
                var categorySlider = tns({
                    container: '#category',
                    items: 1,
                    slideBy: 1,
                    mouseDrag: true,
                    autoWidth: true,
                    gutter: 10,
                    controls: false,
                    nav: false,
                    loop: false,
                    preventScrollOnTouch: 'force'
                });

                var sliderSlots = tns({
                    container: '#sliderSlots',
                    items: 2,
                    slideBy: 1,
                    mouseDrag: true,
                    // autoWidth: true,
                    gutter: 0,
                    controls: false,
                    // controlsText: ['<span class="prev"></span>', '<span class="next"></span>'],
                    nav: false,
                    loop: false,
                    preventScrollOnTouch: 'force',
                    "responsive": {
                        "380": {
                             "items": 2
                        },
                        "768": {
                            "items": 3
                        },
                        "1024": {
                            "items": 4
                        },
                        "1280": {
                            "items": 6
                        },
                        "1440": {
                            "items": 7
                        }
                    },
                });
                // requestAnimationFrame(() => {
                    // ajaxGetSlotsByCategory();
                // });
            } else {
                console.error('Ошибка: tns() не найден!');
            }
        };
    }

})();