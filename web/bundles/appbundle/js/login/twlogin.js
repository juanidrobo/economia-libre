
$(".btn-tw-login").click(function () {
    $(".login.alert-danger").hide();
    twLogin();
});


function twLogin()
{
    var params = ""
    if ($("#userEmail")) {
        userEmail = $("#userEmail").text().trim();
        if (userEmail !== "")
            params = "?email=" + $("#userEmail").text().trim();
    }

    if ($("#eventCode").val()) {
        if (params === "")
            params = params + "?"
        else
            params = params + "&"
        params = params + "eventCode=" + $("#eventCode").val();
    }


    window.location.href = twLoginUrl + params;

}

