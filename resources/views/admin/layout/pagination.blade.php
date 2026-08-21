@if ($paginator->hasPages())
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item @if ($paginator->onFirstPage()) disabled @endif">
                <a class="page-link" href="{{$elements[0][1]}}" tabindex="-1" aria-disabled="true">Previous</a>
            </li>
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item  @if ($page == $paginator->currentPage()) active @endif"><a class="page-link" href="{{$url}}">{{$page}}</a></li>
                    @endforeach
                @endif
            @endforeach
            <li class="page-item  @if (!$paginator->hasMorePages()) disabled @endif">
                <a class="page-link" href="{{$elements[0][count($elements[0])]}}">Next</a>
            </li>
        </ul>
    </nav>
@endif