
$("#btn-created-promises").click(function () {
    $("#table-created-promises").toggle("slow");
});

$("#btn-refresh-created-promises").click(function () {
    $("#btn-refresh-created-promises").hide();

    var offset = 0;
    $("#table-created-promises tbody tr").each(function () {
        $(this).remove();
    });
    getMoreCreatedPromises(offset);

});




$("#btn-more-created-promises").click(function () {
    $("#table-created-promises .alert-danger").text("");
    var offset = $("#table-created-promises tbody tr").length;
    getMoreCreatedPromises(offset);

});

function getMoreCreatedPromises(offset) {
    $("#btn-more-created-promises").hide();
    $("#table-created-promises .processing-image").show();


    $.ajax({
        type: "POST",
        url: getMoreCreatedPromisesUrl,
        data: {offset: offset},
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
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-created-promises .alert-danger").text(json.error);
            }
        },
        complete: function () {
            $("#btn-refresh-created-promises").show();
            $("#table-created-promises").find(".processing-image").hide();
        }

    });
}


function promiseToggle(elem) {

    var promise = $(elem).parent().parent().parent().parent().attr("promise-code");
    console.log(promise);

    var className = "visible";
    if ($(elem).parent().parent().parent().hasClass("visible")) {
        className = "visible-responsive";
    }

    if ($(elem).prop("checked")) {
        $(elem).prop("checked", true);
        $(elem).parent().parent().parent().parent().find("." + className).find("label input").prop("checked", true);

    } else {

        $(elem).removeProp("checked");
        $(elem).parent().parent().parent().parent().find("." + className).find("label input").removeProp("checked");

    }

    $.ajax({
        type: "POST",
        url: togglePromiseUrl,
        data: {promise: promise},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            if (json.success) {

            }


        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $("#table-created-promises .alert-danger").text("Error inesperado, prueba mas tarde.");
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-created-promises .alert-danger").text(json.error);

            }
        }

    });


}

function fillTableCreatedPromises(json) {
    if (json.countCreatedPromises) {
        $("#count-created-promises").text("(" + json.countCreatedPromises + ")");
    }
    if (json.nextCreatedPromises === "more" || json.nextCreatedPromises === "no-more")
    {

        if (json.createdPromises) {

            for (i = 0; i < json.createdPromises.length; i++)
            {

                var description = json.createdPromises[i]["description"];
                var action = json.createdPromises[i]["action"];
                var datePromise = json.createdPromises[i]["datePromise"];
                var lastDateAction = json.createdPromises[i]["dateEvent"];
                var visible = json.createdPromises[i]["visible"];


                var ownerCode = json.createdPromises[i]["ownerCode"];
                var ownerEmail = json.createdPromises[i]["ownerEmail"];
                var ownerPhone = json.createdPromises[i]["ownerPhone"];
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

                var receiverCode = json.createdPromises[i]["receiverCode"];
                var receiverEmail = json.createdPromises[i]["receiverEmail"];

                var receiverPhone = json.createdPromises[i]["receiverPhone"];
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

                var code = json.createdPromises[i]["promiseCode"];

                /*if (action === "release") {
                 receiverDisplay = "Libre";
                 }
                 if (action === "anonymousgrab") {
                 receiverDisplay = "Anónimo";
                 }*/

                //to translate to any desire language   translate-actions.js
                var actionText = translateActions(action);

                var checked = "";
                if (visible === "1") {
                    checked = "checked='checked'";
                }

                if (json.createdPromises[i]["active"] == true) {
                    if (i === 0) {
                        $("#table-created-promises tbody").append("<tr class='scroll-mark' promise-code=" + code + ">");
                    } else {
                        $("#table-created-promises tbody").append("<tr promise-code=" + code + ">");
                    }
                    $("#table-created-promises tbody tr:last").append("<td><div class='date-action'>" + datePromise + "</div><div><a  href=promise/" + code + "><div class='dotdotdot description'>" + description + "</div></a></div></td>");

                } else {
                    if (i === 0) {
                        $("#table-created-promises tbody").append("<tr class='scroll-mark table-danger' promise-code=" + code + ">");

                    }
                    else {
                        $("#table-created-promises tbody").append("<tr class='table-danger' promise-code=" + code + ">");
                    }
                    $("#table-created-promises tbody tr:last").append("<td><div class='date-action'>" + datePromise + "</div><<div class='description dotdotdot'>" + description + "</div> <a href='activate/" + code + "'> activar</a></td>");


                }


                if (action === "claim" || action === "review") {
                    $("#table-created-promises tbody td:last").append("<div class='responsive-actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + ownerDisplay + "</div><a href='profile?info=" + ownerCode + "' > <div class='dotdotdot'> <b>==> Ver perfil público</b> </div></a></div>");
                } else {
                    $("#table-created-promises tbody td:last").append("<label class='text-checkbox'>Visible en perfil publico</label><div class='checkbox checkbox-slider-lg checkbox-slider--b  checkbox-slider-info '><label><input type='checkbox' " + checked + " ><span class='checkboxspan'></span></label></div>");

                    $("#table-created-promises tbody td:last").append("<div class='responsive-actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + receiverDisplay + "</div><a href='profile?info=" + ownerCode + "' > <b>==> Ver perfil público</b> </a></div>");

                }


                if (action === "claim" || action === "review") {
                    if (action === "claim") {
                        $("#table-created-promises tbody tr:last").addClass("table-danger");
                    } else {
                        $("#table-created-promises tbody tr:last").addClass("table-success");
                    }
                    $("#table-created-promises tbody tr:last").append("<td class='actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + "</div><div class='display-name'>" + ownerDisplay + "</div><a href='profile?info=" + ownerCode + "' > <div> <b>==> Ver perfil público</b> </div></a>");


                } else {

                    $("#table-created-promises tbody tr:last").append("<td class='actions'><div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + "</div><div class='display-name'>" + receiverDisplay + "</div><a href='profile?info=" + receiverCode + "' > <div > <b>==> Ver perfil público</b> </div></a></td>");

                }
            }


            $("#table-created-promises .dotdotdot").dotdotdot({
                ellipsis: '... ',
                wrap: 'word',
                height: 100,
                watch: true,
            });


            $(".display-name").dotdotdot({
                ellipsis: '... ',
                wrap: 'letter',
                height: 25,
                watch: true,
            });




            if ($("#table-created-promises tbody tr").length > 0) {
                // $("body").animate({scrollTop: $("#table-created-promises tbody .scroll-mark:last").offset().top - 200}, 2000);
            }

            if (json.nextCreatedPromises === "more") {
                $("#count-created-promises").text("(" + $("#table-created-promises tbody tr").length + "+)");

                $("#btn-more-created-promises").show("slow");
            } else {
                $("#count-created-promises").text("(" + $("#table-created-promises tbody tr").length + ")");

            }

            $(".visible input").unbind("click");
            $(".visible-responisive input").unbind("click");


            $("#table-created-promises input").click(function () {
                promiseToggle(this);

            });



        }



    }


}


