const chat = document.getElementById('chat');
const initialChat = chat.innerHTML;

const flows = {
  orcamento: {
    question: 'Quero solicitar um orçamento.',
    answer: 'Claro! Para preparar seu orçamento, precisamos entender o que você procura.\\n\\nQual serviço ou produto deseja contratar?',
    options: [
      ['Informar o que preciso', 'detalhes'],
      ['Voltar ao menu principal', 'menu'],
      ['Falar com atendente', 'atendente']
    ]
  },
  suporte: {
    question: 'Preciso de suporte técnico.',
    answer: 'Vamos ajudar! Para direcionar seu atendimento, escolha a situação:',
    options: [
      ['Computador ou equipamento', 'computador'],
      ['Sistema ou acesso', 'sistema'],
      ['Voltar ao menu principal', 'menu'],
      ['Falar com atendente', 'atendente']
    ]
  },
  duvidas: {
    question: 'Tenho uma dúvida geral.',
    answer: 'Sem problemas. Você pode consultar as informações principais ou falar com nossa equipe.',
    options: [
      ['Consultar informações', 'informacoes'],
      ['Falar com atendente', 'atendente'],
      ['Voltar ao menu principal', 'menu']
    ]
  },
  atendente: {
    question: 'Quero falar com um atendente.',
    answer: 'Certo! Na operação real, esta opção encaminharia a conversa para a equipe responsável.\\n\\nDeixe seu nome e um resumo do que precisa para facilitar o atendimento.',
    options: [['Voltar ao menu principal', 'menu']]
  },
  detalhes: {
    question: 'Informar o que preciso.',
    answer: 'Perfeito. Na próxima etapa, o fluxo solicitaria os dados de contato e os detalhes do pedido. Depois, a solicitação seria encaminhada para a equipe comercial.',
    options: [['Falar com atendente', 'atendente'], ['Voltar ao menu principal', 'menu']]
  },
  computador: {
    question: 'Computador ou equipamento.',
    answer: 'Entendi. O fluxo registraria o tipo de equipamento, o problema apresentado e a melhor forma de contato. Se necessário, a equipe técnica daria continuidade ao atendimento.',
    options: [['Falar com atendente', 'atendente'], ['Voltar ao menu principal', 'menu']]
  },
  sistema: {
    question: 'Sistema ou acesso.',
    answer: 'Entendi. Para direcionar corretamente, a equipe precisaria saber qual sistema está apresentando problema e qual mensagem aparece na tela.',
    options: [['Falar com atendente', 'atendente'], ['Voltar ao menu principal', 'menu']]
  },
  informacoes: {
    question: 'Consultar informações.',
    answer: 'Este espaço pode apresentar horário de atendimento, endereço, serviços disponíveis e perguntas frequentes da empresa.',
    options: [['Falar com atendente', 'atendente'], ['Voltar ao menu principal', 'menu']]
  }
};

function addBubble(text, type) {
  const bubble = document.createElement('div');
  bubble.className = `bubble ${type}`;
  bubble.textContent = text;
  const time = document.createElement('time');
  time.textContent = 'Agora';
  bubble.appendChild(time);
  chat.appendChild(bubble);
  chat.scrollTop = chat.scrollHeight;
}

function showOptions(options) {
  const bubble = document.createElement('div');
  bubble.className = 'bubble incoming menu';
  const title = document.createElement('b');
  title.textContent = 'Como deseja continuar?';
  bubble.appendChild(title);
  options.forEach(([label, key]) => {
    const button = document.createElement('button');
    button.className = 'choice';
    button.textContent = label;
    button.addEventListener('click', () => choose(key, label));
    bubble.appendChild(button);
  });
  const time = document.createElement('time');
  time.textContent = 'Agora';
  bubble.appendChild(time);
  chat.appendChild(bubble);
  chat.scrollTop = chat.scrollHeight;
}

function choose(key, label) {
  if (key === 'menu') {
    addBubble(label, 'outgoing');
    addBubble('Vamos recomeçar. Como podemos ajudar você?', 'incoming');
    showOptions([
      ['1 · Solicitar orçamento', 'orcamento'],
      ['2 · Suporte técnico', 'suporte'],
      ['3 · Dúvidas gerais', 'duvidas'],
      ['4 · Falar com atendente', 'atendente']
    ]);
    return;
  }
  addBubble(label, 'outgoing');
  const flow = flows[key];
  if (!flow) return;
  if (flow.question !== label) addBubble(flow.question, 'outgoing');
  addBubble(flow.answer, 'incoming');
  showOptions(flow.options);
}

document.querySelectorAll('.choice[data-choice]').forEach(button => {
  button.addEventListener('click', () => choose(button.dataset.choice, button.textContent));
});
document.getElementById('restart').addEventListener('click', () => {
  chat.innerHTML = initialChat;
  chat.querySelectorAll('.choice[data-choice]').forEach(button => {
    button.addEventListener('click', () => choose(button.dataset.choice, button.textContent));
  });
});
