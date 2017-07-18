

$("input").click(function () {
    $("input").parent().removeClass("has-warning");
});
$("textarea").click(function () {
    $("textarea").parent().removeClass("has-warning");
});

$('.email').keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        $(".btn-primary").click();
    }
});



$(".create").click(function () {
    if (!$(this).hasClass("disabled")) {
        var email = $(".email").val();
        var description = $("textarea").val();

        if (validateDescription(description)) {

            $("input").parent().removeClass("has-warning");
            if ($("#g-recaptcha-response").val() !== "") {
                var promise = new Object();
                promise.responsible = email;
                promise.description = description;
                createPromise(promise);
            } else {
                $(".alert-danger").text("Comprueba que no eres un robot!");
                $(".alert-danger").show();
                $(".alert-danger").fadeOut(3000);
            }

        } else {
            $("textarea").parent().addClass("has-warning");
        }

    }
}
);
function createPromise(promise) {
    $("button").hide();
    $(".processing-image").show();
    var promiseJSON = JSON.stringify(promise);


    $.ajax({
        type: "POST",
        url: createPromiseUrl,
        data: {
            'promise': promiseJSON,
            'captcha': $("#g-recaptcha-response").val()
        },
        success: function (data, txtStatus, jqXHR) {
            console.log(data);
            var json = JSON.parse(data);

            if (json.success) {
                var promise = json.promise;
                promiseUrl = promiseUrl.replace("promiseId", promise);
                $.redirect(promiseUrl);
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

            }
        }

    });
}


function validateDescription(description) {
    return (description !== "");
}