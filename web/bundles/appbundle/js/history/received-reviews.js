
$("#btn-received-reviews").click(function () {
    $("#table-received-reviews").toggle("slow");
});

$("#btn-refresh-received-reviews").click(function () {
    $("#btn-refresh-received-reviews").hide();
    var offset = 0;
    $("#table-received-reviews tbody tr").each(function () {
        $(this).remove();
    });
    getMoreReceivedReviews(offset);

});

$("#btn-more-received-reviews").click(function () {

    $("#table-received-reviews .alert-danger").text("");
    var offset = $("#table-received-reviews tbody tr").size();
    getMoreReceivedReviews(offset);

});

function getMoreReceivedReviews(offset) {
    $("#btn-more-received-reviews").hide();
    $("#table-received-reviews .processing-image").show();

    $.ajax({
        type: "POST",
        url: getMoreReceivedReviewsUrl,
        data: {offset: offset},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            if (json.success) {

                fillTableReceivedReviews(json);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $("#table-received-reviews .alert-danger").text("Error inesperado, prueba mas tarde.");

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-received-reviews .alert-danger").text(json.error);


            }
        },
        complete: function () {
            $("#table-received-reviews").find(".processing-image").hide();
            $("#btn-refresh-received-reviews").show();
        }

    });
}



function fillTableReceivedReviews(json) {

    if (json.nextReceivedReviews === "more" || json.nextReceivedReviews === "no-more")
    {


        $("#btn-received-reviews").show();
        $("#btn-refresh-received-reviews").show();
        if (json.receivedReviews) {

            for (i = 0; i < json.receivedReviews.length; i++)
            {

                var userCode = json.receivedReviews[i]['userCode'];
                var description = json.receivedReviews[i]["description"];
                var review = json.receivedReviews[i]["review"];
                if (json.receivedReviews[i]["name"])
                {
                    var user = json.receivedReviews[i]["name"];

                } else if (json.receivedReviews[i]["email"])
                {
                    var user = json.receivedReviews[i]["email"];
                }
                else if (json.receivedReviews[i]["phone"])
                {

                    var user = json.receivedReviews[i]["phone"];
                }
                var dateReview = json.receivedReviews[i]["dateReview"];
                var datePromise = json.receivedReviews[i]["datePromise"];
                var code = json.receivedReviews[i]["code"];



                if (i === 0) {
                    $("#table-received-reviews tbody").append("<tr class='table-success scroll-mark'>");
                } else {
                    $("#table-received-reviews tbody").append("<tr class='table-success'>");
                }


                $("#table-received-reviews tbody tr:last").append("<td ><div class='date-action'>"+datePromise+"</div><a  href=promise/" + code + "><div class='dotdotdot description'>" + description + "</div></a>");
                $("#table-received-reviews tbody td:last").append("<div class='responsive-actions' ><div class='date-action'>" + dateReview + "</div><div class='dotdotdot'>" + review + "</div><div class='text-action' >Moneda utilizada por " + user + "</div><a href='profile?info=" + userCode + "' > <div > <b>==> Ver perfil público</b> </div></a></div>");

                $("#table-received-reviews tbody tr:last").append("<td class='actions' ><div class='date-action'>" + dateReview + "</div><div class='dotdotdot'>" + review + "</div></td>");
                $("#table-received-reviews tbody td:last").append("<div class='text-action' >Moneda utilizada por " + user + "</div><a href='profile?info=" + userCode + "' > <div > <b>==> Ver perfil público</b> </div></a>");


            }


            $("#table-received-reviews .dotdotdot").dotdotdot({
                ellipsis: '... ',
                wrap: 'word',
                height: 100,
                watch: true
            });




            if ($("#table-received-reviews tbody tr").length > 0) {

                // $("body").animate({scrollTop: $("#table-received-reviews tbody .scroll-mark:last").offset().top - 200}, 2000);
            }
            if (json.nextReceivedReviews === "more") {
                $("#count-received-reviews").text("(" + $("#table-received-reviews tbody tr").length + "+)");

                $("#btn-more-received-reviews").show("slow");
            } else {
                $("#count-received-reviews").text("(" + $("#table-received-reviews tbody tr").length + ")");

            }



        }



    }



}
