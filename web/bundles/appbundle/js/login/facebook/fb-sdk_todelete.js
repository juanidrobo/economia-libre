window.fbAsyncInit = function () {
    FB.init({
        appId: '267111790406866',
        xfbml: false,
        version: 'v2.8'
    });
    //FB.AppEvents.logPageView();
   //FB.getLoginStatus(function (response) {



    // });

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