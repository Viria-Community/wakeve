@extends(auth()->user()->isPlatformMember() ? 'layouts.admin' : 'layouts.app')
@section('title', 'Recherche - PNMG')

@push('styles')
<style>
.rch { color: #0b2f28; }
.rch-entete { margin-bottom: 22px; }
.rch-fil { font-size: 13px; color: #64748b; }
.rch-titre { font-size: 32px; font-weight: 800; color: #264653; margin: 6px 0 4px; letter-spacing: -.01em; }
.rch-titre::after { content: ""; display: block; width: 60px; height: 4px; border-radius: 4px; margin-top: 10px; background: linear-gradient(90deg, #087A5A, #E9C46A); }
.rch-sous { color: #64748b; font-size: 14px; margin: 0; }

.rch-filtres { background: linear-gradient(135deg, #ffffff, #f7f5ef); border: 1px solid #e6e2d6; border-radius: 22px; padding: 22px; box-shadow: 0 16px 38px rgba(38,70,83,.10); margin-bottom: 22px; }
.rch-grille { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)) auto; gap: 18px; align-items: end; }
.rch-action { display: flex; align-items: flex-end; }
.rch-champ label { display: block; font-size: 13px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 9px; }
.rch-champ select, .rch-champ input { width: 100%; box-sizing: border-box; height: 56px; padding: 0 16px; border-radius: 14px; border: 1.5px solid #cfcab9; background: #fff; color: #1e293b; font-size: 16px; line-height: 1.2; transition: border-color .2s ease, box-shadow .2s ease; }
.rch-champ select:focus, .rch-champ input:focus { outline: none; border-color: #087A5A; box-shadow: 0 0 0 4px rgba(8,122,90,.16); }
.rch-bouton { height: 56px; min-width: 170px; padding: 0 26px; border: 0; border-radius: 14px; background: linear-gradient(135deg, #087A5A, #0a6b4f); color: #fff; font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 12px 24px rgba(8,122,90,.28); transition: transform .2s ease, box-shadow .25s ease; }
.rch-bouton:hover { transform: translateY(-2px); box-shadow: 0 16px 30px rgba(8,122,90,.36); }
.rch-bouton:active { transform: scale(.98); }

.rch-onglets { display: inline-flex; gap: 8px; padding: 6px; background: #efece2; border-radius: 16px; margin-bottom: 18px; }
.rch-onglets button { border: 0; background: transparent; padding: 10px 18px; border-radius: 12px; font-weight: 700; font-size: 14px; color: #475569; cursor: pointer; transition: background .2s ease, color .2s ease, box-shadow .2s ease; }
.rch-onglets button:hover { color: #264653; }
.rch-onglets button.actif { background: #fff; color: #087A5A; box-shadow: 0 6px 16px rgba(38,70,83,.12); }
.rch-onglets button .nombre { display: inline-block; margin-left: 6px; min-width: 22px; padding: 1px 7px; border-radius: 999px; background: #e6f5ef; color: #066b4d; font-size: 12px; }

.rch-liste { display: grid; gap: 16px; }
.rch-carte { position: relative; background: #fff; border: 1px solid #e6e2d6; border-radius: 18px; padding: 20px 22px; box-shadow: 0 8px 22px rgba(38,70,83,.07); display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center; transition: transform .22s ease, box-shadow .22s ease; overflow: hidden; }
.rch-carte::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(180deg, #087A5A, #E9C46A); }
.rch-carte:hover { transform: translateY(-3px); box-shadow: 0 18px 38px rgba(38,70,83,.14); }
.rch-carte h3 { margin: 0 0 2px; font-size: 17px; }
.rch-carte h3 a { color: #264653; text-decoration: none; }
.rch-carte h3 a:hover { color: #087A5A; }
.rch-carte .rch-sous { font-size: 13px; }
.rch-trajet { display: flex; align-items: center; gap: 10px; margin-top: 12px; font-weight: 600; color: #264653; flex-wrap: wrap; }
.rch-arret { width: 12px; height: 12px; border-radius: 50%; border: 3px solid #087A5A; background: #fff; }
.rch-arret.or { border-color: #E9C46A; background: #E9C46A; }
.rch-trait { flex: 1; min-width: 40px; height: 3px; border-radius: 3px; background: linear-gradient(90deg, #087A5A, #E9C46A); opacity: .6; }
.rch-date { text-align: center; min-width: 96px; padding: 12px 14px; border-radius: 16px; background: linear-gradient(160deg, #087A5A, #0b3b2f); color: #fff; }
.rch-date strong { display: block; font-size: 30px; line-height: 1; }
.rch-date span { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; opacity: .9; }
.rch-date small { display: block; margin-top: 6px; font-size: 13px; font-weight: 700; color: #E9C46A; }
.rch-badge { display: inline-flex; padding: 4px 11px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.rch-badge.active, .rch-badge.scheduled { background: #e6f5ef; color: #066b4d; }
.rch-badge.inactive, .rch-badge.cancelled { background: #eef0f3; color: #475569; }
.rch-meta { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 10px; font-size: 13px; color: #475569; }

.rch-vide { text-align: center; padding: 46px 20px; color: #64748b; background: #fff; border: 1px dashed #d6d3c7; border-radius: 18px; }
.rch-vide svg { width: 44px; height: 44px; color: #087A5A; margin-bottom: 10px; }
.rch-pagination { margin-top: 18px; }

@media (max-width: 1100px) { .rch-grille { grid-template-columns: repeat(2, minmax(0, 1fr)); } .rch-action .rch-bouton { width: 100%; } }
@media (max-width: 640px) { .rch-grille { grid-template-columns: 1fr; } .rch-carte { grid-template-columns: 1fr; } .rch-date { justify-self: start; } }
@media (prefers-reduced-motion: reduce) { .rch *, .rch *::before { transition: none !important; animation: none !important; } }
</style>
@endpush

@section('content')
<div class="rch">
    <div class="rch-entete">
        <div class="rch-fil">Offre de transport › Recherche</div>
        <h1 class="rch-titre">Recherche et filtrage</h1>
        <p class="rch-sous">Consultez les lignes actives et les départs à venir.</p>
    </div>

    <form class="rch-filtres" method="GET" action="{{ route('search.index') }}">
        <div class="rch-grille">
            <div class="rch-champ">
                <label for="rch-compagnie">Compagnie</label>
                <select id="rch-compagnie" name="company_id"><option value="">Toutes les compagnies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->legal_name }}</option>@endforeach</select>
            </div>
            <div class="rch-champ">
                <label for="rch-origine">Origine</label>
                <select id="rch-origine" name="origin_place_id"><option value="">Toutes les origines</option>@foreach($originPlaces as $place)<option value="{{ $place->id }}" @selected((string) request('origin_place_id') === (string) $place->id)>{{ $place->name }}</option>@endforeach</select>
            </div>
            <div class="rch-champ">
                <label for="rch-destination">Destination</label>
                <select id="rch-destination" name="destination_place_id"><option value="">Toutes les destinations</option>@foreach($destinationPlaces as $place)<option value="{{ $place->id }}" @selected((string) request('destination_place_id') === (string) $place->id)>{{ $place->name }}</option>@endforeach</select>
            </div>
            <div class="rch-champ">
                <label for="rch-date">Date du départ</label>
                <input id="rch-date" name="date" type="date" value="{{ request('date') }}">
            </div>
        </div>
        <div class="rch-action">
            <button class="rch-bouton" type="submit">Rechercher</button>
        </div>
    </form>

    <div class="rch-onglets" role="tablist">
        <button type="button" class="actif" id="onglet-lignes" role="tab" aria-selected="true" onclick="switchTab('lines')">Lignes <span class="nombre">{{ $lines->total() }}</span></button>
        <button type="button" id="onglet-departs" role="tab" aria-selected="false" onclick="switchTab('departures')">Départs <span class="nombre">{{ $departures->total() }}</span></button>
    </div>

    <section id="linesResults" role="tabpanel">
        @if($lines->isEmpty())
            <div class="rch-vide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>
                <div>Aucune ligne ne correspond aux critères.</div>
            </div>
        @else
            <div class="rch-liste">
                @foreach($lines as $line)
                    <article class="rch-carte">
                        <div>
                            <h3><a href="{{ route('lines.show', $line) }}">{{ $line->label }}</a></h3>
                            <div class="rch-sous">{{ $line->company->legal_name }} · {{ $line->transportMode->label ?? '—' }}</div>
                            <div class="rch-trajet">
                                <span>{{ $line->originPlace->name ?? '—' }}</span>
                                <span class="rch-arret or" aria-hidden="true"></span>
                                <span class="rch-trait" aria-hidden="true"></span>
                                <span class="rch-arret" aria-hidden="true"></span>
                                <span>{{ $line->destinationPlace->name ?? '—' }}</span>
                            </div>
                            <div class="rch-meta">
                                <span class="rch-badge {{ $line->status }}">{{ ['active' => 'Active', 'inactive' => 'Inactive'][$line->status] }}</span>
                            </div>
                        </div>
                        <a class="rch-bouton" style="width:auto;height:auto;padding:12px 18px;display:inline-block;text-decoration:none" href="{{ route('lines.show', $line) }}">Voir la ligne</a>
                    </article>
                @endforeach
            </div>
            <div class="rch-pagination">{{ $lines->links('pagination.pnmg') }}</div>
        @endif
    </section>

    <section id="departuresResults" role="tabpanel" style="display:none">
        @if($departures->isEmpty())
            <div class="rch-vide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                <div>Aucun départ ne correspond aux critères.</div>
            </div>
        @else
            <div class="rch-liste">
                @foreach($departures as $departure)
                    <article class="rch-carte">
                        <div>
                            <h3><a href="{{ route('departures.show', $departure) }}">{{ $departure->line->label }}</a></h3>
                            <div class="rch-sous">{{ $departure->line->company->legal_name }}</div>
                            <div class="rch-trajet">
                                <span>{{ $departure->line->originPlace->name ?? '—' }}</span>
                                <span class="rch-arret or" aria-hidden="true"></span>
                                <span class="rch-trait" aria-hidden="true"></span>
                                <span class="rch-arret" aria-hidden="true"></span>
                                <span>{{ $departure->line->destinationPlace->name ?? '—' }}</span>
                            </div>
                            <div class="rch-meta">
                                <span class="rch-badge {{ $departure->status }}">{{ ['scheduled' => 'Programmé', 'cancelled' => 'Annulé'][$departure->status] }}</span>
                                <span>{{ $departure->fleetUnit->type ?? '—' }} · {{ $departure->fleetUnit->capacity ?? '—' }} places</span>
                            </div>
                        </div>
                        <div class="rch-date">
                            <span>{{ $departure->departure_date->locale('fr')->translatedFormat('M') }}</span>
                            <strong>{{ $departure->departure_date->format('d') }}</strong>
                            <small>{{ $departure->departure_time->format('H:i') }}</small>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="rch-pagination">{{ $departures->links('pagination.pnmg') }}</div>
        @endif
    </section>
</div>

<script>
function switchTab(nom) {
    var lignes = nom === 'lines';
    document.getElementById('linesResults').style.display = lignes ? '' : 'none';
    document.getElementById('departuresResults').style.display = lignes ? 'none' : '';
    document.getElementById('onglet-lignes').classList.toggle('actif', lignes);
    document.getElementById('onglet-departs').classList.toggle('actif', !lignes);
    document.getElementById('onglet-lignes').setAttribute('aria-selected', lignes ? 'true' : 'false');
    document.getElementById('onglet-departs').setAttribute('aria-selected', lignes ? 'false' : 'true');
}
</script>
@endsection
