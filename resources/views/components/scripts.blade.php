<script>
    var appname = "{{ config('app.name') }}";
    window[appname] = {
        base_url: "{{ url('/') }}",
        today: "{{ getToday('d-m-Y') }}",
        start_date:"{{ getFYDate('S') }}",
        fyDate:"{{getCurrentDate()}}",
        end_date:"{{ getFYDate('E') }}",
    };

</script>
<script defer src= "https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" > </script>
{{-- <script type="text/javascript" defer src="https://cdn.datatables.net/v/dt/dt-1.12.1/b-2.2.3/cr-1.5.6/fc-4.1.0/fh-3.2.4/r-2.3.0/rr-1.2.8/sl-1.4.0/datatables.min.js"></script> --}}
<script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script defer src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-1.13.6/b-2.4.1/b-html5-2.4.1/b-print-2.4.1/fc-4.3.0/fh-3.4.0/r-2.5.0/rg-1.4.0/datatables.min.js"></script>


<script type="module">

    $(document).on('mouseenter', '.iw-sidebar', function() {
        if ($('body').hasClass('sidebar-mini')) {
            $(this).removeClass('expand');   //chnaged by neha because of a bug // replaced toggleClass with removeClass
            $('.iw-sidenav').toggleClass('expand');
            $('.iw-logo').toggleClass('expand');
        }
    });

    $(document).on('mouseleave', '.iw-sidebar', function() {
        if ($('body').hasClass('sidebar-mini')) {
            $(this).addClass('expand'); // replaced toggleClass with addClass
            $('.iw-sidenav').toggleClass('expand');
            $('.iw-logo').toggleClass('expand');
        }
    });

</script>
