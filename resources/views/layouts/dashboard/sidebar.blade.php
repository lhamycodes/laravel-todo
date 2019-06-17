<div class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color:#135ab6">
    <a class="navbar-brand" href="{{ URL('dashboard') }}">
        <img alt="Logo" src="{{ asset('assets/img/logo.svg') }}" />
    </a>
    <div class="d-flex align-items-center">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="d-block d-lg-none ml-2">
            <div class="dropdown">
                <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img alt="Image" src="{{ asset('/storage/user_avatar/'.Auth::user()->avatar_img.'') }}" class="avatar" />
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                    <a class="dropdown-item" href="{{ route('profile.settings') }}">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                    Logout</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="collapse navbar-collapse flex-column" id="navbar-collapse">
        <ul class="navbar-nav d-lg-block">

            <li class="nav-item">
                <a class="nav-link" href="{{ URL('dashboard') }}">Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('projects') }}">Projects</a>
            </li>
            
        </ul>
    </div>
    <div class="d-none d-lg-block">
        <div class="dropup">
            <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img alt="Image" src="{{ asset('/storage/user_avatar/'.Auth::user()->avatar_img.'') }}" class="avatar avatar-lg" />
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                <a class="dropdown-item" href="{{ route('profile.settings') }}">Settings</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                Logout</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>