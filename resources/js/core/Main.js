
    // making ajax request url different from index url to prevent browser
    // to show show JSON on back button


axios.interceptors.request.use(function (config) {
    // console.log("BL:OCK VALUE "+ window[appname]['block_loading'] );
        if(window[appname]['block_loading']  && window[appname]['block_loading'] == true){
            $.blockUI();
        }
        config.url += (config.url.indexOf('?') > -1 ? '&url-ts=' : '?url-ts=')+ moment().format('x');
        return config;
    }, 	function (error) {
        return Promise.reject(error);
});

// axios response interceptor
axios.interceptors.response.use(function (config) {
    $.unblockUI();
    return config;
}, function (error) {
    if(error && error.response && error.response.status && error.response.status == 401){
        if($('#sessionModal').length > 0){
            // $('#sessionModal').modal('show');
        }
        $.unblockUI();
        return true;

    }
    $.unblockUI();
    return Promise.reject(error);
});

