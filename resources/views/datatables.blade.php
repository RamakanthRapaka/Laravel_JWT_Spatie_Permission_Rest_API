<!-- DataTables -->
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
$(function () {
    var api_token = localStorage.getItem("api_token");
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "http://arc.test/api/v1/getusersdatatables",
            headers: {
                'Authorization': 'Bearer ' + api_token
            },
            "type": "POST",
            "data": function (data) {
                // Read values
                var state_id = $('#state_select').val();
                var id = $('#district_select').val();
                var is_active = $('#district_active').val();
                var is_sensitive = $('#district_sensitive').val();

                // Append to data
                data.state_id = state_id;
                data.id = id;
                data.is_active = is_active;
                data.is_sensitive = is_sensitive;
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "email"},
            {"data": "created_at"},
            {"data": "roles[, ].name"}
        ]
    });

    $('#roleexample2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "http://arc.test/api/v1/getrolesdatatables",
            headers: {
                'Authorization': 'Bearer ' + api_token
            },
            "type": "POST",
            "data": function (data) {
                // Read values
                var state_id = $('#state_select').val();
                var id = $('#district_select').val();
                var is_active = $('#district_active').val();
                var is_sensitive = $('#district_sensitive').val();

                // Append to data
                data.state_id = state_id;
                data.id = id;
                data.is_active = is_active;
                data.is_sensitive = is_sensitive;
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "created_at"}
        ]
    });

    $('#permissionexample2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "http://arc.test/api/v1/getpermissionsdatatables",
            headers: {
                'Authorization': 'Bearer ' + api_token
            },
            "type": "POST",
            "data": function (data) {
                // Read values
                var state_id = $('#state_select').val();
                var id = $('#district_select').val();
                var is_active = $('#district_active').val();
                var is_sensitive = $('#district_sensitive').val();

                // Append to data
                data.state_id = state_id;
                data.id = id;
                data.is_active = is_active;
                data.is_sensitive = is_sensitive;
            }
        },
        "columns": [
            {"data": "id"},
            {"data": "name"},
            {"data": "created_at"}
        ]
    });
});
</script>