

jQuery(document).ready(function ($) {


    $('[scroll]').click(function (e) {
        var target = '#' + $(this).attr('scroll');
        var position = $(target).position();

        var $top = position.top;

        $(window).scrollTop($top);



    });
    $('.myDataTable').DataTable({
        "paging": false,
        "order": [],
        "dom": ''
    });

});


