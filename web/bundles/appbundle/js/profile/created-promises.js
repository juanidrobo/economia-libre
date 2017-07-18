
$("#btn-created-promises").click(function () {
    $("#table-created-promises").toggle("slow");
});

$("#btn-more-created-promises").click(function () {

    user = new Object();
    user.email = $(".user-email").text();
    user.offset = $("#table-created-promises tbody tr").length;
    getMoreCreatedPromises(user);

});

function getMoreCreatedPromises(user) {
    $("#btn-more-created-promises").hide();
    $("#table-created-promises .processing-image").show();
    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: getMoreCreatedPromisesUrl,
        data: {user: userJSON},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            if (json.success) {
                fillTableCreatedPromises(json);
            }


        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $("#table-created-promises .alert-danger").text("Error inesperado, prueba mas tarde.");
            $("#table-created-promises").find(".processing-image").hide();

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-created-promises .alert-danger").text(json.error);


            }
        },
        complete: function () {
            $("#table-created-promises").find(".processing-image").hide();

        }

    });
}



function fillTableCreatedPromises(json) {
    if (json.nextCreatedPromises === "more" || json.nextCreatedPromises === "no-more")
    {

        $("#btn-created-promises").show();

        $(".panel-body hr").show();

        if (json.createdPromises) {

            for (i = 0; i < json.createdPromises.length; i++)
            {


                var description = json.createdPromises[i]["description"];
                var action = json.createdPromises[i]["action"];
                var lastDateAction = json.createdPromises[i]["dateEvent"];
                var datePromise = json.createdPromises[i]["datePromise"];



                if (json.createdPromises[i]["ownerName"])
                {
                    var ownerDisplay = json.createdPromises[i]["ownerName"];


                } else if (json.createdPromises[i]["ownerEmail"])
                {

                    var ownerDisplay = json.createdPromises[i]["ownerEmail"];

                }
                else if (json.createdPromises[i]["ownerPhone"])
                {

                    var ownerDisplay = json.createdPromises[i]["ownerPhone"];

                }

                if (json.createdPromises[i]["receiverName"])
                {
                    var receiverDisplay = json.createdPromises[i]["receiverName"];
                } else if (json.createdPromises[i]["receiverEmail"])
                {
                    var receiverDisplay = json.createdPromises[i]["receiverEmail"];
                }
                else if (json.createdPromises[i]["receiverPhone"])
                {
                    var receiverDisplay = json.createdPromises[i]["receiverPhone"];
                }

                var ownerCode = json.createdPromises[i]["ownerCode"];
                var receiverCode = json.createdPromises[i]["receiverCode"];


                var code = json.createdPromises[i]["code"];



                //to translate to any desire language   translate-actions.js
                var actionText = translateActions(action);




                if (i === 0) {
                    $("#table-created-promises tbody").append("<tr class='scroll-mark'>");
                } else {
                    $("#table-created-promises tbody").append("<tr>");
                }
                $("#table-created-promises tbody tr:last").append("<td ><div class='date-action'>"+datePromise+"</div><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a></td>");
                if (action === "claim" || action === "review")
                {
                    var userToDisplay = ownerDisplay;
                    var userToDisplayCode = ownerCode;

                } else {
                    var userToDisplay = receiverDisplay;
                    var userToDisplayCode = receiverCode;
                }

                $("#table-created-promises tbody td:last").append("<div class='responsive-actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + userToDisplay + "</div><a href='profile?info=" + userToDisplayCode + "' > <div > <b>==> Ver perfil público</b> </div></a></td>");
                $("#table-created-promises tbody tr:last").append("<td class='actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + userToDisplay + "</div><a href='profile?info=" + userToDisplayCode + "' > <div > <b>==> Ver perfil público</b> </div></a></td>");




                if (action === "claim") {
                    $("#table-created-promises tbody tr:last").addClass("table-danger");
                } else if (action === "review") {
                    $("#table-created-promises tbody tr:last").addClass("table-success");
                }


            }

            $("#table-created-promises").show("slow", function () {
                $("#table-created-promises").find(".dotdotdot").dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    height: 100,
                    watch: true
                });



                $("#table-created-promises").find(".description").show();

                if ($("#table-created-promises tbody tr").length > 0) {
                    // $("body").animate({scrollTop: $("#table-created-promises tbody .scroll-mark:last").offset().top - 200}, 2000);

                }

                if (json.nextCreatedPromises === "more") {
                    $("#count-created-promises").text("(" + $("#table-created-promises tbody tr").length + "+)");

                    $("#btn-more-created-promises").show("slow");
                } else {
                    $("#count-created-promises").text("(" + $("#table-created-promises tbody tr").length + ")");

                }

            });
        }



    }


}


