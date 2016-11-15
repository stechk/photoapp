$(document).ready(function() {
    $("select").material_select(), $(".confirm").click(function(a) {
        var b = this;
        a.preventDefault(), $("#md1").openModal(), $(".modal-data-text").html($(b).attr("data-confirm")), 
        $("#md1_YesBtn").click(function(a) {
            a.preventDefault(), $("#md1").closeModal(), document.location = $(b).attr("href");
        });
    });
    var a;
    $("#frm-uploadForm-upload").fileupload({
        dataType: "text",
        imageMaxWidth: 1024,
        imageMaxHeight: 1024,
        disableImageResize: !1,
        imageForceResize: !0,
        done: function(b, c) {
            clearInterval(a), a = window.setInterval(function() {
                var b = $("#frm-uploadForm-upload").fileupload("active");
                0 == b && (clearInterval(a), window.location.reload());
            }, 300);
        }
    });
    $('.materialboxed').materialbox();
});