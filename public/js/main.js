$(function() {
  $.material.init();

  initMain();


  $.datetimepicker.setLocale('ru');
  $('input.datetimepicker').datetimepicker({
    timepicker: true,
    dayOfWeekStart: 1,//пн
    format: 'd.m.Y H:i'
  });
  $('input.datepicker').datetimepicker({
    timepicker: false,
    dayOfWeekStart: 1,//пн
    format: 'd.m.Y'
  });
});

function initMain() {
  //popover
  $('[data-toggle="popover"]').popover({
    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title">x</h3><div class="popover-content"></div></div>',
    html: true,
    placement: 'top'
  });
  $('[data-toggle="popover"]').on('shown.bs.popover', function () {
    var $me = $(this), $closeBtn = $('.close', $(this).parent());

    $closeBtn.one('click', function () {
      $me.click();
    });
    $('body, html').one('click', function () {
      $closeBtn.click();
    });
  });//end popover
}
