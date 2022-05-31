<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<style>
    .img-thumbnail {
        height: 89.81px !important;
    }
</style>
<body>
<div class="container">
    <br/>
    <h3 align="center">User List</h3>
    <br/>
    <p class="alert alert-info" style="display: none"></p>
    <div align="right">
        <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Create User
        </button>
    </div>
    <br/>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="user_table">
            <thead>
            <tr>
                <th width="10%">Image</th>
                <th width="10%">First Name</th>
                <th width="10%">Last Name</th>
                <th width="15%">Email</th>
                <th width="25%">Address 1</th>
                <th width="10%">Phone</th>
                <th width="20%">Action</th>
            </tr>
            </thead>
        </table>
    </div>
    <br/>
    <br/>
</div>
</body>
</html>

<div id="formModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New User</h4>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form method="post" id="sample_form" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="control-label col-md-4">First Name </label>
                        <div class="col-md-8">
                            <input type="text" name="first_name" id="first_name" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Last Name </label>
                        <div class="col-md-8">
                            <input type="text" name="last_name" id="last_name" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Email </label>
                        <div class="col-md-8">
                            <input type="text" name="email" id="email" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Address Line 1 </label>
                        <div class="col-md-8">
                            <input type="text" name="address1" id="address1" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Address Line 2 </label>
                        <div class="col-md-8">
                            <input type="text" name="address2" id="address2" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Phone </label>
                        <div class="col-md-8">
                            <input type="text" name="phone" id="phone" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Image </label>
                        <div class="col-md-8">
                            <input type="file" name="image" id="image"/>
                            <span id="store_image"></span>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group" align="center">
                        <input type="hidden" name="action" id="action"/>
                        <input type="hidden" name="hidden_id" id="hidden_id"/>
                        <input type="submit" name="action_button" id="action_button" class="btn btn-warning"
                               value="Add"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this user?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/user.js') }}"></script>