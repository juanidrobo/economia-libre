
/* *********** Review ****************** */
$(".btn-review").click(function () {
    $(".panel-review").find(".alert-danger").html("");
    $(".panel-review").find(".alert-danger").hide();
    var review = new Object();

    review.promise = $("#promise-code").val();
    review.review = $(".panel-review").find("textarea").val();

    reviewPromise(review);

});


function reviewPromise(review) {
    $(".btn-review").hide();
    $(".panel-review .processing-image").show();

    var reviewJSON = JSON.stringify(review);

    $.ajax({
        type: "POST",
        url: reviewUrl,
        data: {review: reviewJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".panel-review").find(".alert-success").text(json.success);
                $(".panel-review").find(".alert-success").css("display","inline-block");
                setTimeout(refreshPromise, 7000);
            }
        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            $(".panel-review").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-review").find(".alert-danger").css("display","inline-block");
            $(".panel-review .processing-image").hide();
            $(".btn-review").show();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".panel-review").find(".alert-danger").text(json.error);
                $(".panel-review").find(".alert-danger").css("display","inline-block");
                $(".panel-review .processing-image").hide();
                $(".btn-review").show();
                $(".panel-review").find(".seckey").val("")
            }
        }

    });
}
