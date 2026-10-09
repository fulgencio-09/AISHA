/**
 * AISHA - Menu
 */

'use strict';

(function (window, document) {

    class Menu {

        constructor(element, options) {

            this.element = element;

            this.options = Object.assign({
                orientation: 'vertical',
                closeChildren: false
            }, options || {});

            this.init();
        }

        init() {

            if (!this.element) {
                return;
            }

            this.initToggles();
            this.initLinks();
        }

        initToggles() {

            const toggles =
                this.element.querySelectorAll('.menu-toggle');

            toggles.forEach(toggle => {

                toggle.addEventListener('click', event => {

                    event.preventDefault();

                    const item =
                        toggle.closest('.menu-item');

                    if (!item) {
                        return;
                    }

                    const isOpen =
                        item.classList.contains('open');

                    if (this.options.closeChildren) {

                        const parent = item.parentElement;

                        if (parent) {

                            parent
                                .querySelectorAll(':scope > .menu-item.open')
                                .forEach(child => {

                                    if (child !== item) {
                                        child.classList.remove('open');
                                    }

                                });

                        }

                    }

                    item.classList.toggle(
                        'open',
                        !isOpen
                    );

                });

            });

        }

        initLinks() {

            const links =
                this.element.querySelectorAll(
                    '.menu-link:not(.menu-toggle)'
                );

            links.forEach(link => {

                link.addEventListener('click', () => {

                    const item =
                        link.closest('.menu-item');

                    if (!item) {
                        return;
                    }

                    this.element
                        .querySelectorAll('.menu-item.active')
                        .forEach(active => {
                            active.classList.remove('active');
                        });

                    item.classList.add('active');

                });

            });

        }

        open(item) {

            if (item) {
                item.classList.add('open');
            }

        }

        close(item) {

            if (item) {
                item.classList.remove('open');
            }

        }

        refresh() {
            this.init();
        }

    }

    /*
     * IMPORTANTE:
     * main.js utiliza:
     *
     * new Menu(...)
     *
     * Por eso Menu debe ser global.
     */

    window.Menu = Menu;

})(window, document);