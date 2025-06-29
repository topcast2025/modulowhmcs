<?php

if (!defined("WHMCS")) {
    die("Este arquivo não pode ser acessado diretamente.");
}

require_once __DIR__ . '/utils.php';

function get_default_templates() {
    return [
        'client_login' => [
            'name' => 'Login do Cliente',
            'message' => "👋 Olá {firstname}!\n\nDetectamos um novo acesso à sua conta em {datetime}.\n\nSe não foi você, entre em contato conosco imediatamente.\n\n*IP:* {ip_address}\n*Navegador:* {browser}"
        ],
        'client_register' => [
            'name' => 'Registro de Cliente',
            'message' => "✨ Bem-vindo(a) {firstname}!\n\nSua conta foi criada com sucesso.\n\n*Dados de acesso:*\nEmail: {email}\nÁrea do cliente: {system_url}\n\nEm caso de dúvidas, entre em contato conosco!"
        ],
        'invoice_created' => [
            'name' => 'Fatura Criada',
            'message' => "📋 *Nova Fatura Gerada*\n\nOlá {firstname},\n\nUma nova fatura foi gerada para você:\n\n*Fatura:* #{invoiceid}\n*Valor:* {amount}\n*Vencimento:* {duedate}\n\n💳 Para visualizar e pagar sua fatura, acesse:\n{invoice_url}\n\n⚠️ Lembre-se: o pagamento deve ser realizado até a data de vencimento para evitar a suspensão dos serviços."
        ],
        'invoice_paid' => [
            'name' => 'Fatura Paga',
            'message' => "✅ *Pagamento Confirmado*\n\nOlá {firstname},\n\nO pagamento da fatura #{invoiceid} foi confirmado com sucesso!\n\n*Valor:* {amount}\n*Data do pagamento:* {date}\n*Método:* {payment_method}\n\nObrigado pela preferência! 🙏\n\nSeus serviços continuam ativos normalmente."
        ],
        'invoice_payment_reminder' => [
            'name' => 'Lembrete de Pagamento de Fatura',
            'message' => "⚠️ *Lembrete de Pagamento*\n\nOlá {firstname},\n\nA fatura #{invoiceid} está próxima do vencimento.\n\n*Valor:* {amount}\n*Vencimento:* {duedate}\n\n🚨 Para evitar a suspensão dos seus serviços, efetue o pagamento o quanto antes:\n{invoice_url}\n\nEm caso de dúvidas, entre em contato conosco!"
        ],
        'invoice_payment_reminder_second' => [
            'name' => 'Lembrete de Pagamento - Segundo Aviso',
            'message' => "🚨 *SEGUNDO AVISO - Fatura Vencida*\n\nOlá {firstname},\n\nA fatura #{invoiceid} está VENCIDA há {days_overdue} dias!\n\n*Valor:* {amount}\n*Vencimento:* {duedate}\n\n⛔ ATENÇÃO: Seus serviços podem ser suspensos a qualquer momento.\n\nRegularize AGORA para evitar interrupções:\n{invoice_url}\n\nPrecisa de ajuda? Entre em contato conosco!"
        ],
        'invoice_payment_reminder_final' => [
            'name' => 'Lembrete de Pagamento - Último Aviso',
            'message' => "⛔ *ÚLTIMO AVISO - SUSPENSÃO IMINENTE*\n\nOlá {firstname},\n\nSua fatura #{invoiceid} está em atraso há {days_overdue} dias e seus serviços serão suspensos nas próximas 24 horas!\n\n*Valor:* {amount}\n*Vencimento:* {duedate}\n\n🔴 AÇÃO URGENTE NECESSÁRIA\n\nRegularize IMEDIATAMENTE para evitar a suspensão:\n{invoice_url}\n\n📞 Em caso de dificuldades, entre em contato conosco HOJE!"
        ],
        'service_created' => [
            'name' => 'Serviço Criado',
            'message' => "🚀 *Serviço Ativado com Sucesso*\n\nOlá {firstname},\n\nSeu serviço foi ativado e está pronto para uso!\n\n*Produto:* {service}\n*Domínio:* {domain}\n*Valor mensal:* {amount}\n*Próximo vencimento:* {nextduedate}\n\n🎯 Acesse sua área do cliente para gerenciar seu serviço:\n{system_url}\n\nPrecisa de ajuda? Nossa equipe está à disposição!"
        ],
        'service_suspended' => [
            'name' => 'Serviço Suspenso',
            'message' => "🔒 *Serviço Suspenso*\n\nOlá {firstname},\n\nInfelizmente, seu serviço foi suspenso devido a pendências financeiras.\n\n*Produto:* {service}\n*Domínio:* {domain}\n\n💡 Para reativar seu serviço:\n1. Acesse sua área do cliente\n2. Quite as faturas pendentes\n3. Aguarde até 24h para reativação automática\n\n🔗 Área do cliente: {system_url}\n\nPrecisa de ajuda? Entre em contato conosco!"
        ],
        'order_accepted' => [
            'name' => 'Pedido Aprovado',
            'message' => "🎉 *Pedido Aprovado e em Processamento*\n\nOlá {firstname},\n\nSeu pedido foi aprovado com sucesso e está sendo processado!\n\n*Valor total:* {amount}\n\n*Produtos/Serviços:*\n{service}\n\n⏱️ Seus serviços serão ativados em até 24 horas.\n\n📊 Acompanhe o status em sua área do cliente:\n{system_url}\n\nObrigado pela confiança!"
        ],
        'ticket_reply' => [
            'name' => 'Resposta de Ticket',
            'message' => "📬 *Nova Resposta no seu Ticket*\n\nOlá {firstname},\n\nHá uma nova resposta no seu ticket de suporte.\n\n*Ticket:* #{ticketid}\n*Assunto:* {ticket_subject}\n\n👀 Acesse para visualizar a resposta:\n{ticket_url}\n\nNossa equipe está sempre pronta para ajudar!"
        ],
        'admin_new_order' => [
            'name' => 'Novo Pedido (Admin)',
            'message' => "🛍️ *Novo Pedido Recebido*\n\n*Cliente:*\nNome: {firstname} {lastname}\nEmail: {email}\nTelefone: {phonenumber}\n\n*Pedido:*\nValor: {amount}\n\n*Produtos/Serviços:*\n{service}\n\n🔧 Acesse o painel administrativo para processar o pedido."
        ],
        'admin_new_ticket' => [
            'name' => 'Novo Ticket (Admin)',
            'message' => "🎫 *Novo Ticket de Suporte*\n\n*Ticket:* #{ticketid}\n*Cliente:* {firstname} {lastname}\n*Email:* {email}\n*Assunto:* {ticket_subject}\n*Prioridade:* {ticket_priority}\n*Departamento:* {ticket_department}\n\n🔧 Acesse o painel administrativo para responder."
        ]
    ];
}

