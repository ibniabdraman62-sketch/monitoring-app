@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigation pagination" class="monitorpro-pagination">
        <div class="pagination-info">
            Affichage de <strong>{{ $paginator->firstItem() }}</strong>
            à <strong>{{ $paginator->lastItem() }}</strong>
            sur <strong>{{ $paginator->total() }}</strong> résultats
        </div>

        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        Suivant <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .monitorpro-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding: 4px 0;
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted, #6c757d);
        }

        .pagination-info strong {
            color: var(--text, #1f2937);
            font-weight: 700;
        }

        .pagination-list {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pagination-list .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border: 1px solid var(--border, #e5e7eb);
            background: var(--bg-card, #ffffff);
            color: var(--text, #374151);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease;
            gap: 6px;
        }

        .pagination-list .page-item .page-link:hover:not(.disabled) {
            background: var(--primary, #1E3A5F);
            color: #ffffff;
            border-color: var(--primary, #1E3A5F);
        }

        .pagination-list .page-item.active .page-link {
            background: var(--primary, #1E3A5F);
            color: #ffffff;
            border-color: var(--primary, #1E3A5F);
            box-shadow: 0 2px 4px rgba(30, 58, 95, 0.2);
        }

        .pagination-list .page-item.disabled .page-link {
            color: var(--text-muted, #9ca3af);
            background: var(--bg-soft, #f9fafb);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-list .page-item .page-link i {
            font-size: 11px;
        }
    </style>
@endif