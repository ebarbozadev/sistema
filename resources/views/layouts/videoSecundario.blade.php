@php
    if ($latestHome->secondaryVideo) {
        $videoData = json_decode($latestHome->secondaryVideo, true);

        // Verifica se existe o campo 'download_link' no JSON decodificado
        if (isset($videoData[0]['download_link'])) {
            $videoPath = $videoData[0]['download_link'];
        }
    }
@endphp

<section id="videoSecundario">
    <div class="videoWrapper">
        <video class="hide-controls" id="meuVideo">
            <source src="{{ asset('storage/' . $videoPath) }}" type="video/mp4">
            Seu navegador não suporta o elemento de vídeo.
        </video>

        <div id="playOverlay" class="play-overlay"></div>
    </div>

    <div class="videoSecundario_description">
        <h2>{{ $latestHome->secondaryVideo_title }}</h2>
        <h4>{{ $latestHome->secondaryVideo_subtitle }}</h4>
        <p>{{ $latestHome->secondaryVideo_description }}</p>
        <a href="{{ $latestHome->secondaryVideo_link }}" target="__blank">know more</a>
    </div>
</section>

<script>
    // Seleciona o vídeo e o overlay
    const video = document.getElementById('meuVideo');
    const playOverlay = document.getElementById('playOverlay');

    // Função para alternar entre play/pause ao clicar no vídeo
    video.addEventListener('click', () => {
        if (video.paused || video.ended) {
            video.play();
            playOverlay.style.display = 'none'; // Esconde o overlay ao começar a reprodução
        } else {
            video.pause();
            playOverlay.style.display = 'flex'; // Mostra o overlay ao pausar
        }
    });

    // Também permite clicar no overlay para iniciar o vídeo
    playOverlay.addEventListener('click', () => {
        video.play();
        playOverlay.style.display = 'none'; // Esconde o overlay ao começar a reprodução
    });

    // Mostra o overlay quando o vídeo está pausado
    video.addEventListener('pause', () => {
        playOverlay.style.display = 'flex'; // Mostra o overlay de play
    });

    // Esconde o overlay quando o vídeo começa a rodar
    video.addEventListener('play', () => {
        playOverlay.style.display = 'none'; // Esconde o overlay de play
    });
</script>
