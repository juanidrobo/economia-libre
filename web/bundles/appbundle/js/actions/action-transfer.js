/****************** Transfer  *********************************/

$(".btn-transfer").click(function () {
    $(".btn-transfer").hide();
    $(".div-transfer").find(".processing-image").show();
    $(".div-transfer").find(".alert-danger").html("");

    var promise = new Object();
    promise.email = $("#email").text();
    promise.transfer = $(".div-transfer").find(".email-to-transfer").val();
    promise.promise = $("#promise-code").val();


    if (validateEmail(promise.transfer)) {
        $(".div-transfer").find(".email-to-transfer").parent().removeClass("has-warning");
        transfer(promise);

    } else {
        $(".div-transfer").find(".email-to-transfer").parent().addClass("has-warning");
        $(".div-transfer").find(".processing-image").hide();
        $(".btn-transfer").show();
    }

});


$('.email-to-transfer').keypress(function (e) {
    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-transfer").click();
    }
});

function transfer(promise)
{

    var promiseJSON = JSON.stringify(promise);
    $.ajax({
        type: "POST",
        url: transferUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".div-transfer").find(".alert-success").text(json.success);
                $(".div-transfer").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 6000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            $(".div-transfer").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".div-transfer").find(".alert-danger").css("display","inline-block");
            $(".div-transfer").find(".processing-image").hide();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".div-transfer").find(".processing-image").hide();
                $(".div-transfer").find(".alert-danger").text(json.error);
                $(".div-transfer").find(".alert-danger").css("display","inline-block");
                $(".btn-transfer").show();

            }
        }
    });
}