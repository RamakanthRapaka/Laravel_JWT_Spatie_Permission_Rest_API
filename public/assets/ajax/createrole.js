/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$("#createrole").click(function () {
    var api_token = localStorage.getItem("api_token");
    $.ajax({
        url: api_url + '/createrole',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        data: {
            name: $("#name").val(),
            id: $("#id").val()
        },
        success: function (data) {
            if (data.status_code == '200') {
            }
            if (data.status_code == '422') {
            }
        }
    });
});