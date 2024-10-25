<section id="parceiros">
    <div class="container">
        @foreach ($parceiros as $parceiro)
            @if ($parceiro->status == true)
                @php
                    $pathImage = \App\Helpers\Utils::fixUrlBar(asset('storage/' . $parceiro->image));
                @endphp

                <div class="card" style="background-image: url('{{ $pathImage }}');">
                </div>
            @endif
        @endforeach
    </div>
</section>
