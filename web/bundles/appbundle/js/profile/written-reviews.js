
$("#btn-written-reviews").click(function () {
    $("#table-written-reviews").toggle("slow");
});
$("#btn-more-written-reviews").click(function () {
    $("#table-written-reviews .alert-danger").text("");
    user = new Object();
    user.email = $(".user-email").text();
    user.offset = $("#table-written-reviews tbody tr").length;
    getMoreWrittenReviews(user);

});

function getMoreWrittenReviews(user) {
    $("#btn-more-written-reviews").hide();
    $("#table-written-reviews .processing-image").show();
    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: getMoreWrittenReviewsUrl,
        data: {user: userJSON},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            if (json.success) {
                fillTableWrittenReviews(json);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $("#table-written-reviews .alert-danger").text("Error inesperado, prueba mas tarde.");
            $("#table-written-reviews").find(".processing-image").hide();

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-written-reviews .alert-danger").text(json.error);


            }
        },
        complete: function () {
            $("#table-written-reviews").find(".processing-image").hide();
       
        }

    });
}



function fillTableWrittenReviews(json) {

    if (json.nextWrittenReviews === "more" || json.nextWrittenReviews === "no-more")
    {


        $("#btn-written-reviews").show();
        if (json.writtenReviews) {

            for (i = 0; i < json.writtenReviews.length; i++)
            {


                var description = json.writtenReviews[i]["description"];
                var responsibleCode = json.writtenReviews[i]["responsibleCode"];

                if (json.writtenReviews[i]["responsibleName"])
                {
                    var responsible = json.writtenReviews[i]["responsibleName"];


                } else if (json.writtenReviews[i]["responsibleEmail"])
                {

                    var responsible = json.writtenReviews[i]["responsibleEmail"];
                }
                else if (json.writtenReviews[i]["responsiblePhone"])
                {

                    var responsible = json.writtenReviews[i]["responsiblePhone"];
                }
                var review = json.writtenReviews[i]["review"];
                var dateReview = json.writtenReviews[i]["dateReview"];
                var code = json.writtenReviews[i]["code"];



                if (i === 0) {
                    $("#table-written-reviews tbody").append("<tr class='success scroll-mark'>");
                } else {
                    $("#table-written-reviews tbody").append("<tr class='success'>");
                }


                $("#table-written-reviews tbody tr:last").append("<td ><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a>" +
                        "<div class='responsive-actions'><div class='date-action'>" + dateReview + "</div> <div class='dotdotdot'>" + review + "</div><div class='text-action'>Moneda creada por " + responsible + "</div> <a href='profile?info=" + responsibleCode + "' > <div> <b>==> Ver perfil público</b> </div></a></div></td>");

                $("#table-written-reviews tbody tr:last").append("<td class='actions' ><div class='date-action'>" + dateReview + "</div> <div class='dotdotdot'>" + review + "</div></td>");
                $("#table-written-reviews tbody td:last").append("<div class='text-action'>Moneda creada por " + responsible + "</div> <a href='profile?info=" + responsibleCode + "' > <div> <b>==> Ver perfil público</b> </div></a>");


            }
            $("#table-written-reviews").show("slow", function () {

                $("#table-written-reviews").find(".dotdotdot").dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    height: 100,
                    watch: true
                });



                if ($("#table-written-reviews tbody tr").length > 0) {
                    //$("body").animate({scrollTop: $("#table-written-reviews tbody .scroll-mark:last").offset().top - 200}, 2000);
                }

                if (json.nextWrittenReviews === "more") {
                    $("#count-written-reviews").text("(" + $("#table-written-reviews tbody tr").length + "+)");

                    $("#btn-more-written-reviews").show("slow");
                } else {
                    $("#count-written-reviews").text("(" + $("#table-written-reviews tbody tr").length + ")");

                }

            });

        }



    }



}
