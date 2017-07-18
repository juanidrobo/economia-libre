// initialize Account Kit with CSRF protection
AccountKit_OnInteractive = function () {
    AccountKit.init(
            {
                appId: fb_client_id,
                state: $("#csrf").val(),
                version: fb_account_kit_version,
                fbAppEventsEnabled: true
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
            if ($("#eventCode")) {
                user.eventCode = $("#eventCode").val();
            }
            loginKit(user);
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


$("#phone_number").keypress(function (e) {

    var key = e.which;
    if (key === 13)  // the enter key code
    {
        smsLogin();
    }

});
// phone form submission handler
function smsLogin() {
    var countryCode = document.getElementById("country_code").value;
    var phoneNumber = document.getElementById("phone_number").value;
    AccountKit.login(
            'PHONE',
            {
                countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
    loginCallback
            );
}


$("#email").keypress(function (e) {

    var key = e.which;
    if (key === 13)  // the enter key code
    {
        emailLogin();
    }

});

// email form submission handler
function emailLogin() {
    var emailAddress = document.getElementById("email").value;
    AccountKit.login(
            'EMAIL',
            {emailAddress: emailAddress},
    loginCallback
            );
}


function loginKit(user) {
    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: accountFbKitLoginUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {

                window.location.href = window.location.href;
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
                $(".login.alert-danger").show("slow");
                $(".login.alert-danger").text(json.error);
            }
        }

    });
}





