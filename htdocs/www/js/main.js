$(document).ready(function() {
    $("select").material_select(), $(".confirm").click(function(a) {
        var b = this;
        a.preventDefault(), $("#md1").openModal(), $(".modal-data-text").html($(b).attr("data-confirm")), 
        $("#md1_YesBtn").click(function(a) {
            a.preventDefault(), $("#md1").closeModal(), document.location = $(b).attr("href");
        });
    });
    var a, b = 0;
    $(document).bind("drop dragover", function(a) {
        a.preventDefault();
    }), $("body").on("drop", function(a) {
        return !1;
    }), $("#frm-uploadForm-upload").fileupload({
        dataType: "text",
        imageMaxWidth: 1600,
        imageMaxHeight: 1600,
        disableImageResize: !1,
        imageForceResize: !0,
        done: function(c, d) {
            clearInterval(a), a = window.setInterval(function() {
                var c = $("#frm-uploadForm-upload").fileupload("active");
                if (0 == c) {
                    clearInterval(a);
                    var d = String(window.location).replace(/&count=[0-9]+/i, "");
                    window.location.replace(d + "&count=" + b);
                }
            }, 300);
        }
    }).bind("fileuploadadd", function(a, c) {
        b++;
    }), null !== document.getElementById("theDate") && (document.getElementById("theDate").valueAsDate = new Date()), 
    $("#theDate").on("input", function() {
        "undefined" !== this.value && this.value.length > 0 ? $("#upload-btn").show() : $("#upload-btn").hide();
    });
});