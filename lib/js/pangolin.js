jQuery.noConflict();
jQuery(document).ready(function () {
    "use strict";
    // Featured Product Slider
    jQuery('.single-item').slick();
    jQuery('.multiple-items').slick({
            dots: false,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 3,
            arrows:true,
        });
    // Equal Height
    jQuery('.equal-height').matchHeight();
    jQuery('.front__product-featured__text, .front__product-featured__image').matchHeight();
    jQuery('.front__product-featured__left--2,.front__product-featured__right--2').matchHeight();
    jQuery('.front__product-featured__left--3,.front__product-featured__right--3').matchHeight();
    jQuery('.product--l4').matchHeight();
    jQuery('.product-card__inner').matchHeight();
    jQuery('.product-card__inner--l3').matchHeight();
});