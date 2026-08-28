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
                <a class="btn btn-outline-primary" href="{{route('add_floor')}}" title="Create New">Create New</a>
            </div>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="80px" class="text-center">No</th>
                        <th scope="col" width="200px"></th>
                        <th scope="col">Title</th>
                        <th scope="col" width="150px">Create Date</th>
                        <th scope="col" width="200px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($floors) == 0)
                        <tr>
                            <td valign="middle" class="text-center" colspan="5">No data available</td>
                        </tr>
                    @endif
                    @foreach ($floors as $key => $floor)
                        <tr>
                            <td valign="middle" class="text-center">{{$key+1}}</td>
                            <td valign="middle" class="text-center">
                                @if (!empty($floor->plan_image))
                                    <img src="{{ asset($floor->plan_image) }}" alt="{{$floor->name}}" class="object-fit-cover" style="max-width: 100%; max-height: 150px;">
                                @else
                                    <img src="{{asset('library/admin/default-image.png')}}" alt="{{$floor->name}}" style="max-width: 100%; max-height: 150px;">
                                @endif
                            </td>
                            <td valign="middle">{{$floor->name}}</td>
                            <td valign="middle">{{$floor->created_at->format('d/m/Y')}}</td>
                            <td valign="middle" class="text-center">
                                <a href="{{route('edit_floor',[$floor->id])}}" class="btn btn-outline-info" title="Update"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-outline-danger" title="Delete" onclick="deleteItem({{$floor->id}},'floor','{{route('delete_floor')}}');"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$floors->links('admin.layouts.pagination')}}
        </div>
    </div>
@endsection
