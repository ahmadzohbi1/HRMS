<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                @admin

                @can('view admins')
                    <li>
                        <a href="{{ route('root') }}" class="waves-effect">
                            <i class="bx bx-home-circle"></i>
                            <span key="t-contact">Dashboard</span>
                        </a>
                    </li>
                @endcan


                @can('view admins')
                    <li>
                        <a href="{{ route('admins.index') }}" class="waves-effect">
                            <i class="bx bx-user"></i>
                            <span key="t-contact">Admins</span>
                        </a>
                    </li>
                @endcan

                @can('view roles')
                    <li>
                        <a href="{{ route('roles.index') }}" class="waves-effect">
                            <i class="bx bx-pencil"></i>
                            <span key="t-contact">Roles</span>
                        </a>
                    </li>
                @endcan
                @can('view employees')
                    <li>
                        <a href="{{ route('employees.index') }}" class="waves-effect">
                            <i class="bx bx-user-pin"></i>
                            <span key="t-contact">Employees</span>
                        </a>
                    </li>
                @endcan
                @can('view departments')
                    <li>
                        <a href="{{ route('departments.index') }}" class="waves-effect">
                            <i class="bx bx-building"></i>
                            <span key="t-contact">Departmens</span>
                        </a>
                    </li>
                @endcan
                @can('view positions')
                    <li>
                        <a href="{{ route('positions.index') }}" class="waves-effect">
                            <i class="bx bx-user-check"></i>
                            <span key="t-contact">Positions</span>
                        </a>
                    </li>
                @endcan
                @can('view hour_rate')
                    <li>
                        <a href="{{ route('hour_rate.index') }}" class="waves-effect">
                            <i class="bx bx-dollar-circle"></i>
                            <span key="t-contact">Employees Hour Rate</span>
                        </a>
                    </li>
                @endcan
                @can('view salaries')
                    <li>
                        <a href="{{ route('salary.index') }}" class="waves-effect">
                            <i class="bx bx-money"></i>
                            <span key="t-contact">Salaries</span>
                        </a>
                    </li>
                @endcan
                @can('view shifts')
                    <li>
                        <a href="{{ route('shifts.index') }}" class="waves-effect">
                            <i class="bx bx-timer"></i>
                            <span key="t-contact">Shifts</span>
                        </a>
                    </li>
                @endcan
                @can('view shifts')
                    <li>
                        <a href="{{ route('shifts-rules.index') }}" class="waves-effect">
                            <i class="bx bx-hourglass"></i>
                            <span key="t-contact">Shifts Rules</span>
                        </a>
                    </li>
                @endcan
                @can('view shifts')
                    <li>
                        <a href="{{ route('warnings.index') }}" class="waves-effect">
                            <i class="bx bx-info-circle"></i>
                            <span key="t-contact">Warnings</span>
                        </a>
                    </li>
                @endcan
                @can('view shifts')
                    <li>
                        <a href="{{ route('warnings.index') }}" class="waves-effect">
                            <i class="bx bx-sun"></i>
                            <span key="t-contact">Vacations</span>
                        </a>
                    </li>
                @endcan

                @endadmin

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->