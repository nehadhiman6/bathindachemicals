export default function resources(){
    function getSharedResources(type,id){
        return axios.get(BCL.base_url +'/shared-resources/'+type+'/'+id)
        .then(function(response){
            return response;
        })
    }

    function saveSharedResources(type,form){
        return form['postForm'](BCL.base_url +'/shared-resources/'+type)
        .then(function(response){
            Utilities.showPopMessage('Attachments has been updated','Saved');
        })
        .catch(function(error){
            console.log(error)
        })
    }
    function getCompanyResources(type,id){
        return axios.get(BCL.base_url +'/company-resources/'+type+'/'+id)
        .then(function(response){
            return response;
        })
    }

    function saveCompanyResources(type,form){
        return form['postForm'](BCL.base_url +'/company-resources/'+type)
        .then(function(response){
            Utilities.showPopMessage('Attachments has been updated','Saved');
        })
        .catch(function(error){
            console.log(error)
        })
    }
    function getYearlyResources(type,id){
        return axios.get(BCL.base_url +'/yearly-resources/'+type+'/'+id)
        .then(function(response){
            return response;
        })
    }

    function saveYearlyResources(type,form){
        return form['postForm'](BCL.base_url +'/yearly-resources/'+type)
        .then(function(response){
            Utilities.showPopMessage('Attachments has been updated','Saved');
        })
        .catch(function(error){
            console.log(error)
        })
    }
    return {
        getSharedResources,
        saveSharedResources,
        getCompanyResources,
        saveCompanyResources,
        getYearlyResources,
        saveYearlyResources

    }
}
