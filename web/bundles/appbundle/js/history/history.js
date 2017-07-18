
$(document).ready(function () {
    $(".alert-danger-history").html("");
    if (session) {
        myHistory();
    }
});


function myHistory() {
    $(".btn-history").hide();
    $(".card-footer .processing-image").show();
    $.ajax({
        type: "POST",
        url: getHistoryUrl,
        success: function (data, txtStatus, jqXHR) {
           
            var json = JSON.parse(data);
           
            if (json.success) {
                $(".login").hide("slow");
                $(".user-email").show();
                $(".verification").val(json.verification);
                fillTableCreatedPromises(json);
                fillTableReceivedReviews(json);
                fillTableOwnPromises(json);
                fillTableWrittenReviews(json);
                $(".card-footer .processing-image").hide();

            }
        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $(".alert-danger-history").text("Error inesperado, prueba mas tarde.");
            $(".card-footer processing-image").hide();
            $(".btn-history").show();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".alert-danger-history").text(json.error);
                $(".card-footer processing-image").hide();
                $(".btn-history").show();
                $(".seckey").val("")
            }
        }

    });
}



function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
