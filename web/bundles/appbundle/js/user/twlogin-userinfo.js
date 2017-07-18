
$("#link-tw-login").click(function () {
    $(".login.alert-danger").text("");
    twLogin();
});

function twLogin()
{
    var params = ""
    window.location.href = twLoginUrl + params;

}

