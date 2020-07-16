<!-- Main Sidebar -->
<div id="sidebar">
    <!-- Wrapper for scrolling functionality -->
    <div id="sidebar-scroll">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <div>
                <a href="/dashboard">
                    <img class="logo_admin" src="/img/logo.png" alt="avatar">
                </a>
            </div>
            <!-- Sidebar Navigation -->
            <ul class="sidebar-nav">
                @admin
                <li>
                    <a href="/admin/dashboard"><i class="fa fa-bar-chart-o"></i> Dashboard</a>
                </li>
                <li>
                    <a href="/admin/users"><i class="fa fa-users"></i> Users</a>
                </li>
                <li>
                    <a href="/admin/webhooks"><i class="fa fa-desktop"></i> Webhooks</a>
                </li>
                <li>
                    <a href="/admin/history"><i class="fa fa-history"></i> Payload histories</a>
                </li>
                @else
                <li>
                    <a href="/dashboard"><i class="fa fa-bar-chart-o"></i> Dashboard</a>
                </li>
                <li>
                    <a href="/webhooks"><i class="fa fa-desktop"></i> Webhooks</a>
                </li>
                <li>
                    <a href="/bots"><i class="fa fa-reddit"></i> Bots</a>
                </li>
                <li>
                    <a href="/history"><i class="fa fa-history"></i> Payload histories</a>
                </li>
                @endadmin
            </ul>
            <!-- END Sidebar Navigation -->
        </div>
        <!-- END Sidebar Content -->
    </div>
    <!-- END Wrapper for scrolling functionality -->
</div>
<!-- END Main Sidebar -->
