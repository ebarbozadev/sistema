@php
    $urlEmbed = \App\Helpers\Utils::getYouTubeEmbedUrl($latestHome->videoPrincipal_link);
@endphp

<section id="videoPrincipal">
    <div class="container">
        <iframe width="100%" height="660" src="{{ $urlEmbed }}" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
</section>
