
$(document).on('visibilitychange', function() {
    if(document.visibilityState != 'hidden') {
        window[appname]['block_loading'] = false;
        axios.get(window[appname]['base_url'])
        .then(function (response){
            window[appname]['block_loading'] = true;
        })
        .catch(function (error) {
            if(error.response.status == 401){
                // $('#sessionModal').modal('show');
            }
            window[appname]['block_loading'] = true;
        })
        .then(function (params) {
            window[appname]['block_loading'] = false;
            axios.get(window[appname]['base_url']+'/login')
            .then(function (response) {
                loginToken = response.data;
                window[appname]['block_loading'] = false;
            })
            .catch(function(error){
                window[appname]['block_loading'] = false;
            });
        })
    }
});


// this section is needed for session Login window when timeout
var contentBottom = new Vue({
    el: '#iw-content-bottom'
});
