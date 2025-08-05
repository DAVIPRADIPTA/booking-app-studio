<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
       <p class="text-sm text-gray-600">Tampilan tema: <strong>Light (default)</strong></p>
    </x-settings.layout>
</section>
