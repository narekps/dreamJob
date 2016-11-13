$(function() {
  //initLoader();

  function initLoader() {
    var loaderEl = document.getElementById('loader');
    $('a').click(function (event) {
      var href = $(this).attr('href');
      if (!$(this).attr('target') && href[0] != '#' && $(this).attr('href') != "javascript:void(0)") {
        event.preventDefault();
        var url = $(this).attr('href');

        $('#nav').hide();
        classie.add(loaderEl, 'animate');

        $.get(url, function () {
          location.href = url;

          $("#wrapper").animate({
            opacity: 0
          }, 1500);
          classie.remove(loaderEl, 'animate');
        });
      }
    });//конец Плавные переходы
  }
});