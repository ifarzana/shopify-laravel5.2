<?php
$readonly = false;
?>

@extends('templates.default')

@section('content')

    <link rel="stylesheet" type="text/css" href="{!! asset('css/dropzone.css') !!}">

    <script type="text/javascript" src="{!! asset('js/dropzone.min.js') !!}"></script>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2>Add product</h2>
        </div>
    </div>
    <div class="wrapper wrapper-content">
        <div class="container content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="cipanel">

                                <div class="cipanel-title">
                                    <div class="cipanel-tools">
                                        <a class="btn btn-primary btn-xs panel-back tip-bottom" data-placement="bottom" data-toggle="tooltip"
                                           data-original-title="Back" href="<?php echo UrlHelper::getUrl('HomeController', 'index', array()); ?>">
                                            <span class="fa fa-arrow-left"></span>
                                        </a>
                                    </div>
                                </div>


                                <div class="cipanel-content">

                                    @if (isset($errors) && count($errors) > 0)
                                        <div class="alert alert-danger alert-list" role="alert">
                                            <p>There were one or more issues with your submission. Please correct them as indicated below.</p>

                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{!! $error !!}</li>
                                                @endforeach
                                            </ul>

                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-12">
                                            {!! Form::open(['method' => 'POST', 'url' => UrlHelper::getUrl('HomeController', 'create', array()), 'id'=>'form-guest-upload', 'class'=>'dropzone', 'files' => true ]) !!}

                                            <div class="row client-selected-div">
                                                <div class="col-xs-12 text-right">
                                                    {!! Form::submit('Save', ['id' => 'save-btn', 'class' => 'btn btn-primary']) !!}

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group <?php if($errors->has('title')){ echo "has-error";} ?>">

                                                        {!! Form::label('title', 'Title*') !!}
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-globe"></i></span>
                                                            {!! Form::text('product[title]', null,['class'=>'form-control', 'required', $readonly]) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group <?php if($errors->has('body_html')){ echo "has-error";} ?>">

                                                        {!! Form::label('body_html', 'Description') !!}
                                                        {!! Form::textarea('product[body_html]',null,['class'=>'form-control',  $readonly]) !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group <?php if($errors->has('product_type')){ echo "has-error";} ?>">

                                                        {!! Form::label('price', 'Price') !!}
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                                            {!! Form::number('price',null,['class'=>'form-control', 'step' => 0.1, $readonly]) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {!! Form::close() !!}

                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection