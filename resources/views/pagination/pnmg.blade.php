@if ($paginator->hasPages())
<nav class="pnmg-pagination" role="navigation" aria-label="Pagination">
    <p>Résultats {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur {{ $paginator->total() }}</p>
    <ul>
        <li @class(['disabled' => $paginator->onFirstPage()])>
            @if ($paginator->onFirstPage())
                <x-lucide-chevron-left aria-hidden="true"/>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente"><x-lucide-chevron-left aria-hidden="true"/></a>
            @endif
        </li>

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li @class(['active' => $page == $paginator->currentPage()])>
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Aller à la page {{ $page }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach

        <li @class(['disabled' => ! $paginator->hasMorePages()])>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante"><x-lucide-chevron-right aria-hidden="true"/></a>
            @else
                <x-lucide-chevron-right aria-hidden="true"/>
            @endif
        </li>
    </ul>
</nav>
@endif
