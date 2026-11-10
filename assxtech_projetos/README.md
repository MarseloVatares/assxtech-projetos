# Projetos demonstrativos — ASSX Tech

Pacote com três projetos independentes para estudo, teste e apresentação:
1. Página comercial com fluxo de atendimento via WhatsApp (HTML, CSS e JavaScript).
2. Controle de estoque (PHP + MySQL).
3. Gestão financeira (PHP + MySQL).

## Importante antes de entregar
Estes projetos são uma base funcional e personalizável. Revise os requisitos oficiais da seleção e adapte os nomes, campos e funcionalidades ao que a ASSX Tech solicitar. Não apresente os projetos como trabalho de cliente real: são projetos demonstrativos desenvolvidos para avaliação.

## Requisitos
- Windows 10/11
- XAMPP com Apache, PHP 8.1+ e MySQL/MariaDB
- Navegador atualizado
- Para publicar, hospedagem que aceite PHP e MySQL

## Instalação no Windows com XAMPP
1. Instale e abra o XAMPP.
2. Inicie Apache e MySQL.
3. Copie a pasta `assxtech_projetos` para `C:\xampp\htdocs\`.
4. Abra `http://localhost/phpmyadmin`.
5. Importe o arquivo `banco-de-dados/assxtech_projetos.sql`.
6. Confira as credenciais no arquivo `config.php` de cada sistema. O padrão local do XAMPP costuma ser usuário `root` e senha vazia.
7. Acesse:
   - Página comercial: `http://localhost/assxtech_projetos/01-pagina-comercial/`
   - Estoque: `http://localhost/assxtech_projetos/02-controle-estoque/`
   - Financeiro: `http://localhost/assxtech_projetos/03-gestao-financeira/`

## Configurar WhatsApp
Abra `01-pagina-comercial/script.js` e altere:
`const WHATSAPP_NUMBER = "5591999999999";`

Use o número real com código do país +55, DDD e telefone, somente números. O valor atual é fictício e deve ser substituído antes da publicação.

## Publicação
- A página HTML pode ser publicada em hospedagem estática (por exemplo, GitHub Pages ou Netlify).
- Os sistemas PHP precisam de hospedagem com PHP e MySQL. GitHub Pages não executa PHP.
- Depois de publicar, teste todos os formulários, operações de cadastro/edição/exclusão e o acesso ao banco.

## Funcionalidades incluídas

### Página comercial
- Layout responsivo para celular e computador.
- Seções de apresentação, serviços, sobre e contato.
- Formulário que valida nome e assunto.
- Montagem de mensagem e abertura do WhatsApp com `wa.me`.
- Número de WhatsApp configurável em um único arquivo.

### Controle de estoque
- Cadastro, edição e exclusão de produtos.
- Busca por nome, código ou categoria.
- Quantidade, preço unitário e valor total estimado.
- Indicador visual para produtos com cinco unidades ou menos.
- Consultas preparadas com PDO e proteção CSRF nos formulários.

### Gestão financeira
- Registro de receitas e despesas.
- Filtro por mês.
- Cálculo automático de receitas, despesas e resultado do mês.
- Histórico com exclusão de lançamentos.
- Consultas preparadas com PDO e proteção CSRF nos formulários.

## Segurança e limites
- Os exemplos foram pensados para ambiente de demonstração local.
- Antes de uso real, adicione autenticação, autorização por usuário, backups, logs, política de privacidade e configuração segura de produção.
- Não publique com a senha padrão do XAMPP. Use credenciais fortes e permissões mínimas.
- O sistema financeiro é um controle simples de lançamentos, não substitui contabilidade.
- O WhatsApp abre uma conversa com texto preenchido; não envia mensagens automaticamente.
