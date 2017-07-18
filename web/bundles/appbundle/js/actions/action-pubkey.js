/****************** PublicKey  *********************************/

$(".btn-public-key-show").click(function () {
    $(this).hide("slow");
    $(".public-key-actions").show();
    $(".panel-release").hide("slow");
    $(".publickey").focus();
});

$(".btn-public-key").click(function () {
    $(".btn-public-key").hide();
    $(".panel-public-key ").find(".alert-danger").html("");
    $(".panel-public-key ").find(".alert-danger").hide();
    $(".panel-public-key").find(".publickey").parent().removeClass("has-warning");
    $(".panel-public-key").find(".publickey2").parent().removeClass("has-warning");
    var promise = new Object();
    promise.email = $("#email").text();
    promise.publickey = $(".panel-public-key").find(".publickey").val();
    promise.publickey2 = $(".panel-public-key").find(".publickey2").val();
    promise.promise = $("#promise-code").val();

    if (validateSecKey(promise.publickey)) {
        if (promise.publickey === promise.publickey2) {
            $(".panel-public-key").find(".processing-image").show();
            promise.publickey = md5(promise.publickey);
            newPublicKey(promise);
        } else {
            $(".panel-public-key").find(".publickey2").parent().addClass("has-warning");
            $(".panel-public-key").find(".alert-danger").text("La llave publica no coincide.");
            $(".panel-public-key").find(".alert-danger").css("display", "inline-block");
            $(".btn-public-key").show();
        }
    } else {
        $(".panel-public-key").find(".publickey").parent().addClass("has-warning");
        $(".panel-public-key").find(".alert-danger").text("La llave publica debe tener al menos 8 caracteres.");
        $(".panel-public-key").find(".alert-danger").css("display", "inline-block");
        $(".btn-public-key").show();
    }



});

$('.panel-public-key').find("input").keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".btn-public-key").click();
    }
});


function newPublicKey(promise)
{

    var promiseJSON = JSON.stringify(promise);
    $.ajax({
        type: "POST",
        url: newPublicKeyUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".panel-public-key").find(".alert-success").text(json.success);
                $(".panel-public-key").find(".alert-success").css("display", "inline-block");
                setTimeout(refreshPromise, 5000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            $(".panel-public-key ").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-public-key ").find(".alert-danger").css("display", "inline-block");
            $(".panel-public-key").find(".processing-image").hide();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".panel-public-key").find(".processing-image").hide();
                $(".panel-public-key ").find(".alert-danger").text(json.error);
                $(".panel-public-key ").find(".alert-danger").css("display", "inline-block");
                $(".btn-public-key").show();


            }
        }
    });
}