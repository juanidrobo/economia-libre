$(document).ready(function () {
    if (autoEmail) {
        $(".email").val(autoEmail);
        setTimeout(startauto, 1000);

    } else if (autoPhone) {
        $("#pnode_number").val(autoPhone);
        setTimeout(startauto, 1000);
    } else if (autoInfo) {
        $("#userCode").val(autoInfo);
        setTimeout(startauto, 1000);
    }
});

function otherPublicProfile() {
    $(".email").val("");
    $("#profile_phone_number").val("");
    $("#userCode").val("");

    $(".user-name").html("");
    $(".user-email").html("");
    $(".user-phone").html("");
    $(".info-others-emails div").remove();
    $(".info-others-phones div").remove();
    $(".info-networks").html("");

    $("#div-contact-info").hide("slow");
    $(".login").show("");


    $("#count-created-promises").html("");
    $("#table-created-promises tbody").html("");

    $("#count-received-reviews").html("");
    $("#table-received-reviews tbody").html("");

    $("#count-written-reviews").html("")
    $("#table-written-reviews tbody").html("");
    $(".user-name").html("");

    $("#div-history-promises").hide();
    $(".btn-profile").show();
    $(".email").focus();
}

function startauto() {
    $(".btn-profile").click();
}

$(".btn-profile").click(function () {

    $(".card-footer .alert-danger").html("");
    $(".card-footer .alert-danger").hide();
    var user = new Object();
    user.email = $(".email").val().trim();
    user.phone = $("#country_code").val() + "-" + $("#profile_phone_number").val().trim();

    var readyToGo = false;
    if (validateEmail(user.email)) {
        readyToGo = true;
    }
    if (validatePhone(user.phone))
    {
        user.phone = $("#country_code").val() + "-" + $("#profile_phone_number").val();
        if (user.phone.charAt(0) != '+') {
            user.phone = '+' + user.phone;
        }
        readyToGo = true

    }
    if (readyToGo) {
        //console.log(user);

        profile(user);
    }

    else {
        if ($("#userCode").val() != "") {
            user.info = $("#userCode").val();
            profile(user);
        } else {
            $(".card-footer .alert-danger").text("Ingresa información valida para continuar!");
            $(".card-footer .alert-danger").css("display", "inline-block");
            $(".card-footer .alert-danger").fadeOut(5000);
        }
    }


});

$('.email').keypress(function (e) {
    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-profile").click();
    }
});

$('#profile_phone_number').keypress(function (e) {
    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-profile").click();
    }
});

$("#btnShowUserEmails").click(function () {
    $(this).hide("slow");
    $(".info-others-emails").show();
})

$("#btnShowUserPhones").click(function () {
    $(this).hide("slow");
    $(".info-others-phones").show();
})

function profile(user) {
    $(".btn-profile").hide();
    $(".card-footer .processing-image").show();
    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: getProfileUrl,
        data: {user: userJSON},
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);
            //console.log(json);
            if (json.success) {
                $(".login").hide("");
                $("#div-contact-info").show("");
                $(".user-name").text(json.displayName);
                
                if (json.userEmail || json.userPhone || json.userNetworks.length>0){
                    $(".verified").css("display","inline-block");
                    $(".not-verified").hide();
                }else{
                    $(".not-verified").css("display","inline-block");
                    $(".verified").hide();
                }
                if (json.userEmail) {
                    $(".user-email").text(json.userEmail);
                    $(".info-email").show();

                }
                if (json.userPhone) {
                    $(".user-phone").text(json.userEmail);
                    $(".info-phone").show();

                }
                $(".user-phone").text(json.userPhone);

                //console.log(json.userNetworks);
                if (json.userNetworks.length > 0) {
                    $(".info-networks").show();
                    $(".info-networks").html("<div><label class=''>Ha iniciado sesión con:</label></div>");
                }
                for (i = 0; i < json.userNetworks.length; i++) {
                    //if the user name is verified
                    var url;
                    var html = "";
                    if (json.userNetworks[i]['name'] == "fb") {
                        html += "<span class='fa fa-facebook-official fa-lg'></span>";
                    }
                    if (json.userNetworks[i]['name'] == "g") {
                        html += "<span class='fa fa-google-plus-official fa-lg'></span>";
                    }

                    if (json.userNetworks[i]['name'] == "tw") {
                        html += "<span class='fa fa-twitter fa-lg'></span>";
                    }

                    $(".info-networks").append(html);

                    if (json.userNetworks[i]["userNameVerified"]) {

                        url = json.userNetworks[i]["displayUrl"] + "/" + json.userNetworks[i]["userName"];
                        html = "<a style='margin-left:5px;' target='_blank' href='" + url + "' class='lead info text-primary user-network'>" + url + "</a>";
                        $(".info-networks").append(html);

                    }
                    $(".info-networks").append("</br>");

                }


                for (i = 0; i < json.userInfo.length; i++) {
                    if (json.userInfo[i][0] === "email") {
                        $(".info-others-emails").append("<div><label class=' text-primary'>" + json.userInfo[i][1] + "</label></div>");
                        $("#btnShowUserEmails").show();
                    }
                    if (json.userInfo[i][0] === "phone") {
                        $(".info-others-phones").append("<div><label class=' text-primary'>" + json.userInfo[i][1] + "</label></div>");
                        $("#btnShowUserPhones").show();
                    }
                }

                $(".info").show();

                $(".verification").val(json.verification);
                fillTableCreatedPromises(json);
                fillTableReceivedReviews(json);
                fillTableWrittenReviews(json);
                $(".card-footer .processing-image").hide();
                $("#div-history-promises").css("display", "block");
            }
        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
            $(".card-footer .alert-danger").text("Error inesperado, prueba mas tarde.");
            $(".card-footer .alert-danger").css("display", "inline-block");
            $(".card-footer .alert-danger").fadeOut(5000);
            $(".card-footer .processing-image").hide();
            $(".card-footer .btn-profile").show();
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {

                $(".card-footer .alert-danger").hide();
                $(".card-footer .alert-danger").text(json.error);
                $(".card-footer .alert-danger").css("display", "inline-block");
                $(".card-footer .alert-danger").fadeOut(5000);
                $(".card-footer .processing-image").hide("slow");
                $(".card-footer .btn-profile").show();

            }
        },
        complete: function (jqXHR, textStatus) {

        }

    });
}



function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^\+?\d{1,3}?[- .]?\(?(?:\d{2,3})\)?[- .]?\d\d\d[- .]?\d\d\d\d$/;
    return re.test(phone);
}
