@if (! $employee->user)
    <span class="employee-status inactive">À régulariser</span>
@elseif ($employee->user->status === 'active')
    <span class="employee-status active">Actif</span>
@else
    <span class="employee-status inactive">Désactivé</span>
@endif
