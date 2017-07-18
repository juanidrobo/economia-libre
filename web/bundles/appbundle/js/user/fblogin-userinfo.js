
window.fbAsyncInit = function () {
    FB.init({
        appId: fb_client_id,
        xfbml: true,
        version: fb_client_version,
    });
    FB.AppEvents.logPageView();
};


(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/es_LA/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


$("#link-fb-login").click(function () {
    $("#adding-processing-image").show();
    $(".success-add-social").text("");
    $(".success-add-social").hide();
    $(".alert-add-social").text("");
    $(".alert-add-social").hide();
    fbLogin();
});


function fbLogin()
{
    FB.login(function (response) {
        var accessToken = response.authResponse.accessToken;

        if (response.status === 'connected') {
            var user = new Object();
            user.access_token = accessToken;

            loginWithFb(user);
        } else {
             
        }

    }, {scope: 'public_profile,email'
        , auth_type: "rerequest"}
       // , auth_type: "reauthenticate"}
    );
}


function loginWithFb(user) {

    var userJSON = JSON.stringify(user);
    $.ajax({
        type: "POST",
        url: fbLoginUrl,
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
            console.log(txtStatus);
            console.log("fail");
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
        