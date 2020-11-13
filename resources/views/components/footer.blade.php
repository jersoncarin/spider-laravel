<footer class="footer">
    <div class="container-fluid">
        <ul class="nav">
        <li class="nav-item">
            <a href="/arena" class="nav-link">
            Arena
            </a>
        </li>
        <li class="nav-item">
            <a  data-toggle="modal" data-target="#RULES" href='#' class="nav-link">
            Rules
            </a>
        </li>
        <li class="nav-item">
            <a href="/api/docs/" class="nav-link">
            API Docs
            </a>
        </li>
        </ul>
        <div class="copyright">
        © {{ date('Y') }} {{ config('app.name', 'Spider Fighting International') }}
        </div>
    </div>
</footer>