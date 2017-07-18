$(document).ready(function () {
    gapi.load('client:auth2', initClient); //initClient();

});

var GoogleAuth; // Google Auth object.
function initClient() {
    gapi.client.init({
        'apiKey': g_api_key,
        'clientId': g_client_id,
        'scope': 'profile email'

    }).then(function () {
        GoogleAuth = gapi.auth2.getAuthInstance();
        $('.btn-g-login').removeAttr("disabled")
    });

    $('.btn-g-login').click(function () {
    $(".login.alert-danger").hide();
    $("#login-processing-image").show();
        gLogin();
    });

}


function gLogin() {
    GoogleAuth.signIn()
            .then(function (promise) {
                var user = new Object();
                user.access_token = promise.Zi.access_token;
                if ($("#eventCode")) {
                    user.eventCode = $("#eventCode").val();
                }
                if ($("#userEmail")) {
                    userEmail = $("#userEmail").text().trim();
                    if (userEmail !== "")
                        user.email = $("#userEmail").text().trim();
                }

           
                loginWithG(user);
            });

}

function loginWithG(user)
{
    var userJSON = JSON.stringify(user);
    $.ajax({
        type: "POST",
        url: gLoginUrl,
        data: {
            'user': userJSON,
        },
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                if (window.location.href.indexOf("newkey/") !== -1) {
                    window.location.href = homeUrl;
                } else {
                    window.location.href = window.location.href;
                }
            }

        },
        fail: function (data, txtStatus, jqXHR) {

        },
        error: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data.responseText);
            if (json.error) {
                $(".login.alert-danger").css("display","inline-block");
                $(".login.alert-danger").text(json.error);
            }
        }, complete: function () {
            $("#login-processing-image").hide();
        }

    });
}


