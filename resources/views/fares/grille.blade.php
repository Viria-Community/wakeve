@extends('layouts.app')
@section('title', 'Tarifs de la ligne - PNMG')

@section('content')
<a href="{{ route('lines.show', $line) }}">← Retour à la ligne</a>
<h1 style="margin:8px 0 4px;font-size:26px">Tarifs de la ligne {{ $line->label }}</h1>
<p style="color:#64748b;margin:0 0 20px">Un prix par classe. Les prix proposés viennent de la grille de la plateforme, vous pouvez les corriger.</p>

@if($errors->any())
<div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">
    @foreach($errors->all() as $erreur)<div>{{ $erreur }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('fares.grille.save', $line) }}" style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px">
    @csrf
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="text-align:left;color:#64748b;font-size:13px">
                <th style="padding:8px">Classe</th>
                <th style="padding:8px">Prix (FCFA)</th>
                <th style="padding:8px">Trajet partiel (50 %)</th>
            </tr>
        </thead>
        <tbody>
        @foreach($classes as $code => $libelle)
            @php($valeur = old('montants.'.$code, $tarifs->get($code)?->amount ?? ($grille[$code] ?? '')))
            <tr style="border-top:1px solid #f1f5f9">
                <td style="padding:12px 8px"><strong>{{ $libelle }}</strong></td>
                <td style="padding:12px 8px">
                    <input type="number" min="0" step="1" name="montants[{{ $code }}]" value="{{ $valeur }}" style="width:180px;padding:8px;border:1px solid #cbd5e1;border-radius:8px">
                </td>
                <td style="padding:12px 8px;color:#64748b">
                    {{ $valeur !== '' ? number_format((float) $valeur * config('offre.pourcentage_trajet_partiel') / 100, 0, ',', ' ').' FCFA' : '—' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:18px;text-align:right">
        <button class="btn-action primary" type="submit">Enregistrer les tarifs</button>
    </div>
</form>
@endsection
