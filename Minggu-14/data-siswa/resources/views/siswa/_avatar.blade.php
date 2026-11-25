@if ($s->foto_url)
    <img src="{{ $s->foto_url }}" class="avatar" style="width: {{ $size ?? 40 }}px; height: {{ $size ?? 40 }}px" alt="">
@else
    <span class="avatar" style="width: {{ $size ?? 40 }}px; height: {{ $size ?? 40 }}px; font-size: {{ round(($size ?? 40) / 2.6) }}px">{{ $s->inisial }}</span>
@endif
