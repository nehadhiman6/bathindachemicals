import Errors from './Errors';

class Form {
  /**
   * Create a new Form instance.
   *
   * @param {object} data
   */
    constructor(data) {
        this.originalData = data;
        this.copyData = data;
        for (let field in data) {
        this[field] = data[field];
        }
        this.errors = new Errors();
    }

  postForm(url,errorStatus=true){
    if(this.form_id >0){
      url = url+'/'+this.form_id;
      return this.patch(url,errorStatus);
    }
    else{
      return this.post(url,errorStatus);
    }
  }
  /**
   * Fetch all relevant data for the form.
   */
  data() {
    let data = {};

    for (let property in this.originalData) {
      data[property] = this[property];
    }

    return data;
  }

  /**
   * Reset the form fields.
   */
   reset() {
        for (let field in this.originalData) {
        this[field] = '';
        }

        this.errors.clear();
    }

  /**
   * Send a POST request to the given URL.
   * .
   * @param {string} url
   */
    post(url,errorStatus=true) {
        if(typeof($('.serverError')[0])!= "undefined"){
        $('.serverError').remove();
        }
        return this.submit('post', url,errorStatus);
    }


  /**
   * Send a PUT request to the given URL.
   * .
   * @param {string} url
   */
    put(url,errorStatus=true) {
        if(typeof($('.serverError')[0])!= "undefined"){
        $('.serverError').remove();
        }
        return this.submit('put', url);
    }

  /**
   * Send a PATCH request to the given URL.
   * .
   * @param {string} url
   */
    patch(url,errorStatus=true) {
        if(typeof($('.serverError')[0])!= "undefined"){
        $('.serverError').remove();
        }
        return this.submit('patch', url);
    }

  /**
   * Send a DELETE request to the given URL.
   * .
   * @param {string} url
   */
    delete(url,errorStatus=true) {
        return this.submit('delete', url);
    }

  /**
   * Submit the form.
   *
   * @param {string} requestType
   * @param {string} url
   */
    submit(requestType, url,errorStatus) {
        return new Promise((resolve, reject) => {
        //Added by neha to clear previos errors after submit
        this.errors = new Errors();
        axios[requestType](url, this.data())
            .then(response => {
                this.onSuccess(response.data);
                resolve(response.data,errorStatus);
            })
            .catch(error => {
                this.onFail(error.response,errorStatus);
                reject(error.response.data);
            });
        });
    }

  /**
   * Handle a successful form submission.
   *
   * @param {object} data
   */
    onSuccess(data) {
        //alert(data.message); // temporary

        // this.reset();
    }

  /**
   * Handle a failed form submission.
   *
   * @param {object} errors
   */
    onFail(errors,errorStatus) {
        // console.log(errors);
        if(errors.status != 422){
            var msg = errors.statusText;
            if(errors.data && errors.data.exception && errors.data.message) {
                msg = errors.data.message;
            }
            if(errorStatus != false){
                if(typeof($('.serverError')[0]) == "undefined"){
                    $($(".form")[0]).prepend("<div class='serverError text-orange-700 border-l-4 mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative'><i class='fa fa-exclamation-triangle fa-2x' style='margin-right: 10px; vertical-align: middle;'></i>"+ msg +"</div>");
                    window.scrollTo(0,0);
                }
                else{
                    $('.serverError').css({"background": "#dac111", "padding": "10px 20px","text-align": "center", "color": "#000","font-size": "16px"});
                    $('.serverError').html("<i class='fa fa-exclamation-triangle fa-2x' style='margin-right: 10px; vertical-align: middle;'></i>"+ msg);
                }
            }
        }
        else{
            if (errors && errors.data && errors.data.errors && errors.data.errors.gen_msg) {
                $($(".form")[0]).prepend("<div class='serverError text-orange-700 border-l-4 mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative'><i class='fa fa-exclamation-triangle fa-2x' style='margin-right: 10px; vertical-align: middle;'></i>"+ errors.data.errors.gen_msg +"</div>");
                window.scrollTo(0,0);
            }
            this.errors.record(errors.data.errors);
        }
    }

    isDirty() {
        var copyData = JSON.parse(JSON.stringify(this.copyData));
        // var copyData = this.copyData;
        // var mainData = this.data();
        var mainData = JSON.parse(JSON.stringify(this.data()));
        var dirty = !this.deepEqual(copyData, mainData);
        if(!dirty) {
            var dirty = !this.deepEqual(mainData, copyData);
        }
        // console.log(dirty);
        return dirty;


    }

    deepEqual(x,y) {
        const ok = Object.keys, tx = typeof x, ty = typeof y;
        var result = true;
        // console.log('type:', tx);
        if(x && y && tx === 'object' && tx === ty) {
            // if(ok(x).length !== ok(y).length) {
            //     result = false;
            // }
            // if(result == true) {
                result = ok(x).every(key => this.deepEqual(x[key], y[key]));
            // }
        } else if(tx == 'function') {
            result = true;
        } else {
            var x1 = (x == undefined) ? '':x;
            var y1 = (y == undefined) ? '':y;
            result = (x1 == y1);
        }
        // if(result==false) {
        //     console.log('x:', x);
        //     console.log('y:', y);
        //     console.log('x1:', x1);
        //     console.log('y1:', y1);
        // }
        return result;

        // return x && y && tx === 'object' && tx === ty ? (
        //     ok(x).length === ok(y).length &&
        //     ok(x).every(key => this.deepEqual(x[key], y[key]))
        // ) : (x == y);
    }
}

export default Form;
