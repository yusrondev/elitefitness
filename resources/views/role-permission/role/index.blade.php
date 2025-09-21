@extends('layouts.backoffice')
@section('title', 'Roles')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>
                            @can('create-role')
                                <a href="{{ url('roles/create') }}" class="btn btn-primary btn-sm float-end">Add Role</a>
                            @endcan
                        </h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th width="40%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <a href="{{ url('roles/' . $role->id . '/give-permissions') }}"
                                                class="btn btn-sm btn-warning">
                                                Add / Edit Role Permission
                                            </a>

                                            @can('update-role')
                                                <a href="{{ url('roles/' . $role->id . '/edit') }}" class="btn btn-sm btn-success">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('delete-role')
                                                <a href="{{ url('roles/' . $role->id . '/delete') }}" class="btn btn-sm btn-danger mx-2">
                                                    Delete
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
