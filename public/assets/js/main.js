/**
 * Main - AISHA
 */

'use strict';

(function () {

    // Evitar que main.js se ejecute dos veces
    if (window.__AISHA_MAIN_LOADED__) {
        return;
    }

    window.__AISHA_MAIN_LOADED__ = true;


    // =====================================================
    // VERIFICAR DEPENDENCIAS
    // =====================================================

    if (typeof window.Menu === 'undefined') {
        console.error('AISHA: Menu no está definido. Verifique menu.js');
        return;
    }

    if (typeof window.Helpers === 'undefined') {
        console.error('AISHA: Helpers no está definido. Verifique helpers.js');
        return;
    }


    // =====================================================
    // VARIABLES
    // =====================================================

    let menu = null;
    let animate = false;


    // =====================================================
    // INICIALIZAR MENU
    // =====================================================

    const layoutMenuEl = document.querySelectorAll('#layout-menu');

    layoutMenuEl.forEach(function (element) {

        menu = new window.Menu(element, {
            orientation: 'vertical',
            closeChildren: true
        });

        window.Helpers.scrollToActive(false);

        window.Helpers.mainMenu = menu;

    });


    // =====================================================
    // MENU TOGGLE
    // =====================================================

    const menuToggler =
        document.querySelectorAll('.layout-menu-toggle');

    menuToggler.forEach(function (item) {

        item.addEventListener('click', function (event) {

            event.preventDefault();

            window.Helpers.toggleCollapsed();

        });

    });


    // =====================================================
    // MENU HOVER
    // =====================================================

    const layoutMenu = document.getElementById('layout-menu');

    if (layoutMenu) {

        let timeout = null;

        layoutMenu.addEventListener('mouseenter', function () {

            if (!window.Helpers.isSmallScreen()) {

                timeout = setTimeout(function () {

                    const toggle =
                        document.querySelector('.layout-menu-toggle');

                    if (toggle) {
                        toggle.classList.add('d-block');
                    }

                }, 300);

            }

        });


        layoutMenu.addEventListener('mouseleave', function () {

            clearTimeout(timeout);

            const toggle =
                document.querySelector('.layout-menu-toggle');

            if (toggle) {
                toggle.classList.remove('d-block');
            }

        });

    }


    // =====================================================
    // MENU SCROLL
    // =====================================================

    const menuInnerContainer =
        document.getElementsByClassName('menu-inner')[0];

    const menuInnerShadow =
        document.getElementsByClassName('menu-inner-shadow')[0];

    if (menuInnerContainer && menuInnerShadow) {

        menuInnerContainer.addEventListener(
            'ps-scroll-y',
            function () {

                const thumb =
                    this.querySelector('.ps__thumb-y');

                if (thumb && thumb.offsetTop) {

                    menuInnerShadow.style.display = 'block';

                } else {

                    menuInnerShadow.style.display = 'none';

                }

            }
        );

    }


    // =====================================================
    // BOOTSTRAP TOOLTIPS
    // =====================================================

    if (
        typeof window.bootstrap !== 'undefined' &&
        window.bootstrap.Tooltip
    ) {

        const tooltipTriggerList =
            [].slice.call(
                document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'
                )
            );

        tooltipTriggerList.forEach(function (tooltipTriggerEl) {

            new window.bootstrap.Tooltip(tooltipTriggerEl);

        });

    }


    // =====================================================
    // ACCORDION
    // =====================================================

    const accordionActiveFunction = function (e) {

        const item =
            e.target.closest('.accordion-item');

        if (!item) {
            return;
        }

        if (e.type === 'show.bs.collapse') {

            item.classList.add('active');

        } else {

            item.classList.remove('active');

        }

    };


    const accordionTriggerList =
        [].slice.call(
            document.querySelectorAll('.accordion')
        );


    accordionTriggerList.forEach(function (accordionTriggerEl) {

        accordionTriggerEl.addEventListener(
            'show.bs.collapse',
            accordionActiveFunction
        );

        accordionTriggerEl.addEventListener(
            'hide.bs.collapse',
            accordionActiveFunction
        );

    });


    // =====================================================
    // HELPERS
    // =====================================================

    if (typeof window.Helpers.setAutoUpdate === 'function') {
        window.Helpers.setAutoUpdate(true);
    }


    // =====================================================
    // PASSWORD TOGGLE
    // =====================================================

    if (typeof window.Helpers.initPasswordToggle === 'function') {
        window.Helpers.initPasswordToggle();
    }


    // =====================================================
    // SPEECH TO TEXT
    // =====================================================

    if (typeof window.Helpers.initSpeechToText === 'function') {
        window.Helpers.initSpeechToText();
    }


    // =====================================================
    // PANTALLA PEQUEÑA
    // =====================================================

    if (window.Helpers.isSmallScreen()) {
        return;
    }


    // =====================================================
    // MENU COLAPSADO
    // =====================================================

    if (typeof window.Helpers.setCollapsed === 'function') {

        window.Helpers.setCollapsed(true, false);

    }


    console.log('AISHA: main.js cargado correctamente');

})();