/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    var api_token = localStorage.getItem("api_token");
    $.ajax({
        url: api_url + '/getrole',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
                $.each(data.data, function (k, v) {
                    var markup = "<option value=" + v.id + ">" + v.name + "</option>";
                    $("#role_id").append(markup);
                });
            }
            if (data.status_code != '200') {
                window.location.replace('/');
            }
        }
    });

    $.ajax({
        url: api_url + '/getusers',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
                $.each(data.data, function (k, v) {
                    var markup = "<option value=" + v.id + ">" + v.name + "</option>";
                    $("#user_id").append(markup);
                });
            }
            if (data.status_code != '200') {
                window.location.replace('/');
            }
        }
    });

    $.ajax({
        url: api_url + '/getpermissions',
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        method: 'POST',
        success: function (data) {
            if (data.status_code == '200') {
                $.each(data.data, function (k, v) {
                    var markup = "<option value=" + v.id + ">" + v.name + "</option>";
                    $("#permission_id").append(markup);
                });
            }
            if (data.status_code != '200') {
                window.location.replace('/');
            }
        }
    });
});

