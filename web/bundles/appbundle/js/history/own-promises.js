
$("#btn-own-promises").click(function () {
    $("#table-own-promises").toggle("slow");
});

$("#btn-refresh-own-promises").click(function () {
    $("#btn-refresh-own-promises").hide();

    var offset = 0;
    $("#table-own-promises tbody tr").each(function () {
        $(this).remove();
    });
    getMoreOwnPromises(offset);

});

$("#btn-more-own-promises").click(function () {

    $("#table-own-promises .alert-danger").text("");
    user = new Object();
    var offset = $("#table-own-promises tbody tr").length;
    getMoreOwnPromises(offset);

});

function getMoreOwnPromises(offset) {
    $("#btn-more-own-promises").hide();
    $("#table-own-promises .processing-image").show();

    $.ajax({
        type: "POST",
        url: getMoreOwnPromisesUrl,
        data: {offset: offset},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            if (json.success) {
                fillTableOwnPromises(json)
               

            }


        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $("#table-own-promises .alert-danger").text("Error inesperado, prueba mas tarde.");

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#table-own-promises .alert-danger").text(json.error);

            }

        },
        complete: function () {
            $("#table-own-promises").find(".processing-image").hide();
            $("#btn-refresh-own-promises").show();
        }

    });
}



function fillTableOwnPromises(json) {

    if (json.nextOwnPromises === "more" || json.nextOwnPromises === "no-more")
    {


        $("#btn-own-promises").show();
        $("#btn-refresh-own-promises").show();
        if (json.ownPromises) {

            for (i = 0; i < json.ownPromises.length; i++)
            {

                var description = json.ownPromises[i]["description"];
                var responsibleCode = json.ownPromises[i]["responsibleCode"];
                if (json.ownPromises[i]["name"])
                {
                    var responsible = json.ownPromises[i]["name"];

                } else if (json.ownPromises[i]["email"])
                {
                    var responsible = json.ownPromises[i]["email"];
                }
                else if (json.ownPromises[i]["phone"])
                {

                    var responsible = json.createdPromises[i]["phone"];
                }

                var action = json.ownPromises[i]["action"];
                var lastDateAction = json.ownPromises[i]["dateEvent"];
                var datePromise = json.ownPromises[i]["datePromise"];
                var code = json.ownPromises[i]["code"];
                var eventCode = json.ownPromises[i]["eventCode"];

                //to translate to any desire language   translate-actions.js
                var actionText = translateActions(action);



                var table_class = "";
                var scroll_mark = "";
                if (i === 0) {
                    scroll_mark = "scroll-mark";
                }
                if (action === "transfer" || action === "claim") {
                    table_class = "table-danger";
                }
                if (action === "review") {
                    table_class = "table-success";
                }
                $("#table-own-promises tbody").append("<tr class='" + table_class + " " + scroll_mark + " '>");
                if (action === "transfer") {
                    $("#table-own-promises tbody tr:last").append("<td><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a>"
                            + " <div class ='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + " ti</div><a  href=activate/" + code + "&" + eventCode + ">Confirmar transferencia</a></td>");
                } else if (action === "claim") {
                    $("#table-own-promises tbody tr:last").append("<td><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a>"
                            + "<div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + " ti</div><a  href=promise/" + code + ">Comentar moneda</a></td>");
                } else {
                    $("#table-own-promises tbody tr:last").append("<td ><a  href=promise/" + code + "><div class='description dotdotdot'>" + description + "</div></a>" +
                            "<div class='date-action'>" + lastDateAction + "</div><div class='text-action'>" + actionText + " ti</div></td>");

                }

                $("#table-own-promises tbody td:last").append("<div class='responsive-actions'><div class='date-action'>"+datePromise+"</div><div class='text-action'>Moneda creada por " + responsible + "</div><a href='profile?info=" + responsibleCode + "' > <div> <b>==> Ver perfil público</b> </div></a></div>");

                $("#table-own-promises tbody tr:last").append("<td class='actions'><div class='date-action'>"+datePromise+"</div><div class='text-action'>Moneda creada por " + responsible + "</div><a href='profile?info=" + responsibleCode + "' > <div> <b>==> Ver perfil público</b> </div></a></td>");


            }


            $("#table-own-promises .dotdotdot").dotdotdot({
                ellipsis: '... ',
                wrap: 'word',
                height: 100,
                watch: true
            });


            if ($("#table-own-promises tbody tr").length > 0) {
                //  $("body").animate({scrollTop: $("#table-own-promises tbody .scroll-mark:last").offset().top - 200}, 2000);
            }
            if (json.nextOwnPromises === "more") {
                $("#count-own-promises").text("(" + $("#table-own-promises tbody tr").length + "+)");

                $("#btn-more-own-promises").show("slow");
            } else {
                $("#count-own-promises").text("(" + $("#table-own-promises tbody tr").length + ")");

            }



        }



    }



}
