@php
    $selectedProvince = old('province', $location['province'] ?? null);
    $selectedCity = old('city', $location['city'] ?? '');
    $provinceCities = config('geography.'.$selectedProvince.'.cities', []);
    $customCity = $selectedCity !== '' && !in_array($selectedCity, $provinceCities, true);
@endphp
<div class="{{ $fieldClass ?? '' }}"><label for="province">Province</label>
<select id="province" name="province"><option value="">Sélectionner une province</option>
@foreach(config('geography') as $code => $province)<option value="{{ $code }}" @selected($selectedProvince === $code)>{{ $province['name'] }}</option>@endforeach
</select>@error('province')<span role="alert">{{ $message }}</span>@enderror</div>
<div class="{{ $fieldClass ?? '' }}"><label for="city">Ville / localité</label>
<select id="city" name="city" data-selected-city="{{ $selectedCity }}" @disabled(!$selectedProvince)>
<option value="">{{ $selectedProvince ? 'Sélectionner une ville ou localité' : 'Choisissez d’abord une province' }}</option>
@foreach($provinceCities as $city)<option value="{{ $city }}" @selected(!$customCity && $selectedCity === $city)>{{ $city }}</option>@endforeach
<option value="__other__" @selected($customCity)>Ajouter une autre localité</option>
</select>
<div id="city-custom-wrapper" @if(!$customCity) hidden @endif>
<label for="city_custom" class="locality-custom-label">Nouvelle localité</label>
<input id="city_custom" name="city_custom" value="{{ $customCity ? $selectedCity : old('city_custom', '') }}" maxlength="100" autocomplete="address-level2" placeholder="Saisir le nom de la localité" @required($customCity) @disabled(!$customCity)>
</div>
@error('city')<span role="alert">{{ $message }}</span>@enderror</div>
<div class="{{ $fieldClass ?? '' }}"><label for="address">Adresse précise</label>
<input id="address" name="address" value="{{ old('address', $location['address'] ?? '') }}" maxlength="255" autocomplete="street-address" placeholder="Quartier, rue, bâtiment ou repère">
@error('address')<span role="alert">{{ $message }}</span>@enderror</div>
<script>(() => {
const provinces = {{ Illuminate\Support\Js::from(config('geography')) }};
const province = document.getElementById('province');
const city = document.getElementById('city');
const customWrapper = document.getElementById('city-custom-wrapper');
const customInput = document.getElementById('city_custom');
if (!province || !city || !customWrapper || !customInput) return;

const toggleCustom = () => {
    const enabled = !!province.value && city.value === '__other__';
    customWrapper.hidden = !enabled;
    customInput.disabled = !enabled;
    customInput.required = enabled;
    if (enabled) customInput.focus();
};

const populateCities = (selected = '') => {
    const cities = provinces[province.value]?.cities || [];
    city.replaceChildren();
    const prompt = document.createElement('option');
    prompt.value = '';
    prompt.textContent = province.value ? 'Sélectionner une ville ou localité' : 'Choisissez d’abord une province';
    city.append(prompt);
    for (const name of cities) {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;
        city.append(option);
    }
    const other = document.createElement('option');
    other.value = '__other__';
    other.textContent = 'Ajouter une autre localité';
    city.append(other);
    city.disabled = !province.value;
    city.value = cities.includes(selected) ? selected : (selected ? '__other__' : '');
    if (selected && !cities.includes(selected)) customInput.value = selected;
    toggleCustom();
};

province.addEventListener('change', () => {
    customInput.value = '';
    populateCities();
});
city.addEventListener('change', toggleCustom);
toggleCustom();
})();</script>
<style>.locality-custom-label{margin-top:10px}</style>
