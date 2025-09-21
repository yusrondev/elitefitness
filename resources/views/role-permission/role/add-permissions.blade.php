@extends('layouts.backoffice')
@section('title', 'Add Permission')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4>{{ $role->name }}</h4>
                    </div>
                    <div class="card-body">

                        <form action="{{ url('roles/'.$role->id.'/give-permissions') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                @error('permission')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <label for="">Daftar Permissions :</label>

                                @php
                                    $groupedPermissions = [];

                                    foreach ($permissions as $permission) {
                                        $parts = explode('-', $permission->name);
                                        $category = $parts[1] ?? 'Other';
                                        $groupedPermissions[$category][] = $permission;
                                    }
                                @endphp

                                <div class="row">
                                    @foreach ($groupedPermissions as $category => $perms)
                                        <div class="col-md-12 mt-2">
                                            <strong>{{ ucfirst($category) }}</strong>
                                        </div>
                                        @foreach ($perms as $permission)
                                            <div class="col-md-2">
                                                <label>
                                                    <input
                                                        type="checkbox"
                                                        name="permission[]"
                                                        value="{{ $permission->name }}"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked':'' }}
                                                    />
                                                    {{ ucwords(str_replace('-',' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <hr>
                                    @endforeach
                                </div>

                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
