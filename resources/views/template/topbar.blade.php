<header class="page-topbar" id="header">
    <div class="navbar navbar-fixed">
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-light">
            <div class="nav-wrapper mr-2">
                <div class="header-search-wrapper hide-on-med-and-down mt-0" style="width: 0">
                </div>

                <img style="max-width: 100%; height: 100%; margin-left: 60px" src="{{asset('logo.PNG')}}" />

                <ul class="navbar-list right mr-2">

                    {{language_switcher()}}

                    <li>
                        <a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown">
                            <span class="avatar-status avatar-online">
                                @if(!is_null(Auth::user()->user_img))
                                    <img src="{{ Storage::url("users/") . Auth::user()->user_img}}" alt="profile" style="height: 30px"/>
                                @endif
                                <i></i>
                            </span>
                            <span class="ml-2">
                                {{ Auth::user()->name }}
                            </span>
                        </a>
                    </li>
                </ul>
                <!-- profile-dropdown-->
                <ul class="dropdown-content" id="profile-dropdown">
                    <li><a class="grey-text text-darken-1" onclick="document.location.href='{{ route('logout') }}'"><i class="material-icons">keyboard_tab</i><span>Esci</span></a></li>
                </ul>
            </div>
            <nav class="display-none search-sm">
                <div class="nav-wrapper">
                    <form id="navbarForm">
                        <div class="input-field search-input-sm">
                            <input class="search-box-sm mb-0" type="search" required="" id="search" placeholder="Explore Materialize" data-search="template-list">
                            <label class="label-icon" for="search"><i class="material-icons search-sm-icon">search</i></label><i class="material-icons search-sm-close">close</i>
                            <ul class="search-list collection search-list-sm display-none"></ul>
                        </div>
                    </form>
                </div>
            </nav>
        </nav>
    </div>
</header>
<!-- END: Header-->
