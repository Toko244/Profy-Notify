<!DOCTYPE html>
<html lang="en">
    @include('partials.head')


<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        @include('partials.nav')
        @include('partials.sidebar')
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    @yield('header')
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>
        @include('partials.footer')
    </div>

    @include('partials.scripts')
</body>

</html>
