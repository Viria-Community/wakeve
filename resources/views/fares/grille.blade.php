@extends('layouts.app')

@section('title', 'Grille tarifaire - PNMG')

@section('content')
<a href="{{ route('lines.show', $line) }}">← Retour à la ligne</a>

<h1 style="margin:8px 0 4px;font-size:26px">
    Grille tarifaire — {{ $line->label }}
</h1>

<p style="color:#64748b;margin:0 0 20px">
    Définissez le prix de chaque trajet et pour chaque classe de voyage.
    Les prix sont exprimés en FCFA.
</p>

@if(session('status'))
    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:16px">
        {{ session('status') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">
        @foreach($errors->all() as $erreur)
            <div>{{ $erreur }}</div>
        @endforeach
    </div>
@endif

@if($arrets->count() < 2)
    <div style="background:#fef3c7;color:#92400e;padding:16px;border-radius:10px">
        Cette ligne doit avoir au moins deux arrêts enregistrés avant de définir ses tarifs.
    </div>
@else

    @if($anciensTarifs->isNotEmpty())
        <div style="background:#eff6ff;color:#1e40af;padding:14px;border-radius:10px;margin-bottom:18px">
            <strong>Tarifs de référence existants</strong>
            <p style="margin:5px 0 8px">
                Ces anciens tarifs sont conservés. Ils restent utilisés lorsqu'aucun prix personnalisé
                n'est défini pour un trajet.
            </p>

            <div style="display:flex;gap:18px;flex-wrap:wrap">
                @foreach($classes as $code => $libelle)
                    @if($anciensTarifs->has($code))
                        <span>
                            {{ $libelle }} :
                            <strong>{{ number_format((float) $anciensTarifs->get($code)->amount, 0, ',', ' ') }} FCFA</strong>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('fares.grille.save', $line) }}">
        @csrf

        @for($i = 0; $i < $arrets->count() - 1; $i++)
            @for($j = $i + 1; $j < $arrets->count(); $j++)
                @php
                    $origine = $arrets[$i];
                    $destination = $arrets[$j];
                @endphp

                <section style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px;margin-bottom:16px">
                    <h2 style="font-size:17px;margin:0 0 14px">
                        {{ $origine->name }} → {{ $destination->name }}
                    </h2>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px">
                        @foreach($classes as $code => $libelle)
                            @php
                                $cle = $origine->id.'-'.$destination->id.'-'.$code;
                                $valeur = old(
                                    'montants.'.$origine->id.'.'.$destination->id.'.'.$code,
                                    $tarifs->get($cle)?->amount ?? ''
                                );
                            @endphp

                            <label style="display:block;color:#475569;font-size:13px;font-weight:600">
                                {{ $libelle }} (FCFA)

                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    name="montants[{{ $origine->id }}][{{ $destination->id }}][{{ $code }}]"
                                    value="{{ $valeur }}"
                                    placeholder="Saisir un prix"
                                    style="display:block;width:100%;box-sizing:border-box;margin-top:6px;padding:10px;border:1px solid #cbd5e1;border-radius:8px"
                                >
                            </label>
                        @endforeach
                    </div>
                </section>
            @endfor
        @endfor

        <div style="display:flex;justify-content:flex-end;margin-top:20px">
            <button class="btn-action primary" type="submit">
                Enregistrer les tarifs
            </button>
        </div>
    </form>
@endif
@endsection
