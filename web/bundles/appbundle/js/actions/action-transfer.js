/****************** Transfer  *********************************/

$(".btn-transfer").click(function () {
    $(".btn-transfer").hide();
    $(".panel-transfer").find(".processing-image").show();
    $(".panel-transfer").find(".alert-danger").html("");

    var promise = new Object();
    promise.email = $("#email").text();
    promise.transfer = $(".panel-transfer").find(".email-to-transfer").val();
    promise.promise = $("#promise-code").val();


    if (validateEmail(promise.transfer)) {
        $(".panel-transfer").find(".email-to-transfer").parent().removeClass("has-warning");
        transfer(promise);

    } else {
        $(".panel-transfer").find(".email-to-transfer").parent().addClass("has-warning");
        $(".panel-transfer").find(".processing-image").hide();
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
                $(".panel-transfer").find(".alert-success").text(json.success);
                $(".panel-transfer").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 6000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            $(".panel-transfer").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-transfer").find(".alert-danger").css("display","inline-block");
            $(".panel-transfer").find(".processing-image").hide();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".panel-transfer").find(".processing-image").hide();
                $(".panel-transfer").find(".alert-danger").text(json.error);
                $(".panel-transfer").find(".alert-danger").css("display","inline-block");
                $(".btn-transfer").show();

            }
        }
    });
}