$("input").click(function(){
    if ($(this).val()==""){
     $(this).next().find(".btn-edit").trigger("click");
    }
});

$("#btn-user-info").click(function () {
    $("#btn-user-info").toggle();
    $(".div-edit-user").toggle("slow");
});
$(".close").click(function () {
    $(this).parent().parent().toggle("slow");
    $("#btn-user-info").toggle();
});
$(".btn-edit").click(function () {
    $(this).parent().parent().find('input:visible').removeAttr("readonly");
    $(this).parent().parent().find('input').focus();
    $(this).parent().parent().find('.btn-ok').css("display", "inline-block");
    $(this).parent().parent().find('.btn-cancel').css("display", "inline-block");
    $(this).hide();

});
$(".btn-cancel").click(function () {
    $(this).parent().parent().find('input:visible').val($(this).parent().parent().find('input:hidden').val());
    $(this).parent().parent().find('input:visible').attr("readonly", true);
    $(this).parent().parent().find('.btn-ok').hide();
    $(this).parent().parent().find('.btn-cancel').hide();
    $(this).parent().parent().find('.btn-edit').css("display", "inline-block");

});
$(".btn-ok").click(function () {
    if ($(this).parent().parent().find('input:visible').val() !== "") {
        $(this).parent().parent().find('input:visible').attr("readonly", true);
        $(this).parent().parent().find('.btn-ok').hide();
        $(this).parent().parent().find('.btn-cancel').hide();
        $(this).parent().parent().find('.btn-edit').css("display", "inline-block");
        $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");
    } else {
        $(this).next().trigger("click");
    }

});
function btn_info_actions() {
    $(".btn-trash").unbind("click");
    $(".btn-refresh").unbind("click");
    $(".btn-up-email").unbind("click");
    $(".btn-up-phone").unbind("click");

    $(".btn-trash").click(function () {

        $(this).parent().parent().find("input.form-control").addClass("deleted");
        $(this).parent().parent().find(".btn-refresh").css("display", "inline-block");
        $(this).parent().parent().find(".btn-up").hide();
        $(this).hide();
        $(this).parent().prev().hide();
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");
    });
    $(".btn-refresh").click(function () {

        $(this).parent().parent().find("input").removeClass("deleted");
        $(this).parent().parent().find(".btn-trash").css("display", "inline-block");
        $(this).parent().parent().find(".btn-up").css("display", "inline-block");
        $(this).hide();
        $(this).parent().prev().show();

    });

    $(".btn-up-phone").click(function () {

        var newPrimaryEmail = ($(this).parent().parent().find("input")).val();
        $(this).parent().parent().find("input").val($("#primaryPhone").val());
        $("#primaryPhone").val(newPrimaryEmail);

        var newPrimaryInputUserInfo = $(this).parent().parent().parent().find(".inputUserInfo.phone").parent().html();
        console.log(newPrimaryInputUserInfo);
        var oldPrimaryInputUserInfo = $("#tableUserInfo").find(".inputUserInfo.phone").parent().html();
        console.log(oldPrimaryInputUserInfo);


        $("#tableUserInfo").find(".inputUserInfo.phone").parent().html(newPrimaryInputUserInfo);
        $(this).parent().parent().parent().parent().find(".inputUserInfo.phone").parent().html(oldPrimaryInputUserInfo);

        setUpToogleUserInfo();
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");

    });

    $(".btn-up-email").click(function () {

        var newPrimaryEmail = ($(this).parent().parent().find("input")).val();
        $(this).parent().parent().find("input").val($("#primaryEmail").val());
        $("#primaryEmail").val(newPrimaryEmail);

        var newPrimaryInputUserInfo = $(this).parent().parent().parent().find(".inputUserInfo.email").parent().html();

        var oldPrimaryInputUserInfo = $("#tableUserInfo").find(".inputUserInfo.email").parent().html();


        $("#tableUserInfo").find(".inputUserInfo.email").parent().html(newPrimaryInputUserInfo);
        $(this).parent().parent().parent().parent().find(".inputUserInfo.email").parent().html(oldPrimaryInputUserInfo);

        setUpToogleUserInfo();
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");

    });
}
btn_info_actions();

$(".btn-edit-social").click(function () {
    $(this).parent().parent().find('input:visible').removeAttr("readonly");
    $("#fb-given-username").focus();
    $(this).parent().parent().find('.btn-ok-social').css("display", "inline-block");
    $(this).parent().parent().find('.btn-cancel-social').css("display", "inline-block");
    $(this).parent().parent().find('.btn-trash-social').css("display", "none");
    $(this).hide();

});

