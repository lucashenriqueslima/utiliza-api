<div>
</div>
<script>
    var w = (window.parent)?window.parent:window
    w.location.assign("utilizaApp://growth-utiliza.com")
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}


{{-- @script
<script>

$wire.on('redirect-to-utiliza-app', (data) => {


    window.location.href = "utilizaApp://growth-utiliza.com";

    setTimeout(function () {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Você não possui o aplicativo instalado em seu dispositivo.',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        })
    }, 2500);

})

$wire.on('show-error-alert', (data) => {

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: data.message,
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        })

})


</script>
@endscript --}}
