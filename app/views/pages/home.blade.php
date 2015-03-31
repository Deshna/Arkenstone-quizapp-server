@extends('pages.layout')

@section('content')
			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Home</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            List of Quizzes
                        </div>
                        <!-- .panel-heading -->
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                            	@foreach($quizzes as $quiz)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <div><a data-toggle="collapse" data-parent="#accordion" href="#{{$quiz->course_code}}-{{$quiz->id}}" aria-expanded="false" class="collapsed">{{$quiz->course_code}}:{{$quiz->id}}</a></div>
                                        </h4>
                                    </div>
                                    <div id="{{$quiz->course_code}}-{{$quiz->id}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                        	<div class="col-md-12">
                                            	{{str_replace('\n', '<br>', $quiz->description)}}
                                            </div>
                                            <div class="col-md-12">
                                            	<br>
                                            	<a class="btn btn-success" href="{{URL::to('/quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}">See Quiz</a>
                                                <a class="btn btn-danger" href="{{URL::to('/delete-quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}" onclick="return confirm('Are you sure want to delete this Quiz?');">Delete this Quiz</a>
                                                <a class="btn btn-info" href="{{URL::to('/summary')}}/{{$quiz->course_code}}:{{$quiz->id}}">See submissions</a>
                                                <a class="btn btn-warning" href="{{URL::to('/quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}/download">Download Quiz</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               	@endforeach
                            </div>
                        </div>
                        <!-- .panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
@endsection