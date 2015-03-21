@extends('pages.layout')
@section('content')
			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Quiz Summary</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
            	@if(sizeof($results)==0)
            	<div class="col-md-12">
            		<blockquote>No Submissions Till Now</blockquote>
            	</div>
            	@else
					<div class="col-lg-12">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            List of Students
                        </div>
                        <!-- .panel-heading -->
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                @foreach($results as $student)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#r{{$student->id}}" aria-expanded="false" class="collapsed">{{$student->student_roll}} : {{$student->student_name}}</a>
                                        </h4>
                                    </div>
                                    <div id="r{{$student->id}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            @if($student->symbol_verify==0)
                                            <div class="col-md-12">
                                            	<div class="alert alert-warning">
					                                The Authentication Process is not Completed yet
					                            </div>
                                            </div>
                                            @elseif($student->question_get==0)
                                            <div class="col-md-12">
                                            	<div class="alert alert-info">
					                                The user has not recieved questions yet
					                            </div>
                                            </div>
                                            @elseif($student->submitted==0)
                                            <div class="col-md-12">
                                            	<div class="alert alert-success">
					                                The user has not submitted any response
					                            </div>
                                            </div>
                                            @endif
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
            	@endif
            </div>
@endsection