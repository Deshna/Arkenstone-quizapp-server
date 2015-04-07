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
                        <a href="{{URL::to('/summary')}}/{{$quiz->course_code}}:{{$quiz->id}}/submission" class="btn btn-warning">Download Submission in CSV</a>
                        <a href="{{URL::to('/summary')}}/{{$quiz->course_code}}:{{$quiz->id}}/logs" class="btn btn-info">Download Logs in CSV</a>
                    </div>
                    <hr>
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
                                            <span class="pull-right">
                                                @if($student->submitted > 1)
                                                <i class="alert-danger">Multiple resubmissions</i>
                                                @elseif($student->submitted == 1)
                                                <i class="alert-success">Marks : {{$student->results[0]->marks}}</i>
                                                @else
                                                <i>No Submission</i>
                                                @endif
                                            </span>
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
                                            @else
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="well">
                                                            <b>#Submissions : {{$student->submitted}}</b><br>
                                                            <b>#Question Recieved : {{$student->question_get}}</b>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel panel-yellow">
                                                    <div class="panel-heading">
                                                        List of Submission
                                                    </div>
                                                    <div class="panel-body">
                                                        
                                                        @foreach($student->results as $key => $result)
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title">
                                                                    <div class="panel-heading">
                                                                        <span>Submission #{{$key+1}} marks <b>{{$result->marks}}</b></span>
                                                                        <span class="pull-right">{{$result->updated_at}}</span>
                                                                    </div>
                                                                </h4>
                                                            </div>
                                                            <div class="panel-body">
                                                                <table class="table table-striped table-bordered table-hover marks-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>QNo</th>
                                                                            <th>Result</th>
                                                                            <th>Correct Answer</th>
                                                                            <th>Given Answer</th>
                                                                            <th>Marks Obtained</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($result->responses as $row)
                                                                        <tr>
                                                                            <td>{{$row->qno}}</td>
                                                                            <td>{{$row->result}}</td>
                                                                            <td>{{join(" , ",$row->correct_answer)}}</td>
                                                                            <td>{{join(" , ",$row->given_answer)}}</td>
                                                                            <td>{{$row->marks_obtained}}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @if(sizeof($student->logs) > 0)
                                                <div class="panel panel-red">
                                                    <div class="panel-heading">
                                                        Logs
                                                    </div>
                                                    <div class="panel-body">
                                                    <ul>
                                                    @foreach($student->logs as $log)
                                                        <li>{{$log->message}}</li>
                                                    @endforeach
                                                    </ul>
                                                    </div>
                                                </div>
                                                @endif
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