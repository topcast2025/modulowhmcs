# Módulo WhatsApp Notification para WHMCS

Este módulo permite o envio automático de notificações via WhatsApp para clientes do WHMCS usando a API do Botconect.

## Características

- ✅ Notificações automáticas para eventos do WHMCS
- ✅ Templates personalizáveis com emojis
- ✅ Envio de PDFs de faturas
- ✅ Painel administrativo completo
- ✅ Logs detalhados de mensagens
- ✅ Validação de números de telefone
- ✅ Suporte a múltiplos eventos

## Eventos Suportados

### Para Clientes:
- Login do cliente
- Registro de novo cliente
- Criação de fatura
- Pagamento de fatura
- Lembretes de pagamento (1º, 2º e 3º aviso)
- Criação de serviço/hospedagem
- Suspensão de serviço
- Aprovação de pedido
- Resposta de ticket

### Para Administradores:
- Novo pedido recebido
- Novo ticket de suporte
- Pagamento recebido
- Serviço suspenso

## Instalação

1. Faça upload dos arquivos para a pasta `/modules/addons/whatsapp_notification/`
2. No painel administrativo do WHMCS, vá em **Setup > Addon Modules**
3. Encontre "Notificação WhatsApp" e clique em **Activate**
4. Configure as chaves da API do Botconect
5. Personalize os templates conforme necessário

## Configuração

### 1. Obter Chaves da API

1. Acesse [botconect.site](https://botconect.site)
2. Crie uma conta ou faça login
3. No painel, vá em "Configurações" > "API"
4. Copie as chaves "App Key" e "Auth Key"

### 2. Configurar o Módulo

1. No WHMCS, vá em **Addons > Notificação WhatsApp**
2. Na aba "Configurações":
   - Cole as chaves da API
   - Configure o formato da data
   - Ative/desative o envio de PDFs
   - Use "Testar Conexão" para verificar

### 3. Personalizar Templates

- Acesse as abas "Templates Cliente" e "Templates Admin"
- Personalize as mensagens usando as variáveis disponíveis
- Ative/desative templates conforme necessário
- Use emojis para tornar as mensagens mais atrativas

## Variáveis Disponíveis

### Variáveis do Sistema
- `{system_url}` - URL do sistema WHMCS
- `{company_name}` - Nome da empresa
- `{date}` - Data atual (dd/mm/yyyy)
- `{time}` - Hora atual (HH:mm)
- `{datetime}` - Data e hora atual

### Variáveis do Cliente
- `{firstname}` - Primeiro nome
- `{lastname}` - Sobrenome
- `{email}` - Email
- `{phonenumber}` - Telefone
- `{companyname}` - Nome da empresa

### Variáveis da Fatura
- `{invoiceid}` - ID da fatura
- `{amount}` - Valor total
- `{duedate}` - Data de vencimento
- `{invoice_url}` - URL para visualizar a fatura

### Variáveis do Serviço
- `{service}` - Nome do produto/serviço
- `{domain}` - Domínio
- `{nextduedate}` - Próximo vencimento

## Requisitos

- WHMCS 8.0 ou superior
- PHP 7.4 ou superior
- Conta ativa no Botconect
- WhatsApp Business conectado no Botconect

## Formato dos Números

Os números de telefone devem estar no formato internacional:
- Exemplo: 5511999999999 (Brasil + DDD + número)
- O módulo automaticamente remove caracteres especiais
- Adiciona código do país (55) se necessário

## Logs e Relatórios

O módulo mantém logs detalhados de todas as mensagens enviadas:
- Data e hora do envio
- Número de destino
- Conteúdo da mensagem
- Status de entrega
- Código de resposta da API

## Solução de Problemas

### Mensagens não são enviadas
1. Verifique se as chaves da API estão corretas
2. Teste a conexão no painel do módulo
3. Verifique se o WhatsApp está conectado no Botconect
4. Confirme se os números estão no formato correto

### Templates não funcionam
1. Verifique se o template está ativo
2. Confirme se as variáveis estão corretas
3. Teste com um template simples primeiro

### Erros de conexão
1. Verifique a conectividade com a internet
2. Confirme se não há firewall bloqueando
3. Teste as chaves da API no site do Botconect

## Suporte

Para suporte técnico:
- Email: contato@botconect.site
- Site: [botconect.site](https://botconect.site)
- Documentação: [botconect.site/docs](https://botconect.site/docs)

## Changelog

### Versão 10.0
- Lançamento inicial
- Suporte a todos os eventos principais do WHMCS
- Interface administrativa completa
- Sistema de templates personalizáveis
- Logs detalhados
- Validação de números de telefone
- Envio de PDFs de faturas

## Licença

Este módulo é proprietário e licenciado pela Bot Conect.