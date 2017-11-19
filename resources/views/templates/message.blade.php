<style>
    @media(max-width: 991px) {
        #msg-row {
            width: 100%;
        }
    }
</style>

<div class="container content">
    <div class="row" id="msg-row">

        <div class="col-md-10 col-md-offset-1">

            @if(Session::has('message'))

            <div id="message-alert" class="alert {{ Session::get('alert-class') }}" role="alert">
                <strong>{{Session::get('alert-title')}} !</strong>
                <p>{{ Session::get('message') }}</p>
            </div>

            @endif

        </div>

        <div class="col-md-1"></div>

    </div>
</div>

<script type="text/javascript">
    var count = 1;

    var counter = setInterval(timer, 1000);

    function timer()
    {
        count = count - 1;
        if (count <= 0) {
            clearInterval(counter);
            $("#message-alert").fadeOut(750);
            return true;
        }

    }
</script>