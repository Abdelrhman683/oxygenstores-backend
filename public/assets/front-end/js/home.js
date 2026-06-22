"use strict";

updateFlashDealProgressBar();
setInterval(updateFlashDealProgressBar, 10000);

$(document).ready(function () {
    var directionFromSession = $("#direction-from-session").data("value");

    $(".flash-deal-slider").owlCarousel({
        loop: false,
        autoplay: true,
        center: false,
        margin: 10,
        nav: true,
        navText:
            directionFromSession === "rtl"
                ? [
                      "<i class='czi-arrow-right'></i>",
                      "<i class='czi-arrow-left'></i>",
                  ]
                : [
                      "<i class='czi-arrow-left'></i>",
                      "<i class='czi-arrow-right'></i>",
                  ],
        dots: false,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 1.1,
            },
            360: {
                items: 1.2,
            },
            375: {
                items: 1.4,
            },
            480: {
                items: 1.8,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 4,
            },
            1200: {
                items: 4,
            },
        },
    });

    $(".flash-deal-slider-mobile").owlCarousel({
        loop: true,
        autoplay: true,
        center: true,
        margin: 10,
        nav: true,
        navText:
            directionFromSession === "rtl"
                ? [
                      "<i class='czi-arrow-right'></i>",
                      "<i class='czi-arrow-left'></i>",
                  ]
                : [
                      "<i class='czi-arrow-left'></i>",
                      "<i class='czi-arrow-right'></i>",
                  ],
        dots: false,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 1.1,
            },
            360: {
                items: 1.2,
            },
            375: {
                items: 1.4,
            },
            480: {
                items: 1.8,
            },
            576: {
                items: 2,
            },
            768: {
                items: 3,
            },
            992: {
                items: 4,
            },
            1200: {
                items: 4,
            },
        },
    });

    //featured_products_list old
    // let featuredProductsLoopEnable = $('#featured_products_list').data('loop')?.toString() === 'true';
    // $("#featured_products_list").owlCarousel({
    //     loop: featuredProductsLoopEnable,
    //     autoplay: true,
    //     margin: 20,
    //     nav: true,
    //     navText:
    //         directionFromSession === "rtl"
    //             ? [
    //                   "<i class='czi-arrow-right'></i>",
    //                   "<i class='czi-arrow-left'></i>",
    //               ]
    //             : [
    //                   "<i class='czi-arrow-left'></i>",
    //                   "<i class='czi-arrow-right'></i>",
    //               ],
    //     dots: false,
    //     autoplayHoverPause: true,
    //     rtl: directionFromSession === "rtl",
    //     ltr: directionFromSession === "ltr",
    //     responsive: {
    //         0: {
    //             items: 1,
    //         },
    //         360: {
    //             items: 1,
    //         },
    //         375: {
    //             items: 1,
    //         },
    //         540: {
    //             items: 2,
    //         },
    //         576: {
    //             items: 2,
    //         },
    //         768: {
    //             items: 3,
    //         },
    //         992: {
    //             items: 4,
    //         },
    //         1200: {
    //             items: 6,
    //         },
    //     },
    // });

    //featured_products_list New
    $(".featured_products_listSlide").each(function(){
        let maxItems = $(this).data('slide-items');
        $(this).owlCarousel({
            autoplay: true,
            margin: 20,
            nav: true,
            navText:
            directionFromSession === "rtl"
                ? [
                      "<i class='czi-arrow-right'></i>",
                      "<i class='czi-arrow-left'></i>",
                  ]
                : [
                      "<i class='czi-arrow-left'></i>",
                      "<i class='czi-arrow-right'></i>",
                  ],
        dots: false,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",

            responsive: {
                0: {
                    items: 1,
                    loop: maxItems > 1
                },
                360: {
                    items: 1.02,
                    loop: maxItems > 1.02
                },
                375: {
                    items: 1.02,
                    loop: maxItems > 1.02
                },
                540: {
                    items: 2,
                    loop: maxItems > 2
                },
                576: {
                    items: 2,
                    loop: maxItems > 2
                },
                768: {
                    items: 3,
                    loop: maxItems > 3
                },
                992: {
                    items: 4,
                    loop: maxItems > 4
                },
                1200: {
                    items: 6,
                    loop: maxItems > 6
                },
            },
        });
    })


    //New Arrivals Product New
    $(".new-arrivals-product").each(function(){
        let maxItems = $(this).data('slide-items');
        $(this).owlCarousel({
            autoplay: true,
            margin: 20,
            nav: true,
            navText:
                directionFromSession === "rtl"
                    ? [
                        "<i class='czi-arrow-right'></i>",
                        "<i class='czi-arrow-left'></i>",
                    ]
                    : [
                        "<i class='czi-arrow-left'></i>",
                        "<i class='czi-arrow-right'></i>",
                    ],
            dots: false,
            autoplayHoverPause: true,
            rtl: directionFromSession === "rtl",

            responsive: {
                0: {
                    items: 1,
                    loop: maxItems > 1
                },
                360: {
                    items: 1.02,
                    loop: maxItems > 1.02
                },
                375: {
                    items: 1.02,
                    loop: maxItems > 1.02
                },
                540: {
                    items: 2,
                    loop: maxItems > 2
                },
                576: {
                    items: 2,
                    loop: maxItems > 2
                },
                768: {
                    items: 2,
                    loop: maxItems > 2
                },
                992: {
                    items: 2,
                    loop: maxItems > 2
                },
                1200: {
                    items: 4,
                    loop: maxItems > 4
                },
                1400: {
                    items: 4,
                    loop: maxItems > 4
                },
            },
        });
    })
    //New Arrivals Product old
    // $(".new-arrivals-product").owlCarousel({
    //     loop: true,
    //     autoplay: true,
    //     margin: 20,
    //     nav: true,
    //     navText:
    //         directionFromSession === "rtl"
    //             ? [
    //                   "<i class='czi-arrow-right'></i>",
    //                   "<i class='czi-arrow-left'></i>",
    //               ]
    //             : [
    //                   "<i class='czi-arrow-left'></i>",
    //                   "<i class='czi-arrow-right'></i>",
    //               ],
    //     dots: false,
    //     autoplayHoverPause: true,
    //     rtl: directionFromSession === "rtl",
    //     ltr: directionFromSession === "ltr",
    //     responsive: {
    //         0: {
    //             items: 1,
    //         },
    //         360: {
    //             items: 1.02,
    //         },
    //         375: {
    //             items: 1.02,
    //         },
    //         540: {
    //             items: 2,
    //         },
    //         576: {
    //             items: 2,
    //         },
    //         768: {
    //             items: 2,
    //         },
    //         992: {
    //             items: 2,
    //         },
    //         1200: {
    //             items: 4,
    //         },
    //         1400: {
    //             items: 4,
    //         },
    //     },
    // });





    //category-wise-product-slider Old
    // $(".category-wise-product-slider").each(function () {
    //     let loopEnable = $(this).data('loop')?.toString() === 'true';

    //     $(this).owlCarousel({
    //         loop: loopEnable,
    //         autoplay: true,
    //         margin: 20,
    //         nav: true,
    //         navText:
    //             directionFromSession === "rtl"
    //                 ? [
    //                       "<i class='czi-arrow-right'></i>",
    //                       "<i class='czi-arrow-left'></i>",
    //                   ]
    //                 : [
    //                       "<i class='czi-arrow-left'></i>",
    //                       "<i class='czi-arrow-right'></i>",
    //                   ],
    //         dots: false,
    //         autoplayHoverPause: true,
    //         rtl: directionFromSession === "rtl",
    //         responsive: {
    //             0: {
    //                 items: 1.2,
    //             },
    //             375: {
    //                 items: 1.4,
    //             },
    //             425: {
    //                 items: 2,
    //             },
    //             576: {
    //                 items: 3,
    //             },
    //             768: {
    //                 items: 4,
    //             },
    //             992: {
    //                 items: 5,
    //             },
    //             1200: {
    //                 items: 6,
    //             },
    //         },
    //         onInitialized: checkNavigationButtons,
    //     });
    // });

    //category-wise-product-slider New
    $(".category-wise-product-slider").each(function(){
        let maxItems = $(this).data('slide-items');
        $(this).owlCarousel({
            autoplay: true,
            margin: 20,
            nav: true,
            navText:
                directionFromSession === "rtl"
                    ? [
                          "<i class='czi-arrow-right'></i>",
                          "<i class='czi-arrow-left'></i>",
                      ]
                    : [
                          "<i class='czi-arrow-left'></i>",
                          "<i class='czi-arrow-right'></i>",
                      ],
            dots: false,
            autoplayHoverPause: true,
            rtl: directionFromSession === "rtl",
            responsive: {
                0: {
                    items: 1.2,
                    loop: maxItems > 1.2
                },
                375: {
                    items: 1.4,
                    loop: maxItems > 1.4
                },
                425: {
                    items: 2,
                    loop: maxItems > 2
                },
                576: {
                    items: 3,
                    loop: maxItems > 3
                },
                768: {
                    items: 4,
                    loop: maxItems > 4
                },
                992: {
                    items: 5,
                    loop: maxItems > 5
                },
                1200: {
                    items: 6,
                    loop: maxItems > 6
                },
            },
            onInitialized: checkNavigationButtons,
        });
    })

    function checkNavigationButtons(event) {
        var itemCount = event.item.count;
        let owlNav = $(".owl-nav");
        itemCount > 1 ? owlNav.show() : owlNav.hide();
    }

    let isLoopHeroSlider = $(".hero-slider").data('loop')?.toString() === '1';

    $(".hero-slider").owlCarousel({
        loop: isLoopHeroSlider,
        autoplay: isLoopHeroSlider,
        margin: 20,
        nav: isLoopHeroSlider,
        navText:
            directionFromSession === "rtl"
                ? [
                      "<i class='czi-arrow-right'></i>",
                      "<i class='czi-arrow-left'></i>",
                  ]
                : [
                      "<i class='czi-arrow-left'></i>",
                      "<i class='czi-arrow-right'></i>",
                  ],
        dots: isLoopHeroSlider,
        autoplayHoverPause: isLoopHeroSlider,
        autoplaySpeed: 1500,
        slideTransition: "linear",
        items: 1,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
    });

    //Brands SLider Old
    // $(".brands-slider").owlCarousel({
    //     loop: false,
    //     autoplay: true,
    //     margin: 10,
    //     nav: true,
    //     navText:
    //         directionFromSession === "rtl"
    //             ? [
    //                   "<i class='czi-arrow-right'></i>",
    //                   "<i class='czi-arrow-left'></i>",
    //               ]
    //             : [
    //                   "<i class='czi-arrow-left'></i>",
    //                   "<i class='czi-arrow-right'></i>",
    //               ],
    //     dots: false,
    //     rtl: directionFromSession === "rtl",
    //     ltr: directionFromSession === "ltr",
    //     autoplayHoverPause: true,
    //     responsive: {
    //         0: {
    //             items: 4,
    //         },
    //         360: {
    //             items: 5,
    //         },
    //         576: {
    //             items: 6,
    //         },
    //         768: {
    //             items: 7,
    //         },
    //         992: {
    //             items: 9,
    //         },
    //         1200: {
    //             items: 11,
    //         },
    //         1400: {
    //             items: 12,
    //         },
    //     },
    // });

    //New Arrivals Product New
    $(".brands-slider").each(function(){
        let maxItems = $(this).data('slide-items');
        $(this).owlCarousel({
            autoplay: true,
            margin: 20,
            nav: true,
            navText:
            directionFromSession === "rtl"
                ? [
                      "<i class='czi-arrow-right'></i>",
                      "<i class='czi-arrow-left'></i>",
                  ]
                : [
                      "<i class='czi-arrow-left'></i>",
                      "<i class='czi-arrow-right'></i>",
                  ],
            dots: false,
            rtl: directionFromSession === "rtl",
            ltr: directionFromSession === "ltr",
            autoplayHoverPause: true,
            
            responsive: {
                0: {
                    items: 2,
                    loop: maxItems > 2
                },
                360: {
                    items: 2,
                    loop: maxItems > 2
                },
                576: {
                    items: 3,
                    loop: maxItems > 3
                },
                768: {
                    items: 3,
                    loop: maxItems > 3
                },
                992: {
                    items: 4,
                    loop: maxItems > 4
                },
                1200: {
                    items: 5,
                    loop: maxItems > 5
                },
                1400: {
                    items: 5,
                    loop: maxItems > 5
                },
            },            
        });
    })

    $(".footer-banner-slider").owlCarousel({
        loop: true,
        autoplay: true,
        margin: 10,
        nav: false,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        autoplayHoverPause: true,
        items: 1,
    });

    $("#category-slider, #top-seller-slider").owlCarousel({
        loop: false,
        autoplay: true,
        margin: 20,
        nav: false,
        dots: true,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 2,
            },
            360: {
                items: 3,
            },
            375: {
                items: 3,
            },
            540: {
                items: 4,
            },
            576: {
                items: 5,
            },
            768: {
                items: 6,
            },
            992: {
                items: 8,
            },
            1200: {
                items: 10,
            },
            1400: {
                items: 11,
            },
        },
    });

    $(".categories--slider").owlCarousel({
        loop: false,
        autoplay: true,
        margin: 20,
        nav: false,
        dots: false,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 2.2,
                margin: 10,
            },
            375: {
                items: 2.5,
                margin: 10,
            },
            540: {
                items: 3.5,
                margin: 15,
            },
            768: {
                items: 4.5,
                margin: 20,
            },
            992: {
                items: 5,
                margin: 20,
            },
            1200: {
                items: 5,
                margin: 25,
            },
        },
    });

    const othersStore = $(".others-store-slider").owlCarousel({
        responsiveClass: true,
        nav: false,
        dots: false,
        loop: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        smartSpeed: 600,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 1.3,
                margin: 10,
            },
            480: {
                items: 2,
                margin: 26,
            },
            768: {
                items: 2,
                margin: 26,
            },
            992: {
                items: 3,
                margin: 26,
            },
            1200: {
                items: 4,
                margin: 26,
            },
        },
    });

    $(".store-next").on("click", function () {
        othersStore.trigger("next.owl.carousel", [600]);
    });

    $(".store-prev").on("click", function () {
        othersStore.trigger("prev.owl.carousel", [600]);
    });

    $(".premium-product-carousel").owlCarousel({
        loop: false,
        autoplay: true,
        margin: 20,
        nav: false,
        dots: true,
        autoplayHoverPause: true,
        rtl: directionFromSession === "rtl",
        ltr: directionFromSession === "ltr",
        responsive: {
            0: {
                items: 1.2,
                margin: 10,
            },
            375: {
                items: 1.5,
                margin: 15,
            },
            540: {
                items: 2.2,
                margin: 15,
            },
            768: {
                items: 3.2,
                margin: 20,
            },
            992: {
                items: 4,
                margin: 20,
            },
            1200: {
                items: 6,
                margin: 20,
            },
            1400: {
                items: 6,
                margin: 20,
            },
        },
    });

    /* =========================================================
       Recommended Products (Category Tabs) - Swiper & Tabs Logic
       ========================================================= */
    const rpSwiperConfig = {
        dir: 'rtl',
        loop: false,
        spaceBetween: 12,
        slidesPerView: 2,
        pagination: {
            el: '.rp-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            480:  { slidesPerView: 3, spaceBetween: 12 },
            768:  { slidesPerView: 4, spaceBetween: 14 },
            992:  { slidesPerView: 5, spaceBetween: 14 },
            1200: { slidesPerView: 6, spaceBetween: 16 },
        }
    };

    if (document.querySelector('#rp-swiper-washers')) {
        const rpSwiperWashers      = new Swiper('#rp-swiper-washers',      rpSwiperConfig);
        const rpSwiperFridges      = new Swiper('#rp-swiper-refrigerators', rpSwiperConfig);
        const rpSwiperConditioners = new Swiper('#rp-swiper-conditioners',  rpSwiperConfig);

        const tabBtns = document.querySelectorAll('#recommendedTabsNav .rp-tab-btn');
        const tabPanes = document.querySelectorAll('#recommendedTabsContent .rp-tab-pane');

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                var target = document.getElementById(btn.getAttribute('data-target'));
                if (target) {
                    target.classList.add('active');
                    [rpSwiperWashers, rpSwiperFridges, rpSwiperConditioners].forEach(s => {
                        if (s && typeof s.update === 'function') s.update();
                    });
                }
            });
        });
    }

    /* =========================================================
       Order Success Modal & Copy ID Logic
       ========================================================= */
    const orderModalEl = document.getElementById('order_successfully');
    if (orderModalEl) {
        const orderModal = new bootstrap.Modal(orderModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        orderModal.show();

        document.querySelectorAll('.copy-order-id').forEach(function(copyBtn) {
            copyBtn.addEventListener('click', function() {
                let orderTextEl = this.closest('tr')?.querySelector('.order-id-text');
                if (!orderTextEl) {
                    orderTextEl = this.parentElement.querySelector('.order-id-text');
                }
                const orderText = orderTextEl?.textContent.trim();
                if (orderText) {
                    navigator.clipboard.writeText(orderText).then(() => {
                        if (typeof toastr !== 'undefined') toastr.success('Order ID copied successfully!');
                    }).catch(err => {
                        console.warn('Clipboard error:', err);
                        if (typeof toastr !== 'undefined') toastr.warning('Unable to copy. Clipboard requires HTTPS or localhost.');
                    });
                }
            });
        });

        const closeBtn = document.getElementById('modal-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                setTimeout(() => { orderModal.hide(); }, 600);
            });
        }
    }

    /* =========================================================
       Popup Modal Logic
       ========================================================= */
    const popupModal = $('#popup-modal');
    if (popupModal.length > 0) {
        popupModal.modal('show');
    }
});
