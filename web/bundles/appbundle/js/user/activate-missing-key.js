$('input').keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".login").click();
    }
});

$("input").click(function () {
    $("input").parent().removeClass("has-warning");

});

$(".login").click(function () {


    var secKey1 = $("#seckey1").val();
    var secKey2 = $("#seckey2").val();


    if (validateSecKey(secKey1, secKey2)) {

        var user = new Object();
        user.code = $("#userCode").val();
        user.newseckey = md5(secKey1);

        createSecKey(user);

    } else {
        $("input").parent().addClass("has-warning");
    }

}
);
function createSecKey(user) {

    $("button").hide();
    $("#processing-image").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: createSecKeyUrl,
        data: {'user': userJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            console.log(json);
            if (json.success) {
                $.redirect(activatePromiseUrl);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                console.log(json.error);
            }
        }

    });
}

function validateSecKey(secKey1, secKey2) {
    if (secKey1.length < 8) {
        $(".old-school-login .alert-danger").text("La contraseña debe de tener al menos 8 caracteres.");
        $(".old-school-login .alert-danger").css("display", "inline-block");
        return false;
    }
    if (secKey1 !== secKey2) {
        $(".old-school-login .alert-danger").text("Las 2 contraseñas no coinciden.");
        $(".old-school-login .alert-danger").css("display", "inline-block");
        return false;
    }
    return true;
}
