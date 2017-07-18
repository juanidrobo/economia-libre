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
        $('#link-g-login').removeAttr("disabled")
    });

    $('#link-g-login').click(function () {
        gLogin();
    });


}

function gLogin() {
    GoogleAuth.signIn()
            .then(function (promise) {
                var user = new Object();
                user.access_token = promise.Zi.access_token;
                $("#adding-processing-image").show();
                $(".success-add-social").text("");
                $(".success-add-social").hide();
                $(".alert-add-social").text("");
                $(".alert-add-social").hide();
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
                $(".success-add-social").text(json.success);
                $(".success-add-social").css("display","inline-block");
                setTimeout(refreshPage, 5000);
            }

        },
        fail: function (data, txtStatus, jqXHR) {

        },
        error: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data.responseText);

            if (json.error) {
                $(".alert-add-social").text(json.error);
                $(".alert-add-social").css("display","inline-block");
                $("#adding-processing-image").hide();
            }
        }

    });
}



function refreshPage() {
    window.location.reload();
}
