<!-- BEGIN: SideNav-->
<aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-dark sidenav-active-rounded">
    <div class="brand-sidebar hide-on-med-and-down">
        <h1 class="logo-wrapper mt-2"><a class="brand-logo darken-1" href="/">Preventivi</a></h1>
        <!-- <h1 class="logo-wrapper"><a class="darken-1" href="/"><img class="hide-on-med-and-down ml-4" src="{{asset('followupper.png')}}" alt="logo" style="height: 60px"/><img class="show-on-medium-and-down hide-on-med-and-up" src="{{asset('followupper.png')}}" alt="materialize logo" style="height: 60px"/></a><!--<a class="navbar-toggler" href="#"><i class="material-icons">radio_button_checked</i></a> </h1> -->
    </div>
    <div class="divider"></div>
    <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="accordion">
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id == 1)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('roles.index') }}"><i class="material-icons">panorama_fish_eye</i><span class="menu-title"> @lang('Roles') </span></a>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 3)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('users.index') }}"><i class="material-icons">person</i><span class="menu-title"> @lang('Users') </span></a>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 3)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('companies.index') }}"><i class="material-icons">contacts</i><span class="menu-title"> @lang('Companies') </span></a>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 3)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('quotations.index') }}"><i class="material-icons">dehaze</i><span class="menu-title"> @lang('Quotations') </span></a>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Request::is('*quotations*'))
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
              <li class="bold">
                  <a class="waves-effect waves-cyan " href="{{ route('quotations_status.index') }}"><i class="material-icons" style="font-size: 0.7rem">radio_button_unchecked</i><span class="menu-title"> @lang('Quotation Status') </span></a>
              </li>
            @endif
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('quotations_typology.index') }}"><i class="material-icons" style="font-size: 0.7rem">radio_button_unchecked</i><span class="menu-title"> @lang('Quotation Typology') </span></a>
            </li>
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('quotations_methodology.index') }}"><i class="material-icons" style="font-size: 0.7rem">radio_button_unchecked</i><span class="menu-title"> @lang('Quotation Methodology') </span></a>
            </li>
        @endif
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('reports') }}"><i class="material-icons">mail</i><span class="menu-title"> Report </span></a>
            </li>
        @endif
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('todos.superadmin-all') }}"><i class="material-icons">assignment</i><span class="menu-title"> @lang("All Todo") </span></a>
            </li>
        @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
            <li class="bold">
                <a class="waves-effect waves-cyan " href="{{ route('activities.index') }}"><i class="material-icons">mail</i><span class="menu-title"> @lang("Activities") </span></a>
            </li>
        @endif
    </ul>

    <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</aside>
<!-- END: SideNav-->
