
$("input").click(function () {
    $("input").parent().removeClass("has-warning");
});

$(".btn-primary").click(function () {

    var email = $("input").val();
    if (validateEmail(email)) {
        if ($("#g-recaptcha-response").val() !== "") {
            var user = new Object();
            user.email = email;
            notifyNewKey(user);
        } else {
            $(".alert-danger").text("Comprueba que no eres un robot!");
            $(".alert-danger").show();
            $(".alert-danger").fadeOut(3000);
        }
    } else {
        $("input").parent().addClass("has-warning");
        $(".alert-danger").hide();
    }

});

$("input").keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".btn-primary").click();
    }
});

function notifyNewKey(user) {
    $("button").hide();
    $("input").prop("disabled", true);
    $(".processing-image").show();

    var userJSON = JSON.stringify(user);


    $.ajax({
        type: "POST",
        url: notifyNewKeyUrl,
        data: {
            'user': userJSON,
            'captcha': $("#g-recaptcha-response").val()
        },
        success: function (data, txtStatus, jqXHR) {
            console.log(data);
            var json = JSON.parse(data);

            if (json.success) {
                $(".processing-image").hide();
                $(".alert-success").css("display", "inline-block");
                $(".alert-success").text(json.success);
                $(".g-recaptcha").hide("slow");
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".processing-image").hide();
                $(".alert-danger").show();
                $(".alert-danger").text(json.error);
                $("button").show();
                $("input").prop("disabled", false);

            }
        }

    });
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
