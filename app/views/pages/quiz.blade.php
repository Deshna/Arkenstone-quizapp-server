@extends('pages.layout')

@section('content')
			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Quiz Code : "{{$quiz->course_code}}:{{$quiz->id}}"</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<div class="row">
                <div class="col-lg-12">
                    <h2>Passcode:</h2>
                    <div class="col-md-12">
                        {{Passcode::printcode(json_decode($quiz->key))}}
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                                                <a class="btn btn-danger" href="{{URL::to('/delete-quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}">Delete this Quiz</a>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Quiz Description
                        </div>
                        <div class="panel-body">
                            <p>{{$quiz->description}}</p>
                        </div>
                    </div>
                </div>
            </div>
@endsection