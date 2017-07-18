
/* *********** Release ****************** */
$(".btn-release").click(function () {
    $(".div-release").find(".alert-danger").html("");
    var promise = new Object();
    promise.promise = $("#promise-code").val();
    releasePromise(promise);
});


function releasePromise(promise) {
    $(".btn-release").hide();
    $(".div-release .processing-image").show();

    var promiseJSON = JSON.stringify(promise);

    $.ajax({
        type: "POST",
        url: releasePromiseUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                console.log("success");
                $(".div-release").find(".alert-success").text(json.success);
                $(".div-release").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 3000);

            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $(".div-release ").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".div-release ").find(".alert-danger").css("display","inline-block");
            $(".div-release .processing-image").hide();
            $(".btn-release").show();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".div-release").find(".alert-danger").text(json.error);
                $(".div-release").find(".alert-danger").css("display","inline-block");
                $(".div-release .processing-image").hide();
                $(".btn-release").show();
                $(".div-release").find(".seckey").val("")
            }
        }

    });
}
