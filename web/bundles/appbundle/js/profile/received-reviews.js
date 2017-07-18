
$("#btn-received-reviews").click(function () {
    $("#table-received-reviews").toggle("slow");
});
$("#btn-more-received-reviews").click(function () {
    $("#table-received-reviews .alert-danger").text("");
    user = new Object();
    user.email = $(".user-email").text();
    user.offset = $("#table-received-reviews tbody tr").length;
    getMoreReceivedReviews(user);

});

function getMoreReceivedReviews(user) {
    $("#btn-more-received-reviews").hide();
    $("#table-received-reviews .processing-image").show();
    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: getMoreReceivedReviewsUrl,
        data: {user: userJSON},
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

        }

    });
}



function fillTableReceivedReviews(json) {

    if (json.nextReceivedReviews === "more" || json.nextReceivedReviews === "no-more")
    {

        $("#btn-received-reviews").show();
        if (json.receivedReviews) {

            for (i = 0; i < json.receivedReviews.length; i++)
            {

                var description = json.receivedReviews[i]["description"];
                var review = json.receivedReviews[i]["review"];
                var reviewerCode = json.receivedReviews[i]["reviewerCode"];


                if (json.receivedReviews[i]["reviewerName"])
                {
                    var reviewer = json.receivedReviews[i]["reviewerName"];


                } else if (json.receivedReviews[i]["reviewerEmail"])
                {

                    var reviewer = json.receivedReviews[i]["reviewerEmail"];
                }
                else if (json.receivedReviews[i]["reviewerPhone"])
                {

                    var reviewer = json.receivedReviews[i]["reviewerPhone"];
                }


                var dateReview = json.receivedReviews[i]["dateReview"];
                var code = json.receivedReviews[i]["code"];



                if (i === 0) {
                    $("#table-received-reviews tbody").append("<tr class='success scroll-mark'>");
                } else {
                    $("#table-received-reviews tbody").append("<tr class='success'>");
                }


                $("#table-received-reviews tbody tr:last").append("<td class=''><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a>" +
                        "<div class='responsive-actions'><div class='date-action'>" + dateReview + "</div><div class='dotdotdot'>" + review + "</div><div class='text-action'><div class='email' >" + reviewer + "</div><a href='profile?info=" + reviewerCode + "' > <div class='email'> <b>==> Ver perfil público</b> </div></div></a></div></div></td>");

                $("#table-received-reviews tbody tr:last").append("<td class='actions' ><div class='date-action'>" + dateReview + "</div><div class='dotdotdot'>" + review + "</div></td>");
                $("#table-received-reviews tbody td:last").append("<div class='text-action'><div>" + reviewer + "</div><a href='profile?info=" + reviewerCode + "' > <div class='email'> <b>==> Ver perfil público</b> </div></div></a></div>");

            }
            $("#table-received-reviews").show();
            // $("#table-received-reviews").show("slow", function () {

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

            // });

        }



    }



}
