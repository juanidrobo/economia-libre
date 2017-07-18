$('input').keypress(function (e) {

    var key = e.which;
    if (key === 13)  // the enter key code
    {
        $(".btn-question").click();
    }

});


$(".btn-question").click(function () {
    $('input').attr("disabled",true);
    $(".alert-danger").text("");
    $(".btn-question").hide();
    $(".processing-image").show();

    var question = $("#question").val();
    $.ajax({
        type: "POST",
        url: newQuestionUrl,
        data: {
            'question': question,
            'captcha': $("#g-recaptcha-response").val()
        },
        success: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data);

            if (json.success) {
                $(".alert-success").text(json.success);
   
                $(".processing-image").hide("slow");

            }

        },
        fail: function (data, txtStatus, jqXHR) {
            console.log(txtStatus);
            console.log("fail");

        },
        error: function (data, txtStatus, jqXHR) {

            var json = JSON.parse(data.responseText);
            if (json.error) {

                $(".alert-danger").text(json.error);
                $(".processing-image").hide();
                $(".btn-question").show();
                $('input').removeAttr("disabled");
            }
        }

    });
});



