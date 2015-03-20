@extends('pages.layout')

@section('content')
			<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Quiz</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Upload the markdown File for Quiz
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @if (Session::get('error') != null && Session::get('error')->has('message'))
                                    <div class="col-md-12">
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        {{ Session::get('error')->first('message') }}
                                        </div>
                                    </div>
                                    @endif
                                    <form role="form" method="post" enctype="multipart/form-data" >
                                        <div class="form-group">
                                            <label>File input (2MB max)</label>
                                            <input type="file" name="file" required>
                                        </div>
                                        <button type="submit" class="btn btn-info">Submit Button</button>
                                        <button type="reset" class="btn btn-default">Reset Button</button>
                                    </form>
                                </div>
                               
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
@endsection