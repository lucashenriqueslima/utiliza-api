<div class="wrapper w-full md:max-w-5xl mx-auto pt-20 px-4 mb-5">
    <h1 class="text-3xl font-bold text-center text-gray-800 my-7">Modelos de Fotos</h1>
    <div class="flex flex-row">
        <img
        src="{{ asset('img/photo-models-expertise.png') }}"
        alt="Modelos de Fotos para Perícia"
        class="w-1/2">

        <img
        src="{{ asset('img/photo-models-expertise-2.png') }}"
        alt="Modelos de Fotos para Perícia"
        class="w-1/2">
    </div>

    <h1 class="text-3xl font-bold text-center text-gray-800 my-7">Formulário de Fotos</h1>

    <form wire:submit="submit">
        {{ $this->form }}
    </form>
</div>

@script
<script>

$wire.on('redirect', () => {

    setTimeout(() => {
        window.location.href = 'https://www.aaprovel.com.br/'
    }, 2000); // 2 seconds delay

})

</script>
@endscript
