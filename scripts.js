// Função para abrir e fechar o menu ao clicar no hambúrguer
// Função para abrir e fechar o menu ao clicar no hambúrguer
const hamburger = document.getElementById('hamburger');
const menu = document.getElementById('menu');

hamburger.addEventListener('click', () => {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
});


// fim

let horas = 0;
let minutos = 0;
let segundos = 0;
let cronometro;
let pausado = false; // Nova variável para controlar o estado de pausa

const horasEl = document.getElementById('horas');
const minutosEl = document.getElementById('minutos');
const segundosEl = document.getElementById('segundos');
const iniciarBtn = document.getElementById('iniciarBtn');
const pausarBtn = document.getElementById('pausarBtn'); // Novo botão de pausar
const finalizarBtn = document.getElementById('finalizarBtn');

iniciarBtn.addEventListener('click', iniciarCronometro);
pausarBtn.addEventListener('click', pausarCronometro); // Evento para pausar o cronômetro
finalizarBtn.addEventListener('click', finalizarCronometro);

function iniciarCronometro() {
    if (!pausado) { // Apenas iniciar se não estiver pausado
        cronometro = setInterval(() => {
            segundos++;
            if (segundos === 60) {
                segundos = 0;
                minutos++;
            }
            if (minutos === 60) {
                minutos = 0;
                horas++;
            }
            atualizarDisplay();
        }, 1000);
        iniciarBtn.disabled = true;
        pausarBtn.disabled = false;
        finalizarBtn.disabled = false;
    }
    pausado = false; // Reseta o estado de pausa
}

function pausarCronometro() {
    clearInterval(cronometro);
    iniciarBtn.disabled = false;
    pausarBtn.disabled = true; // Desativa o botão de pausar
    finalizarBtn.disabled = false;
    pausado = true; // Marca como pausado
}

function finalizarCronometro() {
    clearInterval(cronometro);
    iniciarBtn.disabled = false; // Habilita o botão de iniciar
    pausarBtn.disabled = true; // Desativa o botão de pausar
    finalizarBtn.disabled = true; // Desativa o botão de finalizar

    // Exibe o modal de confirmação
    const modal = document.getElementById("modalConfirmacao");
    modal.style.display = "block"; // Mostra o modal

    // Eventos dos botões do modal
    document.getElementById("btnConfirmar").onclick = function() {
        enviarTempo(); // Envia o tempo e recarrega a página
        modal.style.display = "none"; // Fecha o modal
    }

    document.getElementById("btnCancelar").onclick = function() {
        modal.style.display = "none"; // Fecha o modal
        iniciarBtn.disabled = false; // Habilita novamente o botão de iniciar
        pausarBtn.disabled = false; // Habilita o botão de pausar
        finalizarBtn.disabled = false; // Habilita o botão de finalizar
    }
}



function atualizarDisplay() {
    horasEl.textContent = horas < 10 ? `0${horas}` : horas;
    minutosEl.textContent = minutos < 10 ? `0${minutos}` : minutos;
    segundosEl.textContent = segundos < 10 ? `0${segundos}` : segundos;
}

function enviarTempo() {
    const provaSalva = localStorage.getItem('provaSelecionada'); // Obtém a prova selecionada do localStorage
    
    if (!provaSalva) {
        console.log('Nenhuma prova selecionada.');
        return; // Se não houver prova selecionada, não envia os dados
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "salvar_tempo.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Tempo enviado com sucesso!');
            window.location.reload(); // Recarrega a página após o sucesso
        }
    };

    // Inclui a prova selecionada nos dados enviados
    xhr.send(`horas=${horas}&minutos=${minutos}&segundos=${segundos}&prova_selecionada=${provaSalva}`);
}

// Seleção das provas (sem alterações)
const listaProvas = document.getElementById('lista-provas');
const salvarButton = document.getElementById('salvar');
const mensagem = document.getElementById('mensagem');
let provaSelecionada = null;

// Adiciona evento de clique para cada item da lista
listaProvas.querySelectorAll('li').forEach(item => {
    item.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        
        // Remove a seleção da prova anterior, se houver
        if (provaSelecionada) {
            provaSelecionada.classList.remove('selected');
        }
        
        // Marca a nova seleção
        provaSelecionada = this;
        provaSelecionada.classList.add('selected');
    });
});

// Evento para salvar a seleção
salvarButton.addEventListener('click', function() {
    if (provaSelecionada) {
        const provaSalva = provaSelecionada.getAttribute('data-value');
        localStorage.setItem('provaSelecionada', provaSalva);
        mensagem.innerText = 'Seleção salva com sucesso: ' + provaSalva;
    } else {
        mensagem.innerText = 'Nenhuma prova selecionada.';
    }
});

// Ao carregar a página, exibe a prova salva, se houver
window.onload = function() {
    const provaSalva = localStorage.getItem('provaSelecionada');
    if (provaSalva) {
        const listItem = listaProvas.querySelector(`li[data-value="${provaSalva}"]`);
        if (listItem) {
            listItem.classList.add('selected'); // Marca como selecionada
            provaSelecionada = listItem; // Armazena a prova selecionada
        }
    }
};
