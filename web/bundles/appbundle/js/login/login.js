$(window).ready(function () {
    // $("body").animate({scrollTop: $(".login-email").offset().top}, 1500);
     $(".login-email").focus();
});
$('.login-email').keypress(function (e) {
    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-check").click();
    }

});

$('.login-seckey').keypress(function (e) {

    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-login").click();
    }

});


$(".btn-check").click(function () {

    $(".login.alert-danger").text("");
    $(".login.alert-danger").hide();
    $(".login-email").parent().removeClass("has-warning");//REVISAR!!

    var user = new Object();
    user.email = $(".login-email").val();
    if (!validateEmail(user.email)) {
        $(".login-email").parent().addClass("has-warning"); //REVISAR!!
        return;
    }

    $(".div-check-email").hide();
    $(".login.processing-image").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: validateUserUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".div-email").hide("slow");
                $(".label-email").html($(".login-email").val());
                $(".label-email").show("slow");
                $(".div-password").show("slow");
                $(".div-login-password").show("slow");
                $(".login.processing-image").hide("slow");
                $(".login-seckey").focus();
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");

        },
        error: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".div-email").hide("slow");
                $(".label-email").html($(".login-email").val());
                $(".label-email").show("slow");
                $(".login.processing-image").hide("slow");
                $(".div-register").show("slow");
                $(".btn-register").focus();

            }
        }

    });
});

$(".btn-register-back").click(function () {

    $(".label-email").html("");
    $(".label-email").hide();
    $(".div-email").show("slow");
    $(".div-check-email").show("slow");
    $(".div-register").hide();
    $(".login-email").val("");
    $(".login-email").focus();
    $(".login-email").parent().removeClass("has-warning");
    $(".login.alert-danger").hide("");


});

$(".btn-register").click(function () {


    var user = new Object();
    user.email = $(".login-email").val();


    $(".btn-register").hide();
    $(".btn-register-back").hide();
    $(".login.processing-image").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: registerUserUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                $(".login.processing-image").hide("slow");
                $(".login.alert-success").show("slow");
                $(".login.alert-success").text(json.success);
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                console.log(json);
                $(".div-email").hide("slow");
                $(".label-email").html($(".login-email").val());
                $(".label-email").show("slow");
                $(".login.processing-image").hide("slow");
                $(".div-register").show("slow");
                $(".btn-register-back").show();
                $(".btn-register").show();
                $(".btn-register").focus();
                $(".login.alert-danger").text(json.error);
                $(".login.alert-danger").show();

            }
        }

    });
});

$(".btn-login").click(function () {

    $(".login.alert-danger").text("");
    $(".login.alert-danger").hide();
    $(".login-email").parent().removeClass("has-warning");

    var user = new Object();
    user.email = $(".login-email").val();
    if (!validateEmail(user.email)) {
        $(".login-email").parent().addClass("has-warning");
        return;
    }
    user.seckey = md5($(".login-seckey").val());

    $(".login.processing-image").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: userLoginUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            console.log(data);
            var json = JSON.parse(data);
            console.log(json);
            if (json.success) {
                window.location.href = window.location.href;
            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");

        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".login-seckey").val("");
                $(".login.processing-image").hide("slow");
                $(".login.alert-danger").show("slow");
                $(".login.alert-danger").text(json.error);

            }
        }

    });
});



$(".btn-login-back").click(function () {

    $(".label-email").html("");
    $(".label-email").hide();
    $(".div-email").show("slow");
    $(".div-check-email").show("slow");
    $(".div-password").hide("slow");
    $(".div-login-password").hide("slow");
    $(".login-email").val("");
    $(".login-email").focus();
    $(".login-email").parent().removeClass("has-warning");
    $(".login.alert-danger").hide("");


});


function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

