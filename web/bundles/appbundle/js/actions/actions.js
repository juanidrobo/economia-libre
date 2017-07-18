
$(".close-actions").click(function () {
    $(this).parent().hide("slow");
    $(".btn-actions").parent().show("slow");
    $(".action .panel").hide();
});

$(".close").click(function () {

    $(this).parent().hide("slow");

    if ($(this).parent().parent().find(".card:visible").length === 1) { //size===1 because the hide("slow") takes time to complete
     
        $(".btn-actions").show("slow");
    }
});

$(".release").click(function () {
    if (!$(this).hasClass("disabled")) {
        console.log(1);
        $(".btn-actions").hide("slow");
        $(".div-release").css("display", "block");
        $(".panel-public-key").show("slow");
        $(".panel-public-key").find(".publickey2").val("");
        $(".panel-public-key").find(".publickey").parent().removeClass("has-warning");
        $(".panel-public-key").find(".publickey2").parent().removeClass("has-warning");
        $(".panel-public-key ").find(".alert-danger").html("");
        $(".public-key-actions").hide();
        $(".btn-public-key-show").show();
        $("body").animate({scrollTop: $("body").height() - 300}, 'slow');
        console.log(999);
    }

});


$(".grab").click(function () {
    if (!$(this).hasClass("disabled")) {
        $(".btn-actions").hide("slow");
        $(".panel-grab-promise").show("slow");
        $(".panel-grab-promise-anonymous").show("slow");

        $(".panel-grab-promise .alert-danger").text("");
        $(".panel-grab-promise .alert-info").text("");
        $(".panel-grab-promise .alert-success").text("");
        $(".panel-grab-promise .pubkey").val("");
        $(".panel-grab-promise .pubkey").focus();
        $(".panel-grab-promise-after-pubkey").hide();
        $(".panel-grab-promise .btn-validate-pubkey").show();
        $(".panel-grab-user-key").hide();
        $(".panel-grab-promise .email").show();
        $(".panel-grab-promise .email").focus;
        $(".panel-grab-promise .email").val("");
        $(".panel-grab-promise .email-text").hide();
        $("body").animate({scrollTop: 99999}, 'slow');
     
    }

});

$(".transfer").click(function () {
    if (!$(this).hasClass("disabled")) {
        $(".btn-actions").hide("slow");
        $(".panel-transfer").show("slow");
        $(".panel-transfer").find(".email-to-transfer").val("");
        $(".panel-transfer").find(".email-to-transfer").parent().removeClass("has-warning");
        $(".panel-transfer").find(".email-to-transfer").focus();
        $(".panel-transfer").find(".alert-danger").html("");

        $("body").animate({scrollTop: 99999}, 'slow');
    }

});
$(".claim").click(function () {
    if (!$(this).hasClass("disabled")) {
        $(".btn-actions").hide("slow");
        $(".panel-claim").show("slow");
        $(".panel-claim").find(".alert-danger").html("");
        $("body").animate({scrollTop: 99999}, 'slow');
    }

});

$(".review").click(function () {
    if (!$(this).hasClass("disabled")) {
        $(".btn-actions").hide("slow");
        $(".panel-review").show("slow", function () {

        });
        $("body").animate({scrollTop: $(".panel-review").offset().top - 100}, 'slow');
    }

});



/**************** Functions *******************/

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function validateSecKey(secKey) {
    if (secKey.length < 8) {
        return false;
    }
    return true;
}

function refreshPromise() {
    var url = window.location.href;
    console.log(url);
    var position = url.indexOf("#");
    if (position !== -1) {
        url = url.substr(0, position);
    }
    position = url.indexOf("&");
    if (position !== -1) {
        url = url.substr(0, position);
    }
    window.location.href = url;
}

