<header class="navbar navbar-default">
    <!-- Right Header Navigation -->
    <ul class="nav navbar-nav-custom pull-right">
        <!-- User Dropdown -->
        <li class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                <img src="/img/avatar2.jpg" alt="avatar">
                &nbsp{{Auth::user()->name}}&nbsp&nbsp
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                <li class="dropdown-header text-center">Account</li>
                <li>
                    <a href="#modal-user-settings" data-toggle="modal">
                        <i class="fa fa-cog fa-fw pull-right"></i>
                        Settings
                    </a>

                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                        <i class="fa fa-ban fa-fw pull-right"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
        <!-- END User Dropdown -->
    </ul>
    <!-- END Right Header Navigation -->
</header>
