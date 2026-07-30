<?php
$curPageName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
?>
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="../index">Company name</a>
    <nav id="dashboardNav">
        <div>
            <ul>
                <li class="nav-item me-3 me-3">
                    <a class="nav-link <?php echo  $curPageName == 'index.php' ? 'active' : '' ?>" aria-current="page" href="index">
                        <span data-feather="home"></span>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item me-3">
                    <a class="nav-link <?php echo  $curPageName == 'api.php' ? 'active' : '' ?>" href="api">
                        <span data-feather="send"></span>
                        API
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo  $curPageName == 'documentation.php' ? 'active' : '' ?>" href="documentation">
                        <span data-feather="paperclip"></span>
                        Documentation
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo  $curPageName == 'usage.php' ? 'active' : '' ?>" href="usage">
                        <span data-feather="mouse-pointer"></span>
                        Usage
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link <?php echo  $curPageName == 'support.php' ? 'active' : '' ?>" href="support">
                        <span data-feather="help-circle"></span>
                        Support
                    </a>
                </li>

                <li class="nav-item me-3">
                    <a class="nav-link <?php echo  $curPageName == 'settings.php' ? 'active' : '' ?>" href="settings">
                        <span data-feather="settings"></span>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="../core/logout">Log out</a>
        </div>
    </div>
</header>