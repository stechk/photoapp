$(document).ready(function () {
    $('select').material_select();


    $(".confirm").click(function (e) {
        var that = this;
        e.preventDefault();
        $('#md1').openModal();
        $('.modal-data-text').html($(that).attr("data-confirm"));
        $('#md1_YesBtn').click(function (e) {
            e.preventDefault();
            $('#md1').closeModal();
            document.location = $(that).attr("href");
        });
    })
    var interval;
    var filestoupload = 0;

    $('#frm-uploadForm-upload').fileupload({
        //url: $("#frm-form").attr("action"),
        dataType: 'text',
        imageMaxWidth: 1024,
        imageMaxHeight: 1024,
        disableImageResize: false,
        imageForceResize: true,
        done: function (e, data) {
            clearInterval(interval);
            interval = window.setInterval(function () {
                var activeUploads = $('#frm-uploadForm-upload').fileupload('active');
                if (activeUploads == 0) {
                    clearInterval(interval);
                    //reload window after successfull upload all files
                    // window.location.reload();
                    var urlWithoutCount = String(window.location).replace(/&count=[0-9]+/i, '');
                    console.log(urlWithoutCount);
                    window.location.replace(urlWithoutCount + '&count=' + filestoupload);
                }
            }, 300);
        }

    })
        .bind('fileuploadadd', function (e, data) {
            filestoupload++;
        });

});