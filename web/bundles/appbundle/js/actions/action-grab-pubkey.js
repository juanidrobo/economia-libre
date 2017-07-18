/***************** Grab public key Promise *********************/
$(".btn-grab-pubkey").click(function () {
    var promise = new Object();
    promise.email = $("#email").text();
    promise.promise = $("#promise-code").val();
    promise.pubkey = md5($("#pubkey").val());
    grabPubkeyPromise(promise);

});

$('#pubkey').keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".btn-grab-pubkey").click();
    }
});


function grabPubkeyPromise(promise)
{
    $(".panel-grab-promise-pubkey").find(".alert-danger").text("");
    $(".panel-grab-promise-pubkey").find(".alert-danger").hide();
    $(".btn-grab-pubkey").hide();
    $(".panel-grab-promise-pubkey").find(".processing-image").show();
    var promiseJSON = JSON.stringify(promise);
    $.ajax({
        type: "POST",
        url: grabPromiseUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                //La recogida del papel tuvo success
                $(".panel-grab-promise-pubkey").find(".alert-success").text(json.success);
                $(".panel-grab-promise-pubkey").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 5000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {

            $(".panel-grab-promise-pubkey").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-grab-promise-pubkey").find(".alert-danger").css("display","inline-block");
            $(".panel-grab-promise-pubkey").find(".processing-image").hide();

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {

                $(".panel-grab-promise-pubkey").find(".processing-image").hide();
                $(".panel-grab-promise-pubkey").find(".alert-danger").text(json.error);
                $(".panel-grab-promise-pubkey").find(".alert-danger").css("display","inline-block");
                $(".btn-grab-pubkey").show();
                $("#pubkey").val("");
            }
        }
    });
}