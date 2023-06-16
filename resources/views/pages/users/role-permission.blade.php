<x-app-layout title="Message Test">

    <div class="app-content">
        <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="page-description page-description-tabbed">
                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="user-tab" data-bs-toggle="tab"
                                        data-bs-target="#users" type="button" role="tab" aria-controls="user"
                                        aria-selected="true">Users</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="roles-tab" data-bs-toggle="tab"
                                        data-bs-target="#roles" type="button" role="tab" aria-controls="roles"
                                        aria-selected="false">Roles</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="permission-tab" data-bs-toggle="tab"
                                        data-bs-target="#permissions" type="button" role="tab" aria-controls="permission"
                                        aria-selected="false">Permissions</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="users" role="tabpanel"
                                aria-labelledby="user-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Users</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <table id="tableUsers" class="display management" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Name</th>
                                                            <th class="text-center">Email</th>
                                                            <th class="text-center">Role</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($users as $user)
                                                            <tr>
                                                                <td class="text-center">{{ $user->name }}</td>
                                                                <td class="text-center">{{ $user->email }}</td>
                                                                <td class="text-center text-success">{{ $user->roles->pluck('name') }}</td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-start">
                                                                        <button class="btn btn-sm btn-primary btn-edit-user" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#assignRole">
                                                                            Rubah
                                                                        </button>
                                                                        {{-- <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#Detailuser">
                                                                            Detail
                                                                            <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span>
                                                                        </button> --}}
                                                                        {{-- <form action='{{ route("product.destroy", $user->id) }}' method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button class="btn btn-sm btn-danger" type="submit">
                                                                                Delete
                                                                                <span class="material-icons-outlined" style="font-size: 15px !important;">delete_outline</span>
                                                                            </button>
                                                                        </form> --}}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-end">
                                        <div class="d-flex justify-content-right">
                                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                                                data-bs-target="#addRole">
                                                <i class="material-icons-outlined">add</i>Tambah
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5>Roles</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <table id="tableRoles" class="display" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Name</th>
                                                            <th class="text-center">Permissions</th>
                                                            <th class="text-center">User Count</th>
                                                            <th class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($roles as $role)
                                                            <tr>
                                                                <td class="text-center">{{ $role->name }}</td>
                                                                <td class="text-center">{{ $role->name == 'super_admin' ? '[all]' : $role->permissions->pluck('name') }}</td>
                                                                <td class="text-center">{{ $role->users_count }}</td>
                                                                <td class="text-center">
                                                                    @if ($role->name != 'super_admin')
                                                                        <div class="d-flex justify-content-start">
                                                                            <button class="btn btn-sm btn-primary btn-edit-role me-1" data-id="{{ $role->id }}" data-bs-toggle="modal" data-bs-target="#addRole">
                                                                                Rubah
                                                                            </button>
                                                                            {{-- <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $role->id }}" data-bs-toggle="modal" data-bs-target="#Detailrole">
                                                                                Detail
                                                                                <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span>
                                                                            </button> --}}
                                                                            <form action='{{ route("role.destroy", $role->id) }}' method="POST">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button class="btn btn-sm btn-danger" type="submit">
                                                                                    Hapus
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permission-tab">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-end">
                                        <div class="d-flex justify-content-right">
                                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" id="addModal"
                                                data-bs-target="#addPermission">
                                                <i class="material-icons-outlined">add</i>Tambah
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5>Permissions</h5>
                                        <div class="example-container">
                                            <div class="example-content">
                                                <table id="tablePermissions" class="display management" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center w-25">No</th>
                                                            <th class="text-center w-100">Name</th>
                                                            <th class="text-center w-25">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($permissions as $key => $permission)
                                                            <tr>
                                                                <td class="text-center">{{ $key + 1 }}</td>
                                                                <td class="text-center">{{ $permission->name }}</td>
                                                                <td class="text-center w-25">
                                                                    <div class="d-flex justify-content-start">
                                                                        <button class="btn btn-sm btn-primary btn-edit-permission me-1" data-id="{{ $permission->id }}" data-bs-toggle="modal" data-bs-target="#addPermission">
                                                                            Rubah
                                                                        </button>
                                                                        {{-- <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $permission->id }}" data-bs-toggle="modal" data-bs-target="#Detailpermission">
                                                                            Detail
                                                                            <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span>
                                                                        </button> --}}
                                                                        <form action='{{ route("permission.destroy", $permission->id) }}' method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button class="btn btn-sm btn-danger" type="submit">
                                                                                Hapus
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignRole" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="exAssignRole" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exAssignRole">Assign Role</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    @if (session()->has('role-alert'))
                        <x-alert>
                            @slot('type', session('role-alert')['type'])
                            @slot('msg', session('role-alert')['msg'])
                        </x-alert>
                    @endif
                </div>
                <div class="modal-body">
                    <form action="{{ route('role.assign') }}" method="POST" id="assignRoleForm">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="username" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="username" value="" disabled>
                            </div>
                            <div class="col">
                                <label for="useremail" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control" id="useremail" value="" disabled>
                            </div>
                            <input type="hidden" name="user_id" id="user_id" required value="">
                        </div>
                        <div class="mt-2">
                            <label for="roles-list">Roles</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-4 mt-1">
                                        <div class="card p-2 border border-info">
                                            <div class="form-check m-0">
                                                <input type="checkbox" class="form-check-input roles" id="{{ $role->id }}" name="roles[]" value="{{ $role->name }}">
                                                <label class="form-check-label" for="{{ $role->id }}">{{ $role->name }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <label for="username" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-batal" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="submit" class="btn btn-primary">Rubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addRole" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Role</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    @if (session()->has('role-alert'))
                        <x-alert>
                            @slot('type', session('role-alert')['type'])
                            @slot('msg', session('role-alert')['msg'])
                        </x-alert>
                    @endif
                </div>
                <div class="modal-body">
                    <form action="{{ route('role.add') }}" method="POST" id="roleForm">
                        @csrf
                        <div id="patchRubahRole"></div>
                        <label for="title-roles" class="form-label">Name</label>
                        <div class="input-group mb-3">
                            <input type="text" name="title" class="form-control" id="title-roles" value="{{ old('title') }}" required>
                            <span class="input-group-text" id="onRubahPermission">
                                <input type="checkbox" class="form-check-input" id="editPermissionCheck" name="is_edit" value="0">
                            </span>
                        </div>
                        <input type="hidden" name="name" class="form-control" id="roles-input" value="{{ old('name') }}" required>
                        <div class="input-group mt-2">
                            <label for="permissions-list">Permissions</label>
                            <div class="row w-100">
                                    @foreach ($permissions as $permission)
                                        <div class="col-4 mt-1">
                                            <div class="card p-2 border border-info">
                                                <div class="form-check m-0">
                                                    <input type="checkbox" class="form-check-input permissions" id="{{ $permission->name }}" name="permissions[]" value="{{ $permission->name }}">
                                                    <label class="form-check-label" for="{{ $permission->name }}">{{ $permission->name }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-batal" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="addRoleBtn" name="submit" class="btn btn-primary">Tambah</button>
                            <button type="submit" id="updateRoleBtn" name="submit" class="btn btn-primary">Rubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPermission" data-bs-backdrop="static" data-bs-keyboard="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionBtn">Permission</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <form action="{{ route('permission.add') }}" method="POST" id="permissionForm">
                        @csrf
                        <div id="patchRubahPerm"></div>
                        <label for="permission" class="form-label">Name</label>
                        <input type="text" name="permission" class="form-control" id="permission" value="{{ old('permission') }}" required>
                        <input type="hidden" name="name" class="form-control" id="permission-name" value="{{ old('name') }}" required>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-batal" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="addPerm" name="submit" class="btn btn-primary">Tambah</button>
                            <button type="submit" id="updatePerm" name="submit" class="btn btn-primary">Rubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/pages/datatables.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function(){

            $('#updateRole').hide();
            $('#updatePerm').hide();
            $('#onRubahPermission').hide();
            $('#updateRoleBtn').hide();


            $('#editPermissionCheck').on('change', function() {
                if($(this).prop("checked")) {
                    $('#title-roles').prop('disabled', false);
                    $(this).val(1)
                } else {
                    $('#title-roles').prop('disabled', true);
                    $(this).val(0)
                }
            });

            $('.btn-edit-user').on('click', function(){
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("user-detail", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#username').val(data.name);
                        $('#useremail').val(data.email);
                        $('#user_id').val(data.id);
                        if (data.roles) {
                            
                            var roles = data.roles.map(function(role){
                                return role.name;
                            });

                            $('.roles').each(function(i){
                                if(roles.indexOf($(this).val()) >= 0){
                                    $(this).prop('checked', true);
                                }
                            });
                        }

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            });

            $('.btn-edit-role').on('click', function(){
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("role.get", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#title-roles').val(data.name);
                        $('#title-roles').prop('disabled', true);
                        $('#roles-input').val(data.name);
                        if (data.permissions) {
                            
                            var permission = data.permissions.map(function(permission){
                                return permission.name;
                            });

                            $('.permissions').each(function(i){
                                if(permission.indexOf($(this).val()) >= 0){
                                    $(this).prop('checked', true);
                                }
                            });
                        }
                        $('#addRoleBtn').hide();
                        $('#updateRoleBtn').show();
                        $('#onRubahPermission').show();
                        $('#roleForm').attr("action", "{{ route('role.update', ":id") }}".replace(':id', id));
                        $('#patchRubahRole').html('<input type="hidden" name="_method" value="PATCH" id="RubahPatch">');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            });

            $('.btn-edit-permission').on('click', function(){
                var id = $(this).attr('data-id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN':$("meta[name='csrf-token']").attr('content')
                    }
                });

                var url = '{{ route("permission.get", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#permission').val(data.name);
                        $('#permission-name').val(data.name);
                        $('#addPerm').hide();
                        $('#updatePerm').show();
                        $('#permissionForm').attr("action", "{{ route('permission.update', ":id") }}".replace(':id', id));
                        $('#patchRubahPerm').html('<input type="hidden" name="_method" value="PATCH" id="RubahPatch">');

                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            });

            $('#checkboxRubahRole').on('change', function(){
                if($(this).is(':checked')){
                    $('#title-roles').prop('disabled', false);
                    $('#title-roles').focus();
                }else{
                    $('#title-roles').prop('disabled', true);
                }
            })

            $('.btn-batal').on('click', function () {
                $('#permission').val(null);
                $('#title-roles').prop('disabled', false);
                $('#title-roles').val(null);
                $('#permission-name').val(null);
                $('#title').val(null);
                $('#price').val(null);
                $('input[name="permissions[]"]').each(function () { $(this).prop('checked', false); });
                $('#onRubahPermission').hide();
                $('#addRoleBtn').show();
                $('#updateRoleBtn').hide();
                $('#addPerm').show();
                $('#updatePerm').hide();
                $('#permissionForm').attr("action", "{{ route('permission.add') }}");
                $('#patchRubahPerm').html('');
                $('#roleForm').attr("action", "{{ route('role.add') }}");
                $('#patchRubahRole').html('');
            });

            $('#permission').on('change', function (e) {
                var val = $(this).val();
                var slug = val.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#permission-name').val(slug);
            });

            $('#title-roles').on('change', function (e) {
                var val = $(this).val();
                var slug = val.toLowerCase();
                $('#roles-input').val(slug);
            });

            $('button[data-bs-toggle="tab"]').on('click', function (e) {
                localStorage.setItem('lastTab', $(this).attr('data-bs-target'));
            });

            var lastTab = localStorage.getItem('lastTab');
            
            if (lastTab) {
                $('[data-bs-target="' + lastTab + '"]').tab('show');
            }

            $("#tableUsers").removeAttr('width').DataTable({
                columnDefs: [
                    { targets: 3, width: "150px" },
                ],
                fixedColumns: true,
                responsive: true
            });

            $("#tableRoles").removeAttr('width').DataTable({
                columnDefs: [
                    { targets: 3, width: "150px" },
                ],
                fixedColumns: true,
                responsive: true
            });

            $("#tablePermissions").removeAttr('width').DataTable({
                columnDefs: [
                    { targets: 1, width: 150 },
                ],
                fixedColumns: true,
                responsive: true
            });
        })
    </script>
</x-app-layout>
