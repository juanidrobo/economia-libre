$("input").keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".btn-login").click();
    }
});

$("input").click(function () {
    $("input").parent().removeClass("has-warning");
    $(".alert-danger").hide();
});

$(".btn-login").click(function () {


    var secKey1 = $("#seckey1").val();
    var secKey2 = $("#seckey2").val();


    if (validateSecKey(secKey1, secKey2)) {

        var user = new Object();
        user.code = $("#userCode").val();
        user.seckey = md5(secKey1);
        user.verification = $("#verification").val();

        
        changeKey(user);

    } else {
        $("input").parent().addClass("has-warning");
    }

}
);


function redirectToMyHistory() {
    $.redirect(myHistoryUrl);
}
function changeKey(user) {

    $(".btn-login").hide();
    $("#processing-image").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: changeKeyUrl,
        data: {'user': userJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                //$("#processing-image").hide();
                $(".alert-success").css("display","inline-block");
                $(".div-password").hide("slow");
                $(".alert-success").text(json.success);
                $(".alert-danger").hide();
                setTimeout(redirectToMyHistory, 3000);

            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $("#processing-image").hide();
                $(".alert-danger").css("display","inline-block");
                $(".alert-danger").text(json.error);
                $("button").show("slow");
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