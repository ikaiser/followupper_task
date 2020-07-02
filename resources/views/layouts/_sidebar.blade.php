    <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
            {{-- <li class="nav-item">
                <a class="nav-link" href="index.html">
                <i class="mdi mdi-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="mdi mdi-circle-outline menu-icon"></i>
                <span class="menu-title">UI Elements</span>
                <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
                    <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
                </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/forms/basic_elements.html">
                <i class="mdi mdi-view-headline menu-icon"></i>
                <span class="menu-title">Form elements</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/charts/chartjs.html">
                <i class="mdi mdi-chart-pie menu-icon"></i>
                <span class="menu-title">Charts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/tables/basic-table.html">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Tables</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/icons/mdi.html">
                <i class="mdi mdi-emoticon menu-icon"></i>
                <span class="menu-title">Icons</span>
                </a>
            </li> --}}

            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id == 1)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('roles.index') }}">
                    <i class="mdi mdi-circle-outline menu-icon"></i>
                    <span class="menu-title">Ruoli</span>
                    </a>
                </li>
            @endif
            @if(\Illuminate\Support\Facades\Auth::user()->roles->first()->id <= 2)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Utenti</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route('projects.index') }}">
                <i class="mdi mdi-file-document-box-outline menu-icon"></i>
                <span class="menu-title">Progetti</span>
                </a>
            </li>
            </ul>
        @if(isset($project) && !isset($projects) && !empty($project->logo))
            <img class="img-fluid p-2" style="max-width: 100%; height: auto;" src="{{Storage::url("/project/{$project->id}/") . $project->logo}}"/>
        @endif
    </nav>
