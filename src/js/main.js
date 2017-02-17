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

    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    $('body').on('drop', function (e) {
        return false;
    });

    $('#frm-uploadForm-upload').fileupload({
        //url: $("#frm-form").attr("action"),
        dataType: 'text',
        imageMaxWidth: 1600,
        imageMaxHeight: 1600,
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
                    window.location.replace(urlWithoutCount + '&count=' + filestoupload);
                }
            }, 300);
        }

    })
        .bind('fileuploadadd', function (e, data) {
            filestoupload++;

        });


    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    // if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = mm+'/'+dd+'/'+yyyy;
    if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = yyyy+'-' +mm+'-'+dd;

    // $('#theDate').attr('value', today);
    document.getElementById("theDate").defaultValue =today;

    // alert($('#theDate').attr('value'));

    $("#theDate").on("input", function() {

        if(this.value !== 'undefined' && this.value.length > 0){
            $('#upload-btn').show();
        } else {
            $("#upload-btn").hide();
        }

    })
    //
});