$(".btn-trash-social").click(function () {

    $(this).parent().parent().find("a").addClass("deleted");
    $(this).parent().parent().find("label").addClass("deleted");
    $(this).parent().parent().find("input").addClass("deleted");
    $(this).parent().parent().find(".btn-refresh-social").css("display", "inline-block");
    $(this).parent().parent().find(".btn-edit-social").css("display", "none");
    $(this).hide();
    $(this).parent().parent().prev().hide();
     $("#alert-save-data").show();
    $("#btn-save-changes1").removeAttr("disabled");
});
$(".btn-cancel-social").click(function () {
    $(this).parent().parent().find('input:visible').val($(this).parent().find('input.last-fb-username').val());
    $(this).parent().parent().find('input:visible').attr("readonly", true);
    $(this).parent().parent().find('.btn-ok-social').hide();
    $(this).hide();
    $(this).parent().parent().find('.btn-edit-social').css("display", "inline-block");
    $(this).parent().parent().find('.btn-trash-social').css("display", "inline-block");
});

$(".btn-refresh-social").click(function () {

    $(this).parent().parent().find("a").removeClass("deleted");
    $(this).parent().parent().find("label").removeClass("deleted");
    $(this).parent().parent().find("input").removeClass("deleted");
    $(this).parent().find(".btn-trash-social").css("display", "inline-block");
    $(this).parent().find(".btn-edit-social").css("display", "inline-block");
    $(this).hide();
    $(this).parent().prev().show();

});
$(".btn-ok-social").click(function () {
    if ($(this).parent().parent().find('input:visible').val() !== "") {
        $("#fb-given-username").attr("readonly", true);
        $(this).hide();
        $(this).parent().parent().find('.btn-cancel-social').hide();
        $(this).parent().parent().find('.btn-edit-social').css("display", "inline-block");
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");
    } else {
        $(this).next().trigger("click");
    }

});




$("#btnShowUserEmails").click(function () {
    $(this).hide("slow");
    $("#tableUserEmails").css("display", "inline-table");
})

$("#btnShowUserPhones").click(function () {
    $(this).hide("slow");
    $("#tableUserPhones").css("display", "inline-table");
})



$("#btn-save-changes1").click(function () {

    var infoToRemove = new Array();
    var networkToRemove = new Array();

    for (i = 0; i < $("#contactInfo input.deleted").length; i++) {
        infoToRemove[i] = $($("#contactInfo input.deleted")[i]).val();
    }

    if (infoToRemove.length > 0) {
        $(".alert-danger-user-edit").text("¿Estás seguro de remover un dato VERIFICADO de tu perfil?");
        $(".alert-danger-user-edit").css("display","inline-block");

        $("#btn-save-changes1").hide();
        $("#btn-save-changes2").css("display", "inline-block");
        $("#btn-cancel-changes").css("display", "inline-block");
        return;
    }

    for (i = 0; i < $("#socialInfo a.deleted").length; i++) {
        networkToRemove[i] = $($("#socialInfo a.deleted")[i]).prev().val();
    }
    if ($(".div-edit-network-user label.deleted").length > 0) {
        networkToRemove.push($(".div-edit-network-user label.deleted").prev().val());
    }

    if (networkToRemove.length > 0) {
        $(".alert-danger-user-edit").text("¿Estás seguro de remover una red social VERIFICADA de tu perfil?");
        $(".alert-danger-user-edit").css("display","inline-block");
        $("#btn-save-changes1").hide();
        $("#btn-save-changes2").css("display", "inline-block");
        $("#btn-cancel-changes").css("display", "inline-block");
        return;
    }


    $("#btn-save-changes2").trigger("click");

});
$("#btn-save-changes2").click(function () {
    var user = new Object();
    if ($("#input-name").prop("readonly")) {
        if ($("#input-name").val() !== $("#input-name").parent().find("input:hidden").val()) {
            user.name = $("#input-name").val();
        }
    }
    user.email = $("#primaryEmail").val();
    user.phone = $("#primaryPhone").val();

    var infoToRemove = new Array();


    for (i = 0; i < $("#contactInfo input.deleted").length; i++) {
        infoToRemove[i] = $($("#contactInfo input.deleted")[i]).val();
    }

    user.infoToRemove = infoToRemove;

    var infoToToggle = new Array();
    $(".inputUserInfo").each(function () {
        if ($(this).attr("org_checked") == "true") {

            if (!$(this).attr("checked")) {
                infoToToggle.push($(this).parent().parent().parent().parent().find("input").val());
            }
        } else {
            if ($(this).attr("checked") == "checked") {

                infoToToggle.push($(this).parent().parent().parent().parent().find("input").val());
            }
        }
    });

    user.infoToToggle = infoToToggle;

    var networkToToggle = new Array();
    $(".inputUserNetwork").each(function () {
        if ($(this).attr("org_checked") == "true") {

            if (!$(this).attr("checked")) {
                networkToToggle.push($(this).parent().parent().parent().parent().find("input.network-code").val());
            }
        } else {
            if ($(this).attr("checked") == "checked") {

                networkToToggle.push($(this).parent().parent().parent().parent().find("input.network-code").val());
            }
        }
    });

    user.networkToToggle = networkToToggle;

    var networkToRemove = new Array();

    for (i = 0; i < $("#socialInfo a.deleted").length; i++) {
        networkToRemove[i] = $($("#socialInfo a.deleted")[i]).prev().val();
    }
    if ($(".div-edit-network-user label.deleted").length > 0) {
        networkToRemove.push($(".div-edit-network-user label.deleted").prev().val());
    }
    user.networkToRemove = networkToRemove;

    if ($("#fb-given-username").length > 0)
    {
        if ($("#fb-given-username").val() !== $("#fb-user-given-name").attr("org_value")) {

            user.fbGivenUsername = $("#fb-given-username").val();

        }
    }

    console.log(user);
    editUser(user);

});

