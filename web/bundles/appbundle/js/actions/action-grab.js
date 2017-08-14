/***************** Grab Promise *********************/
$(".btn-grab").click(function () {
    var promise = new Object();
    promise.promise = $("#promise-code").val();
    promise.pubkey = "";
    promise.verification=$("#promise-verification").val();
    grabPromise(promise);

});

function grabPromise(promise)
{
    $(".btn-grab").hide();
    $(".panel-grab-promise").find(".processing-image").show();
    var promiseJSON = JSON.stringify(promise);
    $.ajax({
        type: "POST",
        url: grabPromiseUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                //La recogida del papel tuvo success
                $(".panel-grab-promise").find(".alert-success").text(json.success);
                $(".panel-grab-promise").find(".alert-success").css("display","block");
                setTimeout(refreshPromise, 5000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {

            $(".panel-grab-promise").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-grab-promise").find(".alert-danger").css("display","block");
            $(".panel-grab-promise").find(".processing-image").hide();

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {

                $(".panel-grab-promise").find(".processing-image").hide();
                $(".panel-grab-promise").find(".alert-danger").text(json.error);
                $(".panel-grab-promise").find(".alert-danger").css("display","block");
                // $(".panel-grab-user-key").hide();

            }
        }
    });
}