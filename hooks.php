<?php

if (!defined("WHMCS")) {
    die("Este arquivo não pode ser acessado diretamente.");
}

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/templates.php';
require_once __DIR__ . '/utils.php';

// Hook para login do cliente
add_hook('ClientLogin', 1, function($vars) {
    try {
        logActivity("Hook ClientLogin acionado - UserID: " . $vars['userid']);
        
        $template = get_template('client_login');
        if (is_template_active('client_login')) {
            // Adiciona informações extras para o login
            $vars['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
            $vars['browser'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $vars['date'] = date('d/m/Y H:i');
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook ClientLogin: " . $e->getMessage());
    }
});

// Hook para registro de cliente
add_hook('ClientAdd', 1, function($vars) {
    try {
        logActivity("Hook ClientAdd acionado - UserID: " . $vars['userid']);
        
        // Busca dados completos do cliente
        $result = select_query('tblclients', '*', ['id' => $vars['userid']]);
        $client = mysql_fetch_array($result);
        
        if ($client) {
            $vars = array_merge($vars, [
                'firstname' => $client['firstname'],
                'lastname' => $client['lastname'],
                'email' => $client['email'],
                'phonenumber' => $client['phonenumber'],
                'companyname' => $client['companyname'],
                'address1' => $client['address1'],
                'address2' => $client['address2'],
                'city' => $client['city'],
                'state' => $client['state'],
                'postcode' => $client['postcode'],
                'country' => $client['country']
            ]);
        }

        $template = get_template('client_register');
        if (is_template_active('client_register')) {
            $message = replace_variables($template, $vars);
            logActivity("Mensagem de registro: " . $message);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook ClientAdd: " . $e->getMessage());
    }
});

// Hook para criação de fatura
add_hook('InvoiceCreation', 1, function($vars) {
    try {
        logActivity("Hook InvoiceCreation acionado - InvoiceID: " . $vars['invoiceid']);
        
        $moduleParams = getModuleConfigParams();
        $template = get_template('invoice_created');
        
        if (is_template_active('invoice_created')) {
            // Busca dados da fatura
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $vars['amount'] = format_as_currency($invoice['total']);
                $vars['duedate'] = date('d/m/Y', strtotime($invoice['duedate']));
                $vars['invoice_url'] = generate_secure_invoice_url($vars['invoiceid'], $vars['userid']);
                $vars['userid'] = $invoice['userid']; // Garantir que temos o userid
            }
            
            $message = replace_variables($template, $vars);
            
            if ($moduleParams['send_pdf'] == 'on') {
                $pdfFile = generateInvoicePDF($vars['invoiceid']);
                send_whatsapp_notification($vars['userid'], $message, $pdfFile);
                if ($pdfFile && file_exists($pdfFile)) {
                    unlink($pdfFile);
                }
            } else {
                send_whatsapp_notification($vars['userid'], $message);
            }
        }
    } catch (Exception $e) {
        logActivity("Erro no hook InvoiceCreation: " . $e->getMessage());
    }
});

// Hook para pagamento de fatura
add_hook('InvoicePaid', 1, function($vars) {
    try {
        logActivity("Hook InvoicePaid acionado - InvoiceID: " . $vars['invoiceid']);
        
        $moduleParams = getModuleConfigParams();
        $template = get_template('invoice_paid');
        
        if (is_template_active('invoice_paid')) {
            // Busca dados da fatura
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $vars['amount'] = format_as_currency($invoice['total']);
                $vars['date'] = date('d/m/Y');
                $vars['userid'] = $invoice['userid']; // Garantir que temos o userid
                $vars['payment_method'] = $invoice['paymentmethod'];
            }
            
            $message = replace_variables($template, $vars);
            
            if ($moduleParams['send_pdf'] == 'on') {
                $pdfFile = generateInvoicePDF($vars['invoiceid']);
                send_whatsapp_notification($vars['userid'], $message, $pdfFile);
                if ($pdfFile && file_exists($pdfFile)) {
                    unlink($pdfFile);
                }
            } else {
                send_whatsapp_notification($vars['userid'], $message);
            }
        }
    } catch (Exception $e) {
        logActivity("Erro no hook InvoicePaid: " . $e->getMessage());
    }
});

// Hook para lembrete de pagamento
add_hook('InvoicePaymentReminder', 1, function($vars) {
    try {
        logActivity("Hook InvoicePaymentReminder acionado - InvoiceID: " . $vars['invoiceid']);
        
        $template = get_template('invoice_payment_reminder');
        if (is_template_active('invoice_payment_reminder')) {
            // Busca dados da fatura
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $vars['amount'] = format_as_currency($invoice['total']);
                $vars['duedate'] = date('d/m/Y', strtotime($invoice['duedate']));
                $vars['invoice_url'] = generate_secure_invoice_url($vars['invoiceid'], $invoice['userid']);
                $vars['userid'] = $invoice['userid'];
                $vars['days_overdue'] = days_until_due($invoice['duedate']) * -1; // Negativo para dias em atraso
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook InvoicePaymentReminder: " . $e->getMessage());
    }
});

// Hook para segundo lembrete de pagamento
add_hook('InvoicePaymentSecondReminder', 1, function($vars) {
    try {
        logActivity("Hook InvoicePaymentSecondReminder acionado - InvoiceID: " . $vars['invoiceid']);
        
        $template = get_template('invoice_payment_reminder_second');
        if (is_template_active('invoice_payment_reminder_second')) {
            // Busca dados da fatura
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $vars['amount'] = format_as_currency($invoice['total']);
                $vars['duedate'] = date('d/m/Y', strtotime($invoice['duedate']));
                $vars['invoice_url'] = generate_secure_invoice_url($vars['invoiceid'], $invoice['userid']);
                $vars['userid'] = $invoice['userid'];
                $vars['days_overdue'] = days_until_due($invoice['duedate']) * -1;
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook InvoicePaymentSecondReminder: " . $e->getMessage());
    }
});

// Hook para terceiro lembrete de pagamento
add_hook('InvoicePaymentThirdReminder', 1, function($vars) {
    try {
        logActivity("Hook InvoicePaymentThirdReminder acionado - InvoiceID: " . $vars['invoiceid']);
        
        $template = get_template('invoice_payment_reminder_final');
        if (is_template_active('invoice_payment_reminder_final')) {
            // Busca dados da fatura
            $result = select_query('tblinvoices', '*', ['id' => $vars['invoiceid']]);
            $invoice = mysql_fetch_array($result);
            
            if ($invoice) {
                $vars['amount'] = format_as_currency($invoice['total']);
                $vars['duedate'] = date('d/m/Y', strtotime($invoice['duedate']));
                $vars['invoice_url'] = generate_secure_invoice_url($vars['invoiceid'], $invoice['userid']);
                $vars['userid'] = $invoice['userid'];
                $vars['days_overdue'] = days_until_due($invoice['duedate']) * -1;
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook InvoicePaymentThirdReminder: " . $e->getMessage());
    }
});

// Hook para criação de serviço
add_hook('AfterModuleCreate', 1, function($vars) {
    try {
        logActivity("Hook AfterModuleCreate acionado - ServiceID: " . $vars['serviceid']);
        
        $template = get_template('service_created');
        if (is_template_active('service_created')) {
            $service_info = get_service_info($vars['serviceid']);
            
            if ($service_info) {
                $vars['service'] = $service_info['product']['name'];
                $vars['domain'] = $service_info['service']['domain'];
                $vars['amount'] = format_as_currency($service_info['service']['amount']);
                $vars['userid'] = $service_info['service']['userid'];
                $vars['nextduedate'] = date('d/m/Y', strtotime($service_info['service']['nextduedate']));
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook AfterModuleCreate: " . $e->getMessage());
    }
});

// Hook para suspensão de serviço
add_hook('AfterModuleSuspend', 1, function($vars) {
    try {
        logActivity("Hook AfterModuleSuspend acionado - ServiceID: " . $vars['serviceid']);
        
        $template = get_template('service_suspended');
        if (is_template_active('service_suspended')) {
            $service_info = get_service_info($vars['serviceid']);
            
            if ($service_info) {
                $vars['service'] = $service_info['product']['name'];
                $vars['domain'] = $service_info['service']['domain'];
                $vars['userid'] = $service_info['service']['userid'];
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook AfterModuleSuspend: " . $e->getMessage());
    }
});

// Hook para pedido pago
add_hook('OrderPaid', 1, function($vars) {
    try {
        logActivity("Hook OrderPaid acionado - OrderID: " . $vars['orderid']);
        
        // Busca dados do pedido
        $result = select_query('tblorders', '*', ['id' => $vars['orderid']]);
        $order = mysql_fetch_array($result);
        
        if (!$order) {
            logActivity("Pedido não encontrado: " . $vars['orderid']);
            return;
        }

        // Busca dados do cliente
        $client_result = select_query('tblclients', '*', ['id' => $order['userid']]);
        $client = mysql_fetch_array($client_result);
        
        if (!$client) {
            logActivity("Cliente não encontrado para o pedido: " . $vars['orderid']);
            return;
        }

        // Prepara as variáveis
        $vars['userid'] = $order['userid'];
        $vars['firstname'] = $client['firstname'];
        $vars['lastname'] = $client['lastname'];
        $vars['email'] = $client['email'];
        $vars['phonenumber'] = $client['phonenumber'];
        $vars['amount'] = format_as_currency($order['amount']);
        
        // Busca itens do pedido
        $items = [];
        
        // Busca produtos de hospedagem
        $hosting_result = select_query('tblhosting', 'packageid,domain', ['orderid' => $vars['orderid']]);
        while ($hosting = mysql_fetch_array($hosting_result)) {
            $product_result = select_query('tblproducts', 'name', ['id' => $hosting['packageid']]);
            $product = mysql_fetch_array($product_result);
            if ($product) {
                $items[] = $product['name'] . (!empty($hosting['domain']) ? ' (' . $hosting['domain'] . ')' : '');
            }
        }
        
        // Busca domínios
        $domain_result = select_query('tbldomains', 'domain,registrationperiod', ['orderid' => $vars['orderid']]);
        while ($domain = mysql_fetch_array($domain_result)) {
            $items[] = "Domínio: " . $domain['domain'] . " (" . $domain['registrationperiod'] . " ano" . ($domain['registrationperiod'] > 1 ? 's' : '') . ")";
        }
        
        // Busca addons
        $addon_result = select_query('tblhostingaddons', 'addonid', ['orderid' => $vars['orderid']]);
        while ($addon = mysql_fetch_array($addon_result)) {
            $addon_info = select_query('tbladdons', 'name', ['id' => $addon['addonid']]);
            $addon_data = mysql_fetch_array($addon_info);
            if ($addon_data) {
                $items[] = "Addon: " . $addon_data['name'];
            }
        }

        $vars['service'] = empty($items) ? "Nenhum item encontrado" : implode("\n• ", $items);
        if (!empty($items)) {
            $vars['service'] = "• " . $vars['service']; // Adiciona bullet no primeiro item
        }
        
        // Notifica o admin
        $template = get_template('admin_new_order');
        if (is_template_active('admin_new_order')) {
            $adminMessage = replace_variables($template, $vars);
            notify_admins($adminMessage);
        }
        
        // Notifica o cliente
        $template = get_template('order_accepted');
        if (is_template_active('order_accepted')) {
            $message = replace_variables($template, $vars);
            logActivity("Mensagem do pedido: " . $message);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook OrderPaid: " . $e->getMessage());
    }
});

// Hook para novo ticket
add_hook('TicketOpen', 1, function($vars) {
    try {
        logActivity("Hook TicketOpen acionado - TicketID: " . $vars['ticketid']);
        
        // Busca dados do ticket
        $result = select_query('tbltickets', '*', ['id' => $vars['ticketid']]);
        $ticket = mysql_fetch_array($result);
        
        if (!$ticket) {
            logActivity("Ticket não encontrado: " . $vars['ticketid']);
            return;
        }

        // Busca dados do cliente
        $client_result = select_query('tblclients', '*', ['id' => $ticket['userid']]);
        $client = mysql_fetch_array($client_result);
        
        if ($client) {
            $vars['userid'] = $ticket['userid'];
            $vars['firstname'] = $client['firstname'];
            $vars['lastname'] = $client['lastname'];
            $vars['email'] = $client['email'];
            $vars['phonenumber'] = $client['phonenumber'];
        }

        $vars['ticket_subject'] = $ticket['title'];
        $vars['ticket_priority'] = $ticket['urgency'];
        $vars['ticket_department'] = get_department_name($ticket['did']);
        
        // Notifica admins sobre novo ticket
        $template = get_template('admin_new_ticket');
        if (is_template_active('admin_new_ticket')) {
            $adminMessage = replace_variables($template, $vars);
            notify_admins($adminMessage);
        }
        
    } catch (Exception $e) {
        logActivity("Erro no hook TicketOpen: " . $e->getMessage());
    }
});

// Hook para resposta de ticket
add_hook('TicketAdminReply', 1, function($vars) {
    try {
        logActivity("Hook TicketAdminReply acionado - TicketID: " . $vars['ticketid']);
        
        $template = get_template('ticket_reply');
        if (is_template_active('ticket_reply')) {
            // Busca dados do ticket
            $result = select_query('tbltickets', '*', ['id' => $vars['ticketid']]);
            $ticket = mysql_fetch_array($result);
            
            if ($ticket) {
                $vars['userid'] = $ticket['userid'];
                $vars['ticket_subject'] = $ticket['title'];
                $vars['ticket_url'] = generate_secure_ticket_url($vars['ticketid'], $ticket['userid']);
            }
            
            $message = replace_variables($template, $vars);
            send_whatsapp_notification($vars['userid'], $message);
        }
    } catch (Exception $e) {
        logActivity("Erro no hook TicketAdminReply: " . $e->getMessage());
    }
});

function send_whatsapp_notification($userid, $message, $attachment = null) {
    try {
        $moduleParams = getModuleConfigParams();
        
        // Verifica se o módulo está desativado
        if ($moduleParams['disabled'] == 'on') {
            logActivity("WhatsApp: Notificação não enviada - módulo desativado");
            return false;
        }
        
        // Verifica se é um userid válido
        if (empty($userid) || !is_numeric($userid)) {
            logActivity("WhatsApp: UserID inválido - " . $userid);
            return false;
        }
        
        $client = select_query('tblclients', '*', ['id' => $userid]);
        $client = mysql_fetch_array($client);
        
        if (!$client) {
            logActivity("WhatsApp: Cliente não encontrado - ID: " . $userid);
            return false;
        }
        
        $phone = validate_phone_number($client['phonenumber']);
        if (!$phone) {
            logActivity("WhatsApp: Número de telefone inválido - Cliente: " . $client['firstname'] . " " . $client['lastname'] . " - Telefone: " . $client['phonenumber']);
            return false;
        }
        
        // Limpa a mensagem
        $message = clean_whatsapp_text($message);
        
        if (empty($message)) {
            logActivity("WhatsApp: Mensagem vazia após limpeza");
            return false;
        }
        
        return whatsapp_send_message($phone, $message, $attachment);
    } catch (Exception $e) {
        logActivity("Erro ao enviar notificação WhatsApp: " . $e->getMessage());
        return false;
    }
}

function notify_admins($message) {
    try {
        $admins = select_query('tbladmins', '*', ['disabled' => 0]);
        while ($admin = mysql_fetch_array($admins)) {
            if (!empty($admin['mobile'])) {
                $phone = validate_phone_number($admin['mobile']);
                if ($phone) {
                    whatsapp_send_message($phone, $message);
                }
            }
        }
    } catch (Exception $e) {
        logActivity("Erro ao notificar admins: " . $e->getMessage());
    }
}

function is_template_active($template_key) {
    try {
        $result = select_query('mod_whatsapp_templates', 'active', ['template_key' => $template_key]);
        $template = mysql_fetch_array($result);
        return $template ? (bool)$template['active'] : true;
    } catch (Exception $e) {
        logActivity("Erro ao verificar status do template: " . $e->getMessage());
        return true;
    }
}
?>