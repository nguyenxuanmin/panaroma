@extends('admin.layouts.master-page')

@section('title')
    User
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">User</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                      <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                      <li class="breadcrumb-item active" aria-current="page">User</li>
                    </ol>
                  </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="80px" class="text-center">No</th>
                        <th scope="col">Project</th>
                        <th scope="col" width="300px">User Name</th>
                        <th scope="col" width="150px">Create Date</th>
                        <th scope="col" width="200px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($projects) == 0)
                        <tr>
                            <td valign="middle" class="text-center" colspan="5">No data available</td>
                        </tr>
                    @endif
                    @foreach ($projects as $key => $project)
                        <tr>
                            <td valign="middle" class="text-center">{{$key+1}}</td>
                            <td valign="middle">{{$project->name}}</td>
                            <td valign="middle">{{$project->user_name}}</td>
                            <td valign="middle">{{$project->created_at->format('d/m/Y')}}</td>
                            <td valign="middle" class="text-center">
                                <a href="{{route('change_password_project',[$project->id])}}" class="btn btn-outline-info" title="Change password">Change password</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$projects->links('admin.layouts.pagination')}}
        </div>
    </div>
@endsection
