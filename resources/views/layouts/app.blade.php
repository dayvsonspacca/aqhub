<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased bg-base-300">

    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    <x-main>
        <x-slot:sidebar drawer="main-drawer" class="lg:bg-inherit">
            <div class="bg-base-100 mt-5 p-5 rounded-2xl">
                <x-app-brand class="px-5 pt-4" />
    
                <x-menu activate-by-route>
                    <x-menu-sub title="World">
                        <x-menu-item title="Locations" link="#" />
                        <x-menu-item title="Monsters" link="/monsters" />
                        <x-menu-item title="Factions" link="#" />
                        <x-menu-item title="Quests" link="#" />
                        <x-menu-item title="NPCs" link="#" />
                        <x-menu-item title="Shops" link="#" />
                    </x-menu-sub>
    
                    <x-menu-sub title="Items">
                        <x-menu-item title="Armors" link="#" />
                        <x-menu-item title="Capes" link="#" />
                        <x-menu-item title="Helmets" link="#" />
                        <x-menu-sub title="Pets">
                            <x-menu-item title="Normal" link="#" />
                            <x-menu-item title="Combat" link="#" />
                        </x-menu-sub>
                        <x-menu-item title="Misc. Items" link="#" />
                        <x-menu-item title="Grounds" link="#" />
                        <x-menu-item title="Necklaces" link="#" />
                        <x-menu-sub title="Weapons" link="#">
                            <x-menu-item title="Axes" link="#" />
                            <x-menu-item title="Bows" link="#" />
                            <x-menu-item title="Daggers" link="#" />
                            <x-menu-item title="Gauntlets" link="#" />
                            <x-menu-item title="Guns" link="#" />
                            <x-menu-item title="Maces" link="#" />
                            <x-menu-item title="Polearms" link="#" />
                            <x-menu-item title="Staffs" link="#" />
                            <x-menu-item title="Swords" link="#" />
                            <x-menu-item title="Wands" link="#" />
                            <x-menu-item title="Whips" link="#" />
                        </x-menu-sub>
                    </x-menu-sub>
                </x-menu>
            </div>
        </x-slot:sidebar>

        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-toast />
</body>
</html>
