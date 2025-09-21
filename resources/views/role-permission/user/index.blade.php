@extends('layouts.backoffice')
@section('title', 'Users')
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
                            @can('create-user')
                            <a href="{{ url('users/create') }}" class="btn btn-sm btn-primary float-end">Add User</a>
                            @endcan
                        </h4>
                        <form method="GET" action="{{ url('admin/users') }}" class="mb-3 d-flex gap-2">
                            <input type="text" name="query" value="{{ $query }}" placeholder="Cari user..." class="form-control" style="width: 200px;">
                            
                            <select name="status" class="form-select" style="width: 200px;">
                                <option value="">-- Semua Status --</option>
                                <option value="registered" {{ $status === 'registered' ? 'selected' : '' }}>Sudah Terdaftar</option>
                                <option value="unregistered" {{ $status === 'unregistered' ? 'selected' : '' }}>Belum Terdaftar</option>
                            </select>
                        
                            <select name="role" class="form-select" style="width: 200px;">
                                <option value="">-- Semua Role --</option>
                                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="trainer" {{ $role === 'trainer' ? 'selected' : '' }}>Trainer</option>
                                <option value="member" {{ $role === 'member' ? 'selected' : '' }}>Member</option>
                            </select>
                        
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>


                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Status Member</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if (!empty($user->getRoleNames()))
                                                @foreach ($user->getRoleNames() as $rolename)
                                                    <label class="badge bg-primary text-dark mx-1">{{ $rolename }}</label>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->member && $user->member->idmember ? 'Sudah terdaftar' : 'Belum terdaftar' }}
                                        </td>

                                        <td>
                                            @can('update-user')
                                                <a href="{{ url('/admin/edit_member/'.$user->id) }}" class="btn btn-sm btn-success">Edit</a>
                                            @endcan
    
                                            @can('delete-user')
                                                <a href="{{ url('users/'.$user->id.'/delete') }}" class="btn btn-sm btn-danger mx-2">Delete</a>
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
    </div>

@endsection