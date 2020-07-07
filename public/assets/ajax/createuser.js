/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$("#createuser").click(function () {
    var api_token = localStorage.getItem("api_token");
    $.ajax({
        url: 'http://arc.test/api/v1/createuser',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        data: {
            name: $("#name").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            role_id: $("#role_id").val(),
        },
        success: function (data) {
            if (data.status_code == '200') {
            }
            if (data.status_code == '422') {
            }
        }
    });
});