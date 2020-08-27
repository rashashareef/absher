/**
 * @file
 * Behaviors for the vartheme theme.
 */


jQuery(document).ready(function ($) {
  $('#block-views-block-news-slideshow-block-1 .views-row').parent().addClass('bxstart');
  $('form#search-block-form button span').removeClass('sr-only');
  $('#block-views-block-important-news-block-1 ul').simplyScroll({
    speed: 1
  });
  $('.form-item-field-sex-value:nth-child(2) , .marriage .form-item-field-gender-value:nth-child(3)').append('<div class="font-icon"><i class="fa fa-male" aria-hidden="true"></i></div>');
  $('.form-item-field-sex-value:nth-child(3) , .marriage .form-item-field-gender-value:nth-child(2)').append('<div class="font-icon"><i class="fa fa-female" aria-hidden="true"></i></div>');

  $("article.our-services .field--name-field-description p").next().addClass("abuse");

  $('.slideshow-news #block-views-block-news-slideshow-block-1 .bxstart ').bxSlider({
     mode: 'fade',
     controls: false,
     pager: true,
     infiniteLoop: true,
     minSlides: 1,
     maxSlides: 1,
     responsive: true,
  });
  $('.view-thiker ul ').bxSlider({
     mode: 'fade',
     controls: false,
     infiniteLoop: true,
     minSlides: 1,
     maxSlides: 1,
     responsive: true,
     //slideWidth: 555
    });
});
