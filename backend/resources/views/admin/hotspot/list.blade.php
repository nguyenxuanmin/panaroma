@extends('admin.layouts.master-page')

@section('title')
    Hotspot
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Hotspot</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                      <li class="breadcrumb-item"><a href="{{route('admin')}}">Dashboard</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Hotspot</li>
                    </ol>
                  </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="{{route('add_hotspot')}}" title="Create New">Create New</a>
            </div>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" width="80px" class="text-center">No</th>
                        <th scope="col">Title</th>
                        <th scope="col" width="300px">Panaroma</th>
                        <th scope="col" width="300px">Target Panaroma</th>
                        <th scope="col" width="150px">Create Date</th>
                        <th scope="col" width="200px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($hotspots) == 0)
                        <tr>
                            <td valign="middle" class="text-center" colspan="6">No data available</td>
                        </tr>
                    @endif
                    @foreach ($hotspots as $key => $hotspot)
                        <tr>
                            <td valign="middle" class="text-center">{{$key+1}}</td>
                            <td valign="middle">{{$hotspot->title}}</td>
                            <td valign="middle">{{$hotspot->panaroma->name}}</td>
                            <td valign="middle">{{$hotspot->targetPanaroma->name}}</td>
                            <td valign="middle">{{$hotspot->created_at->format('d/m/Y')}}</td>
                            <td valign="middle" class="text-center">
                                <a href="{{route('edit_hotspot',[$hotspot->id])}}" class="btn btn-outline-info" title="Update"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button class="btn btn-outline-danger" title="Delete" onclick="deleteItem({{$hotspot->id}},'hotspot','{{route('delete_hotspot')}}');"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{$hotspots->links('admin.layouts.pagination')}}
        </div>
    </div>
@endsection
