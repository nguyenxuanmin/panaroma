@extends('admin.layouts.master-page')

@section('title')
    Paronama Category
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Paronama Category</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                      <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Paronama Category</li>
                    </ol>
                  </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="{{route('add_building')}}" title="Create New">Create New</a>
            </div>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="80px" class="text-center">No</th>
                        <th scope="col" width="200px"></th>
                        <th scope="col">Title</th>
                        <th scope="col" width="200px">Type</th>
                        <th scope="col" width="150px">Create Date</th>
                        <th scope="col" width="200px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($buildings) == 0)
                        <tr>
                            <td valign="middle" class="text-center" colspan="6">No data available</td>
                        </tr>
                    @endif
                    @foreach ($buildings as $key => $building)
                        <tr>
                            <td valign="middle" class="text-center">{{$key+1}}</td>
                            <td valign="middle" class="text-center">
                                @if (!empty($building->plan_image))
                                    <img src="{{ asset($building->plan_image) }}" alt="{{$building->name}}" class="object-fit-cover" style="max-width: 100%; max-height: 150px;">
                                @else
                                    <img src="{{asset('library/admin/default-image.png')}}" alt="{{$building->name}}" style="max-width: 100%; max-height: 150px;">
                                @endif
                            </td>
                            <td valign="middle">{{$building->name}}</td>
                            <td valign="middle">{{$building->type}}</td>
                            <td valign="middle">{{$building->created_at->format('d/m/Y')}}</td>
                            <td valign="middle" class="text-center">
                                <a href="{{route('edit_building',[$building->id])}}" class="btn btn-outline-info" title="Update"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-outline-danger" title="Delete" onclick="deleteItem({{$building->id}},'building','{{route('delete_building')}}');"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$buildings->links('admin.layouts.pagination')}}
        </div>
    </div>
@endsection
