@props([
    'name'        => null,
    'id'          => null,
    'value'       => 1,
    'min'         => 0,
    'max'         => null,
    'step'        => 1,
    'xModel'      => null,       {{-- Alpine model name e.g. "quantity" --}}
    'required'    => false,
    'disabled'    => false,
    'inputClass'  => 'w-16',
    'colorScheme' => 'green',    {{-- green | blue | amber | gray --}}
    'extraAttrs'  => '',
])

@php
    $colors = [
        'green' => 'bg-green-100 text-green-700 hover:bg-green-200 border-green-200',
        'blue'  => 'bg-blue-100 text-blue-700 hover:bg-blue-200 border-blue-200',
        'amber' => 'bg-amber-100 text-amber-700 hover:bg-amber-200 border-amber-200',
        'gray'  => 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200',
    ];
    $btnClass = $colors[$colorScheme] ?? $colors['green'];
    $uid = $id ?? $name ?? ('ns_' . uniqid());
    $stepNum  = is_numeric($step) ? (float)$step : 1;
    $minNum   = is_numeric($min)  ? (float)$min  : 0;
    $maxExpr  = $max !== null ? (float)$max : 'null';
@endphp

<div
    x-data="{
        nsVal: {{ $xModel ? $xModel : (is_numeric($value) ? (float)$value : 0) }},
        nsStep: {{ $stepNum }},
        nsMin: {{ $minNum }},
        nsMax: {{ $maxExpr !== 'null' ? $maxExpr : 'null' }},
        nsDecrement() {
            let v = parseFloat(this.nsVal) || 0;
            v = Math.round((v - this.nsStep) * 1e10) / 1e10;
            this.nsVal = Math.max(this.nsMin, v);
            @if($xModel) {{ $xModel }} = this.nsVal; @endif
        },
        nsIncrement() {
            let v = parseFloat(this.nsVal) || 0;
            v = Math.round((v + this.nsStep) * 1e10) / 1e10;
            this.nsVal = this.nsMax !== null ? Math.min(this.nsMax, v) : v;
            @if($xModel) {{ $xModel }} = this.nsVal; @endif
        },
    }"
    x-init="nsVal = {{ $xModel ? $xModel : (is_numeric($value) ? (float)$value : 0) }}"
    @if($xModel)
    x-effect="{{ $xModel }} = nsVal"
    @endif
    class="inline-flex items-center rounded-xl border border-gray-200 overflow-hidden shadow-sm bg-white"
    @wheel.prevent
>
    {{-- Decrement --}}
    <button
        type="button"
        @click="nsDecrement"
        :disabled="{{ $disabled ? 'true' : 'nsVal <= nsMin' }}"
        class="px-2.5 py-1.5 text-base font-black leading-none transition-colors border-r {{ $btnClass }} disabled:opacity-30 disabled:cursor-not-allowed select-none"
        tabindex="-1"
    >−</button>

    {{-- Number input --}}
    <input
        type="number"
        id="{{ $uid }}"
        @if($name) name="{{ $name }}" @endif
        x-model.number="nsVal"
        min="{{ $min }}"
        @if($max !== null) max="{{ $max }}" @endif
        step="{{ $step }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @wheel.prevent
        {{ $attributes->except(['name','id','value','min','max','step','x-model','required','disabled','inputClass','colorScheme','extraAttrs','xModel']) }}
        class="text-center font-bold text-gray-900 border-0 focus:ring-2 focus:ring-green-400 py-1.5 px-1 bg-transparent text-sm {{ $inputClass }}"
    >

    {{-- Increment --}}
    <button
        type="button"
        @click="nsIncrement"
        :disabled="{{ $disabled ? 'true' : ($max !== null ? 'nsVal >= nsMax' : 'false') }}"
        class="px-2.5 py-1.5 text-base font-black leading-none transition-colors border-l {{ $btnClass }} disabled:opacity-30 disabled:cursor-not-allowed select-none"
        tabindex="-1"
    >+</button>
</div>
