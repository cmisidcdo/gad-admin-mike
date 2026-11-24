<?php

namespace App\Livewire\Concerns;

/**
 * Amount inputs are displayed with thousands separators ("3,000,000.00").
 * Strip the commas as soon as the value reaches the server so validation / saving get plain numbers.
 * Components list their amount properties in $moneyFields (e.g. 'officeBudget', 'entry.moe').
 */
trait CleansMoney
{
    public function updated($name, $value)
    {
        $fields = property_exists($this, 'moneyFields') ? $this->moneyFields : [];

        if (!in_array($name, $fields, true) || !is_string($value)) {
            return;
        }

        $clean = str_replace(',', '', trim($value));

        if (str_contains($name, '.')) {
            [$prop, $key] = explode('.', $name, 2);
            $arr = $this->{$prop};
            $arr[$key] = $clean;
            $this->{$prop} = $arr;
        } else {
            $this->{$name} = $clean;
        }
    }
}
