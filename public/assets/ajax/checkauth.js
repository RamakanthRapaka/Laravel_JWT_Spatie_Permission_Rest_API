/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    var api_token = localStorage.getItem("api_token");
    $.ajax({
        url: 'http://arc.test/api/v1/me',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
            }
            if (data.status_code != '200') {
                window.location.replace('/');
            }
        }
    });
});

