<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Teste</title>
    <style>
        /* Estilos gerais */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        /* Estilo do container do menu */
        nav {
            background-color: #006400; /* Verde mais escuro */
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra leve */
        }

        /* Ícone de hambúrguer */
        .hamburger {
            font-size: 30px;
            color: #FFFFFF; /* Ícone de hambúrguer branco */
            cursor: pointer;
            display: block;
            transition: transform 0.3s ease;
        }

        .hamburger:hover {
            transform: rotate(90deg); /* Efeito rotativo ao passar o mouse */
        }

        /* Estilos da lista de menu */
        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: none; /* Escondido inicialmente no mobile */
            flex-direction: column;
            width: 100%; /* Ocupa toda a largura em telas menores */
            text-align: center;
            background-color: #006400; /* Cor verde para o menu */
            
        }

        .menu li {
            padding: 15px 0;
            border-bottom: 1px solid #006400; /* Cor da borda mais escura */
        }

        .menu li:last-child {
            border-bottom: none;
        }

        .menu li a {
            color: black; /* Texto branco */
            text-decoration: none;
            display: block;
            font-size: 18px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        .menu li a:hover {
            background-color: #32CD32; /* Cor verde mais clara ao passar o mouse */
            padding-left: 15px; /* Efeito de deslocamento */
        }

        /* Responsividade e estilo para telas maiores */
        @media (min-width: 768px) {
            .hamburger {
                display: none; /* Ícone de hambúrguer escondido em telas maiores */
            }

            .menu {
                display: flex; /* Exibir como flex nas telas maiores */
                flex-direction: row; /* Exibir horizontalmente */
                justify-content: flex-end; /* Alinhar à direita */
                width: auto;
                background-color: transparent;
            }

            .menu li {
                border: none;
            }

            .menu li a {
                padding: 10px 20px;
                font-size: 16px;
                color: #FFFFFF; /* Texto branco em telas maiores */
            }

            .menu li a:hover {
                background-color: transparent;
                color: #FF8800; /* Cor de destaque ao passar o mouse */
            }
        }

        /* Transição suave ao abrir e fechar o menu */
        .menu-open {
            display: flex !important;
            flex-direction: column;
        }
    </style>
</head>
<body>

<nav>
    <div class="hamburger" id="hamburger" aria-expanded="false">
        &#9776; <!-- Ícone de hambúrguer -->
    </div>
    <ul class="menu" id="menu">
        <li><a href="http://localhost/aplicativo_acampamento_controle_de_provas/pontuacao_geral/">Pontuação/Placar</a></li>
        <li><a href="http://localhost/aplicativo_acampamento_controle_de_provas/faixas_tempo_user/">Meus Pontos</a></li>
        <li><a href="#">Adicionar Observação</a></li>
        <li><a href="http://localhost/aplicativo_acampamento_controle_de_provas/">Provas</a></li>
        <li><a href="http://localhost/aplicativo_acampamento_controle_de_provas/login/logout.php">Sair</a></li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', () => {
            const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
            hamburger.setAttribute('aria-expanded', !isExpanded);
            menu.classList.toggle('menu-open'); // Alterna a classe para abrir o menu
        });
    });
</script>

</body>
</html>
