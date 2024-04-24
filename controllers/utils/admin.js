const nav = document.querySelector("nav")

nav.innerHTML = `<nav class="navbar navbar-expand px-4 py-3">
<div class="navbar-collapse collapse">
    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false" id="cuenta-nav">Cuenta: <b
                            id="cuenta-nav">Rodríguez</b></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../admin/perfil.html">Editar perfil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../admin/index.html">Cerrar sesión</a></li>
                    </ul>
                </li>
            </div>
        </ul>
    </div>
</div>
</nav>`
