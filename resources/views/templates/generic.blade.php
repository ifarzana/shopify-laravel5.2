<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title></title>

    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('font-awesome/css/font-awesome.css') }}">

    <!-- Toastr style -->
    <link rel="stylesheet" href="{{ URL::asset('css/plugins/toastr/toastr.min.css') }}">

    <!-- Gritter -->
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/gritter/jquery.gritter.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">

    @include('templates.style')

</head>

<body>

<div id="wrap">
    <div id="main">
        @yield('content')
    </div>
</div>


<!-- Main scripts -->
<script type="text/javascript" src="{!! asset('js/jquery-2.1.1.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/bootstrap.min.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/plugins/metisMenu/jquery.metisMenu.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/plugins/slimscroll/jquery.slimscroll.min.js') !!}"></script>

<!-- Custom and plugin javascript -->
<script type="text/javascript" src="{!! asset('js/custom_script.js') !!}"></script>

<!--
jQuery UI - If e call this it affects the tooltip-->
<script type="text/javascript" src="{!! asset('js/plugins/jquery-ui/jquery-ui.min.js') !!}"></script>


<!-- GRITTER -->
<script type="text/javascript" src="{!! asset('js/plugins/gritter/jquery.gritter.min.js') !!}"></script>

<!-- Toastr -->
<script type="text/javascript" src="{!! asset('js/plugins/toastr/toastr.min.js') !!}"></script>


</body>
</html>