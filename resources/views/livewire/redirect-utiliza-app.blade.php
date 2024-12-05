<div>
    <a href="apputiliza://growth-utiliza.com">My Facebook Group</a>
</div>
<script>

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@script
<script>

$wire.on('redirect-to-utiliza-app', (data) => {


    window.location.href = "utilizaApp://growth-utiliza.com";

    Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: 'Clique no botão abaixo para acessar o aplicativo',
            footer: '<a href="apputiliza://growth-utiliza.com"> Acessar Chamado</a>'
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        })

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
@endscript