function get_template($template_key) {
    try {
        logActivity("Buscando template: " . $template_key);
        
        $result = select_query('mod_whatsapp_templates', 'message', [
            'template_key' => $template_key
        ]);
        $template = mysql_fetch_array($result);
        
        if ($template && !empty($template['message'])) {
            logActivity("Template encontrado no banco: " . truncate_text($template['message'], 100));
            return $template['message'];
        }
        
        $default_templates = get_default_templates();
        if (isset($default_templates[$template_key])) {
            $insert = [
                'template_key' => $template_key,
                'name' => $default_templates[$template_key]['name'],
                'message' => $default_templates[$template_key]['message'],
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            insert_query('mod_whatsapp_templates', $insert);
            logActivity("Template padrão inserido: " . $template_key);
            return $default_templates[$template_key]['message'];
        }
        
        logActivity("Template não encontrado: " . $template_key);
        return '';
    } catch (Exception $e) {
        logActivity("Erro ao buscar template: " . $e->getMessage());
        return '';
    }
}

function replace_variables($message, $vars) {
    try {
        logActivity("Iniciando substituição de variáveis para mensagem");
        
        // Informações do sistema
        $replacements = [
            '{system_url}' => rtrim(WHMCS\Config\Setting::getValue('SystemURL'), '/'),
            '{company_name}' => WHMCS\Config\Setting::getValue('CompanyName'),
            '{email_support}' => WHMCS\Config\Setting::getValue('Email'),
            '{date}' => date('d/m/Y'),
            '{time}' => date('H:i'),
            '{datetime}' => date('d/m/Y H:i'),
            '{ip_address}' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            '{browser}' => isset($_SERVER['HTTP_USER_AGENT']) ? truncate_text($_SERVER['HTTP_USER_AGENT'], 50) : ''
        ];

        // Dados do cliente
        if (isset($vars['userid']) && !empty($vars['userid'])) {
            $result = select_query('tblclients', '*', ['id' => $vars['userid']]);
            $client = mysql_fetch_array($result);
            
            if ($client) {
                $replacements = array_merge($replacements, [
                    '{firstname}' => $client['firstname'],
                    '{lastname}' => $client['lastname'],
                    '{email}' => $client['email'],
                    '{phonenumber}' => $client['phonenumber'],
                    '{companyname}' => $client['companyname'],
                    '{address1}' => $client['address1'],
                    '{address2}' => $client['address2'],
                    '{city}' => $client['city'],
                    '{state}' => $client['state'],
                    '{postcode}' => $client['postcode'],
                    '{country}' => $client['country'],
                    '{credit}' => format_as_currency($client['credit'])
                ]);
            }
        }

        // Dados da fatura
        if (isset($vars['invoiceid']) && !empty($vars['invoiceid'])) {
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $invoice_url = generate_secure_invoice_url($invoice['id'], $invoice['userid']);
                
                $replacements = array_merge($replacements, [
                    '{invoiceid}' => $invoice['id'],
                    '{amount}' => format_as_currency($invoice['total']),
                    '{subtotal}' => format_as_currency($invoice['subtotal']),
                    '{tax}' => format_as_currency($invoice['tax']),
                    '{tax2}' => format_as_currency($invoice['tax2']),
                    '{credit}' => format_as_currency($invoice['credit']),
                    '{total}' => format_as_currency($invoice['total']),
                    '{balance}' => format_as_currency($invoice['total'] - $invoice['credit']),
                    '{duedate}' => date('d/m/Y', strtotime($invoice['duedate'])),
                    '{datepaid}' => $invoice['datepaid'] ? date('d/m/Y', strtotime($invoice['datepaid'])) : '',
                    '{invoice_url}' => $invoice_url,
                    '{payment_method}' => $invoice['paymentmethod'],
                    '{status}' => $invoice['status']
                ]);
            }
        }

        // Dados do serviço
        if (isset($vars['serviceid']) && !empty($vars['serviceid'])) {
            $service_info = get_service_info($vars['serviceid']);
            
            if ($service_info) {
                $service = $service_info['service'];
                $product = $service_info['product'];
                
                $replacements = array_merge($replacements, [
                    '{service}' => $product ? $product['name'] : '',
                    '{domain}' => $service['domain'],
                    '{username}' => $service['username'],
                    '{password}' => $service['password'],
                    '{serverip}' => $service['serverip'],
                    '{serverhost}' => $service['serverhostname'],
                    '{dedicatedip}' => $service['dedicatedip'],
                    '{firstpaymentamount}' => format_as_currency($service['firstpaymentamount']),
                    '{recurringamount}' => format_as_currency($service['amount']),
                    '{billingcycle}' => $service['billingcycle'],
                    '{nextduedate}' => date('d/m/Y', strtotime($service['nextduedate'])),
                    '{status}' => $service['domainstatus']
                ]);
            }
        }

        // Dados do ticket
        if (isset($vars['ticketid']) && !empty($vars['ticketid'])) {
            $result = select_query('tbltickets', '*', ['id' => $vars['ticketid']]);
            $ticket = mysql_fetch_array($result);
            
            if ($ticket) {
                $ticket_url = generate_secure_ticket_url($ticket['id'], $ticket['userid']);
                
                $replacements = array_merge($replacements, [
                    '{ticketid}' => $ticket['tid'],
                    '{ticket_subject}' => $ticket['title'],
                    '{ticket_message}' => truncate_text(strip_tags($ticket['message']), 100),
                    '{ticket_status}' => $ticket['status'],
                    '{ticket_priority}' => $ticket['urgency'],
                    '{ticket_department}' => get_department_name($ticket['did']),
                    '{ticket_url}' => $ticket_url
                ]);
            }
        }

        // Adiciona todas as variáveis do array $vars que ainda não foram definidas
        foreach ($vars as $key => $value) {
            $var_key = '{' . $key . '}';
            if (!isset($replacements[$var_key])) {
                // Se for array, converte para string legível
                if (is_array($value)) {
                    $replacements[$var_key] = array_to_readable_string($value);
                } else {
                    $replacements[$var_key] = (string)$value;
                }
            }
        }

        // Remove variáveis vazias ou substitui por valores padrão
        foreach ($replacements as $key => $value) {
            if (empty($value) && $value !== '0') {
                // Define valores padrão para algumas variáveis importantes
                switch ($key) {
                    case '{firstname}':
                        $replacements[$key] = 'Cliente';
                        break;
                    case '{lastname}':
                        $replacements[$key] = '';
                        break;
                    case '{company_name}':
                        $replacements[$key] = 'Nossa Empresa';
                        break;
                    default:
                        $replacements[$key] = '';
                }
            }
        }

        // Aplica as substituições
        $final_message = str_replace(array_keys($replacements), array_values($replacements), $message);
        
        // Remove linhas vazias excessivas
        $final_message = preg_replace('/\n{3,}/', "\n\n", $final_message);
        
        // Remove espaços no início e fim
        $final_message = trim($final_message);
        
        logActivity("Mensagem final após substituição: " . truncate_text($final_message, 200));
        
        return $final_message;
    } catch (Exception $e) {
        logActivity("Erro ao substituir variáveis: " . $e->getMessage());
        return $message;
    }
}

