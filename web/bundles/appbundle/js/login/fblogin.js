
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


$(".btn-fb-login").click(function () {
    $(".login.alert-danger").hide();
    $("#login-processing-image").show();
    fbLogin();
});


function fbLogin()
{
   
    FB.login(function (response) {
        if (!response.authResponse) {
            $("#login-processing-image").hide();
            return;
        }
        var accessToken = response.authResponse.accessToken;

        if (response.status === 'connected') {
            var user = new Object();
            user.access_token = accessToken;
            if ($("#eventCode")) {
                user.eventCode = $("#eventCode").val();
            }
            if ($("#userEmail")) {
                userEmail = $("#userEmail").text().trim();
                if (userEmail !== "")
                    user.email = $("#userEmail").text().trim();
            }
            
            loginWithFb(user);
        } else {
            // The person is not logged into this app or we are unable to tell. 
        }

    }, {scope: 'public_profile,email' /*, auth_type: "rerequest" */}
    );
}


function loginWithFb(user) {

    var userJSON = JSON.stringify(user);
    //console.log(userJSON);
    //return;
    $.ajax({
        type: "POST",
        url: fbLoginUrl,
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
            console.log(json);
            if (json.error) {
                $(".login.alert-danger").css("display","inline-block");
                $(".login.alert-danger").text(json.error);
            }
        }, complete: function () {
            $("#login-processing-image").hide();
        }

    });
}
        