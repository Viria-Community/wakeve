@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Recherche - PNMG')

@push('styles')
<style>
    .search-head{margin-bottom:28px}.search-head h1{margin:6px 0 0;font-size:32px}.search-panel{background:#fff;border:1px solid #e2e8f0;border-radius:13px;box-shadow:0 1px 3px #0f172a12;overflow:hidden;margin-bottom:24px}.search-filters{display:grid;grid-template-columns:repeat(3,1fr) auto;gap:10px;padding:18px;border-bottom:1px solid #e2e8f0}.search-filters select,.search-filters input{min-width:0;padding:11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff}.section-title{padding:18px 24px;font-size:16px;font-weight:700;border-bottom:1px solid #e2e8f0}.results-list{list-style:none;margin:0;padding:0}.results-list li{padding:16px 24px;border-bottom:1px solid #e2e8f0}.results-list li:last-child{border-bottom:none}.results-list li+.results-list li{margin-top:8px}.result-item h3{margin:0 0 6px;font-size:15px}.result-item h3 a{color:var(--green);text-decoration:none;font-weight:700}.result-item p{margin:0 0 6px;color:#64748b;font-size:13px}.result-item .result-meta{display:flex;gap:8px;flex-wrap:wrap}.result-badge{padding:4px 8px;border-radius:6px;font-size:11px;font-weight:700}.result-badge.scheduled{background:#dcfce7;color:#166534}.result-badge.cancelled{background:#fee2e2;color:#b91c1c}.result-badge.active{background:#dcfce7;color:#166534}.result-badge.inactive{background:#e5e7eb;color:#374151}.empty-row{text-align:center;color:#64748b;padding:35px}.pagination{padding:18px 24px}.search-tabs{display:flex;margin-bottom:0;border-bottom:1px solid #e2e8f0}.search-tabs button{padding:12px 24px;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;font-size:14px;font-weight:700;color:#64748b}.search-tabs button.active{color:var(--green);border-bottom-color:var(--green)}
</style>
@endpush

@section('content')
<div class="search-head">
    <div>
        <div class="breadcrumb">Offre de transport › Recherche</div>
        <h1>Recherche et filtrage</h1>
        <div class="muted">Consultez les lignes actives et les départs à venir.</div>
    </div>
</div>

<div class="search-panel">
    <form class="search-filters" method="GET" action="{{ route('search.index') }}">
        <select name="company_id" aria-label="Compagnie"><option value="">Toutes les compagnies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string)request('company_id')===(string)$company->id)>{{ $company->legal_name }}</option>@endforeach</select>
        <select name="origin_place_id" aria-label="Origine"><option value="">Toutes les origines</option>@foreach($originPlaces as $place)<option value="{{ $place->id }}" @selected((string)request('origin_place_id')===(string)$place->id)>{{ $place->name }}</option>@endforeach</select>
        <select name="destination_place_id" aria-label="Destination"><option value="">Toutes les destinations</option>@foreach($destinationPlaces as $place)<option value="{{ $place->id }}" @selected((string)request('destination_place_id')===(string)$place->id)>{{ $place->name }}</option>@endforeach</select>
        <input name="date" type="date" value="{{ request('date') }}" aria-label="Date" placeholder="Date du départ">
        <button class="btn" type="submit">Rechercher</button>
    </form>
</div>

<div class="search-tabs">
    <button class="active" onclick="switchTab('lines')">Lignes ({{ $lines->total() }})</button>
    <button onclick="switchTab('departures')">Départs ({{ $departures->total() }})</button>
</div>

<div id="linesResults" class="search-panel">
    @if($lines->isEmpty())
        <div class="empty-row">Aucune ligne ne correspond aux critères.</div>
    @else
        <div class="section-title">Lignes</div>
        <ul class="results-list">
            @foreach($lines as $line)
                <li>
                    <div class="result-item">
                        <h3><a href="{{ route('lines.show',$line) }}">{{ $line->label }}</a></h3>
                        <p>{{ $line->company->legal_name }} • {{ $line->transportMode->label ?? '—' }}</p>
                        <div class="result-meta">
                            <span class="result-badge {{ $line->status }}">{{ ['active'=>'Active','inactive'=>'Inactive'][$line->status] }}</span>
                            <span>{{ $line->originPlace->name ?? '—' }} → {{ $line->destinationPlace->name ?? '—' }}</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="pagination">{{ $lines->links('pagination.pnmg') }}</div>
    @endif
</div>

<div id="departuresResults" class="search-panel" style="display:none">
    @if($departures->isEmpty())
        <div class="empty-row">Aucun départ ne correspond aux critères.</div>
    @else
        <div class="section-title">Départs</div>
        <ul class="results-list">
            @foreach($departures as $departure)
                <li>
                    <div class="result-item">
                        <h3><a href="{{ route('departures.show',$departure) }}">{{ $departure->departure_date->locale('fr')->translatedFormat('l d F Y') }} — {{ $departure->departure_time->format('H\\hi') }}</a></h3>
                        <p>{{ $departure->line->label }} • {{ $departure->line->company->legal_name }}</p>
                        <div class="result-meta">
                            <span class="result-badge {{ $departure->status }}">{{ ['scheduled'=>'Programmé','cancelled'=>'Annulé'][$departure->status] }}</span>
                            <span>{{ $departure->line->originPlace->name ?? '—' }} → {{ $departure->line->destinationPlace->name ?? '—' }}</span>
                            <span>{{ $departure->fleetUnit->type }} — {{ $departure->fleetUnit->capacity }} places</span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="pagination">{{ $departures->links('pagination.pnmg') }}</div>
    @endif
</div>

<script>
function switchTab(tab) {
    const linesResults = document.getElementById('linesResults');
    const departuresResults = document.getElementById('departuresResults');
    const buttons = document.querySelectorAll('.search-tabs button');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    if (tab === 'lines') {
        linesResults.style.display = 'block';
        departuresResults.style.display = 'none';
    } else {
        linesResults.style.display = 'none';
        departuresResults.style.display = 'block';
    }
}
</script>
@endsection
