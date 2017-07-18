
/* *********** Claim ****************** */
$(".btn-claim").click(function () {
    $(".panel-claim").find(".alert-danger").html("");
    $(".panel-claim").find(".alert-danger").hide();
    var promise = new Object();
    promise.code = $("#promise-code").val();

    claimPromise(promise);
   


});



function claimPromise(promise) {
    $(".btn-claim").hide();
    $(".panel-claim .processing-image").show();

    var promiseJSON = JSON.stringify(promise);

    $.ajax({
        type: "POST",
        url: claimPromiseUrl,
        data: {promise: promiseJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".panel-claim").find(".alert-success").text(json.success);
                $(".panel-claim").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 5000);
            }
        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            $(".panel-claim").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-claim").find(".alert-danger").css("display","inline-block");
            $(".panel-claim .processing-image").hide();
            $(".btn-claim").show();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".panel-claim").find(".alert-danger").text(json.error);
                $(".panel-claim").find(".alert-danger").css("display","inline-block");
                $(".panel-claim .processing-image").hide();
                $(".btn-claim").show();
        
            }
        }

    });
}
