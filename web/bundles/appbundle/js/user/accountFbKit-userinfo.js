// initialize Account Kit with CSRF protection
AccountKit_OnInteractive = function () {
    AccountKit.init(
            {
                appId: fb_client_id,
                state: $("#csrf").val(),
                version: fb_account_kit_version,
                fbAppEventsEnabled: true,
            }
    );
};

// login callback
function loginCallback(response) {
    if (response.status === "PARTIALLY_AUTHENTICATED") {
        var code = response.code;
        var csrf = response.state;
        console.log(code);
        console.log(csrf);
        if ($("#csrf").val() === csrf) {
            var user = Object();
            user.code = code;

            getInfoFromKit(user);
        }
        // Send code to server to exchange for access token
    }
    else if (response.status === "NOT_AUTHENTICATED") {
        // handle authentication failure

    }
    else if (response.status === "BAD_PARAMS") {
        // handle bad parameters

    }
}

var statusInfo = null;

$(".btn-add-phone").click(function () {

    statusInfo = "phone";
    smsLogin();


});
// phone form submission handler
function smsLogin() {
    AccountKit.login(
            'PHONE',
            {
                countryCode: "+57",
            }, // will use default values if not specified
            loginCallback
            );

}


$(".btn-add-email").click(function () {
    statusInfo = "email";
    emailLogin();
});

// email form submission handler
function emailLogin() {
    AccountKit.login(
            'EMAIL',
            {},
            loginCallback
            );
}


function getInfoFromKit(user) {
    var userJSON = JSON.stringify(user);
    if (statusInfo === "email") {
        $("#processing-image-add-email").show();
    }
    if (statusInfo === "phone") {
        $("#processing-image-add-phone").show();
    }
    $.ajax({
        type: "POST",
        url: getInfoFbKitUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            console.log(json);
            if (json.success) {
                if (json.email) {
                    if ($("#div-primary-email").html().trim() === "") {
                        $("#div-primary-email").html('<input class="form-control text-primary" id="primaryEmail" readonly="" value="' + json.email + '" />');
                        var checkbox = "<div class='checkbox checkbox-slider-lg checkbox-slider--b  checkbox-slider-info '>" +
                                "<label>" +
                                "<input class='inputUserInfo email' type='checkbox' org_checked=true checked='checked'><span></span>" +
                                "</label>" +
                                "</div>";

                        $("#div-primary-email").parent().parent().next().html(checkbox);
                    } else {
                        $("#tableUserEmails").show();
                        var tr = "<tr>" +
                                "<td>" +
                                "<div class='div-edit-secondary-email'>" +
                                "<input class='form-control text-primary' style='margin-right:5px' readonly='' value='" + json.email + "' />" +
                                "<div class='checkbox checkbox-slider-lg checkbox-slider--b  checkbox-slider-info '>" +
                                "<label>" +
                                "<input class='inputUserInfo email' type='checkbox' org_checked=true checked='checked'><span></span>" +
                                "</label>" +
                                "</div>" +
                                "<div class='btn-actions'>" +
                                "<button type='button' class='btn btn-secondary btn-up btn-up-email' style='margin-right:5px'>" +
                                "<span class='fa fa-arrow-up fa-lg'></span>" +
                                "</button>" +
                                "<button type='button' class='btn btn-secondary btn-trash'>" +
                                "<span class='fa fa-trash-o fa-lg'></span>" +
                                "</button>" +
                                "<button type='button' class='btn btn-secondary btn-refresh'>" +
                                "<span class='fa fa-refresh fa-lg'></span>" +
                                "</button>" +
                                "</div>" +
                                "</div>" +
                                "</td>" +
                                "</tr>";
                        $("#tableUserEmails tbody").append(tr);
                        btn_info_actions();
                    }
                    setUpToogleUserInfo();
                    $(".alert-success-add-email").text(json.success);
                    $(".alert-success-add-email").css("display","inline-block");sh
                    $("#processing-image-add-email").hide();

                }
                if (json.phone) {
                    if ($("#div-primary-phone").html().trim() === "") {
                        $("#div-primary-phone").html('<input class="form-control text-primary" id="primaryPhone" readonly="" value="' + json.phone + '" />');
                        var checkbox = "<div class='checkbox checkbox-slider-lg checkbox-slider--b  checkbox-slider-info '>" +
                                "<label>" +
                                "<input class='inputUserInfo phone' type='checkbox' org_checked=true checked='checked'><span></span>" +
                                "</label>" +
                                "</div>";

                        $("#div-primary-phone").parent().parent().next().html(checkbox);
                    } else {
                        $("#tableUserPhones").show();
                        var tr = "<tr>" +
                                "<td>" +
                                "<div class='div-edit-secondary-phone'>" +
                                "<input class='form-control text-primary' style='margin-right:5px' readonly='' value='" + json.phone + "' />" +
                                "<div class='checkbox checkbox-slider-lg checkbox-slider--b  checkbox-slider-info '>" +
                                "<label>" +
                                "<input class='inputUserInfo phone' type='checkbox' org_checked=true checked='checked'><span></span>" +
                                "</label>" +
                                "</div>" +
                                "<div class='btn-actions'>" +
                                "<button type='button' class='btn btn-secondary btn-up btn-up-phone' style='margin-right:5px'>" +
                                "<span class='fa fa-arrow-up fa-lg'></span>" +
                                "</button>" +
                                "<button type='button' class='btn btn-secondary btn-trash'>" +
                                "<span class='fa fa-trash fa-lg'></span>" +
                                "</button>" +
                                "<button type='button' class='btn btn-secondary btn-refresh'>" +
                                "<span class='fa fa-refresh fa-lg'></span>" +
                                "</button>" +
                                "</div>" +
                               "</div>" +
                                "</td>" +
                                "<td>" +
                                "</td>" +
                                "</tr>";
                        $("#tableUserPhones tbody").append(tr);
                    }
                    setUpToogleUserInfo();

                    $(".alert-success-add-phone").text(json.success);
                    $(".alert-success-add-phone").css("display","inline-block");
                    $("#processing-image-add-phone").hide();
                }


            }

        },
        fail: function (data, txtStatus, jqXHR) {
            //console.log(txtStatus);
            //console.log("fail");
        },
        error: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data.responseText);
            // console.log(json);
            if (json.error) {
                if (json.email) {
                    $(".alert-danger-add-email").css("display","inline-block");
                    $(".alert-danger-add-email").text(json.error);
                    $("#processing-image-add-email").hide();
                }
                if (json.phone) {
                    $(".alert-danger-add-phone").css("display","inline-block");
                    $(".alert-danger-add-phone").text(json.error);
                    $("#processing-image-add-phone").hide();
                }
            }
        }

    });
}





