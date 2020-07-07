/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$("#logout").click(function () {
    var api_token = localStorage.getItem("api_token");
    $.ajax({
        url: 'http://arc.test/api/v1/logout',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
                localStorage.clear();
                window.location.replace('/');
            }
            if (data.status_code == '422') {
                alert('Validation Failed');
            }
        }
    });
});