$("#btn-cancel-changes").click(function () {
    $(".alert-danger-user-edit").hide();
    $("#btn-save-changes2").hide();
    $("#btn-save-changes1").css("display", "inline-block");
    $("#btn-cancel-changes").hide();
});

function setUpToogleUserInfo() {
    $(".inputUserInfo").unbind("click");
    $(".inputUserInfo").click(function () {
        if ($(this).attr("checked")) {
            $(this).removeAttr("checked");
        } else {
            $(this).attr("checked", true);
        }
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");
    });
}
setUpToogleUserInfo();
function setUpToogleUserNetwork() {
    $(".inputUserNetwork").unbind("click");
    $(".inputUserNetwork").click(function () {
        if ($(this).attr("checked")) {
            $(this).removeAttr("checked");
        } else {
            $(this).attr("checked", true);
        }
         $("#alert-save-data").show();
        $("#btn-save-changes1").removeAttr("disabled");
    });
}
setUpToogleUserNetwork();

function editUser(user) {

    $("#btn-save-changes1").attr("disabled", true);
    $("#processing-image-user-edit").show();

    var userJSON = JSON.stringify(user);

    $.ajax({
        type: "POST",
        url: editUserUrl,
        data: {'user': userJSON},
        success: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data);
            if (json.success) {
                  $("#alert-save-data").hide();
                $(".deleted").parent().parent().parent().remove();
                $("#processing-image-user-edit").hide();
                $(".alert-success-user-edit").css("display", "inline-block");
                $(".alert-success-user-edit").text(json.success);
                $(".alert-danger-user-edit").text("");
                $(".alert-danger-user-edit").hide("");
                $("#btn-save-changes2").hide();
                $("#btn-save-changes1").css("display", "inline-block");
                $("#btn-cancel-changes").hide();
                $(".alert-success-user-edit").fadeOut(5000);

                for (i = 0; i < user.infoToToggle.length; i++) {
                    for (j = 0; j < $(".inputUserInfo").length; j++) {
                        if (user.infoToToggle[i] === $($(".inputUserInfo")[j]).parent().parent().parent().parent().first().find("input").val())
                        {

                            if ($($(".inputUserInfo")[j]).attr("org_checked")) {
                                $($(".inputUserInfo")[j]).removeAttr("org_checked");
                            } else {
                                $($(".inputUserInfo")[j]).attr("org_checked", "true");

                            }

                        }
                    }
                }

                for (i = 0; i < user.networkToToggle.length; i++) {
                    for (j = 0; j < $(".inputUserNetwork").length; j++) {
                        console.log(user.networkToToggle[i]);
                        console.log($($(".inputUserNetwork")[j]).parent().parent().parent().parent().first().find("input").val());

                        if (user.networkToToggle[i] === $($(".inputUserNetwork")[j]).parent().parent().parent().parent().first().find("input.network-code").val())
                        {
                            if ($($(".inputUserNetwork")[j]).attr("org_checked")) {
                                $($(".inputUserNetwork")[j]).removeAttr("org_checked");
                            } else {
                                $($(".inputUserNetwork")[j]).attr("org_checked", "true");

                            }

                        }
                    }
                }

                $("#input-name").prev().val($("#input-name").val());

                if ($("#fb-given-username").prev().val() !== $("#fb-given-username").val())
                {
                    $("#div-edit-network-user label").first().remove();
                    $("#div-edit-network-user").prepend('<label>Verfica esta información enviando un mensaje a este perfil en fb <a target="_blank" href="https://www.facebook.com/juanidrobo">link</a></label>');
                    $("#fb-given-username").prev().val($("#fb-given-username").val());
                }

                if (user.fbGivenUsername != null) {
                    $("#last-fb-username").val(user.fbGivenUsername);
                    $("#label-ask-fb-username").hide();
                    $("#label-confirm-fb").show();
                }

            }


        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");
        },
        error: function (data, txtStatus, jqXHR) {
            var json = JSON.parse(data.responseText);
            if (json.error) {
                
                $("#processing-image-user-edit").hide();
                $(".alert-danger-user-edit").css("display", "inline-block");
                $(".alert-danger-user-edit").text(json.error);
            }
        }

    });
}


$(".link-social").dotdotdot({
    ellipsis: '... ',
    height: 30,
    wrap: 'letter',
    watch: true,
});



function validateSecKey(secKey1, secKey2) {
    if (secKey1.length < 8) {
        $(".old-school-login .alert-danger").text("La contraseña debe de tener al menos 8 caracteres.");
        return false;
    }
    if (secKey1 !== secKey2) {
        $(".old-school-login .alert-danger").text("Las 2 contraseñas no coinciden.");
        return false;
    }
    return true;
}