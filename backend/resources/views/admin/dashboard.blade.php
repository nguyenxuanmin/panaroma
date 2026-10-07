@extends('admin.layouts.master-page')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3>{{$countProject}}</h3>
                            <p>Project</p>
                        </div>
                        <a href="{{route('list_project')}}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            View details
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3>{{$countBuilding}}</h3>
                            <p>Panaroma Category</p>
                        </div>
                        <a href="{{route('list_building')}}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            View details
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3>{{$countFloor}}</h3>
                            <p>Panaroma Sub-Category</p>
                        </div>
                        <a href="{{route('list_floor')}}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            View details
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3>{{$countPanaroma}}</h3>
                            <p>Panaroma</p>
                        </div>
                        <a href="{{route('list_panaroma')}}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            View details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
