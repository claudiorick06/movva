<?php
session_start();
if (!isset($_SESSION['id_atendente'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Sistema - Movva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="painelstyle.css">
    <link rel="stylesheet" href="sobrestyle.css">
    <link rel="icon" href="images/logo-fav.png" type="image/png">
</head>

<body>

    <div class="wrapper">
        <nav id="sidebar">
            <div>
                <div class="sidebar-header text-center">
                    <img src="./images/logo.png" alt="Movva Logo" class="brand-logo-img">
                </div>

                <ul class="sidebar-menu">
                    <li class="menu-title">Menu Principal</li>

                    <li>
                        <a href="#homeSubmenu" data-bs-toggle="collapse" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-house-door"></i> Home
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse" id="homeSubmenu">
                            <ul class="submenu">
                                <li><a href="novalocacao.php" class="submenu-link"><i
                                            class="bi bi-plus-circle me-1"></i> Nova Locação</a></li>
                                <li><a href="locacoes.php" class="submenu-link"><i class="bi bi-file-text me-1"></i>
                                        Locações</a></li>
                                <li><a href="dashboard.php#estatisticas" class="submenu-link"><i
                                            class="bi bi-graph-up me-1"></i> Estatísticas</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="mt-2">
                        <a href="#veiculosSubmenu" data-bs-toggle="collapse" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-car-front"></i> Veículos
                            </span>
                            <i class="bi bi-chevron-down small"></i>
                        </a>
                        <div class="collapse" id="veiculosSubmenu">
                            <ul class="submenu">
                                <li><a href="veiculos.php" class="submenu-link">Frota Completa</a></li>
                                <li><a href="veiculos.php?status=Disponível" class="submenu-link">Disponíveis</a></li>
                                <li><a href="veiculos.php?status=Alugado" class="submenu-link">Alugados</a></li>
                                <li><a href="veiculos.php?status=Em manutenção" class="submenu-link">Em Manutenção</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="mt-2">
                        <a href="clientes.php" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-people"></i> Clientes
                            </span>
                        </a>
                    </li>

                    <li class="mt-2">
                        <a href="atendentes.php" class="nav-link">
                            <span class="nav-link-content">
                                <i class="bi bi-person-badge"></i> Colaboradores
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="border-top pt-3">
                <a href="sobre.php" class="nav-link active">
                    <span class="nav-link-content">
                        <i class="bi bi-info-circle"></i> Sobre o sistema
                    </span>
                </a>
                <a href="logout.php" class="nav-link text-danger mt-1">
                    <span class="nav-link-content">
                        <i class="bi bi-box-arrow-right"></i> Sair
                    </span>
                </a>
            </div>
        </nav>

        <main id="content">
            <div class="about-page">

                <!-- Navegação superior vinculada aos slides do Bootstrap Carousel -->
                <nav class="about-nav" aria-label="Tópicos da apresentação">
                    <button type="button" class="nav-slide-btn active" data-bs-target="#sobreCarousel"
                        data-bs-slide-to="0">01. O Problema</button>
                    <button type="button" class="nav-slide-btn" data-bs-target="#sobreCarousel" data-bs-slide-to="1">02.
                        A Aplicação</button>
                    <button type="button" class="nav-slide-btn" data-bs-target="#sobreCarousel" data-bs-slide-to="2">03.
                        Mapa do Site</button>
                    <button type="button" class="nav-slide-btn" data-bs-target="#sobreCarousel" data-bs-slide-to="3">04.
                        Tecnologias</button>
                    <button type="button" class="nav-slide-btn" data-bs-target="#sobreCarousel" data-bs-slide-to="4">05.
                        Banco de Dados</button>
                    <button type="button" class="nav-slide-btn" data-bs-target="#sobreCarousel" data-bs-slide-to="5">06.
                        Regras &amp; Segurança</button>
                </nav>

                <!-- Bootstrap 5 Carousel com efeito Crossfade (.carousel-fade) e troca manual -->
                <div id="sobreCarousel" class="carousel slide carousel-fade" data-bs-interval="false"
                    data-bs-touch="false">
                    <div class="carousel-inner">

                        <!-- SLIDE 01: Descrição do Problema -->
                        <div class="carousel-item active">
                            <div class="section-watermark">01</div>
                            <span class="section-eyebrow">O Problema</span>
                            <h1 class="section-title">O Problema Abordado</h1>
                            <p class="section-lead">
                                Locadoras de veículos que operam com planilhas isoladas ou controles manuais sofrem com
                                falta de sincronia entre o pátio e o balcão, gerando reservas conflitantes, erros no
                                cálculo de diárias e perda de rastreabilidade.
                            </p>

                            <div class="split-columns">
                                <div class="col-muted-line">
                                    <h5 class="fw-bold text-dark mb-3">Cenário Anterior (Manual)</h5>
                                    <ul class="clean-list">
                                        <li>Dificuldade para saber em tempo real quais carros estão livres, alugados ou
                                            em manutenção.</li>
                                        <li>Cálculo manual de períodos sujeito a falhas operacionais no fechamento.</li>
                                        <li>Ausência de histórico vinculado entre cliente, veículo e atendente
                                            responsável.</li>
                                    </ul>
                                </div>

                                <div class="col-accent-line">
                                    <h5 class="fw-bold text-dark mb-3">Proposta do Sistema Movva</h5>
                                    <p class="text-secondary mb-3" style="line-height: 1.65;">
                                        Centralizar toda a rotina da locadora em uma plataforma web única e responsiva,
                                        conectando cadastro de clientes, controle de frota, gestão de equipe e emissão
                                        de contratos.
                                    </p>
                                    <p class="text-secondary mb-0" style="line-height: 1.65;">
                                        Cada retirada ou baixa de locação atualiza automaticamente a disponibilidade do
                                        veículo e alimenta os indicadores gerenciais do sistema.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- SLIDE 02: Informações Detalhadas sobre a Aplicação -->
                        <div class="carousel-item">
                            <div class="section-watermark">02</div>
                            <span class="section-eyebrow">A Aplicação</span>
                            <h2 class="section-title">Informações Detalhadas da Aplicação</h2>
                            <p class="section-lead">
                                Módulos integrados que cobrem desde a configuração inicial do ambiente até o
                                encerramento financeiro de cada locação.
                            </p>

                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="bi bi-shield-lock feature-icon"></i>
                                    <div>
                                        <h5>Setup Inicial &amp; Autenticação</h5>
                                        <p>Tela dedicada de primeiro acesso para criação do Gerente mestre e controle de
                                            login protegido por sessão PHP (<code>$_SESSION</code>).</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-speedometer2 feature-icon"></i>
                                    <div>
                                        <h5>Dashboard Gerencial</h5>
                                        <p>Resumo imediato com contagem de veículos disponíveis, locações ativas,
                                            clientes cadastrados e faturamento total via consultas SQL.</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-car-front feature-icon"></i>
                                    <div>
                                        <h5>Controle Completo de Frota</h5>
                                        <p>Cadastro, edição e filtragem dinâmica por status (<em>Disponível</em>,
                                            <em>Alugado</em> e <em>Em manutenção</em>) com validação de placa única.
                                        </p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-people feature-icon"></i>
                                    <div>
                                        <h5>Gestão de Clientes e Colaboradores</h5>
                                        <p>CRUD completo com busca rápida, padronização numérica de CPF e
                                            telefone, além de controle de status de atendentes.</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-file-earmark-check feature-icon"></i>
                                    <div>
                                        <h5>Abertura e Baixa de Locações</h5>
                                        <p>Cálculo automático do valor total conforme as diárias selecionadas e
                                            sincronização instantânea do status do veículo na entrega e devolução.</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-diagram-2 feature-icon"></i>
                                    <div>
                                        <h5>Arquitetura Modular Limpa</h5>
                                        <p>Separação clara entre camadas de apresentação (HTML/CSS), regras de negócio
                                            (PHP/PDO) e persistência relacional (MySQL).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SLIDE 03: ARQUITETURA E SITEFLOW -->
                        <div class="carousel-item">
                            <div class="section-watermark">03</div>
                            <div class="section-eyebrow">Arquitetura & Navegação</div>
                            <h2 class="section-title">Siteflow do Sistema</h2>

                            <div class="text-center mt-2">
                                <img src="images/siteflow.jpg" alt="Siteflow do Sistema Movva"
                                    class="img-fluid siteflow-img">
                            </div>
                        </div>
                        <!-- SLIDE 04: Tecnologias Utilizadas -->
                        <div class="carousel-item">
                            <div class="section-watermark">04</div>
                            <span class="section-eyebrow">Tecnologias</span>
                            <h2 class="section-title">Tecnologias Utilizadas</h2>
                            <p class="section-lead">
                                Conjunto de ferramentas escolhidas pela portabilidade, segurança no acesso a dados e
                                facilidade de implantação local ou em nuvem.
                            </p>

                            <div class="tech-strip">
                                <div class="tech-col tech-col--accent">
                                    <span class="tech-tag">Back-end</span>
                                    <h5>PHP 8+ &amp; PDO</h5>
                                    <p>Toda a comunicação com o banco utiliza <strong>PHP Data Objects</strong> com
                                        <em>Prepared Statements</em>, prevenindo SQL Injection e facilitando a
                                        portabilidade.
                                    </p>
                                </div>

                                <div class="tech-col">
                                    <span class="tech-tag">Banco de Dados</span>
                                    <h5>MySQL / MariaDB</h5>
                                    <p>Modelagem relacional sob engine <strong>InnoDB</strong> com charset
                                        <code>utf8mb4</code>, chaves primárias, índices únicos e chaves estrangeiras.
                                    </p>
                                </div>

                                <div class="tech-col">
                                    <span class="tech-tag">Front-end</span>
                                    <h5>HTML5, CSS3 &amp; JS</h5>
                                    <p>Estrutura semântica com folhas de estilo modulares (<code>indexstyle.css</code>,
                                        <code>painelstyle.css</code>, <code>sobrestyle.css</code>) e scripts de
                                        validação.
                                    </p>
                                </div>

                                <div class="tech-col">
                                    <span class="tech-tag">Interface &amp; Servidor</span>
                                    <h5>Bootstrap 5 &amp; Apache</h5>
                                    <p>Componentes responsivos com Bootstrap 5.3.2 e Bootstrap Icons, executado sobre
                                        servidor Apache e conexão centralizada em <code>conexao.php</code>.</p>
                                </div>
                            </div>
                        </div>

                        <!-- SLIDE 05: Banco de Dados -->
                        <div class="carousel-item">
                            <div class="section-watermark">05</div>
                            <span class="section-eyebrow">Banco de Dados</span>
                            <h2 class="section-title">Modelagem do Banco de Dados (<code>movva</code>)</h2>
                            <p class="section-lead">
                                Quatro tabelas normalizadas interligadas por relacionamentos 1:N, garantindo integridade
                                referencial em todas as operações.
                            </p>

                            <div class="table-responsive">
                                <table class="clean-db-table">
                                    <thead>
                                        <tr>
                                            <th>Tabela</th>
                                            <th>Chave Primária</th>
                                            <th>Colunas Principais</th>
                                            <th>Restrições e Relacionamentos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold font-monospace">atendentes</td>
                                            <td class="font-monospace">id_atendente</td>
                                            <td><code>nome</code>, <code>cpf</code>, <code>telefone</code>,
                                                <code>email</code>, <code>cargo</code>, <code>data_admissao</code>,
                                                <code>status</code>, <code>login</code>, <code>senha</code>
                                            </td>
                                            <td><code>UNIQUE</code> em <code>cpf</code>, <code>email</code> e
                                                <code>login</code>. Entidade pai de <code>locacoes</code>.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold font-monospace">cliente</td>
                                            <td class="font-monospace">id_cliente</td>
                                            <td><code>nome</code>, <code>cpf</code>, <code>telefone</code>,
                                                <code>email</code>, <code>cep</code>, <code>logradouro</code>,
                                                <code>numero</code>, <code>bairro</code>, <code>cidade</code>,
                                                <code>estado</code>
                                            </td>
                                            <td><code>UNIQUE</code> em <code>cpf</code>. Entidade
                                                pai de <code>locacoes</code>.</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold font-monospace">veiculos</td>
                                            <td class="font-monospace">id_veiculo</td>
                                            <td><code>marca</code>, <code>modelo</code>, <code>ano</code>,
                                                <code>placa</code>, <code>cor</code>, <code>categoria</code>,
                                                <code>valor_diaria</code>, <code>status</code>
                                            </td>
                                            <td><code>UNIQUE</code> em <code>placa</code>. Entidade pai de
                                                <code>locacoes</code>.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold font-monospace">locacoes</td>
                                            <td class="font-monospace">id_locacao</td>
                                            <td><code>id_cliente</code>, <code>id_veiculo</code>,
                                                <code>id_atendente</code>, <code>data_retirada</code>,
                                                <code>data_limite_devolucao</code>, <code>valor_total</code>,
                                                <code>status</code>
                                            </td>
                                            <td>3 <code>FOREIGN KEY</code> ligando Cliente, Veículo e Atendente.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SLIDE 06: Regras de Negócio e Informações Relevantes -->
                        <div class="carousel-item">
                            <div class="section-watermark">06</div>
                            <span class="section-eyebrow">Regras</span>
                            <h2 class="section-title">Regras de Negócio e Segurança</h2>
                            <p class="section-lead">
                                Controles aplicados no back-end e no banco de dados para evitar inconsistências durante
                                o uso contínuo da aplicação.
                            </p>

                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="bi bi-key feature-icon"></i>
                                    <div>
                                        <h5>Trava Automática de Setup</h5>
                                        <p>A rota <code>primeiro_acesso.php</code> consulta <code>COUNT(*)</code> na
                                            tabela <code>atendentes</code> e bloqueia permanentemente o cadastro público
                                            após a criação do primeiro gerente.</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-shield-check feature-icon"></i>
                                    <div>
                                        <h5>Bloqueio de Exclusão com Vínculo</h5>
                                        <p>Clientes, veículos ou colaboradores associados a contratos existentes na
                                            tabela <code>locacoes</code> não podem ser removidos, preservando o
                                            histórico operacional.</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-arrow-repeat feature-icon"></i>
                                    <div>
                                        <h5>Sincronização Automática da Frota</h5>
                                        <p>Somente carros <em>Disponíveis</em> são listados em novas locações. Ao
                                            confirmar o contrato passam a <em>Alugado</em> e na devolução voltam a
                                            <em>Disponível</em>.
                                        </p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <i class="bi bi-funnel feature-icon"></i>
                                    <div>
                                        <h5>Sanitização de Dados Críticos</h5>
                                        <p>CPFs e telefones são limpos para armazenar
                                            apenas dígitos numéricos, eliminando duplicidades por máscara de digitação.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Controlador discreto para avançar/voltar slides no Crossfade -->
    <div class="presenter-dock" aria-label="Navegação entre slides">
        <button type="button" data-bs-target="#sobreCarousel" data-bs-slide="prev" title="Slide anterior">
            <i class="bi bi-chevron-left"></i>
        </button>
        <span class="presenter-indicator" id="secIndicator">01 / 06</span>
        <button type="button" data-bs-target="#sobreCarousel" data-bs-slide="next" title="Próximo slide">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const carouselEl = document.getElementById('sobreCarousel');
        const navBtns = Array.from(document.querySelectorAll('.nav-slide-btn'));
        const indicador = document.getElementById('secIndicator');
        const totalSlides = navBtns.length;

        carouselEl.addEventListener('slide.bs.carousel', function (event) {
            const proximoIndice = event.to;
            const numFormatado = String(proximoIndice + 1).padStart(2, '0');
            const totalFormatado = String(totalSlides).padStart(2, '0');

            indicador.textContent = `${numFormatado} / ${totalFormatado}`;

            navBtns.forEach((btn, idx) => {
                btn.classList.toggle('active', idx === proximoIndice);
            });
        });
    </script>
</body>

</html>