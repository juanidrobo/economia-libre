/***************** Grab Promise Anonymous *********************/

$(".btn-grab-anonymous-show").click(function () {
    $(this).hide("slow");
    $(".grab-anonymous-actions").show();
    $(".panel-grab-promise").hide("slow");
    
});

$(".btn-grab-promise-anonymous").click(function () {
    $(".btn-grab-promise-anonymous").hide();
    $(".panel-grab-promise-anonymous").find(".processing-image").show();
    var user = new Object();
    user.promise = $("#promise-code").val();
    grabPromiseAnonymous(user);
});

$(".btn-show-grab-anonymous").click(function () {
$(this).hide();
$(".panel-grab-promise-anonymous .panel-body").show("slow");
});

function grabPromiseAnonymous(user)
{
    var userJSON = JSON.stringify(user);
    $.ajax({
        type: "POST",
        url: grabPromiseAnonymousUrl,
        data: {user: userJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".panel-grab-promise-anonymous").find(".alert-success").text(json.success);
                $("#anonymous-promise-url").val($("#anonymous-promise-url").val() + "&" + json.code);
                setTimeout(refreshAnonymousPromise, 6000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {

            $(".panel-grab-promise-anonymous").find(".alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".panel-grab-promise-anonymous").find(".processing-image").hide();
            $(".btn-grab-promise-anonymous").show();

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {

                $(".panel-grab-promise-anonymous").find(".processing-image").hide();
                $(".panel-grab-promise-anonymous").find(".alert-danger").text(json.error);
                $(".btn-grab-promise-anonymous").show();

            }
        }
    });
}


function refreshAnonymousPromise() {
    window.location.href = $("#anonymous-promise-url").val();
}