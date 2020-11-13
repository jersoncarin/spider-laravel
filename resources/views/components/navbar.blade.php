<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
    <div class="container-fluid">
        <div class="navbar-wrapper">
        <div class="navbar-toggle d-inline">
            <button type="button" class="navbar-toggler">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
            </button>
        </div>
        <a class="navbar-brand" href="/"><img class="img-logo" src="/frontend/static/images/sf-banner.png"></a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav ml-auto">
            <li class="dropdown nav-item">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <i class="tim-icons icon-single-02"></i>
                <b class="caret d-none d-lg-block d-xl-block"></b>
                <p class="d-lg-none">
                {{ Auth::user()->username }}
                </p>
            </a>
            <ul class="dropdown-menu dropdown-navbar">
                <li class="nav-link"><a href="#" data-toggle="modal" data-target="#RULES" class="nav-item dropdown-item">Rules</a></li>
                <li class="nav-link"><a href="/user/profile" class="nav-item dropdown-item">My Profile</a></li>
                <li class="dropdown-divider"></li>
                <li class="nav-link"><a href="/logout" class="nav-item dropdown-item">Log out</a></li>
            </ul>
            </li>
            <li class="separator d-lg-none"></li>
        </ul>
        </div>
    </div>
</nav>