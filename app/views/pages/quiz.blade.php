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
                    <a class="btn btn-danger" href="{{URL::to('/delete-quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}" onclick="return confirm('Are you sure want to delete this Quiz?');">Delete this Quiz</a>
                    <a class="btn btn-info" href="{{URL::to('/summary')}}/{{$quiz->course_code}}:{{$quiz->id}}">See submissions</a>
                    <a class="btn btn-warning" href="{{URL::to('/quiz')}}/{{$quiz->course_code}}:{{$quiz->id}}/download">Download Quiz</a>
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
                            <p>{{str_replace('\n', '<br>', $quiz->description)}}</p>
                            <p><strong>Duration : {{$quiz->time}} seconds</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            List of Questions
                        </div>
                        <!-- .panel-heading -->
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                @foreach($questions as $question)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#q{{$question->id}}" aria-expanded="false" class="collapsed">Question No.: {{$question->question_no}} ({{$question->print_type}})</a>
                                            <span class="pull-right">Marks : {{$question->marks}}</span>
                                        </h4>
                                    </div>
                                    <div id="q{{$question->id}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body">
                                            <div class="col-md-12">
                                                {{str_replace('\n', '<br>', $question->question)}}
                                            </div>
                                            @if($question->type ==3 || $question->type ==5  ||  $question->type ==4 )
                                            <div class="col-md-12">
                                               <strong>Answer : {{$question->print_answer}}</strong>
                                            </div>
                                            @elseif( $question->type ==1  || $question->type ==2)
                                            <div class="col-md-12">
                                                @foreach($question->options as $option)
                                                <div class="col-md-6">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="{{($question->type ==1)?'radio':'checkbox'}}" disabled="disabled" id="optionsRadios1" value="option1" {{($option->ans==1)?"checked":""}}>{{$option->text}}
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
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
                <!-- /.col-lg-12 -->
            </div>
@endsection