@extends('admin.layouts.master-page')

@section('title')
    Project
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Project</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                      <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Project</li>
                    </ol>
                  </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
             <div class="mb-3">
                <a class="btn btn-outline-primary" href="{{route('add_project')}}" title="Create New">Create New</a>
            </div>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="80px" class="text-center">No</th>
                        <th scope="col">Project</th>
                        <th scope="col" width="200px">Username</th>
                        <th scope="col" width="300px">Link Project</th>
                        <th scope="col" width="350px" class="text-center">Action</th>
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
                            <td valign="middle"><a class="badge text-bg-primary" href="{{url($project->slug)}}" target="_blank">{{url($project->slug)}}</a></td>
                            <td valign="middle" class="text-center">
                                <a href="{{route('edit_project',[$project->id])}}" class="btn btn-outline-info" title="Update"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-outline-danger" title="Delete" onclick="deleteItem({{$project->id}},'project','{{route('delete_project')}}');"><i class="fa-solid fa-trash"></i></button>
                                <a href="{{route('change_password_project',[$project->id])}}" class="btn btn-outline-success" title="Change password">Change password</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$projects->links('admin.layouts.pagination')}}
        </div>
    </div>
@endsection