function save_template($template_key, $message) {
    try {
        $result = select_query('mod_whatsapp_templates', 'id', [
            'template_key' => $template_key
        ]);
        $template = mysql_fetch_array($result);
        
        if ($template) {
            $update = [
                'message' => $message,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $success = update_query('mod_whatsapp_templates', $update, [
                'id' => $template['id']
            ]);
        } else {
            $default_templates = get_default_templates();
            $insert = [
                'template_key' => $template_key,
                'name' => isset($default_templates[$template_key]) ? $default_templates[$template_key]['name'] : $template_key,
                'message' => $message,
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $success = insert_query('mod_whatsapp_templates', $insert);
        }
        
        if (!$success) {
            logActivity("Erro ao salvar template WhatsApp: " . mysql_error());
            return [
                'success' => false,
                'message' => 'Erro ao salvar template: ' . mysql_error()
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Template salvo com sucesso!'
        ];
        
    } catch (Exception $e) {
        logActivity("Erro ao salvar template WhatsApp: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erro ao salvar template: ' . $e->getMessage()
        ];
    }
}

function get_template_last_update($template_key) {
    try {
        $result = select_query('mod_whatsapp_templates', 'updated_at', [
            'template_key' => $template_key
        ]);
        $template = mysql_fetch_array($result);
        
        if ($template && $template['updated_at']) {
            return date('d/m/Y H:i', strtotime($template['updated_at']));
        }
        
        return 'Nunca atualizado';
    } catch (Exception $e) {
        logActivity("Erro ao buscar última atualização do template: " . $e->getMessage());
        return 'Erro ao verificar';
    }
}
?>